<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/gemini_config.php";


/*
|--------------------------------------------------------------------------
| Vérifier la méthode
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Méthode non autorisée."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer le message
|--------------------------------------------------------------------------
*/

$input = json_decode(
    file_get_contents("php://input"),
    true
);

$message = trim($input["message"] ?? "");


if ($message === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Le message est vide."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


if (mb_strlen($message) > 500) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Le message est trop long."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer les démarches existantes
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            nom,
            description
        FROM demarches
        ORDER BY id
    ");

    $demarches = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Impossible de récupérer les démarches."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


if (empty($demarches)) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Aucune démarche n'est disponible."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Préparer la liste pour Gemini
|--------------------------------------------------------------------------
*/

$listeDemarches = "";

foreach ($demarches as $demarche) {

    $listeDemarches .=
        "ID : " . $demarche["id"] .
        " | Nom : " . $demarche["nom"] .
        " | Description : " . ($demarche["description"] ?? "") .
        "\n";
}


/*
|--------------------------------------------------------------------------
| Prompt Gemini
|--------------------------------------------------------------------------
*/

$prompt = <<<PROMPT
Tu es le moteur de compréhension du projet ADMIN-GUIDE.

Ton rôle est uniquement d'identifier la démarche administrative recherchée
par le citoyen à partir de son message.

Tu dois choisir UNIQUEMENT une démarche parmi la liste fournie ci-dessous.

Tu ne dois jamais inventer un ID.
Tu ne dois jamais créer une nouvelle démarche.
Tu ne dois pas fournir de documents, frais, délais ou adresses.
Tu ne dois pas inventer d'informations administratives.

Si le message permet d'identifier clairement une démarche,
retourne son ID dans demarche_id et laisse choix_ids vide.

Si plusieurs démarches sont possibles ou si le message est trop vague,
indique que le citoyen doit préciser son besoin et place dans choix_ids
les IDs des démarches pertinentes, avec un maximum de 3 choix.

Si une clarification est nécessaire,
demarche_id doit être null.

LISTE DES DÉMARCHES EXISTANTES :

$listeDemarches

MESSAGE DU CITOYEN :

$message
PROMPT;


/*
|--------------------------------------------------------------------------
| Appel Gemini
|--------------------------------------------------------------------------
*/

$apiKey = GEMINI_API_KEY;

$url =
    "https://generativelanguage.googleapis.com/v1beta/interactions";


$data = [

    "model" => "gemini-3.8-flash",

    "input" => $prompt,
    "response_format" => [

    "type" => "text",

    "mime_type" => "application/json",

    "schema" => [

        "type" => "object",

        "properties" => [

    "demarche_id" => [

        "type" => [
            "integer",
            "null"
        ]
    ],

    "choix_ids" => [

        "type" => "array",

        "items" => [

            "type" => "integer"
        ]
    ],

    "confiance" => [

        "type" => "number"
    ],

    "besoin_clarification" => [

        "type" => "boolean"
    ]

],

"required" => [

    "demarche_id",
    "choix_ids",
    "confiance",
    "besoin_clarification"
],

        "additionalProperties" => false
    ]
]
];


$ch = curl_init($url);

curl_setopt_array($ch, [

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [

        "Content-Type: application/json",

        "x-goog-api-key: " . $apiKey
    ],

    CURLOPT_POSTFIELDS =>
        json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        ),

    CURLOPT_TIMEOUT => 60
]);

$response = curl_exec($ch);

if ($response === false) {

    $erreur = curl_error($ch);

    curl_close($ch);

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Erreur de communication avec Gemini.",
        "erreur" => $erreur
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);

if ($httpCode !== 200) {

    http_response_code($httpCode);

    echo json_encode([
        "success" => false,
        "message" => "Gemini est temporairement indisponible. Veuillez réessayer.",
        "code" => $httpCode
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

/*
|--------------------------------------------------------------------------
| Décoder la réponse Gemini
|--------------------------------------------------------------------------
*/

$gemini = json_decode(
    $response,
    true
);


if (!is_array($gemini)) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
            "Réponse Gemini invalide."

    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer la sortie JSON de Gemini
|--------------------------------------------------------------------------
*/

$jsonTexte = "";


if (
    isset($gemini["steps"]) &&
    is_array($gemini["steps"])
) {

    foreach ($gemini["steps"] as $step) {

        if (
            isset($step["type"]) &&
            $step["type"] === "model_output" &&
            isset($step["content"]) &&
            is_array($step["content"])
        ) {

            foreach ($step["content"] as $content) {

                if (
                    isset($content["type"]) &&
                    $content["type"] === "text"
                ) {

                    $jsonTexte .= $content["text"];
                }
            }
        }
    }
}


$jsonTexte = trim($jsonTexte);


if ($jsonTexte === "") {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
            "Gemini n'a retourné aucune analyse."

    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Convertir le JSON de Gemini
|--------------------------------------------------------------------------
*/

$resultat = json_decode(
    $jsonTexte,
    true
);


if (!is_array($resultat)) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
            "Gemini a retourné un format invalide.",

        "reponse_gemini" => $jsonTexte

    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Vérifier l'ID choisi par Gemini
|--------------------------------------------------------------------------
*/

$demarcheId = $resultat["demarche_id"] ?? null;

$choixIds = $resultat["choix_ids"] ?? [];

if (!is_array($choixIds)) {
    $choixIds = [];
}

$choixIds = array_map("intval", $choixIds);
$choixIds = array_values(array_unique($choixIds));
$choixIds = array_slice($choixIds, 0, 3);

$confiance =
    isset($resultat["confiance"])
    ? (float) $resultat["confiance"]
    : 0;

$besoinClarification =
    !empty($resultat["besoin_clarification"]);


if (
    $demarcheId !== null &&
    !is_int($demarcheId)
) {

    $demarcheId = (int) $demarcheId;
}

/*
|--------------------------------------------------------------------------
| Si Gemini demande une précision
|--------------------------------------------------------------------------
*/

if (
    $besoinClarification ||
    $demarcheId === null
) {

    $choix = [];

    if (!empty($choixIds)) {

        $placeholders = implode(
            ",",
            array_fill(0, count($choixIds), "?")
        );

        $stmt = $pdo->prepare("
            SELECT
                id,
                nom,
                description,
                service
            FROM demarches
            WHERE id IN ($placeholders)
            ORDER BY id
        ");

        $stmt->execute($choixIds);

        $choix = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    echo json_encode([

        "success" => true,

        "trouve" => false,

        "clarification" => true,

        "message" =>
            "Votre demande peut correspondre à plusieurs démarches. Veuillez préciser votre besoin.",

        "confiance" => $confiance,

        "choix" => $choix

    ], JSON_UNESCAPED_UNICODE);

    exit;
}
/*
|--------------------------------------------------------------------------
| Vérifier que l'ID existe réellement dans la base
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nom,
        description,
        service,
        lieu,
        delai,
        frais,
        etapes
    FROM demarches
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([
    $demarcheId
]);

$demarche = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$demarche) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
            "La démarche sélectionnée n'existe pas."

    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer les documents
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nom_document,
        obligatoire
    FROM documents_requis
    WHERE demarche_id = ?
    ORDER BY id
");

$stmt->execute([
    $demarcheId
]);

$documents = $stmt->fetchAll(
    PDO::FETCH_ASSOC
);


/*
|--------------------------------------------------------------------------
| Réponse finale
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "trouve" => true,

    "clarification" => false,

    "confiance" => $confiance,

    "demarche" => $demarche,

    "documents" => $documents

], JSON_UNESCAPED_UNICODE);