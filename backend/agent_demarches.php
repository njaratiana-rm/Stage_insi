<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

session_start();


/*
|--------------------------------------------------------------------------
| Vérifier la connexion
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Vérifier le rôle
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "agent"
) {

    echo json_encode([
        "success" => false,
        "message" => "Accès réservé aux agents."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    $userId = (int) $_SESSION["user_id"];


    /*
    |--------------------------------------------------------------------------
    | Récupérer le service de l'agent
    |--------------------------------------------------------------------------
    */

    $stmtAgent = $pdo->prepare(
        "SELECT service_id
         FROM utilisateurs
         WHERE id = :id
         LIMIT 1"
    );

    $stmtAgent->execute([
        ":id" => $userId
    ]);

    $agent = $stmtAgent->fetch(PDO::FETCH_ASSOC);


    if (
        !$agent ||
        $agent["service_id"] === null
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Aucun service n'est associé à cet agent."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    $serviceId = (int) $agent["service_id"];


    /*
    |--------------------------------------------------------------------------
    | Correspondance service / démarches
    |--------------------------------------------------------------------------
    */

    $servicesDemarches = [

        1 => [1, 2, 3],       // État civil
        2 => [4, 5, 6],       // Fiscalité
        3 => [7, 8, 9],       // Foncier
        4 => [10, 11, 12],    // Entreprise
        5 => [13, 14, 15],    // Social
        6 => [16, 17, 18]     // Transport

    ];


    if (!isset(
        $servicesDemarches[$serviceId]
    )) {

        echo json_encode([
            "success" => false,
            "message" => "Service inconnu."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    $demarchesIds =
        $servicesDemarches[$serviceId];


    /*
    |--------------------------------------------------------------------------
    | Préparer les paramètres SQL
    |--------------------------------------------------------------------------
    */

    $placeholders = implode(
        ",",
        array_fill(
            0,
            count($demarchesIds),
            "?"
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Récupérer les démarches
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            id,
            nom,
            description,
            service,
            lieu,
            delai,
            frais,
            etapes
        FROM demarches
        WHERE id IN ($placeholders)
        ORDER BY id ASC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute($demarchesIds);


    $demarches =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | Récupérer les documents de chaque démarche
    |--------------------------------------------------------------------------
    */

    $sqlDocuments = "
        SELECT
            id,
            demarche_id,
            nom_document,
            description,
            obligatoire
        FROM documents_requis
        WHERE demarche_id IN ($placeholders)
        ORDER BY demarche_id ASC, id ASC
    ";


    $stmtDocuments =
        $pdo->prepare($sqlDocuments);

    $stmtDocuments->execute(
        $demarchesIds
    );


    $documents =
        $stmtDocuments->fetchAll(
            PDO::FETCH_ASSOC
        );


    /*
    |--------------------------------------------------------------------------
    | Associer les documents aux démarches
    |--------------------------------------------------------------------------
    */

    foreach ($demarches as &$demarche) {

        $demarche["documents"] = [];

        foreach ($documents as $document) {

            if (
                (int) $document["demarche_id"]
                === (int) $demarche["id"]
            ) {

                $demarche["documents"][] =
                    $document;
            }
        }
    }

    unset($demarche);


    /*
    |--------------------------------------------------------------------------
    | Réponse
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        "success" => true,
        "service_id" => $serviceId,
        "demarches" => $demarches
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des démarches."
    ], JSON_UNESCAPED_UNICODE);
}