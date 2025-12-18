<?php
// enviar-contacto.php - VERSIÓN MUY SIMPLE
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Manejar OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido',
        'errors' => ['Solo se acepta POST']
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

// Validar campos requeridos
$errors = [];
$required = ['nombre', 'email', 'asunto', 'mensaje'];

foreach ($required as $field) {
    if (empty($input[$field])) {
        $errors[] = ucfirst($field) . " es requerido";
    }
}

// Validar email
if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email no válido";
}

// Si hay errores
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de validación',
        'errors' => $errors
    ]);
    exit;
}

// Guardar datos en log (simulación)
$logFile = __DIR__ . '/email-simulation.log';
$logContent = date('Y-m-d H:i:s') . " - Contacto de: " . 
              ($input['nombre'] ?? '') . " <" . ($input['email'] ?? '') . ">\n" .
              "Asunto: " . ($input['asunto'] ?? '') . "\n" .
              "Mensaje: " . substr(($input['mensaje'] ?? ''), 0, 100) . "...\n" .
              "----------------------------------------\n";

file_put_contents($logFile, $logContent, FILE_APPEND);

// Respuesta de éxito
echo json_encode([
    'success' => true,
    'message' => '✅ Mensaje recibido correctamente (modo desarrollo)',
    'data' => [
        'nombre' => $input['nombre'] ?? '',
        'email' => $input['email'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ]
]);
?>
