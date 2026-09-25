<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $totalDemandes = $pdo
        ->query("SELECT COUNT(*) FROM demandes")
        ->fetchColumn();

    $demandesEnAttente = $pdo
        ->query("SELECT COUNT(*) FROM demandes WHERE statut = 'En attente'")
        ->fetchColumn();

    $totalRendezVous = $pdo
        ->query("SELECT COUNT(*) FROM rendezvous")
        ->fetchColumn();

    $rendezVousConfirmes = $pdo
        ->query("SELECT COUNT(*) FROM rendezvous WHERE statut = 'Confirmé'")
        ->fetchColumn();

    echo json_encode([
        "success" => true,
        "total_demandes" => (int) $totalDemandes,
        "demandes_en_attente" => (int) $demandesEnAttente,
        "total_rendezvous" => (int) $totalRendezVous,
        "rendezvous_confirmes" => (int) $rendezVousConfirmes
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des statistiques."
    ]);
}