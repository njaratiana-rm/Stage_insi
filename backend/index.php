<?php

header("Content-Type: application/json; charset=utf-8");

require "db.php";


/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION D'UNE DÉMARCHE
|--------------------------------------------------------------------------
*/

if (isset($_GET["id"])) {

    $id = filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );

    if ($id === false || $id === null || $id <= 0) {

        http_response_code(400);

        echo json_encode(
            [
                "error" => "Identifiant de démarche invalide."
            ],
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | RÉCUPÉRATION DE LA DÉMARCHE
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT *
        FROM demarches
        WHERE id = :id
    ";

    $resultat = $pdo->prepare($sql);

    $resultat->execute([
        "id" => $id
    ]);

    $demarche = $resultat->fetch(PDO::FETCH_ASSOC);


    if (!$demarche) {

        http_response_code(404);

        echo json_encode(
            [
                "error" => "Démarche introuvable."
            ],
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | RÉCUPÉRATION DES DOCUMENTS
    |--------------------------------------------------------------------------
    */

    $sqlDocuments = "
        SELECT
            nom_document,
            description,
            obligatoire
        FROM documents_requis
        WHERE demarche_id = :id
    ";

    $resultatDocuments =
        $pdo->prepare($sqlDocuments);

    $resultatDocuments->execute([
        "id" => $id
    ]);

    $documents =
        $resultatDocuments->fetchAll(
            PDO::FETCH_ASSOC
        );

    $demarche["documents"] = $documents;


    /*
    |--------------------------------------------------------------------------
    | RÉCUPÉRATION DES MOTS-CLÉS
    |--------------------------------------------------------------------------
    */

    $sqlMotsCles = "
        SELECT mot
        FROM mots_cles
        WHERE demarche_id = :id
    ";

    $resultatMotsCles =
        $pdo->prepare($sqlMotsCles);

    $resultatMotsCles->execute([
        "id" => $id
    ]);

    $motsCles =
        $resultatMotsCles->fetchAll(
            PDO::FETCH_COLUMN
        );

    $demarche["mots_cles"] = $motsCles;


    /*
    |--------------------------------------------------------------------------
    | RÉPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode(
        $demarche,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DE TOUTES LES DÉMARCHES
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM demarches
";

$resultat = $pdo->query($sql);

$demarches =
    $resultat->fetchAll(
        PDO::FETCH_ASSOC
    );


/*
|--------------------------------------------------------------------------
| RÉPONSE
|--------------------------------------------------------------------------
*/

echo json_encode(
    $demarches,
    JSON_UNESCAPED_UNICODE
);