<?php

require "db.php";
$pdo->exec("SET NAMES utf8mb4");

$corrections = [

    1 => [
        "nom" => "Acte de naissance",
        "description" => "Démarche permettant de demander un acte de naissance.",
        "service" => "Service état civil",
        "lieu" => "Commune concernée",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "À déterminer"
    ],

    2 => [
        "nom" => "Acte de décès",
        "description" => "Démarche permettant de demander un acte de décès.",
        "service" => "Service état civil",
        "lieu" => "Commune du lieu du décès",
        "delai" => "30 jours pour déclarer le décès",
        "frais" => "Gratuit pour la première copie",
        "etapes" => "1. Faire constater le décès; 2. Préparer les documents nécessaires; 3. Déclarer le décès à l'état civil compétent; 4. Fournir les informations et documents demandés; 5. Faire établir l'acte de décès; 6. Recevoir la première copie de l'acte"
    ],

    3 => [
        "nom" => "Acte de mariage",
        "description" => "Démarche permettant de demander un acte de mariage.",
        "service" => "Service état civil",
        "lieu" => "Commune du lieu du mariage",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre au service état civil compétent; 3. Fournir les informations et documents demandés; 4. Faire établir ou rechercher l'acte de mariage; 5. Recevoir une copie de l'acte de mariage"
    ]

];

$sql = "UPDATE demarches
        SET
            nom = :nom,
            description = :description,
            service = :service,
            lieu = :lieu,
            delai = :delai,
            frais = :frais,
            etapes = :etapes
        WHERE id = :id";

$stmt = $pdo->prepare($sql);

foreach ($corrections as $id => $data) {

    $stmt->execute([
        "id" => $id,
        "nom" => $data["nom"],
        "description" => $data["description"],
        "service" => $data["service"],
        "lieu" => $data["lieu"],
        "delai" => $data["delai"],
        "frais" => $data["frais"],
        "etapes" => $data["etapes"]
    ]);
}

echo "Les démarches 1 à 3 ont été corrigées avec succès.";

$documents = [

    1 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Certificat de naissance",
            "description" => "Document fourni pour justifier la naissance."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire à remplir pour effectuer la demande."
        ]
    ],

    2 => [
        [
            "nom" => "Certificat médical de décès",
            "description" => "Document attestant le décès de la personne."
        ],
        [
            "nom" => "Pièce d'identité du déclarant",
            "description" => "Document permettant d'identifier la personne qui effectue la déclaration."
        ],
        [
            "nom" => "Pièce d'identité du défunt",
            "description" => "Document permettant de vérifier l'identité du défunt."
        ],
        [
            "nom" => "Livret de famille",
            "description" => "Document pouvant être demandé selon la situation."
        ],
        [
            "nom" => "Acte de naissance du défunt",
            "description" => "Document pouvant être demandé pour vérifier l'identité du défunt."
        ]
    ],

    3 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Livret de famille",
            "description" => "Document pouvant être demandé selon la situation."
        ],
        [
            "nom" => "Informations sur le mariage",
            "description" => "Informations permettant de retrouver ou vérifier le mariage."
        ]
    ]
];

foreach ($documents as $demarcheId => $liste) {

    $sql = "UPDATE documents_requis
            SET nom_document = :nom,
                description = :description
            WHERE demarche_id = :demarche_id
            AND id = :id";

    $stmt = $pdo->prepare($sql);

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "demarche_id" => $demarcheId,
                "id" => $ids[$index]
            ]);
        }
    }
}

echo "Documents corrigés avec succès.";


$documents = [

    5 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le contribuable."
        ],
        [
            "nom" => "Référence fiscale",
            "description" => "Information permettant d'identifier le dossier fiscal du contribuable."
        ],
        [
            "nom" => "Avis ou document fiscal",
            "description" => "Document indiquant les informations relatives à l'impôt à payer."
        ],
        [
            "nom" => "Justificatif de paiement",
            "description" => "Document permettant de conserver la preuve du paiement effectué."
        ]
    ],

    6 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence fiscale",
            "description" => "Information permettant d'identifier le dossier fiscal du demandeur."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire à remplir pour effectuer la demande d'attestation fiscale."
        ]
    ],

    7 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence du titre foncier",
            "description" => "Information permettant d'identifier le titre foncier concerné."
        ],
        [
            "nom" => "Documents relatifs à la propriété",
            "description" => "Documents permettant de fournir des informations sur la propriété concernée."
        ]
    ],

    8 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence de la propriété",
            "description" => "Information permettant d'identifier la propriété concernée."
        ],
        [
            "nom" => "Informations cadastrales",
            "description" => "Informations permettant de retrouver ou identifier la parcelle concernée."
        ]
    ],

    9 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence de la propriété",
            "description" => "Information permettant d'identifier la propriété concernée."
        ],
        [
            "nom" => "Documents relatifs à la propriété",
            "description" => "Documents permettant de fournir les informations nécessaires sur la propriété."
        ]
    ]
];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents des démarches 5 à 9 corrigés avec succès.";


$corrections = [

    5 => [
        "nom" => "Paiement des impôts",
        "description" => "Démarche permettant de consulter les informations relatives aux impôts et d'effectuer un paiement.",
        "service" => "Service fiscal compétent",
        "lieu" => "Centre fiscal compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Vérifier les informations relatives à l'impôt; 2. Préparer les informations nécessaires; 3. Effectuer le paiement; 4. Conserver le justificatif de paiement"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 5 corrigée avec succès.";

$documents = [

    6 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence fiscale",
            "description" => "Information permettant d'identifier le dossier fiscal du demandeur."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire à remplir pour effectuer la demande d'attestation fiscale."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 6 corrigés avec succès.";


$corrections = [

    6 => [
        "nom" => "Attestation fiscale",
        "description" => "Démarche permettant de demander une attestation fiscale.",
        "service" => "Service fiscal compétent",
        "lieu" => "Centre fiscal compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre au service fiscal compétent; 3. Fournir les informations et documents demandés; 4. Effectuer la demande d'attestation fiscale; 5. Recevoir l'attestation fiscale"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 6 corrigée avec succès.";

$documents = [

    7 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence du titre foncier",
            "description" => "Information permettant d'identifier le titre foncier concerné."
        ],
        [
            "nom" => "Documents relatifs à la propriété",
            "description" => "Documents permettant de fournir des informations sur la propriété concernée."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 7 corrigés avec succès.";


$corrections = [

    7 => [
        "nom" => "Titre foncier",
        "description" => "Démarche permettant de consulter ou d'obtenir des informations relatives à un titre foncier.",
        "service" => "Service foncier compétent",
        "lieu" => "Service foncier compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre au service foncier compétent; 3. Fournir les informations relatives à la propriété; 4. Effectuer la demande; 5. Recevoir les informations ou documents relatifs au titre foncier"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 7 corrigée avec succès.";

$documents = [

    8 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence de la propriété",
            "description" => "Information permettant d'identifier la propriété concernée."
        ],
        [
            "nom" => "Informations cadastrales",
            "description" => "Informations permettant de retrouver ou identifier la parcelle concernée."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 8 corrigés avec succès.";

$corrections = [

    8 => [
        "nom" => "Plan cadastral",
        "description" => "Démarche permettant de consulter ou d'obtenir des informations relatives à un plan cadastral.",
        "service" => "Service cadastral compétent",
        "lieu" => "Service cadastral compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre au service cadastral compétent; 3. Fournir les informations relatives à la parcelle; 4. Effectuer la demande; 5. Recevoir le plan cadastral ou les informations demandées"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 8 corrigée avec succès.";

$documents = [

    9 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Référence de la propriété",
            "description" => "Information permettant d'identifier la propriété concernée."
        ],
        [
            "nom" => "Documents relatifs à la propriété",
            "description" => "Documents permettant de fournir les informations nécessaires sur la propriété."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 9 corrigés avec succès.";

$corrections = [

    9 => [
        "nom" => "Certificat de propriété",
        "description" => "Démarche permettant de demander un certificat attestant les informations relatives à une propriété.",
        "service" => "Service foncier compétent",
        "lieu" => "Service foncier compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre au service foncier compétent; 3. Fournir les informations relatives à la propriété; 4. Effectuer la demande de certificat de propriété; 5. Recevoir le certificat de propriété"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 9 corrigée avec succès.";

$documents = [

    10 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Justificatif de domicile",
            "description" => "Document permettant de justifier le domicile du demandeur."
        ],
        [
            "nom" => "Formulaire de création",
            "description" => "Formulaire nécessaire pour effectuer les formalités de création de l'entreprise."
        ],
        [
            "nom" => "Documents relatifs à l'entreprise",
            "description" => "Documents nécessaires selon la forme et la situation de l'entreprise."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 10 corrigés avec succès.";

$corrections = [

    10 => [
        "nom" => "Création d'entreprise",
        "description" => "Démarche permettant d'effectuer les formalités nécessaires à la création d'une entreprise.",
        "service" => "Service compétent pour la création d'entreprise",
        "lieu" => "Centre ou service compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Choisir la forme de l'entreprise; 2. Préparer les documents nécessaires; 3. Remplir les formulaires de création; 4. Déposer le dossier auprès du service compétent; 5. Effectuer les formalités d'immatriculation; 6. Recevoir les documents relatifs à la création de l'entreprise"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 10 corrigée avec succès.";

$documents = [

    11 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Documents de l'entreprise",
            "description" => "Documents permettant de fournir les informations nécessaires sur l'entreprise."
        ],
        [
            "nom" => "Formulaire d'immatriculation",
            "description" => "Formulaire nécessaire pour effectuer la demande d'immatriculation."
        ],
        [
            "nom" => "Justificatif de domicile",
            "description" => "Document permettant de justifier le domicile du demandeur ou de l'entreprise."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 11 corrigés avec succès.";

$corrections = [

    11 => [
        "nom" => "Immatriculation entreprise",
        "description" => "Démarche permettant d'effectuer les formalités nécessaires à l'immatriculation d'une entreprise.",
        "service" => "Service compétent pour l'immatriculation des entreprises",
        "lieu" => "Centre ou service compétent",
        "delai" => "À déterminer",
        "frais" => "À déterminer",
        "etapes" => "1. Préparer les documents nécessaires; 2. Remplir le formulaire d'immatriculation; 3. Déposer le dossier auprès du service compétent; 4. Fournir les informations demandées; 5. Recevoir les documents relatifs à l'immatriculation de l'entreprise"
    ]

];

foreach ($corrections as $id => $correction) {

    $stmt = $pdo->prepare(
        "UPDATE demarches
         SET nom = :nom,
             description = :description,
             service = :service,
             lieu = :lieu,
             delai = :delai,
             frais = :frais,
             etapes = :etapes
         WHERE id = :id"
    );

    $stmt->execute([
        "nom" => $correction["nom"],
        "description" => $correction["description"],
        "service" => $correction["service"],
        "lieu" => $correction["lieu"],
        "delai" => $correction["delai"],
        "frais" => $correction["frais"],
        "etapes" => $correction["etapes"],
        "id" => $id
    ]);
}

echo "Démarche 11 corrigée avec succès.";

$documents = [

    12 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le représentant ou responsable de l'entreprise.",
            "obligatoire" => 1
        ],
        [
            "nom" => "Référence de l'entreprise",
            "description" => "Information permettant d'identifier l'entreprise, notamment son numéro d'immatriculation ou sa référence administrative lorsqu'elle est disponible.",
            "obligatoire" => 1
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire nécessaire pour effectuer la demande du document administratif recherché.",
            "obligatoire" => 1
        ],
        [
            "nom" => "Justificatif de l'entreprise",
            "description" => "Document permettant de justifier l'existence ou la situation administrative de l'entreprise selon le document demandé.",
            "obligatoire" => 0
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmtUpdate = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description,
             obligatoire = :obligatoire
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    $stmtInsert = $pdo->prepare(
        "INSERT INTO documents_requis
         (demarche_id, nom_document, description, obligatoire)
         VALUES
         (:demarche_id, :nom, :description, :obligatoire)"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmtUpdate->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "obligatoire" => $document["obligatoire"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);

        } else {

            $stmtInsert->execute([
                "demarche_id" => $demarcheId,
                "nom" => $document["nom"],
                "description" => $document["description"],
                "obligatoire" => $document["obligatoire"]
            ]);
        }
    }
}

echo "Documents de la démarche 12 corrigés avec succès.";

$demarche12 = [
    "nom" => "Documents d'entreprise",
    "description" => "Démarche permettant à une entreprise d'obtenir ou de consulter les documents administratifs relatifs à son activité et à son immatriculation.",
    "service" => "Service compétent pour les formalités administratives des entreprises",
    "lieu" => "Service ou guichet compétent selon le document demandé",
    "delai" => "Selon la nature du document demandé et le traitement du dossier par le service compétent.",
    "frais" => "Selon la nature du document demandé et les tarifs ou droits applicables.",
    "etapes" => "1. Identifier le document administratif recherché; 2. Vérifier les conditions et informations nécessaires à la demande; 3. Préparer les pièces nécessaires selon le document demandé; 4. S'adresser au service compétent; 5. Déposer la demande et fournir les informations ou pièces demandées; 6. Régler les frais ou droits applicables lorsqu'ils sont exigés; 7. Récupérer le document délivré par le service compétent."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 12"
);

$stmt->execute($demarche12);

echo "Démarche 12 corrigée avec succès.";


$documents = [

    13 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Justificatif de situation",
            "description" => "Document permettant de justifier la situation du demandeur."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire nécessaire pour effectuer la demande d'aide sociale."
        ],
        [
            "nom" => "Justificatifs de ressources",
            "description" => "Documents permettant de justifier les ressources du demandeur selon sa situation."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 13 corrigés avec succès.";

$demarche13 = [
    "nom" => "Aide sociale",
    "description" => "Démarche permettant de demander une aide sociale selon la situation du demandeur.",
    "service" => "Service social compétent",
    "lieu" => "Centre ou service social compétent",
    "delai" => "À déterminer",
    "frais" => "Gratuit",
    "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre auprès du service social compétent; 3. Présenter sa situation et fournir les informations demandées; 4. Déposer la demande d'aide sociale; 5. Attendre l'étude de la demande; 6. Recevoir la décision ou les informations relatives à la demande."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 13"
);

$stmt->execute($demarche13);

echo "Démarche 13 corrigée avec succès.";

$documents = [

    14 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Justificatif de situation",
            "description" => "Document permettant de justifier la situation du demandeur."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire nécessaire pour effectuer la demande de prestation sociale."
        ],
        [
            "nom" => "Justificatifs de ressources",
            "description" => "Documents permettant de justifier les ressources du demandeur selon la prestation demandée."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 14 corrigés avec succès.";

$demarche14 = [
    "nom" => "Prestations sociales",
    "description" => "Démarche permettant de demander ou de bénéficier d'une prestation sociale selon la situation du demandeur.",
    "service" => "Service social compétent",
    "lieu" => "Centre ou service social compétent",
    "delai" => "À déterminer",
    "frais" => "Gratuit",
    "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre auprès du service social compétent; 3. Présenter sa situation et fournir les informations demandées; 4. Déposer la demande de prestation sociale; 5. Attendre l'étude de la demande; 6. Recevoir la décision ou les informations relatives à la prestation."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 14"
);

$stmt->execute($demarche14);

echo "Démarche 14 corrigée avec succès.";

$documents = [

    15 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Justificatif de situation",
            "description" => "Document permettant de justifier la situation du demandeur."
        ],
        [
            "nom" => "Formulaire de demande",
            "description" => "Formulaire nécessaire pour effectuer la demande de protection sociale."
        ],
        [
            "nom" => "Justificatifs de ressources",
            "description" => "Documents permettant de justifier les ressources du demandeur selon sa situation."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 15 corrigés avec succès.";

$demarche15 = [
    "nom" => "Protection sociale",
    "description" => "Démarche permettant de demander ou de bénéficier d'une protection sociale selon la situation du demandeur.",
    "service" => "Service social compétent",
    "lieu" => "Centre ou service social compétent",
    "delai" => "À déterminer",
    "frais" => "Gratuit",
    "etapes" => "1. Préparer les documents nécessaires; 2. Se rendre auprès du service social compétent; 3. Présenter sa situation et fournir les informations demandées; 4. Déposer la demande de protection sociale; 5. Attendre l'étude de la demande; 6. Recevoir la décision ou les informations relatives à la protection sociale."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 15"
);

$stmt->execute($demarche15);

echo "Démarche 15 corrigée avec succès.";

$documents = [

    16 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le demandeur."
        ],
        [
            "nom" => "Formulaire de demande de permis",
            "description" => "Formulaire nécessaire pour effectuer la demande de permis de conduire."
        ],
        [
            "nom" => "Certificat médical",
            "description" => "Document attestant que le demandeur est apte à conduire."
        ],
        [
            "nom" => "Photos d'identité",
            "description" => "Photos nécessaires pour constituer le dossier de permis de conduire."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 16 corrigés avec succès.";

$demarche16 = [
    "nom" => "Permis de conduire",
    "description" => "Démarche permettant de demander un permis de conduire.",
    "service" => "Service compétent pour les permis de conduire",
    "lieu" => "Centre ou service compétent",
    "delai" => "À déterminer",
    "frais" => "À déterminer",
    "etapes" => "1. Préparer les documents nécessaires; 2. Remplir le formulaire de demande de permis de conduire; 3. Effectuer les formalités nécessaires; 4. Passer les examens requis; 5. Déposer ou compléter le dossier auprès du service compétent; 6. Recevoir le permis de conduire."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 16"
);

$stmt->execute($demarche16);

echo "Démarche 16 corrigée avec succès.";

$documents = [

    17 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le propriétaire du véhicule."
        ],
        [
            "nom" => "Certificat de cession",
            "description" => "Document permettant de justifier la cession ou le transfert du véhicule."
        ],
        [
            "nom" => "Certificat de conformité",
            "description" => "Document permettant de vérifier la conformité du véhicule."
        ],
        [
            "nom" => "Formulaire d'immatriculation",
            "description" => "Formulaire nécessaire pour effectuer la demande d'immatriculation du véhicule."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 17 corrigés avec succès.";

$demarche17 = [
    "nom" => "Immatriculation véhicule",
    "description" => "Démarche permettant d'effectuer les formalités nécessaires à l'immatriculation d'un véhicule.",
    "service" => "Service compétent pour l'immatriculation des véhicules",
    "lieu" => "Centre ou service compétent",
    "delai" => "À déterminer",
    "frais" => "À déterminer",
    "etapes" => "1. Préparer les documents nécessaires; 2. Remplir le formulaire d'immatriculation; 3. Fournir les informations relatives au véhicule et à son propriétaire; 4. Déposer le dossier auprès du service compétent; 5. Effectuer les formalités nécessaires; 6. Recevoir les documents relatifs à l'immatriculation du véhicule."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 17"
);

$stmt->execute($demarche17);

echo "Démarche 17 corrigée avec succès.";

$documents = [

    18 => [
        [
            "nom" => "Pièce d'identité",
            "description" => "Document permettant d'identifier le propriétaire du véhicule."
        ],
        [
            "nom" => "Certificat de cession",
            "description" => "Document permettant de justifier la cession ou le transfert du véhicule."
        ],
        [
            "nom" => "Certificat de conformité",
            "description" => "Document permettant de vérifier la conformité du véhicule."
        ],
        [
            "nom" => "Formulaire de demande de carte grise",
            "description" => "Formulaire nécessaire pour effectuer la demande de carte grise."
        ]
    ]

];

foreach ($documents as $demarcheId => $liste) {

    $resultat = $pdo->prepare(
        "SELECT id
         FROM documents_requis
         WHERE demarche_id = :demarche_id
         ORDER BY id"
    );

    $resultat->execute([
        "demarche_id" => $demarcheId
    ]);

    $ids = $resultat->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare(
        "UPDATE documents_requis
         SET nom_document = :nom,
             description = :description
         WHERE id = :id
           AND demarche_id = :demarche_id"
    );

    foreach ($liste as $index => $document) {

        if (isset($ids[$index])) {

            $stmt->execute([
                "nom" => $document["nom"],
                "description" => $document["description"],
                "id" => $ids[$index],
                "demarche_id" => $demarcheId
            ]);
        }
    }
}

echo "Documents de la démarche 18 corrigés avec succès.";

$demarche18 = [
    "nom" => "Carte grise",
    "description" => "Démarche permettant d'effectuer les formalités nécessaires à l'obtention d'une carte grise pour un véhicule.",
    "service" => "Service compétent pour l'immatriculation des véhicules",
    "lieu" => "Centre ou service compétent",
    "delai" => "À déterminer",
    "frais" => "À déterminer",
    "etapes" => "1. Préparer les documents nécessaires; 2. Remplir le formulaire de demande de carte grise; 3. Fournir les informations relatives au véhicule et à son propriétaire; 4. Déposer le dossier auprès du service compétent; 5. Effectuer les formalités nécessaires; 6. Recevoir la carte grise."
];

$stmt = $pdo->prepare(
    "UPDATE demarches
     SET nom = :nom,
         description = :description,
         service = :service,
         lieu = :lieu,
         delai = :delai,
         frais = :frais,
         etapes = :etapes
     WHERE id = 18"
);

$stmt->execute($demarche18);

echo "Démarche 18 corrigée avec succès.";
echo "Démarche 18 corrigée avec succès.";
// Correction ciblée de la démarche 3
$etapesMariage = "1. Préparer les documents nécessaires; 2. Se rendre au service état civil compétent; 3. Fournir les informations et documents demandés; 4. Faire établir ou rechercher l'acte de mariage; 5. Recevoir une copie de l'acte de mariage";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET etapes = :etapes
    WHERE id = 3
");

$stmt->execute([
    "etapes" => $etapesMariage
]);

echo "Étapes de la démarche 3 corrigées avec succès.";

// Correction des 5 documents corrompus de la démarche 3

$documentsMariage = [
    78 => [
        "nom" => "Photo d'identité récente",
        "description" => "Photo d'identité récente du futur époux"
    ],
    79 => [
        "nom" => "Copie légalisée de la CIN des futurs époux",
        "description" => "Copie légalisée des cartes d'identité nationale des futurs époux"
    ],
    80 => [
        "nom" => "Copie légalisée de la CIN des témoins",
        "description" => "Copie légalisée des cartes d'identité nationale des témoins"
    ],
    81 => [
        "nom" => "Imprimé de demande de mariage civil",
        "description" => "Formulaire de demande nécessaire pour la célébration du mariage civil"
    ],
    82 => [
        "nom" => "4 enveloppes timbrées",
        "description" => "Quatre enveloppes timbrées nécessaires au dossier"
    ]
];

$stmt = $pdo->prepare("
    UPDATE documents_requis
    SET nom_document = :nom,
        description = :description
    WHERE id = :id
      AND demarche_id = 3
");

foreach ($documentsMariage as $id => $document) {
    $stmt->execute([
        "id" => $id,
        "nom" => $document["nom"],
        "description" => $document["description"]
    ]);
}

echo "Les 5 documents corrompus de la démarche 3 ont été corrigés avec succès.";

// Correction des informations générales de la démarche 3

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais
    WHERE id = 3
");

$stmt->execute([
    "description" => "Démarche permettant de procéder à la célébration du mariage civil et à l'obtention de l'acte de mariage.",
    "service" => "Service état civil",
    "lieu" => "Firaisana où réside au moins l'un des futurs époux",
    "delai" => "Dossier complet à soumettre 15 à 20 jours avant la date prévue",
    "frais" => "10 000 à 40 000 Ar selon le jour et l'arrondissement ; montant susceptible de varier"
]);

echo "Informations générales de la démarche 3 corrigées avec succès.";

// Correction de la description de la démarche 4

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description
    WHERE id = 4
");

$stmt->execute([
    "description" => "Déclarer ses impôts selon son régime fiscal et le type d'impôt concerné."
]);

echo "Description de la démarche 4 corrigée avec succès.";

// =====================================================
// VERSION FINALE - DEMARCHE 4 : DECLARATION FISCALE
// =====================================================

$stmt = $pdo->prepare("
    UPDATE demarches
    SET nom = :nom,
        description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = 4
");

$stmt->execute([
    "nom" => "Déclaration fiscale",
    "description" => "Démarche permettant au contribuable d'effectuer sa déclaration fiscale selon son régime fiscal et la nature de ses obligations.",
    "service" => "Direction Générale des Impôts",
    "lieu" => "Centre fiscal compétent ou plateforme e-Hetra lorsque la télédéclaration est disponible",
    "delai" => "Dépend de la nature de l'impôt, du régime fiscal et de la période de déclaration",
    "frais" => "La déclaration ne correspond pas à un frais administratif unique. Le montant à payer dépend de l'impôt et de la situation fiscale du contribuable.",
    "etapes" => "1. Identifier l'impôt et le régime fiscal concernés; 2. Préparer les informations et justificatifs nécessaires; 3. Effectuer la déclaration auprès du centre fiscal compétent ou via e-Hetra lorsque le service est disponible; 4. Vérifier les informations déclarées; 5. Valider la déclaration et conserver le récépissé; 6. Effectuer le paiement de l'impôt lorsqu'un montant est dû."
]);

// Suppression des anciens documents de la démarche 4
$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = 4
");

$stmt->execute();

// Ajout des documents finaux
$documents4 = [
    [
        "nom" => "Numéro d'Identification Fiscale (NIF)",
        "description" => "Identifiant fiscal du contribuable.",
        "obligatoire" => 1
    ],
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le contribuable ou le déclarant.",
        "obligatoire" => 1
    ],
    [
        "nom" => "Formulaire ou déclaration fiscale",
        "description" => "Document correspondant à l'impôt concerné, selon le cas.",
        "obligatoire" => 1
    ],
    [
        "nom" => "Justificatifs fiscaux",
        "description" => "Documents nécessaires selon l'impôt, le régime fiscal et la situation du contribuable.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents ou annexes requis",
        "description" => "Documents complémentaires lorsque la déclaration concernée les exige.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES
    (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents4 as $document) {
    $stmt->execute([
        "demarche_id" => 4,
        "nom" => $document["nom"],
        "description" => $document["description"],
        "obligatoire" => $document["obligatoire"]
    ]);
}

echo "Version finale de la démarche 4 enregistrée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 5 : PAIEMENT DES IMPOTS
// =====================================================

$id = 5;

$description = "Démarche permettant au contribuable de régler les impôts et taxes dont il est redevable auprès du service fiscal compétent ou, lorsque le service est disponible, par voie électronique.";

$service = "Direction Générale des Impôts";

$lieu = "Centre fiscal chargé de la gestion du dossier fiscal ou plateforme e-Hetra lorsque le paiement en ligne est disponible";

$delai = "Selon l'échéance applicable à l'impôt ou à la taxe concerné(e)";

$frais = "Le montant à payer dépend de l'impôt ou de la taxe concerné(e) et de la situation fiscale du contribuable.";

$etapes = "1. Identifier l'impôt ou la taxe à payer.
2. Vérifier le montant dû et l'échéance applicable.
3. Préparer les informations ou références fiscales nécessaires au paiement.
4. Effectuer le paiement auprès du service fiscal compétent ou, lorsque le service est disponible, par voie électronique.
5. Vérifier que le paiement a bien été enregistré.
6. Conserver le justificatif ou le récépissé du paiement.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);


// =====================================================
// DOCUMENTS DE LA DEMARCHE 5
// =====================================================

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "NIF / numéro d'identification fiscale",
        "description" => "Information permettant d'identifier le contribuable auprès de l'administration fiscale, selon le service utilisé.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Avis, déclaration ou document fiscal",
        "description" => "Document ou information permettant d'identifier l'impôt ou la taxe et le montant à régler, selon la situation.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Référence fiscale ou référence de paiement",
        "description" => "Référence permettant d'identifier l'opération ou la dette fiscale lorsque celle-ci est fournie.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Justificatif ou récépissé de paiement",
        "description" => "Document à conserver comme preuve du paiement effectué.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}


// =====================================================
// MOTS-CLES DE LA DEMARCHE 5
// =====================================================

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "paiement des impots",
    "payer les impots",
    "payer mes impots",
    "paiement impot",
    "payer un impot",
    "payer mes taxes",
    "paiement des taxes",
    "regler mes impots",
    "regler les impots",
    "acquitter mes impots",
    "impot a payer",
    "taxe a payer",
    "paiement fiscal",
    "comment payer mes impots",
    "je dois payer mes impots",
    "je veux payer mes impots",
    "payer une taxe",
    "regler une taxe",
    "faire un paiement fiscal",
    "payer ma taxe"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 5 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 6 : ATTESTATION FISCALE
// =====================================================

$id = 6;

$description = "Démarche permettant au contribuable d'obtenir une attestation justifiant la régularité de sa situation fiscale auprès du Centre fiscal gestionnaire de son dossier.";

$service = "Direction Générale des Impôts";

$lieu = "Centre fiscal gestionnaire du dossier du contribuable ou service en ligne de la DGI lorsque le document est disponible en ligne";

$delai = "Selon le traitement de la demande et la situation fiscale du contribuable";

$frais = "Selon la nature de l'attestation et les dispositions fiscales applicables.";

$etapes = "1. Vérifier la situation fiscale du contribuable.
2. Préparer les informations fiscales nécessaires à la demande.
3. S'adresser au Centre fiscal gestionnaire du dossier ou utiliser le service en ligne lorsqu'il est disponible.
4. Effectuer la demande d'attestation fiscale.
5. Attendre le traitement de la demande par l'administration fiscale.
6. Récupérer l'attestation et vérifier les informations qu'elle contient.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);


// =====================================================
// DOCUMENTS DE LA DEMARCHE 6
// =====================================================

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "NIF / numéro d'identification fiscale",
        "description" => "Information permettant d'identifier le contribuable auprès de l'administration fiscale.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Informations ou documents fiscaux nécessaires",
        "description" => "Informations ou documents permettant de traiter la demande selon la situation fiscale du contribuable.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}


// =====================================================
// MOTS-CLES DE LA DEMARCHE 6
// =====================================================

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "attestation fiscale",
    "attestation des impots",
    "attestation impot",
    "attestation de regularite fiscale",
    "certificat de regularite fiscale",
    "demander une attestation fiscale",
    "demande d attestation fiscale",
    "obtenir une attestation fiscale",
    "avoir une attestation fiscale",
    "certificat fiscal",
    "document fiscal",
    "justificatif fiscal",
    "preuve fiscale",
    "demander mon attestation fiscale",
    "obtenir mon attestation fiscale",
    "besoin d une attestation fiscale",
    "justificatif des impots",
    "preuve des impots"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 6 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 7 : TITRE FONCIER
// =====================================================

$id = 7;

$description = "Démarche permettant au demandeur d'obtenir des informations ou des documents relatifs à un titre foncier et à une propriété immatriculée.";

$service = "Service foncier compétent";

$lieu = "Service foncier territorialement compétent pour la propriété concernée";

$delai = "Selon la nature de la demande et le traitement du dossier par le service foncier.";

$frais = "Selon la nature de la demande et les tarifs ou droits applicables.";

$etapes = "1. Identifier la propriété et, lorsque celui-ci est connu, le numéro du titre foncier concerné.
2. Préparer les informations et documents nécessaires à la demande.
3. S'adresser au service foncier territorialement compétent.
4. Fournir les informations relatives à la propriété et les documents demandés.
5. Effectuer la demande de consultation, d'information ou de document relatif au titre foncier.
6. Régler les droits ou frais applicables lorsqu'ils sont exigés.
7. Récupérer les informations ou documents délivrés par le service.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Référence ou numéro du titre foncier",
        "description" => "Information permettant d'identifier le titre foncier concerné lorsqu'elle est connue.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Informations relatives à la propriété",
        "description" => "Informations permettant d'identifier ou de localiser la propriété concernée.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents relatifs à la propriété",
        "description" => "Documents permettant de justifier ou de préciser la situation de la propriété selon la nature de la demande.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "titre foncier",
    "titre de propriete",
    "document foncier",
    "demande de titre foncier",
    "obtenir un titre foncier",
    "demander un titre foncier",
    "consulter un titre foncier",
    "information sur un titre foncier",
    "informations sur un titre foncier",
    "reference du titre foncier",
    "numero du titre foncier",
    "propriete fonciere",
    "dossier foncier",
    "faire un titre foncier",
    "obtenir mon titre foncier",
    "demander mon titre foncier",
    "avoir un titre de propriete",
    "obtenir un titre de propriete",
    "demander un titre de propriete",
    "document de propriete fonciere",
    "information fonciere",
    "informations foncieres"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 7 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 8 : PLAN CADASTRAL
// =====================================================

$id = 8;

$description = "Démarche permettant au demandeur d'obtenir ou de consulter des informations relatives au plan cadastral et à l'identification d'une parcelle.";

$service = "Service cadastral compétent";

$lieu = "Service cadastral territorialement compétent pour la parcelle concernée";

$delai = "Selon la nature de la demande et le traitement du dossier par le service cadastral.";

$frais = "Selon la nature de la demande et les tarifs ou droits applicables.";

$etapes = "1. Identifier la parcelle concernée et réunir les informations disponibles sur celle-ci.
2. Préparer les informations et documents nécessaires à la demande.
3. S'adresser au service cadastral territorialement compétent.
4. Fournir les informations permettant d'identifier ou de localiser la parcelle.
5. Effectuer la demande de consultation ou d'obtention du plan cadastral.
6. Régler les droits ou frais applicables lorsqu'ils sont exigés.
7. Récupérer le plan cadastral ou les informations délivrées par le service.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Référence ou numéro de la parcelle",
        "description" => "Information permettant d'identifier la parcelle concernée lorsqu'elle est connue.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Informations relatives à la propriété",
        "description" => "Informations permettant d'identifier ou de localiser la propriété ou la parcelle concernée.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents relatifs à la propriété",
        "description" => "Documents permettant de préciser la situation de la propriété selon la nature de la demande.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "plan cadastral",
    "plan du cadastre",
    "document cadastral",
    "plan de la parcelle",
    "plan parcellaire",
    "demander un plan cadastral",
    "obtenir un plan cadastral",
    "consulter un plan cadastral",
    "information cadastrale",
    "informations cadastrales",
    "parcelle cadastrale",
    "faire un plan cadastral",
    "obtenir le plan de ma parcelle",
    "demander le plan de ma parcelle",
    "plan de ma parcelle",
    "plan de terrain",
    "cadastre de ma parcelle",
    "reference cadastrale",
    "numero de parcelle",
    "information sur une parcelle",
    "informations sur une parcelle"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 8 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 9 : CERTIFICAT DE PROPRIETE
// =====================================================

$id = 9;

$description = "Démarche permettant au demandeur d'obtenir un document attestant les informations relatives à une propriété et à sa situation foncière.";

$service = "Service foncier compétent";

$lieu = "Service foncier territorialement compétent pour la propriété concernée";

$delai = "Selon la nature de la demande et le traitement du dossier par le service foncier.";

$frais = "Selon la nature de la demande et les tarifs ou droits applicables.";

$etapes = "1. Identifier la propriété concernée et réunir les informations disponibles.
2. Préparer les informations et documents nécessaires à la demande.
3. S'adresser au service foncier territorialement compétent.
4. Fournir les informations permettant d'identifier la propriété.
5. Effectuer la demande du certificat ou document relatif à la propriété.
6. Régler les droits ou frais applicables lorsqu'ils sont exigés.
7. Récupérer le certificat ou document délivré par le service.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Référence ou numéro du titre foncier",
        "description" => "Information permettant d'identifier la propriété ou le titre foncier concerné lorsqu'elle est connue.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Informations relatives à la propriété",
        "description" => "Informations permettant d'identifier ou de localiser la propriété concernée.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents relatifs à la propriété",
        "description" => "Documents permettant de préciser ou de justifier la situation de la propriété selon la nature de la demande.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "certificat de propriete",
    "document de propriete",
    "demande de certificat de propriete",
    "demander un certificat de propriete",
    "obtenir un certificat de propriete",
    "consulter un certificat de propriete",
    "preuve de propriete",
    "justificatif de propriete",
    "document prouvant la propriete",
    "faire un certificat de propriete",
    "obtenir mon certificat de propriete",
    "demander mon certificat de propriete",
    "avoir un certificat de propriete",
    "document prouvant que je suis proprietaire",
    "preuve que je suis proprietaire",
    "papiers de propriete",
    "papier de propriete",
    "documents de propriete",
    "attestation de propriete",
    "certificat foncier",
    "prouver que je suis proprietaire",
    "prouver ma propriete",
    "prouver la propriete",
    "justifier que je suis proprietaire",
    "justifier ma propriete",
    "justifier la propriete",
    "document pour prouver ma propriete",
    "document pour justifier ma propriete",
    "prouver que je suis le proprietaire",
    "justifier que je suis le proprietaire"
];



$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 9 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 10 : CREATION D'ENTREPRISE
// =====================================================

$id = 10;

$description = "Démarche permettant au demandeur d'accomplir les formalités nécessaires à la création et à l'immatriculation d'une entreprise.";

$service = "Service compétent pour la création et l'immatriculation des entreprises";

$lieu = "Centre ou service compétent pour les formalités de création d'entreprise";

$delai = "Selon la forme de l'entreprise, la nature du dossier et le traitement des formalités par les services compétents.";

$frais = "Selon la forme de l'entreprise, la nature des formalités et les tarifs ou droits applicables.";

$etapes = "1. Définir l'activité et choisir la forme juridique de l'entreprise.
2. Préparer les informations et documents nécessaires à la création.
3. Remplir les formulaires et documents requis.
4. Déposer le dossier auprès du service compétent.
5. Effectuer les formalités d'immatriculation et les autres formalités applicables.
6. Régler les droits ou frais applicables lorsqu'ils sont exigés.
7. Récupérer les documents attestant la création et l'immatriculation de l'entreprise.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Justificatif du siège ou du domicile",
        "description" => "Document permettant de justifier l'adresse ou le siège de l'entreprise selon la situation.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Formulaire de création",
        "description" => "Formulaire requis pour effectuer les formalités de création selon la forme de l'entreprise.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents relatifs à l'entreprise",
        "description" => "Documents nécessaires selon la forme juridique, l'activité et la situation de l'entreprise.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {
    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "creation d entreprise",
    "creer une entreprise",
    "creer mon entreprise",
    "creation entreprise",
    "creation de societe",
    "creer une societe",
    "ouvrir une entreprise",
    "ouvrir une societe",
    "demande de creation d entreprise",
    "demander la creation d une entreprise",
    "formalites de creation",
    "formalites pour creer une entreprise",
    "lancer une entreprise",
    "demarrer une entreprise",
    "lancer ma societe",
    "lancer une societe",
    "demarrer ma societe",
    "demarrer une societe",
    "ouvrir ma societe",
    "je veux creer une entreprise",
    "je veux creer une societe",
    "je souhaite creer une entreprise",
    "je souhaite creer une societe",
    "comment creer une entreprise",
    "comment creer une societe",
    "comment ouvrir une entreprise",
    "comment ouvrir une societe",
    "creer mon activite",
    "demarrer mon entreprise",
    "commencer une entreprise",
    "creer une activite",
    "creation d activite",
    "ouvrir mon entreprise",
    "lancer mon entreprise",
    "creer ma societe",
    "creation societe",
    "demarrer mon activite",
"je souhaite demarrer mon activite",
    "creer entreprise"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {
    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 10 effectuée avec succès.";

// =====================================================
// CORRECTION FINALE - DEMARCHE 11 : IMMATRICULATION ENTREPRISE
// =====================================================

$id = 11;

$description = "Démarche permettant au demandeur d'effectuer les formalités nécessaires à l'immatriculation d'une entreprise.";

$service = "Service compétent pour l'immatriculation des entreprises";

$lieu = "Centre ou service compétent pour les formalités d'immatriculation de l'entreprise";

$delai = "Selon la forme de l'entreprise, la nature du dossier et le traitement des formalités par les services compétents.";

$frais = "Selon la forme de l'entreprise, la nature des formalités et les tarifs ou droits applicables.";

$etapes = "1. Vérifier les informations relatives à l'entreprise et à son activité.
2. Préparer les informations et documents nécessaires à l'immatriculation.
3. Remplir le formulaire ou les documents requis.
4. Déposer le dossier auprès du service compétent.
5. Fournir les informations ou pièces complémentaires demandées, si nécessaire.
6. Régler les droits ou frais applicables lorsqu'ils sont exigés.
7. Récupérer les documents ou justificatifs relatifs à l'immatriculation de l'entreprise.";

$stmt = $pdo->prepare("
    UPDATE demarches
    SET description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = :id
");

$stmt->execute([
    ':description' => $description,
    ':service' => $service,
    ':lieu' => $lieu,
    ':delai' => $delai,
    ':frais' => $frais,
    ':etapes' => $etapes,
    ':id' => $id
]);

$stmt = $pdo->prepare("
    DELETE FROM documents_requis
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$documents = [
    [
        "nom" => "Pièce d'identité",
        "description" => "Document permettant d'identifier le demandeur.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Documents relatifs à l'entreprise",
        "description" => "Documents nécessaires selon la forme juridique, l'activité et la situation de l'entreprise.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Formulaire d'immatriculation",
        "description" => "Formulaire requis pour effectuer les formalités d'immatriculation.",
        "obligatoire" => 0
    ],
    [
        "nom" => "Justificatif du siège ou du domicile",
        "description" => "Document permettant de justifier l'adresse ou le siège de l'entreprise selon la situation.",
        "obligatoire" => 0
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO documents_requis
    (demarche_id, nom_document, description, obligatoire)
    VALUES (:demarche_id, :nom, :description, :obligatoire)
");

foreach ($documents as $document) {
    $stmt->execute([
        ':demarche_id' => $id,
        ':nom' => $document["nom"],
        ':description' => $document["description"],
        ':obligatoire' => $document["obligatoire"]
    ]);
}

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => $id
]);

$mots_cles = [
    "immatriculation entreprise",
    "immatriculer une entreprise",
    "immatriculer mon entreprise",
    "immatriculation d entreprise",
    "immatriculation de l entreprise",
    "demande d immatriculation entreprise",
    "demander une immatriculation entreprise",
    "obtenir une immatriculation entreprise",
    "immatriculation societe",
    "immatriculer une societe",
    "enregistrer mon entreprise",
    "enregistrement entreprise",
    "numero d immatriculation entreprise",
    "formalites d immatriculation",
    "faire immatriculer mon entreprise",
    "faire immatriculer une entreprise",
    "je veux immatriculer mon entreprise",
    "je souhaite immatriculer mon entreprise",
    "comment immatriculer une entreprise",
    "enregistrer une entreprise",
    "enregistrer une societe",
    "numero d immatriculation",
    "immatriculer mon activite",
    "immatriculation de mon entreprise",
    "demander l immatriculation de mon entreprise",
    "obtenir le numero d immatriculation",
    "obtenir un numero d immatriculation"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {
    $stmt->execute([
        ':demarche_id' => $id,
        ':mot' => $mot
    ]);
}

echo "Correction de la démarche 11 effectuée avec succès.";


// =====================================================
// DEMARCHE 12 : DOCUMENTS D'ENTREPRISE
// =====================================================


$pdo->prepare("
    UPDATE demarches
    SET
        nom = :nom,
        description = :description,
        service = :service,
        lieu = :lieu,
        delai = :delai,
        frais = :frais,
        etapes = :etapes
    WHERE id = 12
")->execute([
    ':nom' => "Documents d'entreprise",

    ':description' =>
        "\u{0044}\u{00E9}marche permettant \u{00E0} une entreprise d'obtenir ou de consulter les documents administratifs relatifs \u{00E0} son activit\u{00E9} et \u{00E0} son immatriculation.",

    ':service' =>
        "Service comp\u{00E9}tent pour les formalit\u{00E9}s administratives des entreprises",

    ':lieu' =>
        "Service ou guichet comp\u{00E9}tent selon le document demand\u{00E9}",

    ':delai' =>
        "Selon la nature du document demand\u{00E9} et le traitement du dossier par le service comp\u{00E9}tent.",

    ':frais' =>
        "Selon la nature du document demand\u{00E9} et les tarifs ou droits applicables.",

    ':etapes' =>
        "1. Identifier le document administratif recherch\u{00E9}.\n" .
        "2. V\u{00E9}rifier les conditions et informations n\u{00E9}cessaires \u{00E0} la demande.\n" .
        "3. Pr\u{00E9}parer les pi\u{00E8}ces n\u{00E9}cessaires selon le document demand\u{00E9}.\n" .
        "4. S'adresser au service comp\u{00E9}tent.\n" .
        "5. D\u{00E9}poser la demande et fournir les informations ou pi\u{00E8}ces demand\u{00E9}es.\n" .
        "6. R\u{00E9}gler les frais ou droits applicables lorsqu'ils sont exig\u{00E9}s.\n" .
        "7. R\u{00E9}cup\u{00E9}rer le document d\u{00E9}livr\u{00E9} par le service comp\u{00E9}tent."
]);

echo "Démarche 12 corrigée avec succès.<br>";

// =====================================================
// MOTS-CLES DE LA DEMARCHE 12
// =====================================================

$stmt = $pdo->prepare("
    DELETE FROM mots_cles
    WHERE demarche_id = :id
");

$stmt->execute([
    ':id' => 12
]);

$mots_cles = [
    "documents d entreprise",
    "documents entreprise",
    "document d entreprise",
    "document entreprise",
    "document administratif entreprise",
    "documents administratifs entreprise",
    "document administratif de l entreprise",
    "documents administratifs de l entreprise",
    "document de mon entreprise",
    "documents de mon entreprise",
    "document pour mon entreprise",
    "documents pour mon entreprise",
    "obtenir un document entreprise",
    "obtenir des documents entreprise",
    "obtenir un document pour mon entreprise",
    "obtenir des documents pour mon entreprise",
    "demander un document entreprise",
    "demander des documents entreprise",
    "demander un document pour mon entreprise",
    "demander des documents pour mon entreprise",
    "chercher un document entreprise",
    "chercher des documents entreprise",
    "je cherche un document pour mon entreprise",
    "je cherche les documents de mon entreprise",
    "je veux un document pour mon entreprise",
    "je veux les documents de mon entreprise",
    "besoin d un document entreprise",
    "besoin de documents entreprise",
    "document administratif d entreprise",
    "documents administratifs d entreprise",
    "justificatif de l entreprise",
    "justificatifs de l entreprise",
    "papier de l entreprise",
    "papiers de l entreprise"
];

$stmt = $pdo->prepare("
    INSERT INTO mots_cles (demarche_id, mot)
    VALUES (:demarche_id, :mot)
");

foreach ($mots_cles as $mot) {

    $stmt->execute([
        ':demarche_id' => 12,
        ':mot' => $mot
    ]);
}

echo "Mots-clés de la démarche 12 corrigés avec succès.";

