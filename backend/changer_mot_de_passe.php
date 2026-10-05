<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$token = trim($_POST["token"] ?? "");
$nouveau_mot_de_passe = $_POST["nouveau_mot_de_passe"] ?? "";

if ($token === "" || $nouveau_mot_de_passe === "") {

    echo json_encode([
        "success" => false,
        "message" => "Informations manquantes."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (strlen($nouveau_mot_de_passe) < 6) {

    echo json_encode([
        "success" => false,
        "message" => "Le mot de passe doit contenir au moins 6 caractères."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT id, reset_token_expiration
        FROM utilisateurs
        WHERE reset_token = :token
        LIMIT 1
    ");

    $stmt->execute([
        ":token" => $token
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {

        echo json_encode([
            "success" => false,
            "message" => "Lien de réinitialisation invalide."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if (
        empty($utilisateur["reset_token_expiration"]) ||
        strtotime($utilisateur["reset_token_expiration"]) < time()
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Le lien de réinitialisation a expiré."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $mot_de_passe_hash = password_hash(
        $nouveau_mot_de_passe,
        PASSWORD_DEFAULT
    );

    $update = $pdo->prepare("
        UPDATE utilisateurs
        SET
            mot_de_passe = :mot_de_passe,
            reset_token = NULL,
            reset_token_expiration = NULL
        WHERE id = :id
    ");

    $update->execute([
        ":mot_de_passe" => $mot_de_passe_hash,
        ":id" => $utilisateur["id"]
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Mot de passe réinitialisé avec succès."
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la réinitialisation du mot de passe."
    ], JSON_UNESCAPED_UNICODE);
}