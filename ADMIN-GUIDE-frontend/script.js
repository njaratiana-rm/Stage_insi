/* =========================================================
   CONFIGURATION
========================================================= */

const API_BASE_URL = "http://localhost/stage/backend";


/* =========================================================
   ELEMENTS
========================================================= */

function getPage() {
    return document.getElementById("page");
}

function getToast() {
    return document.getElementById("toast");
}

function getNotifBadge() {
    return document.getElementById("notifBadge");
}

function escapeHTML(value) {

    if (value === null || value === undefined) {
        return "";
    }

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


/* =========================================================
   SERVICES
========================================================= */

const services = [
    {
        id: "etat-civil",
        name: "État civil",
        icon: "fa-solid fa-address-card",
        desc: "Actes de naissance, mariage, décès et autres démarches."
    },

    {
        id: "fiscalite",
        name: "Fiscalité",
        icon: "fa-solid fa-coins",
        desc: "Déclarations, impôts, taxes et paiements."
    },

    {
        id: "foncier",
        name: "Foncier",
        icon: "fa-solid fa-house",
        desc: "Titres fonciers, propriété et immatriculation."
    },

    {
        id: "entreprise",
        name: "Entreprise",
        icon: "fa-solid fa-building",
        desc: "Création, immatriculation et accompagnement."
    },

    {
        id: "social",
        name: "Social",
        icon: "fa-solid fa-users",
        desc: "Aides, prestations et protection sociale."
    },

    {
        id: "transport",
        name: "Transport",
        icon: "fa-solid fa-car",
        desc: "Permis, immatriculation et cartes de transport."
    }
];


/* =========================================================
   PROCEDURES
========================================================= */

const procedures = {

    "etat-civil": [
        {
            id: 1,
            icon: "fa-solid fa-file-lines",
            title: "Acte de naissance",
            description: "Obtenir une copie ou un extrait d'acte de naissance."
        },

        {
            id: 3,
            icon: "fa-solid fa-ring",
            title: "Acte de mariage",
            description: "Demander un acte ou un certificat de mariage."
        },

        {
            id: 2,
            icon: "fa-solid fa-file-circle-xmark",
            title: "Acte de décès",
            description: "Obtenir un acte ou un certificat de décès."
        }
    ],

    "fiscalite": [
        {
            id: 4,
            icon: "fa-solid fa-file-invoice-dollar",
            title: "Déclaration fiscale",
            description: "Effectuer ou consulter une déclaration fiscale."
        },

        {
            id: 5,
            icon: "fa-solid fa-money-bill-wave",
            title: "Paiement des impôts",
            description: "Consulter les informations concernant les impôts et paiements."
        },

        {
            id: 6,
            icon: "fa-solid fa-receipt",
            title: "Attestation fiscale",
            description: "Demander ou consulter une attestation fiscale."
        }
    ],

    "foncier": [
        {
            id: 7,
            icon: "fa-solid fa-house",
            title: "Titre foncier",
            description: "Consulter les démarches relatives à un titre foncier."
        },

        {
            id: 8,
            icon: "fa-solid fa-map",
            title: "Plan cadastral",
            description: "Demander des informations concernant un plan cadastral."
        },

        {
            id: 9,
            icon: "fa-solid fa-file-contract",
            title: "Certificat de propriété",
            description: "Effectuer une demande de certificat de propriété."
        }
    ],

    "entreprise": [
        {
            id: 10,
            icon: "fa-solid fa-building",
            title: "Création d'entreprise",
            description: "Consulter les étapes nécessaires pour créer une entreprise."
        },

        {
            id: 11,
            icon: "fa-solid fa-file-signature",
            title: "Immatriculation",
            description: "Effectuer les démarches d'immatriculation de votre entreprise."
        },

        {
            id: 12,
            icon: "fa-solid fa-file-circle-check",
            title: "Documents d'entreprise",
            description: "Consulter ou demander les documents administratifs de l'entreprise."
        }
    ],

    "social": [
        {
            id: 13,
            icon: "fa-solid fa-hand-holding-heart",
            title: "Demande d'aide sociale",
            description: "Consulter les conditions et effectuer une demande d'aide sociale."
        },

        {
            id: 14,
            icon: "fa-solid fa-users",
            title: "Prestations sociales",
            description: "Consulter les différentes prestations sociales disponibles."
        },

        {
            id: 15,
            icon: "fa-solid fa-shield-heart",
            title: "Protection sociale",
            description: "Obtenir des informations sur les dispositifs de protection sociale."
        }
    ],

    "transport": [
        {
            id: 16,
            icon: "fa-solid fa-id-card",
            title: "Permis de conduire",
            description: "Consulter les démarches liées au permis de conduire."
        },

        {
            id: 17,
            icon: "fa-solid fa-car",
            title: "Immatriculation",
            description: "Effectuer une démarche d'immatriculation d'un véhicule."
        },

        {
            id: 18,
            icon: "fa-solid fa-file-lines",
            title: "Carte grise",
            description: "Consulter les démarches relatives à la carte grise."
        }
    ]
};


/* =========================================================
   CORRESPONDANCE DEMARCHE → SERVICE
========================================================= */

const serviceParDemarche = {

    1: "etat-civil",
    2: "etat-civil",
    3: "etat-civil",

    4: "fiscalite",
    5: "fiscalite",
    6: "fiscalite",

    7: "foncier",
    8: "foncier",
    9: "foncier",

    10: "entreprise",
    11: "entreprise",
    12: "entreprise",

    13: "social",
    14: "social",
    15: "social",

    16: "transport",
    17: "transport",
    18: "transport"
};


/* =========================================================
   NAVIGATION PROCEDURE
========================================================= */

let procedureOrigin = "services";
let lastAssistantMessage = "";

function openProcedureFromService(serviceId, procedureId) {

    procedureOrigin = "services";

    procedure(serviceId, procedureId);
}


/* =========================================================
   TOAST
========================================================= */

function showToast(message) {

    const toast = getToast();

    if (!toast) {
        return;
    }

    toast.textContent = message;

    toast.classList.add("show");

    setTimeout(function () {
        toast.classList.remove("show");
    }, 3000);
}


/* =========================================================
   AUTHENTIFICATION - SESSION
========================================================= */

let sessionConnectee = false;
let utilisateurConnecte = null;


function protect() {

    if (!sessionConnectee) {

        navigate("login");

        return false;
    }

    return true;
}


async function verifierSession() {

    try {

        const response =
            await fetch(
                API_BASE_URL + "/verifier_session.php",
                {
                    credentials: "same-origin"
                }
            );


        const data =
            await response.json();


        console.log(
            "Vérification session :",
            data
        );


        if (
            data.success &&
            data.connecte === true
        ) {

            sessionConnectee = true;

            utilisateurConnecte =
                data.utilisateur || null;

        } else {

            sessionConnectee = false;

            utilisateurConnecte = null;
        }


    } catch (error) {

        console.error(
            "Erreur vérification session :",
            error
        );

        sessionConnectee = false;

        utilisateurConnecte = null;
    }


    updateHeader();

    return sessionConnectee;
}


function updateHeader() {

    const logged =
        sessionConnectee;


    const navbar =
        document.querySelector(".navbar");


    const footer =
        document.querySelector("footer");


    if (navbar) {

        navbar.style.display =
            logged ? "flex" : "none";
    }


    if (footer) {

        footer.style.display =
            logged ? "flex" : "none";
    }
        updateAdminNav();
}

function updateAdminNav() {

    const link =
        document.getElementById("adminRequestsLink");

    if (!link) {
        return;
    }

    if (
        sessionConnectee &&
        utilisateurConnecte &&
        utilisateurConnecte.role === "admin"
    ) {

        link.style.display = "";

    } else {

        link.style.display = "none";
    }
}


async function logout() {

    try {

        const response =
            await fetch(
                API_BASE_URL + "/deconnexion.php",
                {
                    method: "POST",
                    credentials: "same-origin"
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse déconnexion :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Erreur lors de la déconnexion."
            );

            return;
        }


        sessionConnectee = false;

        utilisateurConnecte = null;


        showToast(
            "Déconnexion réussie ✓"
        );


        setTimeout(function () {

            navigate("login");

        }, 500);


    } catch (error) {

        console.error(
            "Erreur déconnexion :",
            error
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}

function protectAdmin() {

    if (!sessionConnectee) {

        navigate("login");

        return false;
    }

    if (
        !utilisateurConnecte ||
        utilisateurConnecte.role !== "admin"
    ) {

        showToast(
            "Accès administrateur refusé."
        );

        navigate("home");

        return false;
    }

    return true;
}


/* =========================================================
   AUTHENTIFICATION
========================================================= */

function renderLogin() {

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="card auth-card">

                <div class="eyebrow">
                    CONNEXION
                </div>

                <h1>
                    Se connecter
                </h1>

                <p class="muted">
                    Connectez-vous à votre compte ADMIN'GUIDE.
                </p>


                <form onsubmit="login(event)" autocomplete="off">

                    <div class="field">

                        <label for="loginEmail">
                            Adresse email
                        </label>

                        <input
                            id="loginEmail"
                            type="email"
                            placeholder="exemple@email.com"
                            required
                            autocomplete="off"
                        >

                    </div>


                    <div class="field">

                        <label for="loginPassword">
                            Mot de passe
                        </label>

                        <input
                            id="loginPassword"
                            type="password"
                            placeholder="Votre mot de passe"
                            required
                            autocomplete="new-password"
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn"
                    >
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Se connecter
                    </button>

                </form>


                <hr>


                <p class="muted">
                    Vous n'avez pas encore de compte ?
                </p>


                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="navigate('register')"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    Créer un compte
                </button>

            </div>

        </div>
    `;


    const loginEmail =
        document.getElementById("loginEmail");

    const loginPassword =
        document.getElementById("loginPassword");


    if (loginEmail) {
        loginEmail.value = "";
    }

    if (loginPassword) {
        loginPassword.value = "";
    }
}


function renderRegister() {

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="card auth-card">

                <div class="eyebrow">
                    CRÉATION DE COMPTE
                </div>

                <h1>
                    Créer votre compte
                </h1>

                <p class="muted">
                    Inscrivez-vous pour utiliser les services
                    d'ADMIN'GUIDE.
                </p>


                <form onsubmit="register(event)" autocomplete="off">

                    <div class="field">

                        <label for="regNom">
                            Nom
                        </label>

                        <input
                            id="regNom"
                            type="text"
                            placeholder="Votre nom"
                            required
                        >

                    </div>


                    <div class="field">

                        <label for="regPrenom">
                            Prénom
                        </label>

                        <input
                            id="regPrenom"
                            type="text"
                            placeholder="Votre prénom"
                            required
                        >

                    </div>


                    <div class="field">

                        <label for="regEmail">
                            Adresse email
                        </label>

                        <input
                            id="regEmail"
                            type="email"
                            placeholder="exemple@email.com"
                            required
                        >

                    </div>


                    <div class="field">

                        <label for="regPhone">
                            Téléphone
                        </label>

                        <input
                            id="regPhone"
                            type="tel"
                            placeholder="Votre numéro de téléphone"
                            required
                        >

                    </div>


                    <div class="field">

                        <label for="regPassword">
                            Mot de passe
                        </label>

                        <input
                            id="regPassword"
                            type="password"
                            placeholder="Votre mot de passe"
                            required
                        >

                    </div>


                    <div class="field">

                        <label for="regConfirm">
                            Confirmer le mot de passe
                        </label>

                        <input
                            id="regConfirm"
                            type="password"
                            placeholder="Confirmez votre mot de passe"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="btn"
                    >
                        <i class="fa-solid fa-user-plus"></i>
                        Créer mon compte
                    </button>

                </form>


                <hr>


                <p class="muted">
                    Vous avez déjà un compte ?
                </p>


                <button
                    type="button"
                    class="btn btn-outline"
                    onclick="navigate('login')"
                >
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Se connecter
                </button>

            </div>

        </div>
    `;


    const regNom =
        document.getElementById("regNom");

    const regPrenom =
        document.getElementById("regPrenom");

    const regEmail =
        document.getElementById("regEmail");

    const regPhone =
        document.getElementById("regPhone");

    const regPassword =
        document.getElementById("regPassword");

    const regConfirm =
        document.getElementById("regConfirm");


    if (regNom) regNom.value = "";
    if (regPrenom) regPrenom.value = "";
    if (regEmail) regEmail.value = "";
    if (regPhone) regPhone.value = "";
    if (regPassword) regPassword.value = "";
    if (regConfirm) regConfirm.value = "";
}


/* =========================================================
   INSCRIPTION
========================================================= */

async function register(event) {

    event.preventDefault();


    const nom =
        document.getElementById("regNom").value.trim();

    const prenom =
        document.getElementById("regPrenom").value.trim();

    const email =
        document.getElementById("regEmail").value.trim().toLowerCase();

    const telephone =
        document.getElementById("regPhone").value.trim();

    const motDePasse =
        document.getElementById("regPassword").value;

    const confirmation =
        document.getElementById("regConfirm").value;


    if (motDePasse !== confirmation) {

        showToast(
            "Les mots de passe ne correspondent pas."
        );

        return;
    }


    const donnees =
        new URLSearchParams();


    donnees.append("nom", nom);
    donnees.append("prenom", prenom);
    donnees.append("email", email);
    donnees.append("telephone", telephone);
    donnees.append("mot_de_passe", motDePasse);


    try {

        const response =
            await fetch(
                API_BASE_URL + "/inscription.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    body: donnees
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse inscription :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Erreur lors de l'inscription."
            );

            return;
        }


        showToast(
            "Inscription réussie ✓"
        );


        document.querySelector("form").reset();


        setTimeout(function () {

            navigate("login");

        }, 700);


    } catch (error) {

        console.error(
            "Erreur inscription :",
            error
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}


/* =========================================================
   CONNEXION
========================================================= */

async function login(event) {

    event.preventDefault();


    const email =
        document.getElementById("loginEmail").value.trim().toLowerCase();

    const motDePasse =
        document.getElementById("loginPassword").value;


    const donnees =
        new URLSearchParams();


    donnees.append("email", email);
    donnees.append("mot_de_passe", motDePasse);


    try {

        const response =
            await fetch(
                API_BASE_URL + "/connexion.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    body: donnees
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse connexion :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Email ou mot de passe incorrect."
            );

            return;
        }

        await verifierSession();

        showToast(
            "Connexion réussie ✓"
        );


        setTimeout(function () {

            navigate("home");

        }, 700);


    } catch (error) {

        console.error(
            "Erreur connexion :",
            error
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}


/* =========================================================
   AJOUTER UNE DEMANDE
========================================================= */

async function ajouterDemande(demarcheId) {

    if (!sessionConnectee) {

        navigate("login");

        return;
    }


    const donnees =
        new URLSearchParams();


    donnees.append(
        "demarche_id",
        demarcheId
    );


    try {

        const response =
            await fetch(
                API_BASE_URL + "/ajouter_demande.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    credentials: "same-origin",
                    body: donnees
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse ajout demande :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Impossible d'enregistrer la demande."
            );

            return;
        }


        showToast(
            "Demande enregistrée ✓"
        );


        navigate("requests");


    } catch (error) {

        console.error(
            "Erreur ajout demande :",
            error
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}


/* =========================================================
   NAVIGATION
========================================================= */

function navigate(target) {

    window.location.hash = target;

    renderPage(target);
}


/* =========================================================
   PAGE ACCUEIL
========================================================= */

function homePage() {

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <section class="hero">

                <div class="hero-content">

                    <div class="eyebrow">
                        SERVICE PUBLIC NUMÉRIQUE
                    </div>

                    <h1>
                        Comment pouvons-nous vous aider ?
                    </h1>

                    <p>
                        Décrivez votre besoin et laissez notre assistant
                        vous orienter vers le bon service.
                    </p>

                    <div class="search-box">

                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Ex : Je veux créer une entreprise, payer mes impôts..."
                        >

                        <button
                            class="btn"
                            onclick="searchService()"
                        >
                            <i class="fa-solid fa-robot"></i>
                            Trouver mon service
                        </button>

                    </div>

                    <div id="searchResult"></div>

                    <button
                        class="btn btn-outline"
                        onclick="navigate('assistant')"
                    >
                        <i class="fa-regular fa-comment"></i>
                        Demander à l'assistant IA
                    </button>

                </div>

                <div class="hero-card">

                    <div class="bot-icon">
                        <i class="fa-solid fa-robot"></i>
                    </div>

                    <h3>
                        Votre assistant IA
                    </h3>

                    <p>
                        Posez votre question en langage naturel.
                    </p>

                </div>

            </section>


            <section>

                <div class="section-header">

                    <h2>
                        Services populaires
                    </h2>

                    <a
                        href="#"
                        class="link"
                        onclick="navigate('services'); return false;"
                    >
                        Voir tous les services
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>


                <div class="services-grid">

                    ${services.map(function (service) {

                        return `
                            <div
                                class="card service-card"
                                onclick="serviceDetail('${service.id}')"
                            >

                                <div class="service-icon">
                                    <i class="${service.icon}"></i>
                                </div>

                                <h3>
                                    ${service.name}
                                </h3>

                                <p class="muted">
                                    ${service.desc}
                                </p>

                                <a class="link">
                                    Consulter
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                            </div>
                        `;

                    }).join("")}

                </div>

            </section>

        </div>
    `;
}


/* =========================================================
   PAGE SERVICES
========================================================= */

function servicesPage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="section-header">

                <div>

                    <div class="eyebrow">
                        SERVICES
                    </div>

                    <h1>
                        Tous les services
                    </h1>

                    <p class="muted">
                        Choisissez le domaine correspondant à votre démarche.
                    </p>

                </div>

            </div>


            <div class="services-grid">

                ${services.map(function (service) {

                    return `
                        <div
                            class="card service-card"
                            onclick="serviceDetail('${service.id}')"
                        >

                            <div class="service-icon">
                                <i class="${service.icon}"></i>
                            </div>

                            <h3>
                                ${service.name}
                            </h3>

                            <p class="muted">
                                ${service.desc}
                            </p>

                            <a
                                class="link"
                                onclick="event.stopPropagation(); serviceDetail('${service.id}'); return false;"
                            >
                                Consulter
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                        </div>
                    `;

                }).join("")}

            </div>

        </div>
    `;
}


/* =========================================================
   DETAIL SERVICE
========================================================= */

function serviceDetail(serviceId) {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    const service = services.find(function (item) {
        return item.id === serviceId;
    });

    if (!service) {

        showToast("Service introuvable");

        navigate("services");

        return;
    }

    const serviceProcedures =
        procedures[serviceId] || [];


    page.innerHTML = `
        <div class="container">

            <p
                class="link"
                onclick="navigate('services')"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Retour aux services
            </p>


            <div class="card service-detail">

                <div class="row">

                    <div class="service-icon">
                        <i class="${service.icon}"></i>
                    </div>

                    <div>

                        <h1>
                            ${service.name}
                        </h1>

                        <p class="muted">
                            ${service.desc}
                        </p>

                    </div>

                </div>


                <hr>


                <h2>
                    <i class="fa-solid fa-list"></i>
                    Démarches disponibles
                </h2>


                <div class="list">

                    ${
                        serviceProcedures.length > 0

                        ?

                        serviceProcedures.map(function (item) {

                            return `
                                <div class="card procedure-card">

                                    <h3>
                                        <i class="${item.icon}"></i>
                                        ${item.title}
                                    </h3>

                                    <p class="muted">
                                        ${item.description}
                                    </p>

                                    <button
                                        class="btn"
                                        onclick="openProcedureFromService('${serviceId}', ${item.id})"
                                    >
                                        <i class="fa-solid fa-arrow-right"></i>
                                        Voir la procédure
                                    </button>

                                </div>
                            `;

                        }).join("")

                        :

                        `
                            <div class="card">

                                <h3>
                                    <i class="fa-solid fa-circle-info"></i>
                                    Aucune démarche disponible
                                </h3>

                                <p class="muted">
                                    Les démarches pour ce service
                                    seront bientôt disponibles.
                                </p>

                            </div>
                        `
                    }

                </div>

            </div>

        </div>
    `;
}


/* =========================================================
   PAGE PROCEDURE
========================================================= */

async function procedure(serviceId, procedureId) {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    const service = services.find(function (item) {
        return item.id === serviceId;
    });

    if (!service) {

        showToast("Service introuvable");

        return;
    }


    const serviceProcedures =
        procedures[serviceId] || [];


    const selectedProcedure =
        serviceProcedures.find(function (item) {
            return item.id === Number(procedureId);
        });


    if (!selectedProcedure) {

        showToast("Démarche introuvable");

        return;
    }


    try {

        console.log(
            "Récupération de la démarche ID :",
            selectedProcedure.id
        );


        const url =
            API_BASE_URL +
            "/index.php?id=" +
            encodeURIComponent(selectedProcedure.id);


        console.log(
            "URL procédure :",
            url
        );


        const response =
            await fetch(url);


        console.log(
            "Statut HTTP :",
            response.status
        );


        const texte =
            await response.text();


        console.log(
            "Réponse brute index.php :",
            texte
        );


        if (!response.ok) {

            throw new Error(
                "Erreur HTTP " + response.status
            );
        }


        let data;


        try {

            data =
                JSON.parse(texte);

        } catch (e) {

            console.error(
                "Réponse non JSON :",
                texte
            );

            throw new Error(
                "Le serveur n'a pas renvoyé du JSON."
            );
        }


        console.log(
            "Détails reçus :",
            data
        );


        if (!data || data.error) {

            throw new Error(
                data.error ||
                "Données de procédure invalides."
            );
        }


        const etapes =
            data.etapes
                ? data.etapes.split(";")
                : [];


        page.innerHTML = `
            <div class="container">

                <p
                    class="link"
                    onclick="
                        procedureOrigin === 'assistant'
                            ? navigate('assistant')
                            : procedureOrigin === 'home'
                                ? navigate('home')
                                : serviceDetail('${serviceId}')
                    "
                >
                    <i class="fa-solid fa-arrow-left"></i>

                    ${
                        procedureOrigin === 'assistant'
                            ? "Retour à l'assistant"
                            : procedureOrigin === 'home'
                                ? "Retour à l'accueil"
                                : "Retour à " + service.name
                    }

                </p>


                <div class="layout-two">


                    <div class="card">

                        <div class="service-icon">
                            <i class="${selectedProcedure.icon}"></i>
                        </div>


                        <div class="eyebrow">
                            ${service.name.toUpperCase()}
                        </div>


                        <h1>
                            ${escapeHTML(data.nom || selectedProcedure.title)}
                        </h1>


                        <p class="muted">
                            ${escapeHTML(data.description || "")}
                        </p>


                        <hr>


                        <h2>
                            <i class="fa-solid fa-folder-open"></i>
                            Documents nécessaires
                        </h2>


                        ${
                            data.documents &&
                            data.documents.length > 0

                            ?

                            data.documents.map(function (document) {

                                const obligatoire =
                                    Number(document.obligatoire) === 1;

                                return `
                                    <div class="check">

                                        <i class="fa-solid fa-circle-check"></i>

                                        <span>
                                            ${escapeHTML(document.nom_document)}

                                            ${
                                                obligatoire
                                                    ? `<small class="muted"> - Obligatoire</small>`
                                                    : `<small class="muted"> - Selon votre situation</small>`
                                            }
                                        </span>

                                    </div>
                                `;

                            }).join("")

                            :

                            `
                                <p class="muted">
                                    Aucun document enregistré.
                                </p>
                            `
                        }


                        <br>


                        <h2>
                            <i class="fa-solid fa-list-ol"></i>
                            Étapes
                        </h2>


                        ${
                            etapes.length > 0

                            ?

                            etapes.map(function (etape) {

                                return `
                                    <p>
                                        ${escapeHTML(etape.trim())}
                                    </p>
                                `;

                            }).join("")

                            :

                            `
                                <p class="muted">
                                    Aucune étape enregistrée.
                                </p>
                            `
                        }


                        <br>


                        <h2>
                            <i class="fa-solid fa-circle-info"></i>
                            Informations
                        </h2>


                        <p>
                            <strong>Lieu :</strong>
                            ${escapeHTML(data.lieu || "À déterminer")}
                        </p>


                        <p>
                            <strong>Délai :</strong>
                            ${escapeHTML(data.delai || "À déterminer")}
                        </p>


                        <p>
                            <strong>Frais :</strong>
                            ${escapeHTML(data.frais || "À déterminer")}
                        </p>


                        <br>
                        <button
    class="btn"
    onclick="ajouterDemande(${Number(selectedProcedure.id)})"
>
    <i class="fa-solid fa-file-circle-plus"></i>
    Faire une demande
</button>
                        

                    </div>


                    <div class="card">

                        <h3>
                            <i class="fa-solid fa-lightbulb"></i>
                            Conseil
                        </h3>

                        <p class="muted">
                            Vérifiez que tous vos documents
                            sont prêts avant de commencer
                            votre démarche.
                        </p>


                        <hr>


                        <h3>
                            <i class="fa-solid fa-circle-info"></i>
                            Service concerné
                        </h3>


                        <p>
                            <strong>
                                ${service.name}
                            </strong>
                        </p>


                        <p class="muted">
                            ${service.desc}
                        </p>

                    </div>


                </div>

            </div>
        `;


    } catch (error) {

        console.error(
            "ERREUR PROCEDURE :",
            error
        );


        if (error.message === "Démarche introuvable.") {

            showToast(
                "Cette démarche n'existe pas."
            );

        } else {

            showToast(
                "Impossible de récupérer les informations."
            );
        }
    }
}


/* =========================================================
   MES DEMANDES
========================================================= */

async function requestsPage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }


    page.innerHTML = `
        <div class="container">

            <div class="eyebrow">
                SUIVI
            </div>

            <h1>
                Mes demandes
            </h1>

            <p class="muted">
                Retrouvez ici vos demandes administratives.
            </p>

            <div id="requestsContainer">
                <p class="muted">
                    Chargement des demandes...
                </p>
            </div>

        </div>
    `;


    const container =
        document.getElementById("requestsContainer");


    try {

        const response =
            await fetch(
                API_BASE_URL + "/mes_demandes.php",
                {
                    credentials: "same-origin"
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse mes demandes :",
            data
        );


        if (!data.success) {

            container.innerHTML = `
                <div class="card">

                    <p class="muted">
                        ${escapeHTML(
                            data.message ||
                            "Impossible de récupérer vos demandes."
                        )}
                    </p>

                </div>
            `;

            return;
        }


        if (
            !Array.isArray(data.demandes) ||
            data.demandes.length === 0
        ) {

            container.innerHTML = `
                <div class="card">

                    <div class="notification">

                        <div class="round">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>

                        <div>

                            <h3>
                                Aucune demande récente
                            </h3>

                            <p class="muted">
                                Vos demandes apparaîtront ici
                                lorsque vous effectuerez une démarche.
                            </p>

                            <button
                                class="btn"
                                onclick="navigate('services')"
                            >
                                <i class="fa-solid fa-table-cells-large"></i>
                                Consulter les services
                            </button>

                        </div>

                    </div>

                </div>
            `;

            return;
        }


        container.innerHTML = `

            ${data.demandes.map(function (demande) {

                return `
                    <div class="card">

                        <div class="notification">

                            <div class="round">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>

                            <div>

                                <h3>
                                    ${escapeHTML(
                                        demande.demarche_nom
                                    )}
                                </h3>
                                <div>

    <strong>Statut :</strong>

    ${escapeHTML(demande.statut)}
    

</div>

                                <p class="muted">
                                    Date :
                                    ${escapeHTML(
                                        demande.date_demande
                                    )}
                                </p>

                                <button
                                    class="btn"
                                    onclick="viewRequest(${Number(demande.id)})"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                    Voir le détail
                                </button>

                            </div>

                        </div>

                    </div>
                `;

            }).join("")}

        `;


    } catch (error) {

        console.error(
            "Erreur récupération demandes :",
            error
        );


        container.innerHTML = `
            <div class="card">

                <p class="muted">
                    Impossible de contacter le serveur.
                </p>

            </div>
        `;
    }
}

async function adminRequestsPage() {

    if (!protectAdmin()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="eyebrow">
                ADMINISTRATION
            </div>

            <h1>
                Administration des demandes
            </h1>

            <p class="muted">
                Consultez les demandes envoyées par les utilisateurs.
            </p>

            <div id="adminRequestsResult">

                <p class="muted">
                    Chargement des demandes...
                </p>

            </div>

        </div>
    `;

    const result =
        document.getElementById("adminRequestsResult");

    try {

        const response =
            await fetch(
                API_BASE_URL + "/admin_demandes.php",
                {
                    credentials: "same-origin"
                }
            );

        const data =
            await response.json();

        console.log(
            "Demandes administrateur :",
            data
        );

        if (!data.success) {

            result.innerHTML = `
                <div class="card">

                    <p class="muted">
                        ${escapeHTML(
                            data.message ||
                            "Accès refusé."
                        )}
                    </p>

                </div>
            `;

            return;
        }

        if (
            !Array.isArray(data.demandes) ||
            data.demandes.length === 0
        ) {

            result.innerHTML = `
                <div class="card">

                    <p class="muted">
                        Aucune demande reçue.
                    </p>

                </div>
            `;

            return;
        }

        result.innerHTML =
            data.demandes.map(function (demande) {

                return `
                    <div class="card">

                        <h3>
                            ${escapeHTML(
                                demande.demarche_nom
                            )}
                        </h3>

                        <p>
                            <strong>Utilisateur :</strong>
                            ${escapeHTML(
                                demande.prenom
                            )}
                            ${escapeHTML(
                                demande.nom
                            )}
                        </p>

                        <p>
                            <strong>Email :</strong>
                            ${escapeHTML(
                                demande.email
                            )}
                        </p>

                        <p>
                            <strong>Téléphone :</strong>
                            ${escapeHTML(
                                demande.telephone
                            )}
                        </p>

                        
                    <div>

    <strong>Statut :</strong>

    <select
        onchange="modifierStatutDemande(
            ${Number(demande.id)},
            this.value
        )"
    >

        <option
            value="En attente"
            ${demande.statut === "En attente" ? "selected" : ""}
        >
            En attente
        </option>

        <option
            value="Acceptée"
            ${demande.statut === "Acceptée" ? "selected" : ""}
        >
            Acceptée
        </option>

        <option
            value="Refusée"
            ${demande.statut === "Refusée" ? "selected" : ""}
        >
            Refusée
        </option>

        <option
            value="Traitée"
            ${demande.statut === "Traitée" ? "selected" : ""}
        >
            Traitée
        </option>

    </select>

</div>
                        
                        
                        
                        

                        <p class="muted">
                            <strong>Date :</strong>
                            ${escapeHTML(
                                demande.date_demande
                            )}
                        </p>

                    </div>
                `;

            }).join("");

    } catch (error) {

        console.error(
            "Erreur demandes admin :",
            error
        );

        result.innerHTML = `
            <div class="card">

                <p class="muted">
                    Impossible de charger les demandes.
                </p>

            </div>
        `;
    }
}

async function modifierStatutDemande(id, statut) {

    try {

        const response = await fetch(
            API_BASE_URL + "/modifier_statut_demande.php",
            {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },
                body:
                    "demande_id=" +
                    encodeURIComponent(id) +
                    "&statut=" +
                    encodeURIComponent(statut)
            }
        );

        const data =
            await response.json();

        console.log(
            "Modification statut :",
            data
        );

if (!data.success) {

    showToast(
        data.message ||
        "Impossible de modifier le statut."
    );

    return;
}

showToast(
    "Statut modifié avec succès."
);

await adminRequestsPage();
 

    } catch (error) {

        console.error(
            "Erreur modification statut :",
            error
        );

        showToast(
            "Erreur lors de la modification."
        );
    }
}

/* =========================================================
   DETAIL D'UNE DEMANDE
========================================================= */

async function viewRequest(id) {

    if (!protect()) {
        return;
    }


    try {

        const response =
            await fetch(
                API_BASE_URL +
                "/detail_demande.php?id=" +
                encodeURIComponent(id),
                {
                    credentials: "same-origin"
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse détail demande :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Demande introuvable."
            );

            return;
        }


        const demande =
            data.demande;


        const page =
            getPage();


        if (!page) {
            return;
        }


        const etapes =
            demande.etapes
                ? demande.etapes.split(";")
                : [];


        page.innerHTML = `
            <div class="container">

                <p
                    class="link"
                    onclick="navigate('requests')"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    Retour à mes demandes
                </p>


                <div class="card">

                    <div class="eyebrow">
                        DEMANDE
                    </div>


                    <h1>
                        ${escapeHTML(
                            demande.demarche_nom
                        )}
                    </h1>


                    <p class="muted">
                        ${escapeHTML(
                            demande.description || ""
                        )}
                    </p>


                    <hr>


                    <h3>
                        <i class="fa-solid fa-circle-info"></i>
                        Informations
                    </h3>


                    <p>
                        <strong>Statut :</strong>
                        ${escapeHTML(
                            demande.statut
                        )}
                    </p>


                    <p>
                        <strong>Date :</strong>
                        ${escapeHTML(
                            demande.date_demande
                        )}
                    </p>


                    <p>
                        <strong>Service :</strong>
                        ${escapeHTML(
                            demande.service || "À déterminer"
                        )}
                    </p>


                    <p>
                        <strong>Lieu :</strong>
                        ${escapeHTML(
                            demande.lieu || "À déterminer"
                        )}
                    </p>


                    <p>
                        <strong>Délai :</strong>
                        ${escapeHTML(
                            demande.delai || "À déterminer"
                        )}
                    </p>


                    <p>
                        <strong>Frais :</strong>
                        ${escapeHTML(
                            demande.frais || "À déterminer"
                        )}
                    </p>


                    <hr>


                    <h3>
                        <i class="fa-solid fa-list-ol"></i>
                        Étapes
                    </h3>


                    ${
                        etapes.length > 0

                        ?

                        etapes.map(function (etape) {

                            return `
                                <p>
                                    ${escapeHTML(
                                        etape.trim()
                                    )}
                                </p>
                            `;

                        }).join("")

                        :

                        `
                            <p class="muted">
                                Aucune étape enregistrée.
                            </p>
                        `
                    }


                    ${
                        demande.statut !== "Annulée"

                        ?

                        `
                            <br>

                            <button
                                class="btn"
                                onclick="cancelRequest(${Number(demande.id)})"
                            >
                                <i class="fa-solid fa-ban"></i>
                                Annuler la demande
                            </button>
                        `

                        :

                        ""
                    }

                </div>

            </div>
        `;


    } catch (error) {

        console.error(
            "Erreur détail demande :",
            error
        );


        showToast(
            "Impossible de récupérer la demande."
        );
    }
}


/* =========================================================
   ANNULER UNE DEMANDE
========================================================= */

async function cancelRequest(id) {

    if (!protect()) {
        return;
    }


    const confirmation =
        confirm(
            "Voulez-vous vraiment annuler cette demande ?"
        );


    if (!confirmation) {
        return;
    }


    const donnees =
        new URLSearchParams();


    donnees.append(
        "demande_id",
        id
    );


    try {

        const response =
            await fetch(
                API_BASE_URL + "/annuler_demande.php",
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    credentials: "same-origin",
                    body: donnees
                }
            );


        const data =
            await response.json();


        console.log(
            "Réponse annulation :",
            data
        );


        if (!data.success) {

            showToast(
                data.message ||
                "Impossible d'annuler la demande."
            );

            return;
        }


        showToast(
            "Demande annulée ✓"
        );


        setTimeout(function () {

            navigate("requests");

        }, 500);


    } catch (error) {

        console.error(
            "Erreur annulation :",
            error
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}


/* =========================================================
   RENDEZ-VOUS
========================================================= */

function appointmentsPage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="eyebrow">
                AGENDA
            </div>

            <h1>
                Mes rendez-vous
            </h1>

            <p class="muted">
                Gérez vos rendez-vous administratifs.
            </p>


            <div class="card">

                <div class="notification">

                    <div class="round">
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <div>

                        <h3>
                            Aucun rendez-vous
                        </h3>

                        <p class="muted">
                            Vous n'avez actuellement
                            aucun rendez-vous programmé.
                        </p>

                        <button
                            class="btn"
                            onclick="showToast('Fonctionnalité de prise de rendez-vous à venir')"
                        >
                            <i class="fa-solid fa-plus"></i>
                            Nouveau rendez-vous
                        </button>

                    </div>

                </div>

            </div>

        </div>
    `;
}


/* =========================================================
   NOTIFICATIONS
========================================================= */

function notificationsPage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="eyebrow">
                NOTIFICATIONS
            </div>

            <h1>
                Notifications
            </h1>


            <div class="card">

                <div class="notification">

                    <div class="round">
                        <i class="fa-solid fa-info"></i>
                    </div>

                    <div>

                        <h3>
                            Bienvenue sur ADMIN'GUIDE
                        </h3>

                        <p class="muted">
                            Retrouvez ici les informations
                            importantes concernant vos démarches.
                        </p>

                    </div>

                </div>


                <hr>


                <div class="notification">

                    <div class="round">
                        <i class="fa-solid fa-robot"></i>
                    </div>

                    <div>

                        <h3>
                            Assistant IA disponible
                        </h3>

                        <p class="muted">
                            Vous pouvez utiliser l'assistant
                            pour trouver le service adapté
                            à votre besoin.
                        </p>

                    </div>

                </div>

            </div>

        </div>
    `;


    const notifBadge = getNotifBadge();

    if (notifBadge) {
        notifBadge.textContent = "0";
    }
}


/* =========================================================
   PROFIL
========================================================= */

function profilePage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <div class="eyebrow">
                COMPTE
            </div>

            <h1>
                Mon profil
            </h1>


            <div class="card profile-card">

                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>


                <h2>
                    Daniela
                </h2>


                <p class="muted">
                    Utilisateur ADMIN'GUIDE
                </p>


                <hr>


                <div class="check">
                    <i class="fa-solid fa-circle-check"></i>
                    Profil actif
                </div>


                <br>


                <button
                    class="btn btn-outline"
                    onclick="showToast('Modification du profil à venir')"
                >
                    <i class="fa-solid fa-pen"></i>
                    Modifier mon profil
                </button>

                <br>

                <button
                    class="btn"
                    onclick="logout()"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Se déconnecter
                </button>

            </div>

        </div>
    `;
}


/* =========================================================
   ASSISTANT
========================================================= */

function assistantPage() {

    if (!protect()) {
        return;
    }

    const page = getPage();

    if (!page) {
        return;
    }

    page.innerHTML = `
        <div class="container">

            <p
                class="link"
                onclick="navigate('home')"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Retour à l'accueil
            </p>

            <div class="eyebrow">
                ASSISTANT
            </div>

            <h1>
                Assistant IA
            </h1>

            <p class="muted">
                Décrivez simplement votre besoin administratif.
            </p>


            <div class="card">

                <div class="bot-icon">
                    <i class="fa-solid fa-robot"></i>
                </div>


                <h2>
                    Comment puis-je vous aider ?
                </h2>


                <p class="muted">
                    Exemple :
                    « Je veux créer une entreprise »
                </p>


                <div class="search-simple-wrapper">

                    <i class="fa-solid fa-comment"></i>

                    <input
                        class="search-simple"
                        id="assistantInput"
                        type="text"
                        placeholder="Décrivez votre besoin..."
                    >

                </div>


                <br>


                <button
                    class="btn"
                    onclick="assistantSearch()"
                >
                    <i class="fa-solid fa-paper-plane"></i>
                    Rechercher
                </button>


                <div id="assistantResult"></div>

            </div>

        </div>
    `;


    if (lastAssistantMessage) {

        const input =
            document.getElementById("assistantInput");

        if (input) {
            input.value = lastAssistantMessage;
        }

        assistantSearch();
    }
}


/* =========================================================
   AFFICHAGE DES CHOIX D'AMBIGUITE
========================================================= */

function renderChoices(data) {

    if (
        !data ||
        !Array.isArray(data.choix) ||
        data.choix.length === 0
    ) {
        return "";
    }


    let contenu = `
        <div class="assistant-choices">
    `;


    data.choix.forEach(function (choix) {

        const id =
            Number(choix.id);


        if (!id) {
            return;
        }


        contenu += `
            <button
                type="button"
                class="btn"
                onclick="assistantChoice(${id})"
            >
                <i class="fa-solid fa-arrow-right"></i>
                ${choix.texte}
            </button>
        `;

    });


    contenu += `
        </div>
    `;


    return contenu;
}


/* =========================================================
   AFFICHAGE DES SUGGESTIONS
========================================================= */

function renderSuggestions(data) {

    if (
        !data ||
        !Array.isArray(data.suggestions) ||
        data.suggestions.length === 0
    ) {
        return "";
    }


    let contenu = `
        <h4>
            Domaines disponibles
        </h4>
    `;


    data.suggestions.forEach(function (suggestion) {

        contenu += `
            <p class="check">

                <i class="fa-solid fa-circle-check"></i>

                <span>
                    ${suggestion}
                </span>

            </p>
        `;

    });


    return contenu;
}


/* =========================================================
   AFFICHAGE D'UNE REPONSE AMBIGUE
========================================================= */

function renderAmbiguousResult(data) {

    let contenu = `
        <div class="card">

            <h3>
                <i class="fa-solid fa-circle-question"></i>
                Besoin de précision
            </h3>

            <p class="muted">
                ${data.message || "Veuillez préciser votre demande."}
            </p>

            ${renderChoices(data)}

            ${renderSuggestions(data)}

        </div>
    `;


    return contenu;
}


/* =========================================================
   RECHERCHE ASSISTANT
========================================================= */

async function assistantSearch() {

    const input =
        document.getElementById("assistantInput");

    const result =
        document.getElementById("assistantResult");


    if (!input || !result) {
        return;
    }


    const message =
        input.value.trim();

    lastAssistantMessage = message;

    procedureOrigin = "assistant";


    console.log(
        "Message envoyé :",
        message
    );


    if (!message) {

        result.innerHTML = `
            <p class="muted">
                Veuillez décrire votre besoin.
            </p>
        `;

        return;
    }


    try {

        const url =
            API_BASE_URL +
            "/orientation.php?message=" +
            encodeURIComponent(message);


        console.log(
            "URL API Assistant :",
            url
        );


        const response =
            await fetch(url);


        if (!response.ok) {

            throw new Error(
                "Erreur HTTP : " +
                response.status
            );
        }


        const data =
            await response.json();


        console.log(
            "Réponse Assistant :",
            data
        );


        if (data.trouve === true) {

            result.innerHTML = `
                <div class="card">

                    <h3>
                        ${escapeHTML(data.demarche.nom)}
                    </h3>

                    <p class="muted">
                        ${escapeHTML(data.demarche.description || "")}
                    </p>

                    <p>
                        <strong>Lieu :</strong>
                        ${escapeHTML(data.demarche.lieu || "À déterminer")}
                    </p>

                    <p>
                        <strong>Délai :</strong>
                        ${escapeHTML(data.demarche.delai || "À déterminer")}
                    </p>

                    <p>
                        <strong>Frais :</strong>
                        ${escapeHTML(data.demarche.frais || "À déterminer")}
                    </p>


                    <h4>
                        Documents nécessaires
                    </h4>


                    <ul>

                        ${
                            data.documents &&
                            data.documents.length > 0

                            ?

                            data.documents.map(function (document) {

                                const obligatoire =
                                    Number(document.obligatoire) === 1;

                                return `
                                    <li>
                                        ${escapeHTML(document.nom_document)}

                                        ${
                                            obligatoire
                                                ? `<small class="muted"> - Obligatoire</small>`
                                                : `<small class="muted"> - Selon votre situation</small>`
                                        }
                                    </li>
                                `;

                            }).join("")

                            :

                            `
                                <li>
                                    Aucun document enregistré.
                                </li>
                            `
                        }

                    </ul>


                    <br>


                    <button
                        type="button"
                        class="btn"
                        onclick="assistantChoice(${Number(data.demarche.id)})"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                        Voir la procédure complète
                    </button>

                </div>
            `;

            return;
        }


        if (
            data.ambigu === true ||
            (
                Array.isArray(data.choix) &&
                data.choix.length > 0
            )
        ) {

            result.innerHTML =
                renderAmbiguousResult(data);

            return;
        }


        result.innerHTML = `
            <div class="card">

                <h3>
                    <i class="fa-solid fa-circle-question"></i>
                    Besoin de précision
                </h3>

                <p class="muted">
                    ${data.message || "Aucune démarche trouvée."}
                </p>

                ${renderSuggestions(data)}

            </div>
        `;


    } catch (error) {

        console.error(
            "Erreur Assistant :",
            error
        );

        console.error(
            "Message exact :",
            error.message
        );


        result.innerHTML = `
            <p class="muted">
                Impossible de contacter le serveur.
            </p>
        `;
    }
}


/* =========================================================
   CHOIX DE L'ASSISTANT
========================================================= */

function assistantChoice(procedureId) {

    const demarcheId =
        Number(procedureId);


    console.log(
        "ID choisi par l'utilisateur :",
        demarcheId
    );


    const serviceId =
        serviceParDemarche[demarcheId];


    if (!serviceId) {

        showToast(
            "Service correspondant introuvable."
        );

        return;
    }


    const serviceProcedures =
        procedures[serviceId] || [];


    const selectedProcedure =
        serviceProcedures.find(function (item) {

            return item.id === demarcheId;

        });


    if (!selectedProcedure) {

        showToast(
            "Démarche correspondante introuvable."
        );

        return;
    }


    console.log(
        "Choix :",
        selectedProcedure.title
    );


    procedure(
        serviceId,
        selectedProcedure.id
    );
}


/* =========================================================
   RECHERCHE DEPUIS L'ACCUEIL
========================================================= */

async function searchService() {

    console.trace("🚨 searchService() APPELÉE");


    if (!protect()) {
        return;
    }


    const input =
        document.getElementById("searchInput");

    const result =
        document.getElementById("searchResult");


    if (!input || !result) {
        return;
    }


    const message =
        input.value.trim();

    procedureOrigin = "home";


    console.log(
        "Message recherche accueil :",
        message
    );


    if (!message) {

        showToast(
            "Veuillez saisir une demande."
        );

        return;
    }


    try {

        const url =
            API_BASE_URL +
            "/orientation.php?message=" +
            encodeURIComponent(message);


        console.log(
            "URL API :",
            url
        );


        const response =
            await fetch(url);


        if (!response.ok) {

            throw new Error(
                "Erreur HTTP : " +
                response.status
            );
        }


        const data =
            await response.json();


        console.log(
            "Résultat de l'orientation :",
            data
        );


        if (!data.trouve) {

            result.innerHTML =
                renderAmbiguousResult(data);

            return;
        }


        if (
            !data.demarche ||
            !data.demarche.id
        ) {

            showToast(
                "Démarche introuvable dans la réponse."
            );

            return;
        }


        const demarcheId =
            Number(data.demarche.id);


        console.log(
            "ID de la démarche :",
            demarcheId
        );


        const serviceId =
            serviceParDemarche[demarcheId];


        if (!serviceId) {

            showToast(
                "Service correspondant introuvable."
            );

            return;
        }


        const selectedProcedure =
            procedures[serviceId].find(function (item) {

                return item.id === demarcheId;

            });


        if (!selectedProcedure) {

            showToast(
                "Démarche correspondante introuvable."
            );

            return;
        }


        console.log(
            "APPEL DE PROCEDURE OK"
        );


        procedureOrigin = "home";


        await procedure(
            serviceId,
            selectedProcedure.id
        );


    } catch (error) {

        console.error(
            "Erreur lors de l'orientation :",
            error
        );

        console.error(
            "Message exact :",
            error.message
        );


        showToast(
            "Impossible de contacter le serveur."
        );
    }
}


/* =========================================================
   RENDU DES PAGES
========================================================= */

function renderPage(route) {
    if (
        !sessionConnectee &&
        route !== "login" &&
        route !== "register"
    ) {

        route = "login";


        history.replaceState(
            {},
            "",
            "#login"
        );
    }


    if (
        sessionConnectee &&
        (
            route === "login" ||
            route === "register"
        )
    ) {

        route = "home";


        history.replaceState(
            {},
            "",
            "#home"
        );
    }


    switch (route) {

        case "login":
            renderLogin();
            break;

        case "register":
            renderRegister();
            break;

        case "home":
            homePage();
            break;

        case "services":
            servicesPage();
            break;

        case "requests":
            requestsPage();
            break;

        case "admin-requests":
            adminRequestsPage();
            break;

        case "appointments":
            appointmentsPage();
            break;

        case "notifications":
            notificationsPage();
            break;

        case "profile":
            profilePage();
            break;

        case "assistant":
            assistantPage();
            break;

        default:
            homePage();
            break;
    }


    updateActiveNav(route);

    updateHeader();
}


/* =========================================================
   NAVIGATION ACTIVE
========================================================= */

function updateActiveNav(route) {

    const links =
        document.querySelectorAll(
            ".navbar nav a"
        );


    links.forEach(function (link) {

        link.classList.remove("active");


        if (
            link.dataset.page === route
        ) {

            link.classList.add("active");
        }

    });
}


/* =========================================================
   CLICS NAVIGATION
========================================================= */

document.addEventListener(
    "click",
    function (event) {

        const link =
            event.target.closest("[data-page]");


        if (!link) {
            return;
        }


        event.preventDefault();


        const target =
            link.dataset.page;


        if (target) {
            navigate(target);
        }

    }
);


/* =========================================================
   MENU MOBILE
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const menuBtn =
            document.getElementById("menuBtn");


        if (!menuBtn) {
            return;
        }


        menuBtn.addEventListener(
            "click",
            function () {

                const nav =
                    document.querySelector(
                        ".navbar nav"
                    );


                if (nav) {
                    nav.classList.toggle("open");
                }

            }
        );

    }
);


/* =========================================================
   ENTREE CLAVIER
========================================================= */

document.addEventListener(
    "keydown",
    function (event) {

        if (event.key !== "Enter") {
            return;
        }


        if (
            document.activeElement &&
            document.activeElement.id === "searchInput"
        ) {

            event.preventDefault();

            searchService();
        }


        if (
            document.activeElement &&
            document.activeElement.id === "assistantInput"
        ) {

            event.preventDefault();

            assistantSearch();
        }

    }
);


/* =========================================================
   CHANGEMENT DE HASH
========================================================= */

window.addEventListener(
    "hashchange",
    function () {

        const route =
            window.location.hash
                .replace("#", "") || "home";


        renderPage(route);
    }
);


/* =========================================================
   CHARGEMENT INITIAL
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    async function () {

        await verifierSession();
        console.log(
    "UTILISATEUR APRÈS VÉRIFICATION :",
    utilisateurConnecte
);

console.log(
    "ROLE APRÈS VÉRIFICATION :",
    utilisateurConnecte?.role
);


        const route =
            window.location.hash
                .replace("#", "") || "home";


        renderPage(route);
    }
);