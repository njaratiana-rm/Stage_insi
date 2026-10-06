<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";


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
    !in_array(
        $_SESSION["role"],
        ["admin", "agent"],
        true
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Accès refusé."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Vérifier la méthode
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer les données
|--------------------------------------------------------------------------
*/

$demande_id = $_POST["demande_id"] ?? null;
$statut = $_POST["statut"] ?? null;


if (!$demande_id || !$statut) {

    echo json_encode([
        "success" => false,
        "message" => "Données manquantes."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Vérifier le statut
|--------------------------------------------------------------------------
*/

$statutsAutorises = [
    "En attente",
    "Acceptée",
    "Refusée",
    "Traitée"
];


if (!in_array(
    $statut,
    $statutsAutorises,
    true
)) {

    echo json_encode([
        "success" => false,
        "message" => "Statut invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Correspondance services / démarches
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


try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Récupérer la demande
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            utilisateur_id,
            demarche_id
        FROM demandes
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $demande_id
    ]);

    $demande = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$demande) {

        $pdo->rollBack();

        echo json_encode([
            "success" => false,
            "message" => "Demande introuvable."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Vérification du service pour un agent
    |--------------------------------------------------------------------------
    */

    if ($_SESSION["role"] === "agent") {

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

            $pdo->rollBack();

            echo json_encode([
                "success" => false,
                "message" => "Aucun service n'est associé à cet agent."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        $serviceId = (int) $agent["service_id"];


        /*
        |--------------------------------------------------------------------------
        | Vérifier que le service existe
        |--------------------------------------------------------------------------
        */

        if (!isset(
            $servicesDemarches[$serviceId]
        )) {

            $pdo->rollBack();

            echo json_encode([
                "success" => false,
                "message" => "Service inconnu."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Vérifier que la demande appartient au service
        |--------------------------------------------------------------------------
        */

        if (!in_array(
            (int) $demande["demarche_id"],
            $servicesDemarches[$serviceId],
            true
        )) {

            $pdo->rollBack();

            echo json_encode([
                "success" => false,
                "message" => "Vous ne pouvez pas modifier cette demande."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Modifier le statut
    |--------------------------------------------------------------------------
    */

    $sql = "
        UPDATE demandes
        SET statut = :statut
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":statut" => $statut,
        ":id" => $demande_id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Créer la notification
    |--------------------------------------------------------------------------
    */

    $message =
        "Le statut de votre demande a été modifié : "
        . $statut
        . ".";


    $sql = "
        INSERT INTO notifications (
            utilisateur_id,
            type,
            message
        )
        VALUES (
            :utilisateur_id,
            :type,
            :message
        )
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":utilisateur_id" => $demande["utilisateur_id"],
        ":type" => "demande",
        ":message" => $message
    ]);


    /*
    |--------------------------------------------------------------------------
    | Valider
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    echo json_encode([
        "success" => true,
        "message" => "Statut modifié avec succès.",
        "demande_id" => $demande_id,
        "statut" => $statut
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la modification."
    ], JSON_UNESCAPED_UNICODE);
}