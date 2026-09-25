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


try {

    /* =====================================================
       RÉCUPÉRER LES RENDEZ-VOUS
    ===================================================== */

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

            d.nom AS demarche_nom,

            u.nom,
            u.prenom,
            u.email,
            u.telephone

        FROM rendezvous r

        INNER JOIN demarches d
            ON r.demarche_id = d.id

        INNER JOIN utilisateurs u
            ON r.user_id = u.id

        ORDER BY
            r.date_rendezvous ASC,
            r.heure_rendezvous ASC
    ";


    $stmt = $pdo->prepare($sql);

    $stmt->execute();


    $rendezvous =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


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