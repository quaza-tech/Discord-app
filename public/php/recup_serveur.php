<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\ServerRepository;

// ✅ AJOUTER EN TOUT PREMIER (avant tout echo)
header('Content-Type: application/json');

try {
    // 3. Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        // ✅ Retourner du JSON avec un statut d'erreur
        echo json_encode([
            'status' => 'error',
            'message' => 'Non connecté'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $serverRepo = new ServerRepository($pdo);

    // 7. Récupérer les serveurs disponibles
    $servers = $serverRepo->findAvailableForUser(
        (int) $_SESSION['user']
    );


    echo json_encode($servers);  // [] si vide, [...] si données

} catch (\Exception $e) {
    error_log("Erreur recup_serveur : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}