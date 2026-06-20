<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer les classes nécessaires
use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');
try {
    // 3. Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Non connecté']);
        exit;
    }

    // 4. Récupérer l'ID du serveur
    $serverId = $_POST['id'] ?? '';

    // 5. Validation
    if (empty($serverId)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'ID serveur manquant']);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $serverRepo = new ServerRepository($pdo);

    // 7. Récupérer les salons du serveur
    $channels = $serverRepo->getChannels((int) $serverId);

    // 8. Retourner le JSON
    header('Content-Type: application/json');
    echo json_encode($channels);

} catch (\Exception $e) {
    error_log("Erreur recup_salon : " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}