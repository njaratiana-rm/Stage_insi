<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (!isset($_SESSION["user_id"])){
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$utilisateur_id = $_SESSION["user_id"];

try {

    $sql = "
        SELECT
            id,
            type,
            message,
            lu,
            date_creation
        FROM notifications
        WHERE utilisateur_id = ?
        ORDER BY date_creation DESC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$utilisateur_id]);

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "notifications" => $notifications
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des notifications."
    ], JSON_UNESCAPED_UNICODE);
}