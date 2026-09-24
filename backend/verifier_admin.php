<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, role
        FROM utilisateurs
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $_SESSION["user_id"]
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur introuvable."
        ]);
        exit;
    }

    if ($utilisateur["role"] !== "admin") {
        echo json_encode([
            "success" => false,
            "message" => "Accès administrateur refusé."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "admin" => $utilisateur
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur."
    ]);
}