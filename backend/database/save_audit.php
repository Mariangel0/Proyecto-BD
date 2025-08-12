<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

error_log("Datos recibidos: " . $input);

if (
    !$data || 
    !isset($data['score_percentage']) || 
    !isset($data['total_yes']) || 
    !isset($data['total_no']) || 
    !isset($data['total_na']) || 
    !isset($data['total_answered']) || 
    !isset($data['description']) || 
    !isset($data['company']) ||
    !isset($data['answers']) 
) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos", "received" => $data]);
    exit;
}

$score_percentage = intval($data['score_percentage']);
$total_yes = intval($data['total_yes']);
$total_no = intval($data['total_no']);
$total_na = intval($data['total_na']);
$total_answered = intval($data['total_answered']);
$company = trim($data['company']) ?: 'Sin especificar';
$description = trim($data['description'] ?? '');
$answers = $data['answers']; 

if (!isset($con)) {
    if (isset($conn)) {
        $con = $conn;
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error de conexión a la base de datos"]);
        exit;
    }
}

try {

    $con->autocommit(FALSE);
    
    
    $stmt = $con->prepare("INSERT INTO audit_results (score_percentage, total_yes, total_no, total_na, total_answered, description, company) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error preparando la consulta principal: " . $con->error);
    }
    
    $stmt->bind_param("iiiiiss", $score_percentage, $total_yes, $total_no, $total_na, $total_answered, $description, $company);
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando la consulta principal: " . $stmt->error);
    }
    
    $audit_result_id = $stmt->insert_id;
    $stmt->close();
    
    $section_risks = calculateSectionRisks($con, $answers);
    
    $stmt_section = $con->prepare("INSERT INTO section_audit_results (section_id, audit_result_id, integrity, confidentiality, availability) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt_section) {
        throw new Exception("Error preparando consulta de secciones: " . $con->error);
    }
    
    foreach ($section_risks as $section_id => $risks) {
        $stmt_section->bind_param("iisss", 
            $section_id, 
            $audit_result_id, 
            $risks['integrity'], 
            $risks['confidentiality'], 
            $risks['availability']
        );
        
        if (!$stmt_section->execute()) {
            throw new Exception("Error guardando riesgos de sección $section_id: " . $stmt_section->error);
        }
    }
    
    $stmt_section->close();
    
    $con->commit();
    
    $sections_info = getSectionsInfo($con, array_keys($section_risks));
    
    echo json_encode([
        "success" => true,
        "id" => $audit_result_id,
        "message" => "Auditoría y análisis de riesgos guardados correctamente",
        "section_risks" => $section_risks,
        "sections_info" => $sections_info
    ]);
    
    error_log("Auditoría guardada con ID: " . $audit_result_id);
    
} catch (Exception $e) {
    $con->rollback();
    error_log("Error guardando auditoría: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "Error al guardar la auditoría",
        "details" => $e->getMessage()
    ]);
}

if (isset($con)) {
    $con->close();
}

function getSectionsInfo($con, $section_ids) {
    $sections_info = [];
    
    if (empty($section_ids)) return $sections_info;
    
    $placeholders = str_repeat('?,', count($section_ids) - 1) . '?';
    $query = "SELECT id, CODE, title FROM sections WHERE id IN ($placeholders)";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param(str_repeat('i', count($section_ids)), ...$section_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $sections_info[$row['id']] = [
            'code' => $row['CODE'],
            'title' => $row['title']
        ];
    }
    
    $stmt->close();
    return $sections_info;
}


function calculateSectionRisks($con, $answers) {
    $section_risks = [];
    
    $sections_query = "SELECT id, CODE FROM sections ORDER BY sort_order";
    $sections_result = $con->query($sections_query);
    
    if (!$sections_result) {
        throw new Exception("Error obteniendo secciones: " . $con->error);
    }
    
    while ($section = $sections_result->fetch_assoc()) {
        $section_id = $section['id'];
        
        $questions_query = "
            SELECT 
                q.CODE as question_code,
                qr.risk_type_id,
                qr.LEVEL,
                rt.CODE as risk_code
            FROM questions q
            JOIN activities a ON q.activity_id = a.id
            JOIN sections s ON a.section_id = s.id
            LEFT JOIN question_risks qr ON q.id = qr.question_id
            LEFT JOIN risk_types rt ON qr.risk_type_id = rt.id
            WHERE s.id = ?
            ORDER BY q.CODE, rt.id
        ";
        
        $stmt = $con->prepare($questions_query);
        $stmt->bind_param("i", $section_id);
        $stmt->execute();
        $questions_result = $stmt->get_result();
        
        $risk_scores = [
            'I' => ['score' => 100, 'total_questions' => 0],
            'C' => ['score' => 100, 'total_questions' => 0],  
            'D' => ['score' => 100, 'total_questions' => 0]  
        ];
        
        while ($question = $questions_result->fetch_assoc()) {
            $question_code = $question['question_code'];
            $risk_code = $question['risk_code'];
            $risk_level = $question['LEVEL'];
            
            if (!$risk_code) continue;
            
            $risk_scores[$risk_code]['total_questions']++;
            
            $normalized_code = normalizeCodeToName($question_code);
            if (isset($answers[$normalized_code]) && $answers[$normalized_code] === 'no') {
                $penalty = ($risk_level === 'P') ? 25 : 10; 
                $risk_scores[$risk_code]['score'] -= $penalty;
            }
        }
        
        $stmt->close();

        $section_risks[$section_id] = [
            'integrity' => scoreToColor($risk_scores['I']['score']),
            'confidentiality' => scoreToColor($risk_scores['C']['score']),
            'availability' => scoreToColor($risk_scores['D']['score'])
        ];
    }
    
    return $section_risks;
}

function scoreToColor($score) {
    if ($score >= 90) return 'VERDE';
    if ($score >= 70) return 'AMARILLO';
    return 'ROJO';
}

function normalizeCodeToName($code) {
    return strtolower(str_replace('.', '_', trim($code)));
}
?>
