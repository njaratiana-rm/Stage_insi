<?php

header("Content-Type: application/json; charset=UTF-8");

$url = "http://localhost/stage/backend/assistant.php";

$data = [
    "message" => "Je veux faire un acte de mariage."
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);

if ($response === false) {

    echo json_encode([
        "success" => false,
        "erreur" => curl_error($ch)
    ], JSON_UNESCAPED_UNICODE);

    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo json_encode([
    "code_http" => $httpCode,
    "reponse_assistant" => json_decode($response, true)
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);