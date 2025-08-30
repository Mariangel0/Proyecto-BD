<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../../config/config.php';

$sql = "
SELECT
  s.id   AS section_id, s.CODE AS section_code, s.title AS section_title, s.sort_order AS s_order,
  a.id   AS activity_id, a.CODE AS activity_code, a.title AS activity_title, a.sort_order AS a_order,
  q.id   AS question_id, q.CODE AS question_code, q.TEXT  AS question_text, q.sort_order AS q_order,
  MAX(CASE WHEN rt.CODE='I' THEN qr.LEVEL END) AS riesgo_I,
  MAX(CASE WHEN rt.CODE='C' THEN qr.LEVEL END) AS riesgo_C,
  MAX(CASE WHEN rt.CODE='D' THEN qr.LEVEL END) AS riesgo_D,
  COALESCE(GROUP_CONCAT(DISTINCT n.CODE ORDER BY n.CODE SEPARATOR ', '),'') AS normas
FROM questions q
JOIN activities a ON a.id = q.activity_id
JOIN sections  s ON s.id = a.section_id
LEFT JOIN question_risks qr ON qr.question_id = q.id
LEFT JOIN risk_types rt     ON rt.id = qr.risk_type_id
LEFT JOIN question_norms qn ON qn.question_id = q.id
LEFT JOIN norms n           ON n.id = qn.norm_id
GROUP BY s.id, a.id, q.id
ORDER BY s.sort_order, a.sort_order, q.sort_order
";

$res = $con->query($sql);
if (!$res) {
  http_response_code(500);
  echo json_encode(['error' => $con->error], JSON_UNESCAPED_UNICODE);
  exit;
}

$sections = [];
while ($r = $res->fetch_assoc()) {
  $sid = (int)$r['section_id'];
  $aid = (int)$r['activity_id'];

  if (!isset($sections[$sid])) {
    $sections[$sid] = [
      'id' => $sid,
      'code' => $r['section_code'],
      'title' => $r['section_title'],
      'activities' => []
    ];
  }
  if (!isset($sections[$sid]['activities'][$aid])) {
    $sections[$sid]['activities'][$aid] = [
      'id' => $aid,
      'code' => $r['activity_code'],
      'title' => $r['activity_title'],
      'questions' => []
    ];
  }

  $sections[$sid]['activities'][$aid]['questions'][] = [
    'id'   => (int)$r['question_id'],
    'code' => $r['question_code'],
    'text' => $r['question_text'],
    'riesgos' => [
      'I' => $r['riesgo_I'], // 'P' | 'S' | null
      'C' => $r['riesgo_C'],
      'D' => $r['riesgo_D'],
    ],
    'normas' => $r['normas']
  ];
}

$out = array_values(array_map(function($sec){
  $sec['activities'] = array_values(array_map(function($act){
    $act['questions'] = array_values($act['questions']);
    return $act;
  }, $sec['activities']));
  return $sec;
}, $sections));

echo json_encode($out, JSON_UNESCAPED_UNICODE);
