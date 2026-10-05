<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
   
    

    $mail->Host = "sandbox.smtp.mailtrap.io";
    $mail->SMTPAuth = true;
    
    $mail->Username = 'a29bb1b3929012';
    $mail->Password = '5275c685dd861a';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 2525;


    $mail->setFrom(
        "test@example.com",
        "ADMIN-GUIDE"
    );

    $mail->addAddress(
        "test@example.com"
    );

    $mail->isHTML(true);

    $mail->Subject = "Test ADMIN-GUIDE";

    $mail->Body = "
        <h2>Test SMTP ADMIN-GUIDE</h2>
        <p>Si tu vois ce message, PHPMailer fonctionne.</p>
    ";

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "Email envoyé avec succès."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur SMTP : " . $mail->ErrorInfo
    ], JSON_UNESCAPED_UNICODE);
}