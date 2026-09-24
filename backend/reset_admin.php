<?php

require_once "db.php";

$nouveau_mot_de_passe = "Admin1234";

$hash = password_hash(
    $nouveau_mot_de_passe,
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    UPDATE utilisateurs
    SET mot_de_passe = :mot_de_passe
    WHERE id = 1
");

$stmt->execute([
    ":mot_de_passe" => $hash
]);

echo "Mot de passe administrateur réinitialisé.";