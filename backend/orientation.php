<?php

require "db.php";

header("Content-Type: application/json; charset=utf-8");


/*
    1. Récupération du message
*/

$message = $_GET["message"] ?? "";

if ($message == "") {

    echo json_encode([
        "trouve" => false,
        "message" => "Aucun message fourni."
    ]);

    exit;
}


/*
    2. Préparation du message
*/

$message = strtolower(trim($message));

$message = str_replace(
    ["'", "’"],
    " ",
    $message
);
$message = strtr($message, [
    'à' => 'a',
    'â' => 'a',
    'ä' => 'a',
    'á' => 'a',
    'ã' => 'a',

    'ç' => 'c',

    'é' => 'e',
    'è' => 'e',
    'ê' => 'e',
    'ë' => 'e',

    'î' => 'i',
    'ï' => 'i',
    'ì' => 'i',
    'í' => 'i',

    'ô' => 'o',
    'ö' => 'o',
    'ò' => 'o',
    'ó' => 'o',

    'ù' => 'u',
    'û' => 'u',
    'ü' => 'u',
    'ú' => 'u',

    'ÿ' => 'y'
]);


/*
    Corrections de certaines formulations.
    Les expressions sont remplacées uniquement
    lorsqu'elles correspondent à des mots entiers.
*/

$corrections = [
    '/\bveu\b/u' => 'veux',
    '/\bveut\b/u' => 'veux',

    '/\bcree\b/u' => 'creer',
    '/\bcréé\b/u' => 'creer',

    '/\bentreprize\b/u' => 'entreprise',
    '/\bentreprisse\b/u' => 'entreprise',
    '/\bentrprise\b/u' => 'entreprise'
];


foreach ($corrections as $recherche => $remplacement) {

    $message = preg_replace(
        $recherche,
        $remplacement,
        $message
    );
}

/*
    Détection d'une demande alternative.

    Exemple :
    "acte de naissance ou acte de décès"
*/

$alternative = strpos($message, " ou ") !== false;
/*
    2 bis. Gestion des demandes générales
*/

if (
    $message === "entreprise" ||
    (
        strpos($message, "entreprise") !== false &&
        strpos($message, "creer") === false &&
        strpos($message, "immatriculer") === false &&
        strpos($message, "immatriculation") === false &&
        strpos($message, "document") === false
    )
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "entreprise",
        "message" => "Que souhaitez-vous faire concernant votre entreprise ?",
        "choix" => [
            [
                "id" => 10,
                "texte" => "Créer une entreprise"
            ],
            [
                "id" => 11,
                "texte" => "Immatriculer une entreprise"
            ],
            [
                "id" => 12,
                "texte" => "Obtenir des documents d'entreprise"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    $message === "impot" ||
    $message === "impots" ||
    (
        (strpos($message, "impot") !== false || strpos($message, "impots") !== false) &&
        strpos($message, "declar") === false &&
        strpos($message, "payer") === false &&
        strpos($message, "attestation") === false
    )
) {
    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "fiscalite",
        "message" => "Que souhaitez-vous faire concernant les impôts ?",
        "choix" => [
            [
                "id" => 4,
                "texte" => "Faire une déclaration fiscale"
            ],
            [
                "id" => 5,
                "texte" => "Payer les impôts"
            ],
            [
                "id" => 6,
                "texte" => "Obtenir une attestation fiscale"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (
    $message === "etat civil" ||
    (
        strpos($message, "etat civil") !== false &&
        strpos($message, "naissance") === false &&
        strpos($message, "deces") === false &&
        strpos($message, "mariage") === false
    )
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "etat_civil",
        "message" => "Que souhaitez-vous faire concernant l'état civil ?",
        "choix" => [
            [
                "id" => 1,
                "texte" => "Obtenir un acte de naissance"
            ],
            [
                "id" => 2,
                "texte" => "Obtenir un acte de décès"
            ],
            [
                "id" => 3,
                "texte" => "Obtenir un acte de mariage"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    $message === "foncier" ||
    (
        strpos($message, "foncier") !== false &&
        strpos($message, "titre") === false &&
        strpos($message, "cadastral") === false &&
        strpos($message, "propriete") === false
    )
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "foncier",
        "message" => "Que souhaitez-vous faire concernant le foncier ?",
        "choix" => [
            [
                "id" => 7,
                "texte" => "Obtenir un titre foncier"
            ],
            [
                "id" => 8,
                "texte" => "Obtenir un plan cadastral"
            ],
            [
                "id" => 9,
                "texte" => "Obtenir un certificat de propriété"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    $message === "social" ||
    (
        strpos($message, "social") !== false &&
        strpos($message, "aide") === false &&
        strpos($message, "prestation") === false &&
        strpos($message, "protection") === false
    )
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "social",
        "message" => "Que souhaitez-vous faire concernant les services sociaux ?",
        "choix" => [
            [
                "id" => 13,
                "texte" => "Obtenir une aide sociale"
            ],
            [
                "id" => 14,
                "texte" => "Obtenir des prestations sociales"
            ],
            [
                "id" => 15,
                "texte" => "Obtenir une protection sociale"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    $message === "transport" ||
    (
        strpos($message, "transport") !== false &&
        strpos($message, "permis") === false &&
        strpos($message, "immatriculation") === false &&
        strpos($message, "carte grise") === false
    )
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "type_ambiguite" => "transport",
        "message" => "Que souhaitez-vous faire concernant le transport ?",
        "choix" => [
            [
                "id" => 16,
                "texte" => "Obtenir un permis de conduire"
            ],
            [
                "id" => 17,
                "texte" => "Immatriculer un véhicule"
            ],
            [
                "id" => 18,
                "texte" => "Obtenir une carte grise"
            ]
        ]
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if (
    strpos($message, "immatriculation") !== false &&
    strpos($message, "entreprise") === false &&
    strpos($message, "societe") === false &&
    strpos($message, "vehicule") === false &&
    strpos($message, "voiture") === false &&
    strpos($message, "automobile") === false
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "message" => "Votre demande concerne une immatriculation. Veuillez preciser s'il s'agit d'une entreprise ou d'un vehicule."
    ]);

    exit;
}

if (
    strpos($message, "acte") !== false &&
    strpos($message, "naissance") === false &&
    strpos($message, "mariage") === false &&
    strpos($message, "deces") === false
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "message" => "Quel type d'acte recherchez-vous : naissance, mariage ou deces ?"
    ]);

    exit;
}



if (
    strpos($message, "certificat") !== false &&
    strpos($message, "propriete") === false &&
    strpos($message, "propriété") === false
) {

    echo json_encode([
        "trouve" => false,
        "ambigu" => true,
        "message" => "Quel type de certificat recherchez-vous ?"
    ]);

    exit;
}


/*
    3. Récupération des démarches
    et de leurs mots-clés
*/

$sql = "
    SELECT
        d.id,
        d.nom,
        d.description,
        d.service,
        d.lieu,
        d.delai,
        d.frais,
        d.etapes,
        m.mot
    FROM demarches d
    JOIN mots_cles m
        ON d.id = m.demarche_id
";

$stmt = $pdo->query($sql);

$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
    4. Tableau des scores
*/

$scores = [];


/*
    5. Analyse des mots-clés
*/

foreach ($resultats as $resultat) {

    $demarche_id = $resultat["id"];

    $mot = strtolower(trim($resultat["mot"]));

$mot = iconv(
    'UTF-8',
    'ASCII//TRANSLIT//IGNORE',
    $mot
);


    /*
        On ignore les mots-clés vides.
    */

    if ($mot == "") {
        continue;
    }


    /*
        Vérifie si le mot-clé est présent
        dans le message.
    */

    if (strpos($message, $mot) !== false) {


        /*
            Score de base :
            longueur du mot-clé.
        */

        $points = strlen($mot);


        /*
            Bonus pour les mots-clés courts
            mais significatifs.

            Exemple :
            mort
            decede
            naissance
        */

        if (strlen($mot) <= 10) {

            $points += 10;
        }


        /*
            Bonus pour les expressions longues
            et plus précises.

            Exemple :
            acte de naissance
            obtenir un acte de deces
        */

        if (strlen($mot) >= 15) {

            $points += 10;
        }


        /*
            Si cette démarche n'a encore
            aucun score, on l'enregistre.
        */

        if (!isset($scores[$demarche_id])) {

            $scores[$demarche_id] = [

                "score" => $points,

                "mot_cle" => $resultat["mot"],

                "demarche" => $resultat

            ];

        }


        /*
            Si la démarche existe déjà,
            on compare les scores.

            On conserve uniquement le meilleur
            mot-clé pour cette démarche.
        */

        else {

            if ($points > $scores[$demarche_id]["score"]) {

                $scores[$demarche_id]["score"] = $points;

                $scores[$demarche_id]["mot_cle"] =
                    $resultat["mot"];
            }
        }
    }
}


/*
    6. Aucune correspondance
*/

if (empty($scores)) {

    echo json_encode([
        "trouve" => false,
        "message" => "Je peux vous aider à trouver la bonne démarche. Pouvez-vous préciser votre besoin ?",
        "suggestions" => [
            "État civil : naissance, mariage, décès",
            "Fiscalité : déclaration, paiement des impôts, attestation fiscale",
            "Foncier : titre foncier, plan cadastral, propriété",
            "Entreprise : création, immatriculation, documents",
            "Social : aide sociale, prestations, protection sociale",
            "Transport : permis, immatriculation, carte grise"
        ]
    ]);

    exit;
}





/*
    7. Création de l'analyse
*/

$analyse = [];

foreach ($scores as $score) {

    $analyse[] = [

        "demarche" =>
            $score["demarche"]["nom"],

        "score" =>
            $score["score"],

        "mot_cle" =>
            $score["mot_cle"]
    ];
}


/*
    8. Demande alternative avec "ou"

    Exemple :
    "acte de naissance ou acte de décès"

    Si plusieurs démarches sont détectées,
    on demande une précision.
*/

if ($alternative && count($scores) > 1) {

    echo json_encode([

        "trouve" => false,

        "ambigu" => true,

        "message" =>
            "Votre demande peut correspondre a plusieurs demarches. Veuillez preciser votre demande.",

        "analyse" => $analyse

    ]);

    exit;
}


/*
    9. Recherche du meilleur score
*/

$meilleur = null;

foreach ($scores as $score) {

    if (

        $meilleur === null ||

        $score["score"] > $meilleur["score"]

    ) {

        $meilleur = $score;
    }
}


/*
    10. Recherche du deuxième meilleur score
*/

$deuxieme = null;

foreach ($scores as $score) {

    if ($score["demarche"]["id"] == $meilleur["demarche"]["id"]) {

        continue;
    }


    if (

        $deuxieme === null ||

        $score["score"] > $deuxieme["score"]

    ) {

        $deuxieme = $score;
    }
}


/*
    11. Seuil minimum
*/

$seuil = 10;


/*
    12. Vérification d'une ambiguïté
*/

if ($deuxieme !== null) {

    $ecart =
        $meilleur["score"] -
        $deuxieme["score"];


    /*
        Si les scores sont trop proches,
        on demande une précision.
    */

    if ($ecart < 10) {

        echo json_encode([

            "trouve" => false,

            "ambigu" => true,

            "message" =>
                "Votre demande peut correspondre a plusieurs demarches. Veuillez preciser votre demande.",

            "analyse" => $analyse

        ]);

        exit;
    }
}


/*
    13. Vérification du seuil
*/

if ($meilleur["score"] < $seuil) {

    echo json_encode([

        "trouve" => false,

        "message" =>
            "Votre demande n'est pas suffisamment precise.",

        "score" =>
            $meilleur["score"]

    ]);

    exit;
}


/*
    14. Identifiant de la démarche
*/

$demarche_id =
    $meilleur["demarche"]["id"];


/*
    15. Récupération des documents
*/

$sql_documents = "

    SELECT
        nom_document,
        description

    FROM documents_requis

    WHERE demarche_id = :demarche_id

";

$stmt_documents =
    $pdo->prepare($sql_documents);


$stmt_documents->execute([

    "demarche_id" =>
        $demarche_id

]);


$documents =
    $stmt_documents->fetchAll(PDO::FETCH_ASSOC);


/*
    16. Réponse finale
*/

echo json_encode([

    "trouve" => true,

    "score" =>
        $meilleur["score"],

    "analyse" =>
        $analyse,

    "demarche" => [

        "id" =>
            $meilleur["demarche"]["id"],

        "nom" =>
            $meilleur["demarche"]["nom"],

        "description" =>
            $meilleur["demarche"]["description"],

        "service" =>
            $meilleur["demarche"]["service"],

        "lieu" =>
            $meilleur["demarche"]["lieu"],

        "delai" =>
            $meilleur["demarche"]["delai"],

        "frais" =>
            $meilleur["demarche"]["frais"],

        "etapes" =>
            $meilleur["demarche"]["etapes"]
    ],

    "mot_cle_trouve" =>
        $meilleur["mot_cle"],

    "documents" =>
        $documents

]);