<?php
// GET  /api/servers/index.php          → serveurs de l'utilisateur  (ancien : recup_serv_in.php)
// GET  /api/servers/index.php?available → serveurs disponibles      (ancien : recup_serveur.php)
// POST /api/servers/index.php           → rejoindre un serveur      (ancien : join_server.php)

require_once __DIR__ . '/../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new ServerRepository($pdo);

    // -------------------------------------------
    // POST → rejoindre un serveur
    // -------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $serverId = $_POST['server_id'] ?? '';

        if (empty($serverId)) {
            echo json_encode(['status' => 'error', 'message' => 'ID serveur manquant']);
            exit;
        }

        $success = $repo->addMember(
            $_SESSION['user'],
            $serverId,
            $_SESSION['username']
        );

        echo $success
            ? json_encode(['status' => 'success'])
            : json_encode(['status' => 'error', 'message' => 'Impossible de rejoindre']);
        exit;
    }

    // -------------------------------------------
    // GET ?available → serveurs disponibles
    // -------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['available'])) {
            $servers = $repo->findAvailableForUser((int) $_SESSION['user']);
        } else {
            // GET → serveurs de l'utilisateur
            $servers = $repo->findByUser((int) $_SESSION['user']);
        }

        echo json_encode($servers);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);

} catch (\Exception $e) {
    error_log("Erreur servers : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}