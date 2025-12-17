<?php
// Configuración
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $datos = [
        'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
        'nombre' => htmlspecialchars(trim($_POST['nombre'])),
        'apellido' => htmlspecialchars(trim($_POST['apellido'])),
        'dni' => htmlspecialchars(trim($_POST['dni'])),
        'fecha_nacimiento' => htmlspecialchars(trim($_POST['fecha_nacimiento'])),
        'sexo' => htmlspecialchars(trim($_POST['sexo'])),
        'estado_civil' => htmlspecialchars(trim($_POST['estado_civil'])),
        'telefono_celular' => htmlspecialchars(trim($_POST['telefono_celular'])),
        'telefono_linea' => htmlspecialchars(trim($_POST['telefono_linea'] ?? '')),
        'domicilio' => htmlspecialchars(trim($_POST['domicilio'])),
        'numero' => htmlspecialchars(trim($_POST['numero'])),
        'piso' => htmlspecialchars(trim($_POST['piso'] ?? '')),
        'dpto' => htmlspecialchars(trim($_POST['dpto'] ?? '')),
        'codigo_postal' => htmlspecialchars(trim($_POST['codigo_postal'])),
        'localidad' => htmlspecialchars(trim($_POST['localidad'])),
        'nacionalidad' => htmlspecialchars(trim($_POST['nacionalidad']))
    ];
    
    // Validar datos requeridos
    $errores = [];
    $camposRequeridos = ['email', 'nombre', 'apellido', 'dni', 'fecha_nacimiento', 'sexo', 
                        'estado_civil', 'telefono_celular', 'domicilio', 'numero', 
                        'codigo_postal', 'localidad', 'nacionalidad'];
    
    foreach ($camposRequeridos as $campo) {
        if (empty($datos[$campo])) {
            $errores[] = "El campo " . str_replace('_', ' ', $campo) . " es requerido";
        }
    }
    
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
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
        $mail->Host = 'c2881163.ferozo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'contacto@wasi.mutual.ar';
        $mail->Password = 'Contact@2025';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Configuración del correo
        $mail->setFrom('contacto@wasi.mutual.ar', 'Wasi Mutual');
        $mail->addAddress('info@wasi.mutual.ar');
        $mail->addReplyTo($datos['email'], $datos['nombre'] . ' ' . $datos['apellido']);
        
        // Asunto
        $mail->Subject = 'Nueva solicitud de asociación: ' . $datos['nombre'] . ' ' . $datos['apellido'];
        
        // Cuerpo del mensaje en HTML
        $mail->isHTML(true);
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 700px; margin: 0 auto; padding: 20px; }
                .header { background-color: #1a365d; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                .content { background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; border: 1px solid #ddd; }
                .section { margin-bottom: 25px; }
                .section-title { color: #1a365d; border-bottom: 2px solid #009198; padding-bottom: 10px; margin-bottom: 15px; font-size: 18px; }
                .field { margin-bottom: 10px; display: flex; }
                .label { font-weight: bold; min-width: 200px; color: #555; }
                .value { color: #333; }
                .highlight { background-color: #e6f7ff; padding: 15px; border-radius: 5px; border-left: 4px solid #009198; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>¡NUEVA SOLICITUD DE ASOCIACIÓN!</h2>
                    <p>Wasi Mutual</p>
                </div>
                <div class='content'>
                    
                    <div class='section'>
                        <h3 class='section-title'>📋 DATOS PERSONALES</h3>
                        <div class='field'><div class='label'>Email:</div><div class='value'>" . $datos['email'] . "</div></div>
                        <div class='field'><div class='label'>Nombre:</div><div class='value'>" . $datos['nombre'] . "</div></div>
                        <div class='field'><div class='label'>Apellido:</div><div class='value'>" . $datos['apellido'] . "</div></div>
                        <div class='field'><div class='label'>DNI:</div><div class='value'>" . $datos['dni'] . "</div></div>
                        <div class='field'><div class='label'>Fecha de Nacimiento:</div><div class='value'>" . $datos['fecha_nacimiento'] . "</div></div>
                        <div class='field'><div class='label'>Sexo:</div><div class='value'>" . $datos['sexo'] . "</div></div>
                        <div class='field'><div class='label'>Estado Civil:</div><div class='value'>" . $datos['estado_civil'] . "</div></div>
                        <div class='field'><div class='label'>Teléfono Celular:</div><div class='value'>" . $datos['telefono_celular'] . "</div></div>
                        <div class='field'><div class='label'>Teléfono de Línea:</div><div class='value'>" . ($datos['telefono_linea'] ?: 'No proporcionado') . "</div></div>
                    </div>
                    
                    <div class='section'>
                        <h3 class='section-title'>🏠 DOMICILIO</h3>
                        <div class='field'><div class='label'>Calle:</div><div class='value'>" . $datos['domicilio'] . "</div></div>
                        <div class='field'><div class='label'>Número:</div><div class='value'>" . $datos['numero'] . "</div></div>
                        <div class='field'><div class='label'>Piso:</div><div class='value'>" . ($datos['piso'] ?: '-') . "</div></div>
                        <div class='field'><div class='label'>Departamento:</div><div class='value'>" . ($datos['dpto'] ?: '-') . "</div></div>
                        <div class='field'><div class='label'>Código Postal:</div><div class='value'>" . $datos['codigo_postal'] . "</div></div>
                        <div class='field'><div class='label'>Localidad:</div><div class='value'>" . $datos['localidad'] . "</div></div>
                        <div class='field'><div class='label'>Nacionalidad:</div><div class='value'>" . $datos['nacionalidad'] . "</div></div>
                    </div>
                    
                    <div class='highlight'>
                        <p><strong>📅 Fecha de solicitud:</strong> " . date('d/m/Y H:i:s') . "</p>
                        <p><strong>🌐 Enviado desde:</strong> Página Web Wasi Mutual</p>
                        <p><strong>✅ Condiciones aceptadas:</strong> SÍ</p>
                    </div>
                    
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Versión texto plano
        $mail->AltBody = "¡NUEVA SOLICITUD DE ASOCIACIÓN!\n\n" .
                        "DATOS PERSONALES:\n" .
                        "================\n" .
                        "Email: " . $datos['email'] . "\n" .
                        "Nombre: " . $datos['nombre'] . "\n" .
                        "Apellido: " . $datos['apellido'] . "\n" .
                        "DNI: " . $datos['dni'] . "\n" .
                        "Fecha de Nacimiento: " . $datos['fecha_nacimiento'] . "\n" .
                        "Sexo: " . $datos['sexo'] . "\n" .
                        "Estado Civil: " . $datos['estado_civil'] . "\n" .
                        "Teléfono Celular: " . $datos['telefono_celular'] . "\n" .
                        "Teléfono de Línea: " . ($datos['telefono_linea'] ?: 'No proporcionado') . "\n\n" .
                        
                        "DOMICILIO:\n" .
                        "==========\n" .
                        "Calle: " . $datos['domicilio'] . "\n" .
                        "Número: " . $datos['numero'] . "\n" .
                        "Piso: " . ($datos['piso'] ?: '-') . "\n" .
                        "Departamento: " . ($datos['dpto'] ?: '-') . "\n" .
                        "Código Postal: " . $datos['codigo_postal'] . "\n" .
                        "Localidad: " . $datos['localidad'] . "\n" .
                        "Nacionalidad: " . $datos['nacionalidad'] . "\n\n" .
                        
                        "Fecha de solicitud: " . date('d/m/Y H:i:s') . "\n" .
                        "Enviado desde: Página Web Wasi Mutual\n" .
                        "Condiciones aceptadas: SÍ";
        
        // Enviar correo
        if ($mail->send()) {
            echo json_encode(['success' => true, 'message' => '¡Solicitud enviada con éxito! Nos pondremos en contacto contigo en breve.']);
        } else {
            echo json_encode(['success' => false, 'errors' => ['Error al enviar la solicitud. Por favor, inténtalo nuevamente.']]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'errors' => ['Error del servidor de correo: ' . $mail->ErrorInfo]]);
    }
    
} else {
    echo json_encode(['success' => false, 'errors' => ['Método no permitido']]);
}
?>