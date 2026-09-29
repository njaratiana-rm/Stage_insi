<?php

session_start();

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");


// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Vous devez être connecté."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// Vérifier que l'utilisateur est administrateur
if (($_SESSION["role"] ?? "") !== "admin") {

    echo json_encode([
        "success" => false,
        "message" => "Accès réservé à l'administrateur."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


try {

    /*
     * CHANGEMENT DE RÔLE
     */
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $id = intval($_POST["id"] ?? 0);
$role = trim($_POST["role"] ?? "");

$service_id = null;

if (isset($_POST["service_id"]) && $_POST["service_id"] !== "") {
    $service_id = intval($_POST["service_id"]);
}

        $rolesAutorises = [
            "citoyen",
            "agent",
            "responsable",
            "admin"
        ];

        if ($id <= 0) {

            echo json_encode([
                "success" => false,
                "message" => "Utilisateur invalide."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if (!in_array($role, $rolesAutorises, true)) {

            echo json_encode([
                "success" => false,
                "message" => "Rôle invalide."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $servicesAutorises = [1, 2, 3, 4, 5, 6];

if ($service_id !== null && !in_array($service_id, $servicesAutorises, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Service invalide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


        /*
         * Empêcher l'administrateur de modifier son propre rôle
         */
        if ($id === (int) $_SESSION["user_id"]) {

            echo json_encode([
                "success" => false,
                "message" => "Vous ne pouvez pas modifier votre propre rôle."
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $stmt = $pdo->prepare("
    UPDATE utilisateurs
    SET role = :role,
        service_id = :service_id
    WHERE id = :id
");

$stmt->execute([
    ":role" => $role,
    ":service_id" => $service_id,
    ":id" => $id
]);


        echo json_encode([
            "success" => true,
            "message" => "Rôle modifié avec succès."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }


    /*
     * AFFICHAGE DES UTILISATEURS
     */

    $sql = "
    SELECT
        id,
        nom,
        prenom,
        email,
        telephone,
        date_creation,
        role,
        service_id
    FROM utilisateurs
    ORDER BY date_creation DESC
";

    $stmt = $pdo->query($sql);

    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        "success" => true,
        "utilisateurs" => $utilisateurs
    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la gestion des utilisateurs."
    ], JSON_UNESCAPED_UNICODE);
}