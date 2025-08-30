<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../config/config.php';

if (!isset($con)) {
  if (isset($conn)) $con = $conn;
  else {
    http_response_code(500);
    echo json_encode(["error" => "No hay conexión a la base de datos"]);
    exit;
  }
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con->set_charset('utf8mb4');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["error" => "Método no permitido"]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (
  !$data ||
  !isset($data['score_percentage'], $data['total_yes'], $data['total_no'],
          $data['total_na'], $data['total_answered'], $data['description'],
          $data['company'], $data['answers'])
) {
  http_response_code(400);
  echo json_encode(["error" => "Datos incompletos", "received" => $data]);
  exit;
}

$score_percentage = (int)$data['score_percentage'];
$total_yes        = (int)$data['total_yes'];
$total_no         = (int)$data['total_no'];
$total_na         = (int)$data['total_na'];
$total_answered   = (int)$data['total_answered'];
$company          = trim($data['company']) ?: 'Sin especificar';
$description      = trim($data['description'] ?? '');
$answers          = (array)$data['answers'];

try {
  $con->begin_transaction();

  $stmt = $con->prepare("
    INSERT INTO audit_results
      (score_percentage, total_yes, total_no, total_na, total_answered, description, company)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("iiiiiss", $score_percentage, $total_yes, $total_no, $total_na, $total_answered, $description, $company);
  $stmt->execute();
  $audit_result_id = $stmt->insert_id;
  $stmt->close();

  $section_risks = calculateSectionRisks($con, $answers);

  if (!empty($section_risks)) {
    $stmt2 = $con->prepare("
      INSERT INTO section_audit_results
        (section_id, audit_result_id, integrity, confidentiality, availability)
      VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($section_risks as $section_id => $risks) {
      $stmt2->bind_param("iisss",
        $section_id, $audit_result_id,
        $risks['integrity'], $risks['confidentiality'], $risks['availability']
      );
      $stmt2->execute();
    }
    $stmt2->close();
  }

  $con->commit();

  $sections_info = getSectionsInfo($con, array_keys($section_risks));

  echo json_encode([
    "success"       => true,
    "id"            => $audit_result_id,
    "message"       => "Auditoría y análisis de riesgos guardados correctamente",
    "section_risks" => $section_risks,
    "sections_info" => $sections_info
  ]);
} catch (Throwable $e) {
  try { $con->rollback(); } catch (Throwable $ign) {}
  http_response_code(500);
  echo json_encode(["error" => "Error al guardar la auditoría", "details" => $e->getMessage()]);
} finally {
  if (isset($con) && $con instanceof mysqli) $con->close();
}

/* === helpers === */

function getSectionsInfo(mysqli $con, array $section_ids): array {
  if (empty($section_ids)) return [];
  $ph = implode(',', array_fill(0, count($section_ids), '?'));
  $sql = "SELECT id, CODE, title FROM sections WHERE id IN ($ph)";
  $stmt = $con->prepare($sql);
  $stmt->bind_param(str_repeat('i', count($section_ids)), ...$section_ids);
  $stmt->execute();
  $res = $stmt->get_result();

  $out = [];
  while ($r = $res->fetch_assoc()) {
    $out[(int)$r['id']] = ['code' => $r['CODE'], 'title' => $r['title']];
  }
  $stmt->close();
  return $out;
}

function calculateSectionRisks(mysqli $con, array $answers): array {
  $section_risks = [];

  $sections = $con->query("SELECT id, CODE FROM sections ORDER BY sort_order");
  while ($sec = $sections->fetch_assoc()) {
    $section_id = (int)$sec['id'];

    $sql = "
      SELECT 
        q.CODE  AS question_code,
        rt.CODE AS risk_code,
        qr.LEVEL AS level
      FROM questions q
      JOIN activities a ON q.activity_id = a.id
      JOIN sections  s ON a.section_id = s.id
      LEFT JOIN question_risks qr ON q.id = qr.question_id
      LEFT JOIN risk_types    rt ON qr.risk_type_id = rt.id
      WHERE s.id = ?
      ORDER BY q.CODE, rt.id
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $qres = $stmt->get_result();

    $risk_scores = [
      'I' => ['score' => 100],
      'C' => ['score' => 100],
      'D' => ['score' => 100],
    ];

    while ($row = $qres->fetch_assoc()) {
      $risk_code  = $row['risk_code'];   // I|C|D
      $risk_level = $row['level'];       // P|S
      if (!$risk_code) continue;

      $normalized = normalizeCodeToName($row['question_code']);
      if (isset($answers[$normalized]) && $answers[$normalized] === 'no') {
        $penalty = ($risk_level === 'P') ? 25 : 10;
        $risk_scores[$risk_code]['score'] -= $penalty;
      }
    }
    $stmt->close();

    $section_risks[$section_id] = [
      'integrity'       => scoreToColor($risk_scores['I']['score']),
      'confidentiality' => scoreToColor($risk_scores['C']['score']),
      'availability'    => scoreToColor($risk_scores['D']['score'])
    ];
  }

  return $section_risks;
}

function scoreToColor(int $score): string {
  if ($score >= 90) return 'VERDE';
  if ($score >= 70) return 'AMARILLO';
  return 'ROJO';
}
function normalizeCodeToName(string $code): string {
  return strtolower(str_replace('.', '_', trim($code)));
}
