<?php

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    header("Allow: GET");

    echo json_encode(
        [
            "trouve" => false,
            "erreur" =>
                "Méthode HTTP non autorisée."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

require_once "db.php";

ini_set("display_errors", "0");



set_exception_handler(function ($e) {

    http_response_code(500);

    echo json_encode(
        [
            "trouve" => false,
            "erreur" =>
                "Une erreur interne est survenue."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
});



/*
|--------------------------------------------------------------------------
| FONCTION : Nettoyage du message
|--------------------------------------------------------------------------
*/


function nettoyerMessage($message)
{
    // Minuscules
    $message = mb_strtolower(
        trim($message),
        "UTF-8"
    );

    // Uniformiser les apostrophes
    $message = str_replace(
        ["’", "`"],
        "'",
        $message
    );
    $message = preg_replace(
    '/\bl\s+entreprise\b/u',
    'entreprise',
    $message
);

    // Transformer l'apostrophe en espace
    // pour faciliter la recherche des mots-clés
    $message = str_replace(
        "'",
        " ",
        $message
    );

    // Normaliser les accents uniquement
    // pour la recherche
    $message = strtr(
        $message,
        [
            "à" => "a",
            "â" => "a",
            "ä" => "a",
            "á" => "a",
            "ã" => "a",

            "é" => "e",
            "è" => "e",
            "ê" => "e",
            "ë" => "e",
            "ẽ" => "e",

            "î" => "i",
            "ï" => "i",
            "í" => "i",
            "ì" => "i",

            "ô" => "o",
            "ö" => "o",
            "ó" => "o",
            "ò" => "o",
            "õ" => "o",

            "ù" => "u",
            "û" => "u",
            "ü" => "u",
            "ú" => "u",

            "ç" => "c"
        ]
    );

    // Corrections de fautes fréquentes
    $corrections = [

        '/\bveu\b/u' => 'veux',
        '/\bveut\b/u' => 'veux',
        '/\bve\b/u' => 'veux',

        '/\bcree\b/u' => 'creer',

        '/\bentreprize\b/u' => 'entreprise',
        '/\bentreprisse\b/u' => 'entreprise',
        '/\bentrprise\b/u' => 'entreprise',

        '/\bpermi\b/u' => 'permis',

        '/\bimpot\b/u' => 'impot'
    ];

    foreach ($corrections as $pattern => $replacement) {

        $message = preg_replace(
            $pattern,
            $replacement,
            $message
        );
    }

    return $message;
}
/*
|--------------------------------------------------------------------------
| 1. RÉCUPÉRATION DU MESSAGE
|--------------------------------------------------------------------------
*/

$message = trim($_GET["message"] ?? "");

if (mb_strlen($message, "UTF-8") > 500) {

    http_response_code(400);

    echo json_encode(
        [
            "trouve" => false,
            "message" =>
                "Votre demande est trop longue. Veuillez la préciser en 500 caractères maximum."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


if (empty(trim($message))) {

    echo json_encode(
        [
            "trouve" => false,
            "message" => "Veuillez préciser votre demande."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 2. NETTOYAGE DU MESSAGE
|--------------------------------------------------------------------------
*/

$message = nettoyerMessage($message);


/*
|--------------------------------------------------------------------------
| 3. DÉTECTION DE PLUSIEURS DEMANDES AVEC "OU"
|--------------------------------------------------------------------------
*/

if (strpos($message, " ou ") !== false) {

    $parties = explode(
        " ou ",
        $message
    );

    $resultatsOu = [];


    foreach ($parties as $partie) {

        $partie = trim($partie);


        if ($partie === "") {
            continue;
        }


        try {

            $stmtOu = $pdo->prepare("
                SELECT
                    d.id,
                    d.nom
                FROM demarches d
                INNER JOIN mots_cles m
                    ON d.id = m.demarche_id
                WHERE :message LIKE CONCAT('%', m.mot, '%')
                ORDER BY LENGTH(m.mot) DESC
                LIMIT 1
            ");


            $stmtOu->execute(
                [
                    "message" => $partie
                ]
            );


            $ligneOu =
                $stmtOu->fetch(PDO::FETCH_ASSOC);


            if ($ligneOu) {

                $resultatsOu[] = [

                    "id" =>
                        (int)$ligneOu["id"],

                    "nom" =>
                        $ligneOu["nom"]
                ];
            }

        } catch (Exception $e) {

            // On continue normalement
        }
    }


    if (count($resultatsOu) > 1) {

        echo json_encode(
            [
                "trouve" => false,
                "ambigu" => true,
                "type_ambiguite" =>
                    "plusieurs_demandes",

                "message" =>
                    "Votre demande semble concerner plusieurs démarches.",

                "choix" =>
                    $resultatsOu
            ],
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| 4. AMBIGUÏTÉ ENTREPRISE
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "entreprise") !== false &&

    strpos($message, "creer") === false &&
    strpos($message, "ouvrir") === false &&

    strpos($message, "immatriculer") === false &&
    strpos($message, "immatriculation") === false &&

    strpos($message, "document") === false &&
    strpos($message, "documents") === false &&

    strpos($message, "papier") === false &&
    strpos($message, "papiers") === false &&

    strpos($message, "dossier") === false &&
    strpos($message, "dossiers") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "entreprise",

            "message" =>
                "Votre demande concerne une entreprise. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 10,
                    "texte" =>
                        "Créer une entreprise"
                ],

                [
                    "id" => 11,
                    "texte" =>
                        "Immatriculer une entreprise"
                ],

                [
                    "id" => 12,
                    "texte" =>
                        "Obtenir les documents d'une entreprise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 5. AMBIGUÏTÉ FISCALITÉ
|--------------------------------------------------------------------------
*/

if (
    (
        strpos($message, "impot") !== false ||
        strpos($message, "impots") !== false
    )

    &&

    strpos($message, "declar") === false
    &&
    strpos($message, "payer") === false
    &&
    strpos($message, "attestation") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "fiscalite",

            "message" =>
                "Votre demande concerne les impôts. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 4,
                    "texte" =>
                        "Faire une déclaration fiscale"
                ],

                [
                    "id" => 5,
                    "texte" =>
                        "Payer les impôts"
                ],

                [
                    "id" => 6,
                    "texte" =>
                        "Obtenir une attestation fiscale"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 6. AMBIGUÏTÉ ÉTAT CIVIL
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "etat civil") !== false &&

    strpos($message, "naissance") === false &&
    strpos($message, "deces") === false &&
    strpos($message, "mariage") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "etat_civil",

            "message" =>
                "Votre demande concerne l'état civil. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 1,
                    "texte" =>
                        "Obtenir un acte de naissance"
                ],

                [
                    "id" => 2,
                    "texte" =>
                        "Obtenir un acte de décès"
                ],

                [
                    "id" => 3,
                    "texte" =>
                        "Obtenir un acte de mariage"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 7. CONTEXTE ÉTAT CIVIL : ENFANT
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "enfant") !== false &&

    strpos($message, "naissance") === false &&
    strpos($message, "deces") === false &&
    strpos($message, "mariage") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" =>
                "etat_civil_enfant",

            "message" =>
                "Votre demande concerne un enfant. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 1,
                    "texte" =>
                        "Obtenir un acte de naissance"
                ],

                [
                    "id" => 2,
                    "texte" =>
                        "Obtenir un acte de décès"
                ],

                [
                    "id" => 3,
                    "texte" =>
                        "Obtenir un acte de mariage"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 8. AMBIGUÏTÉ FONCIER
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "foncier") !== false &&

    strpos($message, "titre") === false &&
    strpos($message, "cadastral") === false &&
    strpos($message, "propriete") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "foncier",

            "message" =>
                "Votre demande concerne le domaine foncier. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 7,
                    "texte" =>
                        "Obtenir un titre foncier"
                ],

                [
                    "id" => 8,
                    "texte" =>
                        "Obtenir un plan cadastral"
                ],

                [
                    "id" => 9,
                    "texte" =>
                        "Obtenir un certificat de propriété"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 9. CONTEXTE FONCIER : TERRAIN
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "terrain") !== false &&

    strpos($message, "titre") === false &&
    strpos($message, "cadastral") === false &&
    strpos($message, "propriete") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" =>
                "foncier_terrain",

            "message" =>
                "Votre demande concerne un terrain. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 7,
                    "texte" =>
                        "Obtenir un titre foncier"
                ],

                [
                    "id" => 8,
                    "texte" =>
                        "Obtenir un plan cadastral"
                ],

                [
                    "id" => 9,
                    "texte" =>
                        "Obtenir un certificat de propriété"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 10. AMBIGUÏTÉ SOCIAL
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "social") !== false &&

    strpos($message, "aide") === false &&
    strpos($message, "prestation") === false &&
    strpos($message, "protection") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "social",

            "message" =>
                "Votre demande concerne les services sociaux. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 13,
                    "texte" =>
                        "Obtenir une aide sociale"
                ],

                [
                    "id" => 14,
                    "texte" =>
                        "Obtenir des prestations sociales"
                ],

                [
                    "id" => 15,
                    "texte" =>
                        "Obtenir une protection sociale"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 11. DOCUMENTS / PAPIERS / DOSSIER POUR UN VÉHICULE
|--------------------------------------------------------------------------
*/

if (
    (
        strpos($message, "papiers") !== false ||
        strpos($message, "papier") !== false ||
        strpos($message, "documents") !== false ||
        strpos($message, "document") !== false ||
        strpos($message, "dossier") !== false ||
        strpos($message, "dossiers") !== false
    )

    &&

    (
        strpos($message, "voiture") !== false ||
        strpos($message, "vehicule") !== false ||
        strpos($message, "automobile") !== false
    )

    &&

    strpos($message, "permis") === false &&
    strpos($message, "immatriculation") === false &&
    strpos($message, "immatriculer") === false &&
    strpos($message, "carte grise") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" =>
                "transport_vehicule",

            "message" =>
                "Votre demande concerne les documents d'un véhicule. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 17,
                    "texte" =>
                        "Immatriculer un véhicule"
                ],

                [
                    "id" => 18,
                    "texte" =>
                        "Obtenir une carte grise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 11 BIS. CONTEXTE : ACHAT D'UN VÉHICULE
|--------------------------------------------------------------------------
*/

if (
    (
        strpos($message, "achete") !== false ||
        strpos($message, "acheter") !== false ||
        strpos($message, "achat") !== false ||
        strpos($message, "vient d etre achete") !== false
    )

    &&

    (
        strpos($message, "voiture") !== false ||
        strpos($message, "vehicule") !== false ||
        strpos($message, "automobile") !== false
    )

    &&

    strpos($message, "permis") === false &&
    strpos($message, "immatriculation") === false &&
    strpos($message, "immatriculer") === false &&
    strpos($message, "carte grise") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "achat_vehicule",

            "message" =>
                "Vous venez d'acheter un véhicule. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 17,
                    "texte" =>
                        "Immatriculer un véhicule"
                ],

                [
                    "id" => 18,
                    "texte" =>
                        "Obtenir une carte grise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 12. CONTEXTE GÉNÉRAL VÉHICULE
|--------------------------------------------------------------------------
*/

if (
    (
        strpos($message, "voiture") !== false ||
        strpos($message, "vehicule") !== false ||
        strpos($message, "automobile") !== false
    )

    &&

    strpos($message, "permis") === false &&
    strpos($message, "immatriculation") === false &&
    strpos($message, "immatriculer") === false &&
    strpos($message, "carte grise") === false &&

    strpos($message, "papiers") === false &&
    strpos($message, "papier") === false &&
    strpos($message, "documents") === false &&
    strpos($message, "document") === false &&
    strpos($message, "dossier") === false &&
    strpos($message, "dossiers") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "transport",

            "message" =>
                "Votre demande concerne un véhicule. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 16,
                    "texte" =>
                        "Obtenir un permis de conduire"
                ],

                [
                    "id" => 17,
                    "texte" =>
                        "Immatriculer un véhicule"
                ],

                [
                    "id" => 18,
                    "texte" =>
                        "Obtenir une carte grise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 13. CONTEXTE GÉNÉRAL TRANSPORT
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "transport") !== false &&

    strpos($message, "permis") === false &&
    strpos($message, "immatriculation") === false &&
    strpos($message, "immatriculer") === false &&
    strpos($message, "carte grise") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "transport",

            "message" =>
                "Votre demande concerne le domaine du transport. Quelle démarche souhaitez-vous effectuer ?",

            "choix" => [

                [
                    "id" => 16,
                    "texte" =>
                        "Obtenir un permis de conduire"
                ],

                [
                    "id" => 17,
                    "texte" =>
                        "Immatriculer un véhicule"
                ],

                [
                    "id" => 18,
                    "texte" =>
                        "Obtenir une carte grise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 14. AMBIGUÏTÉ IMMATRICULATION
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "immatriculation") !== false &&

    strpos($message, "entreprise") === false &&
    strpos($message, "societe") === false &&
    strpos($message, "vehicule") === false &&
    strpos($message, "voiture") === false &&
    strpos($message, "automobile") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" =>
                "immatriculation",

            "message" =>
                "Le terme immatriculation peut concerner plusieurs démarches. Que souhaitez-vous immatriculer ?",

            "choix" => [

                [
                    "id" => 11,
                    "texte" =>
                        "Immatriculer une entreprise"
                ],

                [
                    "id" => 17,
                    "texte" =>
                        "Immatriculer un véhicule"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 15. AMBIGUÏTÉ ACTE
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "acte") !== false &&

    strpos($message, "naissance") === false &&
    strpos($message, "mariage") === false &&
    strpos($message, "deces") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" => "acte",

            "message" =>
                "Quel type d'acte souhaitez-vous obtenir ?",

            "choix" => [

                [
                    "id" => 1,
                    "texte" =>
                        "Acte de naissance"
                ],

                [
                    "id" => 2,
                    "texte" =>
                        "Acte de décès"
                ],

                [
                    "id" => 3,
                    "texte" =>
                        "Acte de mariage"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 16. AMBIGUÏTÉ CERTIFICAT
|--------------------------------------------------------------------------
*/

if (
    strpos($message, "certificat") !== false &&
    strpos($message, "propriete") === false
) {

    echo json_encode(
        [
            "trouve" => false,
            "ambigu" => true,
            "type_ambiguite" =>
                "certificat",

            "message" =>
                "Quel certificat recherchez-vous ?",

            "choix" => [

                [
                    "id" => 6,
                    "texte" =>
                        "Attestation fiscale"
                ],

                [
                    "id" => 9,
                    "texte" =>
                        "Certificat de propriété"
                ],

                [
                    "id" => 18,
                    "texte" =>
                        "Carte grise"
                ]
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 17. RÉCUPÉRATION DES DÉMARCHES ET MOTS-CLÉS
|--------------------------------------------------------------------------
*/

try {

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
        INNER JOIN mots_cles m
            ON d.id = m.demarche_id
    ";


    $stmt = $pdo->query($sql);


    $lignes =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(
        [
            "trouve" => false,
            "erreur" =>
                "Erreur lors de la récupération des démarches."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 18. RECHERCHE DES MOTS-CLÉS
|--------------------------------------------------------------------------
*/

$scores = [];


foreach ($lignes as $ligne) {

    $id =
        (int)$ligne["id"];


    /*
    | Nettoyage du mot-clé
    */

    $mot = mb_strtolower(
        trim($ligne["mot"]),
        "UTF-8"
    );


    /*
    | Suppression des accents du mot-clé
    */

    $mot = strtr(
        $mot,
        [
            "à" => "a",
            "â" => "a",
            "ä" => "a",

            "é" => "e",
            "è" => "e",
            "ê" => "e",
            "ë" => "e",

            "î" => "i",
            "ï" => "i",

            "ô" => "o",
            "ö" => "o",

            "ù" => "u",
            "û" => "u",
            "ü" => "u",

            "ç" => "c"
        ]
    );


    /*
    | Recherche du mot entier
    |
    | Exemple :
    | "nee" ne correspond pas à "annee"
    */

    $pattern =
        '/(?<![a-z])' .
        preg_quote($mot, '/') .
        '(?![a-z])/u';


    if (preg_match(
        $pattern,
        $message
    )) {

        $score =
            mb_strlen(
                $mot,
                "UTF-8"
            );


        /*
        | Bonus pour les mots-clés courts
        */

        if (
            mb_strlen(
                $mot,
                "UTF-8"
            ) <= 10
        ) {

            $score += 10;
        }


        /*
        | Bonus pour les expressions longues
        */

        if (
            mb_strlen(
                $mot,
                "UTF-8"
            ) >= 15
        ) {

            $score += 10;
        }


        /*
        | On conserve le meilleur mot-clé
        | de chaque démarche
        */

        if (
            !isset($scores[$id]) ||

            $score >
            $scores[$id]["score"]
        ) {

            $scores[$id] = [

                "score" =>
                    $score,

                "mot" =>
                    $mot,

                "demarche" =>
                    $ligne
            ];
        }
    }
}


/*
|--------------------------------------------------------------------------
| 19. AUCUN MOT-CLÉ TROUVÉ
|--------------------------------------------------------------------------
*/

if (empty($scores)) {

    echo json_encode(
        [
            "trouve" => false,

            "message" =>
                "Je peux vous aider à trouver la bonne démarche. Pouvez-vous préciser votre besoin ?",

            "domaines" => [

                "État civil",
                "Fiscalité",
                "Foncier",
                "Entreprise",
                "Social",
                "Transport"
            ]
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 20. TRI DES RÉSULTATS
|--------------------------------------------------------------------------
*/

usort(
    $scores,
    function ($a, $b) {

        return
            $b["score"] <=>
            $a["score"];
    }
);


/*
|--------------------------------------------------------------------------
| 21. MEILLEUR RÉSULTAT
|--------------------------------------------------------------------------
*/

$meilleur =
    $scores[0];


$meilleurScore =
    $meilleur["score"];


/*
|--------------------------------------------------------------------------
| 22. DÉTECTION D'UNE AMBIGUÏTÉ
|--------------------------------------------------------------------------
*/

if (count($scores) >= 2) {

    $deuxieme =
        $scores[1];


    $difference =
        $meilleurScore -
        $deuxieme["score"];


    if ($difference < 10) {

        $choix = [];


        foreach (
            $scores as $resultat
        ) {

            if (
                count($choix) >= 3
            ) {
                break;
            }


            $choix[] = [

                "id" =>
                    (int)$resultat[
                        "demarche"
                    ]["id"],

                "texte" =>
                    $resultat[
                        "demarche"
                    ]["nom"]
            ];
        }


        echo json_encode(
            [
                "trouve" => false,
                "ambigu" => true,

                "type_ambiguite" =>
                    "mots_cles_proches",

                "message" =>
                    "Plusieurs démarches correspondent à votre demande. Quelle démarche recherchez-vous ?",

                "choix" =>
                    $choix
            ],
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| 23. SCORE MINIMUM
|--------------------------------------------------------------------------
*/

if ($meilleurScore < 10) {

    echo json_encode(
        [
            "trouve" => false,

            "message" =>
                "Je n'ai pas suffisamment d'informations pour déterminer la démarche. Pouvez-vous préciser votre demande ?"
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 24. RÉCUPÉRATION DE LA DÉMARCHE
|--------------------------------------------------------------------------
*/

$demarche =
    $meilleur["demarche"];


$demarcheId =
    (int)$demarche["id"];


/*
|--------------------------------------------------------------------------
| 25. RÉCUPÉRATION DES DOCUMENTS
|--------------------------------------------------------------------------
*/

try {

    $stmtDocuments =
    $pdo->prepare("
        SELECT
            nom_document,
            description,
            obligatoire
        FROM documents_requis
        WHERE demarche_id = :demarche_id
    ");
    


    $stmtDocuments->execute(
        [
            "demarche_id" =>
                $demarcheId
        ]
    );


    $documents =
        $stmtDocuments->fetchAll(
            PDO::FETCH_ASSOC
        );



} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(
        [
            "trouve" => false,
            "erreur" =>
                "Erreur lors de la récupération des documents."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 26. PRÉPARATION DE L'ANALYSE
|--------------------------------------------------------------------------
*/

$analyse = [];


foreach (
    $scores as $resultat
) {

    $analyse[] = [

        "demarche" =>
            $resultat[
                "demarche"
            ]["nom"],

        "score" =>
            $resultat["score"],

        "mot_cle" =>
            $resultat["mot"]
    ];
}


/*
|--------------------------------------------------------------------------
| 27. RÉPONSE FINALE
|--------------------------------------------------------------------------
*/

echo json_encode(
    [

        "trouve" =>
            true,

        "score" =>
            $meilleurScore,

        "analyse" =>
            $analyse,

        "demarche" => [

            "id" =>
                $demarcheId,

            "nom" =>
                $demarche["nom"],

            "description" =>
                $demarche["description"],

            "service" =>
                $demarche["service"],

            "lieu" =>
                $demarche["lieu"],

            "delai" =>
                $demarche["delai"],

            "frais" =>
                $demarche["frais"],

            "etapes" =>
                $demarche["etapes"]
        ],

        "mot_cle_trouve" =>
            $meilleur["mot"],

        "documents" =>
            $documents
    ],

    JSON_UNESCAPED_UNICODE
);


exit;

?>