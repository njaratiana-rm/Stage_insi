<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Utilisateur non connecté."
    ]);

    exit;
}

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    echo json_encode([
        "success" => false,
        "message" => "Accès administrateur refusé."
    ]);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ]);

    exit;
}

$demande_id = $_POST["demande_id"] ?? null;
$statut = $_POST["statut"] ?? null;

if (!$demande_id || !$statut) {

    echo json_encode([
        "success" => false,
        "message" => "Données manquantes."
    ]);

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
    ]);

    exit;
}

try {

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

    if ($stmt->rowCount() === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Demande introuvable ou statut inchangé."
        ]);

        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Statut modifié avec succès.",
        "demande_id" => $demande_id,
        "statut" => $statut
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la modification."
    ]);
}