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


// Vérifier l'identifiant du rendez-vous
if (
    !isset($_GET["id"]) ||
    !filter_var($_GET["id"], FILTER_VALIDATE_INT)
) {

    echo json_encode([
        "success" => false,
        "message" => "Identifiant de rendez-vous invalide."
    ]);

    exit;
}


$rendezvousId = (int) $_GET["id"];


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


    $placeholders = implode(
        ",",
        array_fill(
            0,
            count($demarchesIds),
            "?"
        )
    );


    /*
     * Récupérer le rendez-vous uniquement
     * s'il appartient au service de l'agent.
     */

    $sql = "
        SELECT

            r.id,
            r.user_id,
            r.demarche_id,
            r.date_rendezvous,
            r.heure_rendezvous,
            r.motif,
            r.statut,
            r.created_at,

            dem.nom AS demarche,

            u.nom,
            u.prenom,
            u.email,
            u.telephone

        FROM rendezvous r

        INNER JOIN demarches dem
            ON r.demarche_id = dem.id

        INNER JOIN utilisateurs u
            ON r.user_id = u.id

        WHERE r.id = ?
        AND r.demarche_id IN ($placeholders)

        LIMIT 1
    ";


    $params = [
        $rendezvousId,
        ...$demarchesIds
    ];


    $stmt = $pdo->prepare($sql);

    $stmt->execute($params);


    $rendezvous =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$rendezvous) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Rendez-vous introuvable ou non autorisé."
        ]);

        exit;
    }


    echo json_encode([

        "success" => true,

        "rendezvous" => $rendezvous

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    echo json_encode([

        "success" => false,

        "message" =>
            "Erreur lors de la récupération du rendez-vous."

    ]);

}