<?php

$token = trim($_GET["token"] ?? "");

if ($token === "") {
    echo "Lien de réinitialisation invalide.";
    exit;
}

$url = "http://localhost/stage/ADMIN-GUIDE-frontend/?reset_token="
    . urlencode($token);


header("Location: " . $url);
exit;