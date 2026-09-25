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

// Récupérer les données envoyées
$demarche_id = $_POST["demarche_id"] ?? null;
$date_rendezvous = $_POST["date_rendezvous"] ?? null;
$heure_rendezvous = $_POST["heure_rendezvous"] ?? null;
$motif = $_POST["motif"] ?? null;

$user_id = $_SESSION["user_id"];

// Vérifier les champs obligatoires
if (!$demarche_id || !$date_rendezvous || !$heure_rendezvous) {
    echo json_encode([
        "success" => false,
        "message" => "Veuillez remplir tous les champs obligatoires."
    ]);
    exit;
}

try {

    // Vérifier que la démarche existe
    $sql = "SELECT id FROM demarches WHERE id = :demarche_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":demarche_id" => $demarche_id
    ]);

    if (!$stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "La démarche sélectionnée n'existe pas."
        ]);
        exit;
    }

    // Ajouter le rendez-vous
    $sql = "INSERT INTO rendezvous
            (user_id, demarche_id, date_rendezvous, heure_rendezvous, motif)
            VALUES
            (:user_id, :demarche_id, :date_rendezvous, :heure_rendezvous, :motif)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":user_id" => $user_id,
        ":demarche_id" => $demarche_id,
        ":date_rendezvous" => $date_rendezvous,
        ":heure_rendezvous" => $heure_rendezvous,
        ":motif" => $motif
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Rendez-vous ajouté avec succès.",
        "rendezvous_id" => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'ajout du rendez-vous.",
        "error" => $e->getMessage()
    ]);
}