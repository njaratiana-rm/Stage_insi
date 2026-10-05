<?php

header("Content-Type: text/plain; charset=UTF-8");

require_once "gemini_config.php";

if (defined("GEMINI_API_KEY")) {
    echo "OK : la configuration Gemini est chargée.";
} else {
    echo "ERREUR : la configuration Gemini n'est pas chargée.";
}