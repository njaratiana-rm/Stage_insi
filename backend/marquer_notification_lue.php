<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (!isset($_POST["notification_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Notification non spécifiée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$notification_id = (int) $_POST["notification_id"];
$utilisateur_id = $_SESSION["user_id"];

try {

    $sql = "
        UPDATE notifications
        SET lu = 1
        WHERE id = ?
        AND utilisateur_id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $notification_id,
        $utilisateur_id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Notification marquée comme lue."
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la modification de la notification."
    ], JSON_UNESCAPED_UNICODE);
}