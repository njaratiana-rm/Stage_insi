<?php

header("Content-Type: text/plain; charset=UTF-8");

$cle = getenv("GEMINI_API_KEY");

if ($cle === false || $cle === "") {
    echo "ERREUR : la clé Gemini n'est pas accessible par PHP.";
    exit;
}

echo "OK : PHP trouve bien la clé Gemini.";