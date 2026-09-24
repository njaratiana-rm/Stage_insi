<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";


/* =========================================================
   VERIFICATION DE LA CONNEXION
========================================================= */

if (!isset($_SESSION["user_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* =========================================================
   VERIFICATION DE LA METHODE
========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* =========================================================
   RECUPERATION
========================================================= */

$demande_id =
    (int) ($_POST["demande_id"] ?? 0);


if ($demande_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Demande invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    /* Vérifier que la demande appartient à l'utilisateur */

    $verification =
        $pdo->prepare(
            "SELECT id, statut
             FROM demandes
             WHERE id = :demande_id
               AND utilisateur_id = :utilisateur_id
             LIMIT 1"
        );


    $verification->execute([
        ":demande_id" =>
            $demande_id,

        ":utilisateur_id" =>
            $_SESSION["user_id"]
    ]);


    $demande =
        $verification->fetch(PDO::FETCH_ASSOC);


    if (!$demande) {

        echo json_encode([
            "success" => false,
            "message" => "Demande introuvable."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Vérifier si elle est déjà annulée */

    if ($demande["statut"] === "Annulée") {

        echo json_encode([
            "success" => false,
            "message" => "Cette demande est déjà annulée."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Annuler la demande */

    $stmt =
        $pdo->prepare(
            "UPDATE demandes
             SET statut = 'Annulée'
             WHERE id = :demande_id
               AND utilisateur_id = :utilisateur_id"
        );


    $stmt->execute([
        ":demande_id" =>
            $demande_id,

        ":utilisateur_id" =>
            $_SESSION["user_id"]
    ]);


    echo json_encode([
        "success" => true,
        "message" => "Demande annulée avec succès."
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'annulation de la demande."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}