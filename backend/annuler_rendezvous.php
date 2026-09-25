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


$user_id = $_SESSION["user_id"];


/* =========================================================
   RÉCUPÉRER L'ID DU RENDEZ-VOUS
========================================================= */

$rendezvous_id = $_POST["rendezvous_id"] ?? null;


if (!$rendezvous_id) {

    echo json_encode([
        "success" => false,
        "message" => "Identifiant du rendez-vous manquant."
    ]);

    exit;
}


try {

    /* =====================================================
       VÉRIFIER QUE LE RENDEZ-VOUS APPARTIENT À L'UTILISATEUR
    ===================================================== */

    $sql = "
        SELECT id, statut
        FROM rendezvous
        WHERE id = :id
        AND user_id = :user_id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $rendezvous_id,
        ":user_id" => $user_id
    ]);

    $rendezvous = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$rendezvous) {

        echo json_encode([
            "success" => false,
            "message" => "Rendez-vous introuvable."
        ]);

        exit;
    }


    /* =====================================================
       VÉRIFIER LE STATUT
    ===================================================== */

    if ($rendezvous["statut"] !== "En attente") {

        echo json_encode([
            "success" => false,
            "message" => "Ce rendez-vous ne peut plus être annulé."
        ]);

        exit;
    }


    /* =====================================================
       ANNULER LE RENDEZ-VOUS
    ===================================================== */

    $sql = "
        UPDATE rendezvous
        SET statut = 'Annulé'
        WHERE id = :id
        AND user_id = :user_id
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $rendezvous_id,
        ":user_id" => $user_id
    ]);


    echo json_encode([
        "success" => true,
        "message" => "Rendez-vous annulé avec succès."
    ]);


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de l'annulation du rendez-vous.",
        "error" => $e->getMessage()
    ]);
}