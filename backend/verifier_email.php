<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");


/* Récupérer le token */

$token = trim($_GET["token"] ?? "");


if ($token === "") {

    echo json_encode([
        "success" => false,
        "message" => "Token de vérification manquant."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    /* Rechercher l'utilisateur */

    $stmt = $pdo->prepare("
        SELECT id, email, email_verifie, email_token_expiration
        FROM utilisateurs
        WHERE email_token = :token
        LIMIT 1
    ");

    $stmt->execute([
        ":token" => $token
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);


    /* Token inexistant */

    if (!$utilisateur) {

        echo json_encode([
            "success" => false,
            "message" => "Lien de vérification invalide."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Déjà vérifié */

    if ((int)$utilisateur["email_verifie"] === 1) {

        echo json_encode([
            "success" => true,
            "message" => "Cette adresse email est déjà vérifiée."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Vérifier l'expiration */

    if (
        empty($utilisateur["email_token_expiration"]) ||
        strtotime($utilisateur["email_token_expiration"]) < time()
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Le lien de vérification a expiré."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Valider l'adresse email */

    $update = $pdo->prepare("
        UPDATE utilisateurs
        SET
            email_verifie = 1,
            email_token = NULL,
            email_token_expiration = NULL
        WHERE id = :id
    ");

    $update->execute([
        ":id" => $utilisateur["id"]
    ]);

header("Location: http://localhost/stage/ADMIN-GUIDE-frontend/#login");
exit;


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la vérification de l'adresse email."
    ], JSON_UNESCAPED_UNICODE);
}