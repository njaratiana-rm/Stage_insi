<?php

require_once "db.php";

$mots = [
    "creation entreprise",
    "créer une entreprise",
    "creer une entreprise",
    "creation d entreprise",
    "création d entreprise",
    "creer mon entreprise",
    "créer mon entreprise",
    "ouvrir une entreprise",
    "ouvrir mon entreprise",
    "lancer une entreprise",
    "lancer mon entreprise",
    "demarrer une entreprise",
    "démarrer une entreprise",
    "création société",
    "creation societe",
    "créer une société",
    "creer une societe"
];

foreach ($mots as $mot) {

    $sql = "INSERT INTO mots_cles (demarche_id, mot)
            VALUES (:demarche_id, :mot)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":demarche_id" => 10,
        ":mot" => $mot
    ]);
}

echo "Mots-cles de la demarche 10 ajoutes avec succes.";

?>