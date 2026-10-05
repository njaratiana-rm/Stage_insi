<?php

header("Content-Type: text/plain; charset=UTF-8");

require_once __DIR__ . "/gemini_config.php";

$apiKey = GEMINI_API_KEY;

$url = "https://generativelanguage.googleapis.com/v1beta/interactions";

$data = [
    "model" => "gemini-3.8-flash",
    "input" => "Réponds uniquement : Bonjour ADMIN-GUIDE, Gemini fonctionne."
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "x-goog-api-key: " . $apiKey
    ],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);

if ($response === false) {
    echo "ERREUR cURL : " . curl_error($ch);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "Code HTTP : " . $httpCode . "\n\n";
echo $response;