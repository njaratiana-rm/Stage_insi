<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();


$_SESSION = [];

session_destroy();


echo json_encode([
    "success" => true,
    "message" => "Déconnexion réussie."
], JSON_UNESCAPED_UNICODE);