<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

/*function loadEnv(string $path): void {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val);

        // Quita comillas si vienen "así"
        $val = trim($val, "\"'");

        $_ENV[$key] = $val;
    }
}*/

function envOrFail(string $key): string {
    $v = $_ENV[$key] ?? '';
    if ($v === '') {
        http_response_code(500);
        exit("Falta configurar {$key} en .env");
    }
    return $v;
}


if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit('Método no permitido');
}

//Honeypot anti-spam
$company = trim($_POST['company'] ?? '');
if($company !== ''){
    exit('OK');
}

//Campos del formulario
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

//Validaciones
if($name === '' || $email ==='' || $phone==='' || $service ==='' || $message === ''){
    http_response_code(400);
    exit('Faltan datos');
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    exit('Correo inválido');
}

$emailSafe = str_replace(["\r", "\n"], '', $email);

// Variables SMTP desde .env (y falla claro si falta algo)
$smtpHost = envOrFail('SMTP_HOST');
$smtpUser = envOrFail('SMTP_USER');
$smtpPass = envOrFail('SMTP_PASS');
$smtpPort = (int) envOrFail('SMTP_PORT');

// Opcional (si quieres manejar destinatario distinto)
$toEmail = $_ENV['MAIL_TO'] ?? $smtpUser;
$toName  = $_ENV['MAIL_TO_NAME'] ?? 'Contacto';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];
    $mail->Port = (int) $_ENV['SMTP_PORT'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet = 'UTF-8';



    if ($smtpPort === 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->setFrom($_ENV['SMTP_USER'], 'Formulario Web');
    $mail->addAddress($_ENV['SMTP_USER'], 'Contacto');
    $mail->addAddress($toEmail, $toName);
    $mail->addReplyTo($emailSafe, $name);

    /*$mail->addReplyTo($email, $name);
    $mail->addAddress($_ENV['SMTP_USER'], 'Contacto');
    $mail->addReplyTo($email, $name);*/

    $mail->Subject = "Nuevo mensaje desde la web";

    $mail->Body =
    "Nombre: {$name}\n" .
    "Teléfono: {$phone}\n" .
    "Email: {$emailSafe}\n\n" .
    "Servicio: {$service}\n" .
    "Mensaje:\n{$message}\n";

    $mail->send();

    http_response_code(200);
    echo "OK";
    exit;

}catch(Exception $e){
    error_log("MAiler Error: " . $mail->ErrorInfo);
    http_response_code(500);
    echo "Error al enviar el mensaje";
}

