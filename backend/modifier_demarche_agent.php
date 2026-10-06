<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");


// Vérifier la connexion
if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Vérifier le rôle
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "agent") {

    echo json_encode([
        "success" => false,
        "message" => "Accès réservé aux agents."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Vérifier la méthode
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Récupérer les données
$id = isset($_POST["id"])
    ? (int) $_POST["id"]
    : 0;

$nom = trim($_POST["nom"] ?? "");
$description = trim($_POST["description"] ?? "");
$lieu = trim($_POST["lieu"] ?? "");
$delai = trim($_POST["delai"] ?? "");
$frais = trim($_POST["frais"] ?? "");


// Vérifier l'identifiant
if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Identifiant de démarche invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Vérifier les champs obligatoires
if ($nom === "") {

    echo json_encode([
        "success" => false,
        "message" => "Le nom de la démarche est obligatoire."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Mapping service → démarches
$servicesDemarches = [
    1 => [1, 2, 3],
    2 => [4, 5, 6],
    3 => [7, 8, 9],
    4 => [10, 11, 12],
    5 => [13, 14, 15],
    6 => [16, 17, 18]
];


// Récupérer le service de l'agent
$stmt = $pdo->prepare("
    SELECT service_id
    FROM utilisateurs
    WHERE id = :id
");

$stmt->execute([
    ":id" => $_SESSION["user_id"]
]);

$agent = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$agent) {

    echo json_encode([
        "success" => false,
        "message" => "Agent introuvable."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


$serviceId = (int) $agent["service_id"];


// Vérifier que le service existe
if (
    !isset($servicesDemarches[$serviceId]) ||
    !in_array($id, $servicesDemarches[$serviceId], true)
) {

    echo json_encode([
        "success" => false,
        "message" => "Vous ne pouvez pas modifier cette démarche."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Modifier la démarche
$stmt = $pdo->prepare("
    UPDATE demarches
    SET
        nom = :nom,
        description = :description,
        lieu = :lieu,
        delai = :delai,
        frais = :frais
    WHERE id = :id
");

$stmt->execute([
    ":nom" => $nom,
    ":description" => $description,
    ":lieu" => $lieu,
    ":delai" => $delai,
    ":frais" => $frais,
    ":id" => $id
]);


echo json_encode([
    "success" => true,
    "message" => "Démarche modifiée avec succès."
], JSON_UNESCAPED_UNICODE);

