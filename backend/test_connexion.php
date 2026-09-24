<?php

$data = [
    "email" => "test@example.com",
    "mot_de_passe" => "123456"
];

$ch = curl_init("http://localhost/stage/backend/connexion.php");

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

echo $response;