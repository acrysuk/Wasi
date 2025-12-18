<?php
// enviar-asociacion.php - VERSIÓN MUY SIMPLE
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener datos
$input = [];
if (empty($_POST)) {
    $jsonInput = file_get_contents('php://input');
    if (!empty($jsonInput)) {
        $input = json_decode($jsonInput, true);
    }
} else {
    $input = $_POST;
}

// Guardar en log
$logFile = __DIR__ . '/asociaciones-simulation.log';
$logContent = date('Y-m-d H:i:s') . " - Nueva solicitud de: " . 
              ($input['nombre'] ?? '') . " " . ($input['apellido'] ?? '') . "\n" .
              "DNI: " . ($input['dni'] ?? '') . "\n" .
              "Email: " . ($input['email'] ?? '') . "\n" .
              "----------------------------------------\n";

file_put_contents($logFile, $logContent, FILE_APPEND);

// Respuesta
echo json_encode([
    'success' => true,
    'message' => '✅ Solicitud de asociación recibida (modo desarrollo)',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
