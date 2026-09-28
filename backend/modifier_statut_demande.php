<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    echo json_encode([
        "success" => false,
        "message" => "Accès administrateur refusé."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$demande_id = $_POST["demande_id"] ?? null;
$statut = $_POST["statut"] ?? null;

if (!$demande_id || !$statut) {

    echo json_encode([
        "success" => false,
        "message" => "Données manquantes."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$statutsAutorises = [
    "En attente",
    "Acceptée",
    "Refusée",
    "Traitée"
];

if (!in_array($statut, $statutsAutorises, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Statut invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {

    $pdo->beginTransaction();

    /*
     * Récupérer l'utilisateur concerné par la demande
     */
    $sql = "
        SELECT utilisateur_id
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

    $utilisateur_id = $demande["utilisateur_id"];

    /*
     * Modifier le statut de la demande
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
     * Créer la notification
     */
    $message = "Le statut de votre demande a été modifié : " . $statut . ".";

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
        ":utilisateur_id" => $utilisateur_id,
        ":type" => "demande",
        ":message" => $message
    ]);

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