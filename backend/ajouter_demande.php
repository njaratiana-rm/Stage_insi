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
   RECUPERATION DES DONNEES
========================================================= */

$utilisateur_id =
    (int) $_SESSION["user_id"];

$demarche_id =
    (int) ($_POST["demarche_id"] ?? 0);


/* =========================================================
   VERIFICATION
========================================================= */

if ($demarche_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Démarche invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    /* Vérifier que la démarche existe */

    $verification =
        $pdo->prepare(
            "SELECT id, nom
             FROM demarches
             WHERE id = :id
             LIMIT 1"
        );


    $verification->execute([
        ":id" => $demarche_id
    ]);


    $demarche =
        $verification->fetch(PDO::FETCH_ASSOC);


    if (!$demarche) {

        echo json_encode([
            "success" => false,
            "message" => "Cette démarche n'existe pas."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /* Ajouter la demande */

    $sql =
        "INSERT INTO demandes
         (utilisateur_id, demarche_id, statut)
         VALUES
         (:utilisateur_id, :demarche_id, 'En attente')";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([
        ":utilisateur_id" => $utilisateur_id,
        ":demarche_id" => $demarche_id
    ]);


    echo json_encode([
        "success" => true,
        "message" => "Demande enregistrée avec succès.",
        "demande" => [
            "id" => $pdo->lastInsertId(),
            "demarche_id" => $demarche_id,
            "demarche_nom" => $demarche["nom"],
            "statut" => "En attente"
        ]
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'enregistrement de la demande."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
