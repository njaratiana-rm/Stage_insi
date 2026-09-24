<?php

require "db.php";

$texte = "é à è ê ç ù É À È Ê Ç Ù";

echo "<pre>";

echo "Texte envoyé : " . $texte . "\n\n";

echo "HEX : " . bin2hex($texte) . "\n\n";

$resultat = $pdo->query("SELECT 'é à è ê ç ù É À È Ê Ç Ù' AS texte");
$data = $resultat->fetch(PDO::FETCH_ASSOC);

echo "Texte reçu par MariaDB : " . $data["texte"] . "\n\n";

echo "HEX MariaDB : " . bin2hex($data["texte"]);

echo "</pre>";