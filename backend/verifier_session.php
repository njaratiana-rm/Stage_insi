<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

if (isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => true,
        "connecte" => true,
        "utilisateur" => [
            "id" => $_SESSION["user_id"],
            "nom" => $_SESSION["nom"],
            "prenom" => $_SESSION["prenom"],
            "email" => $_SESSION["email"]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


echo json_encode([
    "success" => true,
    "connecte" => false
], JSON_UNESCAPED_UNICODE);