<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=utf-8");

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {

    $sql = "SELECT
                r.id,
                r.user_id,
                r.demarche_id,
                d.nom AS demarche_nom,
                r.date_rendezvous,
                r.heure_rendezvous,
                r.motif,
                r.statut,
                r.created_at
            FROM rendezvous r
            INNER JOIN demarches d
                ON r.demarche_id = d.id
            WHERE r.user_id = :user_id
            ORDER BY r.date_rendezvous ASC, r.heure_rendezvous ASC";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":user_id" => $user_id
    ]);

    $rendezvous = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "rendezvous" => $rendezvous
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la récupération des rendez-vous.",
        "error" => $e->getMessage()
    ]);
}