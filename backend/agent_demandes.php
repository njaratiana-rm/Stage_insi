<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ]);
    exit;
}

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "agent"
) {
    echo json_encode([
        "success" => false,
        "message" => "Accès réservé aux agents."
    ]);
    exit;
}

try {

    $userId = (int) $_SESSION["user_id"];


    // Récupérer le service de l'agent
    $stmtAgent = $pdo->prepare(
        "SELECT service_id
         FROM utilisateurs
         WHERE id = :id
         LIMIT 1"
    );

    $stmtAgent->execute([
        ":id" => $userId
    ]);

    $agent = $stmtAgent->fetch(PDO::FETCH_ASSOC);


    if (!$agent || $agent["service_id"] === null) {

        echo json_encode([
            "success" => false,
            "message" => "Aucun service n'est associé à cet agent."
        ]);

        exit;
    }


    $serviceId = (int) $agent["service_id"];


    // Correspondance entre le service
    // et les démarches
    $servicesDemarches = [

        1 => [1, 2, 3],       // État civil
        2 => [4, 5, 6],       // Fiscalité
        3 => [7, 8, 9],       // Foncier
        4 => [10, 11, 12],    // Entreprise
        5 => [13, 14, 15],    // Social
        6 => [16, 17, 18]     // Transport
    ];


    if (!isset($servicesDemarches[$serviceId])) {

        echo json_encode([
            "success" => false,
            "message" => "Service inconnu."
        ]);

        exit;
    }


    $demarchesIds = $servicesDemarches[$serviceId];

    $placeholders = implode(
        ",",
        array_fill(0, count($demarchesIds), "?")
    );


    $sql = "
        SELECT
            d.id,
            d.utilisateur_id,
            d.demarche_id,
            dem.nom AS demarche,
            d.statut,
            d.date_demande
        FROM demandes d
        INNER JOIN demarches dem
            ON d.demarche_id = dem.id
        WHERE d.demarche_id IN ($placeholders)
        ORDER BY d.date_demande DESC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute($demarchesIds);

    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        "success" => true,
        "service_id" => $serviceId,
        "demandes" => $demandes
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des demandes."
    ]);
}