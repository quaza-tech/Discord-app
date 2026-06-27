<?php
// GET /api/members/index.php?server_id=X → membres + rôles d'un serveur (ancien : getMemberRole.php)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }
    $serverId = $_GET['server_id'] ?? null;
    $convId = $_GET['conv_id'] ?? null;
    $userId = $_GET['user_id'] ?? null;

    $pdo = Database::getConnection();
    switch ($_SERVER['REQUEST_METHOD']) {
        case "GET":
            if (empty($serverId) && empty($convId)) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètre manquant']);
                exit;
            }

            // Membres d'un serveur avec leurs rôles
            $repo = new ServerRepository($pdo);
            $data = $repo->getRoleByServer($serverId);
            if (!empty($data))
                echo json_encode(['status' => 'success', 'data' => $data]);
            else
                echo json_encode(['status' => 'error', 'message' => 'Aucun role trouvé']);
            break;

    }

} catch (\Exception $e) {
    error_log("Erreur members : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}