<?php

require "db.php";

if (isset($_GET["id"])) {

    $id = $_GET["id"];

    $sql = "SELECT * FROM demarches WHERE id = :id";

    $resultat = $pdo->prepare($sql);
    $resultat->execute(["id" => $id]);

    $demarche = $resultat->fetch(PDO::FETCH_ASSOC);

$sqlDocuments = "SELECT nom_document, description 
                 FROM documents_requis 
                 WHERE demarche_id = :id";

$resultatDocuments = $pdo->prepare($sqlDocuments);
$resultatDocuments->execute(["id" => $id]);

$documents = $resultatDocuments->fetchAll(PDO::FETCH_ASSOC);

$demarche["documents"] = $documents;
$sqlMotsCles = "SELECT mot
               FROM mots_cles
               WHERE demarche_id = :id";

$resultatMotsCles = $pdo->prepare($sqlMotsCles);
$resultatMotsCles->execute(["id" => $id]);

$motsCles = $resultatMotsCles->fetchAll(PDO::FETCH_COLUMN);

$demarche["mots_cles"] = $motsCles;

    header("Content-Type: application/json");

    echo json_encode($demarche);

} else {

    $sql = "SELECT * FROM demarches";

    $resultat = $pdo->query($sql);

    $demarches = $resultat->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/json");

    echo json_encode($demarches);
}