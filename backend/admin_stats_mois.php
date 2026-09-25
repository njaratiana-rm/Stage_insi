<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $sql = "
        SELECT
            DATE_FORMAT(date_demande, '%Y-%m') AS mois,
            COUNT(*) AS total
        FROM demandes
        GROUP BY DATE_FORMAT(date_demande, '%Y-%m')
        ORDER BY mois ASC
    ";

    $stmt = $pdo->query($sql);

    $mois = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "mois" => $mois
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des statistiques mensuelles."
    ]);
}