<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');

try {
    // 3. Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Non connecté'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $serverRepo = new ServerRepository($pdo);

    // 7. Récupérer les serveurs de l'utilisateur
    $servers = $serverRepo->findByUser(
        (int) $_SESSION['user']
    );

    echo json_encode($servers);

} catch (\Exception $e) {
    error_log("Erreur recup_serv_in : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}