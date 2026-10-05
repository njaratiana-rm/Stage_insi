<?php

require_once "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$email = trim(
    strtolower($_POST["email"] ?? "")
);

if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            nom,
            prenom,
            email,
            email_verifie
        FROM utilisateurs
        WHERE email = :email
        LIMIT 1
    ");

    $stmt->execute([
        ":email" => $email
    ]);

    $utilisateur =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {

        echo json_encode([
            "success" => false,
            "message" => "Aucun compte ne correspond à cette adresse email."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if ((int)$utilisateur["email_verifie"] === 1) {

        echo json_encode([
            "success" => false,
            "message" => "Cette adresse email est déjà vérifiée."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Générer un nouveau token
    $email_token =
        bin2hex(random_bytes(32));

    // Validité : 1 heure
    $email_token_expiration =
        date(
            "Y-m-d H:i:s",
            time() + 3600
        );

    // Enregistrer le nouveau token
    $update = $pdo->prepare("
        UPDATE utilisateurs
        SET
            email_token = :email_token,
            email_token_expiration = :email_token_expiration
        WHERE id = :id
    ");

    $update->execute([
        ":email_token" =>
            $email_token,

        ":email_token_expiration" =>
            $email_token_expiration,

        ":id" =>
            $utilisateur["id"]
    ]);

    // Lien de vérification
    $lien_verification =
        "http://localhost/stage/backend/verifier_email.php?token="
        . urlencode($email_token);

    // PHPMailer
    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->Host =
        "sandbox.smtp.mailtrap.io";

    $mail->SMTPAuth = true;

    $mail->AuthType =
        "LOGIN";

    /*
     * Utilise ici les mêmes identifiants
     * Mailtrap que dans ton inscription.php
     */
    $mail->Username =
        "a29bb1b3929012";

    $mail->Password =
        "5275c685dd861a";

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 2525;

    $mail->CharSet = "UTF-8";

    $mail->setFrom(
        "no-reply@admin-guide.local",
        "ADMIN'GUIDE"
    );

    $mail->addAddress(
        $utilisateur["email"],
        $utilisateur["prenom"] . " " . $utilisateur["nom"]
    );

    $mail->isHTML(true);

    $mail->Subject =
        "Vérification de votre adresse email - ADMIN'GUIDE";

    $mail->Body = "
        <h2>Vérification de votre adresse email</h2>

        <p>
            Bonjour "
            . htmlspecialchars(
                $utilisateur["prenom"],
                ENT_QUOTES,
                "UTF-8"
            )
            . ",
        </p>

        <p>
            Vous avez demandé un nouvel email de vérification
            pour votre compte ADMIN'GUIDE.
        </p>

        <p>
            Cliquez sur le bouton ci-dessous pour vérifier
            votre adresse email :
        </p>

        <p>
            <a
                href=\""
                . htmlspecialchars(
                    $lien_verification,
                    ENT_QUOTES,
                    "UTF-8"
                )
                . "\"
                style=\"
                    display:inline-block;
                    padding:12px 20px;
                    background:#2563eb;
                    color:white;
                    text-decoration:none;
                    border-radius:6px;
                \"
            >
                Vérifier mon adresse email
            </a>
        </p>

        <p>
            Ce lien est valable pendant 1 heure.
        </p>

        <p>
            Si vous n'êtes pas à l'origine de cette demande,
            vous pouvez ignorer cet email.
        </p>
    ";

    $mail->AltBody =
        "Vérifiez votre adresse email en ouvrant ce lien : "
        . $lien_verification;

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" =>
            "Un nouvel email de vérification vous a été envoyé."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Impossible d'envoyer l'email de vérification."
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Erreur lors du traitement de la demande."
    ], JSON_UNESCAPED_UNICODE);
}