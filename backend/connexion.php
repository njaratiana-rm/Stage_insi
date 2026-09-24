<?php

header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = trim($_POST["email"] ?? "");
$mot_de_passe = $_POST["mot_de_passe"] ?? "";

if ($email === "" || $mot_de_passe === "") {
    echo json_encode([
        "success" => false,
        "message" => "L'email et le mot de passe sont obligatoires."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {

    $sql = "SELECT id, nom, prenom, email, mot_de_passe
            FROM utilisateurs
            WHERE email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email
    ]);

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        echo json_encode([
            "success" => false,
            "message" => "Email ou mot de passe incorrect."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
        echo json_encode([
            "success" => false,
            "message" => "Email ou mot de passe incorrect."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    session_regenerate_id(true);

$_SESSION["user_id"] = $utilisateur["id"];
$_SESSION["nom"] = $utilisateur["nom"];
$_SESSION["prenom"] = $utilisateur["prenom"];
$_SESSION["email"] = $utilisateur["email"];

    unset($utilisateur["mot_de_passe"]);

    echo json_encode([
        "success" => true,
        "message" => "Connexion réussie.",
        "utilisateur" => $utilisateur
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la connexion."
    ], JSON_UNESCAPED_UNICODE);
}