<?php
require_once __DIR__ . '/../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;
use App\Repositories\UserRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $serverId = $_POST['ServerId'] ?? null;
    $convId = $_POST['ConvId'] ?? null;
    $userId = $_POST['userId'] ?? null;
    $type = $convId != null ? 'mp' : 'server';

    if (empty($serverId) && empty($convId)) {
        echo json_encode(['status' => 'error', 'message' => 'Donnée vide']);
        exit;
    }

    $pdo = Database::getConnection();

    switch ($type) {
        case 'server':
            $repo = new ServerRepository($pdo);
            $data = $repo->getMembre((int) $serverId);
            break;

        case 'mp':
            if (empty($userId)) {
                echo json_encode(['status' => 'error', 'message' => 'userId manquant']);
                exit;
            }
            $repo = new UserRepository($pdo);
            $data = $repo->findById((int) $userId);
            $data = $data ? $data->toArray() : null;
            break;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} catch (\Exception $e) {
    error_log("Erreur getMembre : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}