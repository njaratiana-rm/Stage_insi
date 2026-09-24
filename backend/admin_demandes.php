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

try {

    // Vérifier que l'utilisateur est administrateur
    $stmt = $pdo->prepare("
        SELECT role
        FROM utilisateurs
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $_SESSION["user_id"]
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur || $utilisateur["role"] !== "admin") {
        echo json_encode([
            "success" => false,
            "message" => "Accès administrateur refusé."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Récupérer toutes les demandes
    $stmt = $pdo->query("
        SELECT
            d.id,
            d.utilisateur_id,
            d.demarche_id,
            d.statut,
            d.date_demande,

            u.nom,
            u.prenom,
            u.email,
            u.telephone,

            dm.nom AS demarche_nom

        FROM demandes d

        INNER JOIN utilisateurs u
            ON u.id = d.utilisateur_id

        INNER JOIN demarches dm
            ON dm.id = d.demarche_id

        ORDER BY d.date_demande DESC
    ");

    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "demandes" => $demandes
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur."
    ], JSON_UNESCAPED_UNICODE);
}