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

if (!$data || !isset($data['score']) || !isset($data['company'])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos", "received" => $data]);
    exit;
}

$score = intval($data['score']);
$total_yes = intval($data['total_yes'] ?? 0);
$total_answered = intval($data['total_answered'] ?? 0);
$company = trim($data['company']) ?: 'Sin especificar';

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
    $stmt = $con->prepare("INSERT INTO audit_results (score_percentage, total_yes, total_answered, company) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $con->error);
    }
    
    $stmt->bind_param("iiis", $score, $total_yes, $total_answered, $company);
    
    if ($stmt->execute()) {
        $insert_id = $stmt->insert_id;
        echo json_encode([
            "success" => true, 
            "id" => $insert_id,
            "message" => "Auditoría guardada correctamente"
        ]);
        error_log("Auditoría guardada con ID: " . $insert_id);
    } else {
        throw new Exception("Error ejecutando la consulta: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
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
?>
