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


try {

    $utilisateur_id =
        (int) $_SESSION["user_id"];


    $sql = "
        SELECT
            d.id,
            d.demarche_id,
            d.statut,
            d.date_demande,
            dm.nom AS demarche_nom
        FROM demandes d
        INNER JOIN demarches dm
            ON dm.id = d.demarche_id
        WHERE d.utilisateur_id = :utilisateur_id
  AND d.statut <> 'Annulée'
ORDER BY d.date_demande DESC
    
    ";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([
        ":utilisateur_id" => $utilisateur_id
    ]);


    $demandes =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        "success" => true,
        "demandes" => $demandes
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des demandes."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}