<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";

if (isset($_SESSION["user_id"])) {

    try {

        $stmt = $pdo->prepare("
            SELECT
                id,
                nom,
                prenom,
                email,
                role
            FROM utilisateurs
            WHERE id = :id
        ");

        $stmt->execute([
            ":id" => $_SESSION["user_id"]
        ]);

        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$utilisateur) {

            echo json_encode([
                "success" => true,
                "connecte" => false
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        echo json_encode([
            "success" => true,
            "connecte" => true,
            "utilisateur" => $utilisateur
        ], JSON_UNESCAPED_UNICODE);

        exit;

    } catch (PDOException $e) {

        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}

echo json_encode([
    "success" => true,
    "connecte" => false
], JSON_UNESCAPED_UNICODE);