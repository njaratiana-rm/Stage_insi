<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=utf-8");


/* =========================================================
   VÉRIFIER LA CONNEXION
========================================================= */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ]);

    exit;
}


/* =========================================================
   VÉRIFIER LE RÔLE ADMIN
========================================================= */

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {

    echo json_encode([
        "success" => false,
        "message" => "Accès réservé à l'administrateur."
    ]);

    exit;
}


/* =========================================================
   RÉCUPÉRER LES DONNÉES
========================================================= */

$rendezvous_id = $_POST["rendezvous_id"] ?? null;
$statut = $_POST["statut"] ?? null;


if (!$rendezvous_id || !$statut) {

    echo json_encode([
        "success" => false,
        "message" => "Données manquantes."
    ]);

    exit;
}


/* =========================================================
   VÉRIFIER LE STATUT
========================================================= */

$statuts_autorises = [
    "En attente",
    "Confirmé",
    "Annulé",
    "Terminé"
];


if (!in_array($statut, $statuts_autorises, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Statut invalide."
    ]);

    exit;
}


try {

    /* =====================================================
       VÉRIFIER QUE LE RENDEZ-VOUS EXISTE
    ===================================================== */

    $sql = "
        SELECT id
        FROM rendezvous
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $rendezvous_id
    ]);


    if (!$stmt->fetch()) {

        echo json_encode([
            "success" => false,
            "message" => "Rendez-vous introuvable."
        ]);

        exit;
    }


    /* =====================================================
       MODIFIER LE STATUT
    ===================================================== */

    $sql = "
        UPDATE rendezvous
        SET statut = :statut
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":statut" => $statut,
        ":id" => $rendezvous_id
    ]);


    echo json_encode([
        "success" => true,
        "message" => "Statut du rendez-vous modifié avec succès."
    ]);


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la modification du statut.",
        "error" => $e->getMessage()
    ]);
}