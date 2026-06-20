<?php
require_once __DIR__ . '/../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }
    $userId = $_POST['userId'] ?? null;
    $serverId = $_POST['serverId'] ?? null;

    if (empty($userId)) {
        echo json_encode(['status' => 'error', 'message' => 'Donnée vide']);
        exit;
    }

    $pdo = Database::getConnection();


    $repo = new UserRepository($pdo);
    $data = $repo->findById((int) $userId);
    $data = $data->toArray();

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} catch (\Exception $e) {
    error_log("Erreur getMembre : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}