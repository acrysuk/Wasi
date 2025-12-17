<?php
// Configuración
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $asunto = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));
    
    // Validar datos
    $errores = [];
    
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    if (empty($asunto)) {
        $errores[] = "El asunto es requerido";
    }
    
    if (empty($mensaje)) {
        $errores[] = "El mensaje es requerido";
    }
    
    // Si hay errores, mostrarlos
    if (!empty($errores)) {
        echo json_encode(['success' => false, 'errors' => $errores]);
        exit;
    }
    
    // Crear el objeto PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'c2881163.ferozo.com'; // Servidor de tu hosting
        $mail->SMTPAuth = true;
        $mail->Username = 'contacto@wasi.mutual.ar'; // Tu email
        $mail->Password = 'Contact@2025'; // Tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port = 465; // Puerto SMTP SSL
        
        // Configuración del correo
        $mail->setFrom('contacto@wasi.mutual.ar', 'Wasi Mutual');
        $mail->addAddress('info@wasi.mutual.ar'); // Correo donde recibirás los mensajes
        $mail->addReplyTo($email, $nombre); // Para que puedan responder al usuario
        
        // Asunto y cuerpo del mensaje
        $mail->Subject = 'Nuevo mensaje de contacto: ' . $asunto;
        
        // Cuerpo del mensaje en HTML
        $mail->isHTML(true);
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #1a365d; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
                .content { background-color: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #ddd; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #1a365d; }
                .value { color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Nuevo mensaje de contacto - Wasi Mutual</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Nombre:</div>
                        <div class='value'>$nombre</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>$email</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Teléfono:</div>
                        <div class='value'>" . ($telefono ?: 'No proporcionado') . "</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Asunto:</div>
                        <div class='value'>$asunto</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Mensaje:</div>
                        <div class='value'>$mensaje</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Fecha y hora:</div>
                        <div class='value'>" . date('d/m/Y H:i:s') . "</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // También versión texto plano para clientes de correo que no soportan HTML
        $mail->AltBody = "Nuevo mensaje de contacto de Wasi Mutual\n\n" .
                         "Nombre: $nombre\n" .
                         "Email: $email\n" .
                         "Teléfono: " . ($telefono ?: 'No proporcionado') . "\n" .
                         "Asunto: $asunto\n" .
                         "Mensaje: $mensaje\n\n" .
                         "Fecha: " . date('d/m/Y H:i:s');
        
        // Enviar correo
        if ($mail->send()) {
            echo json_encode(['success' => true, 'message' => '¡Mensaje enviado con éxito! Te responderemos en un plazo máximo de 48 horas.']);
        } else {
            echo json_encode(['success' => false, 'errors' => ['Error al enviar el mensaje. Por favor, inténtalo nuevamente.']]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'errors' => ['Error del servidor de correo: ' . $mail->ErrorInfo]]);
    }
    
} else {
    echo json_encode(['success' => false, 'errors' => ['Método no permitido']]);
}
?>