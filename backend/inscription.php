```php
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ]);
    exit;
}

$nom = trim($_POST["nom"] ?? "");
$prenom = trim($_POST["prenom"] ?? "");
$email = trim($_POST["email"] ?? "");
$telephone = trim($_POST["telephone"] ?? "");
$mot_de_passe = $_POST["mot_de_passe"] ?? "";

if (
    $nom === "" ||
    $prenom === "" ||
    $email === "" ||
    $telephone === "" ||
    $mot_de_passe === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont obligatoires."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ]);
    exit;
}

try {

    $verification = $pdo->prepare(
        "SELECT id FROM utilisateurs WHERE email = :email"
    );

    $verification->execute([
        ":email" => $email
    ]);

    if ($verification->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Cette adresse email existe déjà."
        ]);
        exit;
    }

    $mot_de_passe_hash = password_hash(
        $mot_de_passe,
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO utilisateurs
            (nom, prenom, email, telephone, mot_de_passe)
            VALUES
            (:nom, :prenom, :email, :telephone, :mot_de_passe)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":email" => $email,
        ":telephone" => $telephone,
        ":mot_de_passe" => $mot_de_passe_hash
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Compte créé avec succès."
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la création du compte."
    ]);
}

