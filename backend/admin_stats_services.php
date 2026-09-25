<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $sql = "
        SELECT
            d.service,
            COUNT(*) AS total
        FROM demandes de
        INNER JOIN demarches d
            ON de.demarche_id = d.id
        GROUP BY d.service
        ORDER BY total DESC
    ";

    $stmt = $pdo->query($sql);

    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "services" => $services
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des statistiques par service."
    ]);
}