<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración
$destinatario = "tu-email@wasimutual.com"; // Cambia por tu email
$asunto = "Nueva solicitud de asociación - Wasi Mutual";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $datos = json_decode($_POST['datos'], true);
        $metodoPago = $_POST['metodo_pago'];
        
        // Validar datos requeridos
        if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['dni'])) {
            throw new Exception('Faltan datos requeridos');
        }
        
        // Procesar archivo adjunto (comprobante)
        $archivoAdjunto = null;
        $nombreArchivo = null;
        
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $archivoAdjunto = $_FILES['comprobante']['tmp_name'];
            $nombreArchivo = $_FILES['comprobante']['name'];
            $tipoArchivo = $_FILES['comprobante']['type'];
            $tamañoArchivo = $_FILES['comprobante']['size'];
            
            // Validar tamaño del archivo (máximo 5MB)
            if ($tamañoArchivo > 5 * 1024 * 1024) {
                throw new Exception('El archivo es demasiado grande. Máximo 5MB permitido.');
            }
            
            // Validar tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!in_array($tipoArchivo, $tiposPermitidos)) {
                throw new Exception('Tipo de archivo no permitido. Solo JPG, PNG o PDF.');
            }
        }
        
        // Construir el cuerpo del mensaje
        $mensaje = "
        <html>
        <head>
            <title>Nueva Solicitud de Asociación</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4a6bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .field { margin-bottom: 10px; }
                .label { font-weight: bold; color: #333; }
                .value { color: #666; }
                .footer { margin-top: 20px; padding: 10px; text-align: center; color: #888; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nueva Solicitud de Asociación</h1>
                </div>
                <div class='content'>
                    <h2>Datos del Solicitante</h2>
                    
                    <div class='field'>
                        <span class='label'>Nombre completo:</span>
                        <span class='value'>" . htmlspecialchars($datos['nombre']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>DNI:</span>
                        <span class='value'>" . htmlspecialchars($datos['dni']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Email:</span>
                        <span class='value'>" . htmlspecialchars($datos['email']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Teléfono:</span>
                        <span class='value'>" . htmlspecialchars($datos['telefono']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Dirección:</span>
                        <span class='value'>" . htmlspecialchars($datos['direccion']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Localidad:</span>
                        <span class='value'>" . htmlspecialchars($datos['localidad']) . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Método de pago seleccionado:</span>
                        <span class='value'>" . ($metodoPago === 'qr' ? 'Pago con QR' : 'Transferencia Bancaria') . "</span>
                    </div>
                    
                    <div class='field'>
                        <span class='label'>Fecha de solicitud:</span>
                        <span class='value'>" . date('d/m/Y H:i:s') . "</span>
                    </div>
                </div>
                <div class='footer'>
                    <p>Este email fue generado automáticamente desde el formulario de asociación de Wasi Mutual</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Cabeceras del email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "From: Wasi Mutual <no-reply@wasimutual.com>" . "\r\n";
        $headers .= "Reply-To: " . $datos['email'] . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Si hay archivo adjunto, procesarlo
        if ($archivoAdjunto && $nombreArchivo) {
            // Leer el archivo
            $fileContent = file_get_contents($archivoAdjunto);
            $fileContent = chunk_split(base64_encode($fileContent));
            
            // Generar un boundary único
            $uid = md5(uniqid(time()));
            $boundary = "----=_NextPart_".$uid;
            
            // Cabeceras para email con adjunto
            $headers = "From: Wasi Mutual <no-reply@wasimutual.com>" . "\r\n";
            $headers .= "Reply-To: " . $datos['email'] . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Construir el cuerpo del mensaje con adjunto
            $mensajeCompleto = "--".$boundary."\r\n";
            $mensajeCompleto .= "Content-type: text/html; charset=utf-8\r\n";
            $mensajeCompleto .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $mensajeCompleto .= $mensaje . "\r\n\r\n";
            
            // Adjuntar archivo
            $mensajeCompleto .= "--".$boundary."\r\n";
            $mensajeCompleto .= "Content-Type: application/octet-stream; name=\"".$nombreArchivo."\"\r\n";
            $mensajeCompleto .= "Content-Transfer-Encoding: base64\r\n";
            $mensajeCompleto .= "Content-Disposition: attachment; filename=\"".$nombreArchivo."\"\r\n\r\n";
            $mensajeCompleto .= $fileContent . "\r\n\r\n";
            $mensajeCompleto .= "--".$boundary."--";
            
            $mensaje = $mensajeCompleto;
        }
        
        // Enviar el email
        if (mail($destinatario, $asunto, $mensaje, $headers)) {
            // También enviar confirmación al usuario
            $asuntoUsuario = "Confirmación de solicitud de asociación - Wasi Mutual";
            $mensajeUsuario = "
            <html>
            <head>
                <title>Confirmación de Solicitud</title>
            </head>
            <body>
                <h2>¡Gracias por tu interés en asociarte a Wasi Mutual!</h2>
                <p>Hemos recibido tu solicitud de asociación correctamente.</p>
                <p><strong>Datos recibidos:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> " . htmlspecialchars($datos['nombre']) . "</li>
                    <li><strong>DNI:</strong> " . htmlspecialchars($datos['dni']) . "</li>
                    <li><strong>Email:</strong> " . htmlspecialchars($datos['email']) . "</li>
                    <li><strong>Método de pago:</strong> " . ($metodoPago === 'qr' ? 'Pago con QR' : 'Transferencia Bancaria') . "</li>
                </ul>
                <p>Estamos procesando tu solicitud y en las próximas 48 horas hábiles nos contactaremos contigo para confirmar tu asociación.</p>
                <p>Si tenés alguna duda, podés contactarnos a wasi@gmail.com</p>
                <br>
                <p>Saludos cordiales,<br>El equipo de Wasi Mutual</p>
            </body>
            </html>
            ";
            
            $headersUsuario = "MIME-Version: 1.0" . "\r\n";
            $headersUsuario .= "Content-type: text/html; charset=utf-8" . "\r\n";
            $headersUsuario .= "From: Wasi Mutual <no-reply@wasimutual.com>" . "\r\n";
            $headersUsuario .= "Reply-To: wasi@gmail.com" . "\r\n";
            
            mail($datos['email'], $asuntoUsuario, $mensajeUsuario, $headersUsuario);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Solicitud enviada correctamente. Te contactaremos pronto.'
            ]);
        } else {
            throw new Exception('Error al enviar el email. Por favor, intentá nuevamente.');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
}
?>