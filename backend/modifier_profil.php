<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");


/* Vérifier la connexion */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* Récupérer l'utilisateur connecté */

$user_id = intval($_SESSION["user_id"]);


/* Récupérer les données */

$nom = trim($_POST["nom"] ?? "");
$prenom = trim($_POST["prenom"] ?? "");
$email = trim($_POST["email"] ?? "");
$telephone = trim($_POST["telephone"] ?? "");


/* Vérifier les champs obligatoires */

if (
    $nom === "" ||
    $prenom === "" ||
    $email === "" ||
    $telephone === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont obligatoires."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* Vérifier l'email */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* Vérifier que l'email n'est pas déjà utilisé */

$stmt = $pdo->prepare("
    SELECT id
    FROM utilisateurs
    WHERE email = :email
    AND id != :id
    LIMIT 1
");

$stmt->execute([
    ":email" => $email,
    ":id" => $user_id
]);

if ($stmt->fetch()) {

    echo json_encode([
        "success" => false,
        "message" => "Cette adresse email est déjà utilisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/* Modifier le profil */

$stmt = $pdo->prepare("
    UPDATE utilisateurs
    SET
        nom = :nom,
        prenom = :prenom,
        email = :email,
        telephone = :telephone
    WHERE id = :id
");

$stmt->execute([
    ":nom" => $nom,
    ":prenom" => $prenom,
    ":email" => $email,
    ":telephone" => $telephone,
    ":id" => $user_id
]);


/* Réponse */

echo json_encode([
    "success" => true,
    "message" => "Profil modifié avec succès."
], JSON_UNESCAPED_UNICODE);