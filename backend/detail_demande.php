<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";


if (!isset($_SESSION["user_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


$demande_id =
    (int) ($_GET["id"] ?? 0);


if ($demande_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Demande invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    $sql = "
        SELECT
            d.id,
            d.utilisateur_id,
            d.demarche_id,
            d.statut,
            d.date_demande,
            dm.nom AS demarche_nom,
            dm.description,
            dm.service,
            dm.lieu,
            dm.delai,
            dm.frais,
            dm.etapes
        FROM demandes d
        INNER JOIN demarches dm
            ON dm.id = d.demarche_id
        WHERE d.id = :demande_id
          AND d.utilisateur_id = :utilisateur_id
        LIMIT 1
    ";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([
        ":demande_id" =>
            $demande_id,

        ":utilisateur_id" =>
            $_SESSION["user_id"]
    ]);


    $demande =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$demande) {

        echo json_encode([
            "success" => false,
            "message" => "Demande introuvable."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    echo json_encode([
        "success" => true,
        "demande" => $demande
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération de la demande."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}