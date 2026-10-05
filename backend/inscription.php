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
    ]);
    exit;
}

$nom = trim($_POST["nom"] ?? "");
$prenom = trim($_POST["prenom"] ?? "");
$email = trim($_POST["email"] ?? "");
$telephone = trim($_POST["telephone"] ?? "");
$mot_de_passe = $_POST["mot_de_passe"] ?? "";

if (
    $nom === "" ||
    $prenom === "" ||
    $email === "" ||
    $telephone === "" ||
    $mot_de_passe === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont obligatoires."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ]);
    exit;
}

try {

    $verification = $pdo->prepare(
        "SELECT id FROM utilisateurs WHERE email = :email"
    );

    $verification->execute([
        ":email" => $email
    ]);

    if ($verification->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Cette adresse email existe déjà."
        ]);
        exit;
    }

    $mot_de_passe_hash = password_hash(
        $mot_de_passe,
        PASSWORD_DEFAULT
    );

    $email_token = bin2hex(random_bytes(32));
$email_token_expiration = date(
    "Y-m-d H:i:s",
    time() + 3600
);

    $sql = "INSERT INTO utilisateurs
    (
        nom,
        prenom,
        email,
        telephone,
        mot_de_passe,
        role,
        email_verifie,
        email_token,
        email_token_expiration
    )
    VALUES
    (
        :nom,
        :prenom,
        :email,
        :telephone,
        :mot_de_passe,
        'citoyen',
        0,
        :email_token,
        :email_token_expiration
    )";
    

    $stmt = $pdo->prepare($sql);

   $stmt->execute([
    ":nom" => $nom,
    ":prenom" => $prenom,
    ":email" => $email,
    ":telephone" => $telephone,
    ":mot_de_passe" => $mot_de_passe_hash,
    ":email_token" => $email_token,
    ":email_token_expiration" => $email_token_expiration
]);

try {

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";

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
        $email,
        $prenom . " " . $nom
    );

    $lien_verification =
        "http://localhost/stage/backend/verifier_email.php?token="
        . urlencode($email_token);

    $mail->isHTML(true);

    $mail->Subject = "Vérification de votre adresse email - ADMIN-GUIDE";

    $mail->Body = "
        <h2>Bienvenue sur ADMIN-GUIDE</h2>

        <p>
            Bonjour " . htmlspecialchars($prenom, ENT_QUOTES, "UTF-8") . ",
        </p>

        <p>
            Votre compte a été créé avec succès.
        </p>

        <p>
            Pour vérifier votre adresse email, cliquez sur le bouton ci-dessous :
        </p>

        <p>
            <a href=\"" . $lien_verification . "\"
               style=\"
                   display:inline-block;
                   padding:12px 20px;
                   background:#2563eb;
                   color:white;
                   text-decoration:none;
                   border-radius:6px;
               \">
                Vérifier mon adresse email
            </a>
        </p>

        <p>
            Ce lien est valable pendant 1 heure.
        </p>

        <p>
            Si vous n'êtes pas à l'origine de cette inscription,
            vous pouvez ignorer cet email.
        </p>

        <p>
            L'équipe ADMIN-GUIDE
        </p>
    ";

    $mail->send();

    echo json_encode([
        "success" => true,
        "message" => "Compte créé. Un email de vérification vous a été envoyé."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Compte créé, mais l'email de vérification n'a pas pu être envoyé."
    ], JSON_UNESCAPED_UNICODE);
}

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la création du compte."
    ]);
}

