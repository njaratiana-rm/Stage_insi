<?php

require_once "db.php";

$sql = "INSERT INTO mots_cles (demarche_id, mot)
        VALUES (:demarche_id, :mot)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":demarche_id" => 1,
    ":mot" => "naissance"
]);

echo "Mot-cle naissance ajoute avec succes.";

?>