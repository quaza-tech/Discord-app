<?php
// GET /api/members/index.php?server_id=X → membres + rôles d'un serveur (ancien : getMemberRole.php)

require_once __DIR__ . '/../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;
use App\Repositories\UserRepository;

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

    $serverId = $_GET['server_id'] ?? null;
    $convId = $_GET['conv_id'] ?? null;
    $userId = $_GET['user_id'] ?? null;

    if (empty($serverId) && empty($convId)) {
        echo json_encode(['status' => 'error', 'message' => 'Paramètre manquant']);
        exit;
    }

    $pdo = Database::getConnection();

    if (!empty($serverId)) {
        // Membres d'un serveur avec leurs rôles
        $repo = new ServerRepository($pdo);
        $data = $repo->getMembre((int) $serverId);
    } else {
        // Infos d'un user dans le contexte MP
        if (empty($userId)) {
            echo json_encode(['status' => 'error', 'message' => 'userId manquant']);
            exit;
        }
        $repo = new UserRepository($pdo);
        $user = $repo->findById((int) $userId);
        $data = $user ? $user->toArray() : null;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (\Exception $e) {
    error_log("Erreur members : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}