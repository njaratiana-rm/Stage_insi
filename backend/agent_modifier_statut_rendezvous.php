<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";
session_start();

/*
|--------------------------------------------------------------------------
| Vérification de la connexion
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Vérification du rôle agent
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Vérification des données reçues
|--------------------------------------------------------------------------
*/

if (
    !isset($_POST["id"]) ||
    !filter_var($_POST["id"], FILTER_VALIDATE_INT)
) {
    echo json_encode([
        "success" => false,
        "message" => "Identifiant de rendez-vous invalide."
    ]);
    exit;
}

if (
    !isset($_POST["statut"]) ||
    trim($_POST["statut"]) === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Statut manquant."
    ]);
    exit;
}

$rendezvousId = (int) $_POST["id"];
$nouveauStatut = trim($_POST["statut"]);

/*
|--------------------------------------------------------------------------
| Statuts autorisés
|--------------------------------------------------------------------------
*/

$statutsAutorises = [
    "En attente",
    "Confirmé",
    "Annulé"
];

if (!in_array($nouveauStatut, $statutsAutorises, true)) {
    echo json_encode([
        "success" => false,
        "message" => "Statut invalide."
    ]);
    exit;
}

try {

    /*
    |--------------------------------------------------------------------------
    | Récupération du service de l'agent
    |--------------------------------------------------------------------------
    */

    $userId = (int) $_SESSION["user_id"];

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

    /*
    |--------------------------------------------------------------------------
    | Correspondance service → démarches
    |--------------------------------------------------------------------------
    */

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

    $demarchesIds = $servicesDemarches[$serviceId];

    /*
    |--------------------------------------------------------------------------
    | Vérification que le rendez-vous appartient au service de l'agent
    |--------------------------------------------------------------------------
    */

    $placeholders = implode(
        ",",
        array_fill(0, count($demarchesIds), "?")
    );

    $sql = "
        SELECT
            id,
            user_id,
            demarche_id,
            statut
        FROM rendezvous
        WHERE id = ?
        AND demarche_id IN ($placeholders)
        LIMIT 1
    ";

    $params = [
        $rendezvousId,
        ...$demarchesIds
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rendezvous = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rendezvous) {
        echo json_encode([
            "success" => false,
            "message" => "Rendez-vous introuvable ou non autorisé."
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Modification du statut
    |--------------------------------------------------------------------------
    */

    $stmtUpdate = $pdo->prepare(
        "UPDATE rendezvous
         SET statut = :statut
         WHERE id = :id"
    );

    $stmtUpdate->execute([
        ":statut" => $nouveauStatut,
        ":id" => $rendezvousId
    ]);

    /*
    |--------------------------------------------------------------------------
    | Notification du citoyen
    |--------------------------------------------------------------------------
    */

    $message = "Le statut de votre rendez-vous a été modifié : "
             . $nouveauStatut . ".";

    $stmtNotification = $pdo->prepare(
        "INSERT INTO notifications
        (
            utilisateur_id,
            type,
            message
        )
        VALUES
        (
            :utilisateur_id,
            :type,
            :message
        )"
    );

    $stmtNotification->execute([
        ":utilisateur_id" => $rendezvous["user_id"],
        ":type" => "rendezvous",
        ":message" => $message
    ]);

    /*
    |--------------------------------------------------------------------------
    | Réponse
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        "success" => true,
        "message" => "Statut du rendez-vous modifié avec succès."
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la modification du statut."
    ]);
}