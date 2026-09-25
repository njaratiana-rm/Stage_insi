<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $sql = "
        SELECT
            statut,
            COUNT(*) AS total
        FROM rendezvous
        GROUP BY statut
        ORDER BY total DESC
    ";

    $stmt = $pdo->query($sql);

    $statuts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "statuts" => $statuts
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des statistiques des rendez-vous."
    ]);
}