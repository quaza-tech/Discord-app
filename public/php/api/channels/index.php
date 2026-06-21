<?php
// GET /api/channels/index.php?server_id=X → salons d'un serveur (ancien : recup_salon.php)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
        exit;
    }

    $serverId = $_GET['server_id'] ?? '';

    if (empty($serverId)) {
        echo json_encode(['status' => 'error', 'message' => 'ID serveur manquant']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new ServerRepository($pdo);
    $channels = $repo->getChannels((int) $serverId);

    echo json_encode($channels);

} catch (\Exception $e) {
    error_log("Erreur channels : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}