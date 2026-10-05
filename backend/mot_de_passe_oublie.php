<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";
require_once __DIR__ . "/../vendor/autoload.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$email = trim($_POST["email"] ?? "");

if ($email === "") {

    echo json_encode([
        "success" => false,
        "message" => "Veuillez saisir votre adresse email."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email
        FROM utilisateurs
        WHERE email = :email
        LIMIT 1
    ");

    $stmt->execute([
        ":email" => $email
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {

        echo json_encode([
            "success" => false,
            "message" => "Aucun compte ne correspond à cette adresse email."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Générer un token sécurisé
    $reset_token = bin2hex(random_bytes(32));

    // Token valable pendant 1 heure
    $reset_token_expiration = date(
        "Y-m-d H:i:s",
        time() + 3600
    );

    // Enregistrer le token
    $update = $pdo->prepare("
        UPDATE utilisateurs
        SET
            reset_token = :reset_token,
            reset_token_expiration = :reset_token_expiration
        WHERE id = :id
    ");

    $update->execute([
        ":reset_token" => $reset_token,
        ":reset_token_expiration" => $reset_token_expiration,
        ":id" => $utilisateur["id"]
    ]);

    // Préparer l'email
    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->Host = "sandbox.smtp.mailtrap.io";
    $mail->SMTPAuth = true;
    $mail->AuthType = "LOGIN";

    $mail->Username = "a29bb1b3929012";
    $mail->Password = "5275c685dd861a";

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 2525;

    $mail->setFrom(
        "test@example.com",
        "ADMIN-GUIDE"
    );

    $mail->addAddress(
        $utilisateur["email"],
        $utilisateur["prenom"] . " " . $utilisateur["nom"]
    );

    // Lien de réinitialisation
    $lien_reset =
        "http://localhost/stage/backend/reinitialiser_mot_de_passe.php?token="
        . urlencode($reset_token);

    $mail->isHTML(true);

    $mail->Subject = "Réinitialisation de votre mot de passe - ADMIN-GUIDE";

    $mail->Body = "
        <h2>Réinitialisation du mot de passe</h2>

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
            Une demande de réinitialisation de votre mot de passe
            a été effectuée pour votre compte ADMIN-GUIDE.
        </p>

        <p>
            Cliquez sur le bouton ci-dessous pour choisir
            un nouveau mot de passe :
        </p>

        <p>
            <a href=\"" . $lien_reset . "\"
               style=\"
                   display:inline-block;
                   padding:12px 20px;
                   background:#2563eb;
                   color:white;
                   text-decoration:none;
                   border-radius:6px;
               \">
                Réinitialiser mon mot de passe
            </a>
        </p>

        <p>
            Ce lien est valable pendant 1 heure.
        </p>

        <p>
            Si vous n'êtes pas à l'origine de cette demande,
            vous pouvez ignorer cet email.
        </p>

        <p>
            L'équipe ADMIN-GUIDE
        </p>
    ";

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "Un email de réinitialisation vous a été envoyé."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Impossible d'envoyer l'email de réinitialisation."
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération du compte."
    ], JSON_UNESCAPED_UNICODE);
}