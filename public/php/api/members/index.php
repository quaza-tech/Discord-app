<?php
// GET /api/members/index.php?server_id=X → membres + rôles d'un serveur (ancien : getMemberRole.php)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;
use App\Repositories\UserRepository;
use App\Permissions;

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
            break;
        case "POST":
            $repo = new ServerRepository($pdo);
            $permission = $repo->getMemberPermissions($serverId, $_SESSION["user"]);
            if (!Permissions::hasPermission($permission, Permissions::GERER_SERVEUR)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur de permission']);
                exit;
            } else {
                parse_str(file_get_contents('php://input'), $data);
                $userId = $data['id'] ?? '';
                $roleId = $data['roleId'] ?? '';

                $res = $repo->getMemberId($userId, $serverId);
                if ($res === null) {
                    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non présent']);
                    exit;
                } else {
                    $repo->assignRole($res, $roleId);
                    echo json_encode(['status' => 'success', 'message' => 'Assignation de role']);
                }

            }
            break;

        case "DELETE":
            $repo = new ServerRepository($pdo);
            $permission = $repo->getMemberPermissions($serverId, $_SESSION["user"]);
            if (!Permissions::hasPermission($permission, Permissions::GERER_SERVEUR)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur de permission']);
                exit;
            } else {
                parse_str(file_get_contents('php://input'), $data);
                $userId = $data['id'] ?? '';
                $roleId = $data['roleId'] ?? '';

                $res = $repo->getMemberId($userId, $serverId);
                if ($res === null) {
                    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non présent']);
                    exit;
                } else {
                    $repo->removeRole($res, $roleId);
                    echo json_encode(['status' => 'success', 'message' => 'Destition de role']);
                }

            }
            break;
    }

} catch (\Exception $e) {
    error_log("Erreur members : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}