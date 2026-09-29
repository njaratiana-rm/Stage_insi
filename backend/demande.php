<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

session_start();


// Vérifier la connexion
if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ]);

    exit;
}


// Vérifier le rôle
if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "agent"
) {

    echo json_encode([
        "success" => false,
        "message" => "Accès réservé aux agents."
    ]);

    exit;
}


// Vérifier l'identifiant de la demande
if (
    !isset($_GET["id"]) ||
    !filter_var($_GET["id"], FILTER_VALIDATE_INT)
) {

    echo json_encode([
        "success" => false,
        "message" => "Identifiant de demande invalide."
    ]);

    exit;
}


$demandeId = (int) $_GET["id"];


try {

    $userId = (int) $_SESSION["user_id"];


    // Récupérer le service de l'agent
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
        ]);

        exit;
    }


    $serviceId = (int) $agent["service_id"];


    // Correspondance service → démarches
    $servicesDemarches = [

        1 => [1, 2, 3],

        2 => [4, 5, 6],

        3 => [7, 8, 9],

        4 => [10, 11, 12],

        5 => [13, 14, 15],

        6 => [16, 17, 18]

    ];


    if (!isset($servicesDemarches[$serviceId])) {

        echo json_encode([
            "success" => false,
            "message" => "Service inconnu."
        ]);

        exit;
    }


    $demarchesIds =
        $servicesDemarches[$serviceId];


    /*
     * Vérifier que la demande appartient
     * bien au service de l'agent.
     */

    $placeholders = implode(
        ",",
        array_fill(
            0,
            count($demarchesIds),
            "?"
        )
    );


    $sql = "
        SELECT

            d.id,
            d.utilisateur_id,
            d.demarche_id,
            d.statut,
            d.date_demande,

            dem.nom AS demarche,
            dem.description,
            dem.service,
            dem.lieu,
            dem.delai,
            dem.frais,
            dem.etapes

        FROM demandes d

        INNER JOIN demarches dem
            ON d.demarche_id = dem.id

        WHERE d.id = ?
        AND d.demarche_id IN ($placeholders)

        LIMIT 1
    ";


    $params = [
        $demandeId,
        ...$demarchesIds
    ];


    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);


    $demande = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$demande) {

        echo json_encode([
            "success" => false,
            "message" => "Demande introuvable ou non autorisée."
        ]);

        exit;
    }


    echo json_encode([

        "success" => true,

        "demande" => $demande

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Erreur lors de la récupération de la demande."

    ]);

}