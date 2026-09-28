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
    SELECT id, user_id
    FROM rendezvous
    WHERE id = :id
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":id" => $rendezvous_id
]);

$rendezvous = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rendezvous) {

    echo json_encode([
        "success" => false,
        "message" => "Rendez-vous introuvable."
    ]);

    exit;
}

$utilisateur_id = $rendezvous["user_id"];   

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


/* =====================================================
   CRÉER LA NOTIFICATION
===================================================== */

$message = "Le statut de votre rendez-vous a été modifié : " . $statut . ".";

$sql = "
    INSERT INTO notifications
    (
        utilisateur_id,
        type,
        message,
        lu
    )
    VALUES
    (
        :utilisateur_id,
        :type,
        :message,
        0
    )
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":utilisateur_id" => $utilisateur_id,
    ":type" => "rendezvous",
    ":message" => $message
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