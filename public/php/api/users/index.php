<?php
// GET  /api/users/index.php          → infos user connecté (ancien : get_user_info.php)
// GET  /api/users/index.php?id=X     → profil d'un user   (ancien : getUser.php)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new UserRepository($pdo);

    // -------------------------------------------
    // GET ?id=X → profil d'un user spécifique
    // -------------------------------------------
    if (!empty($_GET['id'])) {
        $userId = (int) $_GET['id'];
        $serverId = !empty($_GET['server_id']) ? (int) $_GET['server_id'] : null;

        $user = $repo->findById($userId, $serverId);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Utilisateur introuvable']);
            exit;
        }

        echo json_encode(['status' => 'success', 'data' => $user->toArray()]);
        exit;
    }

    // -------------------------------------------
    // GET (sans id) → infos du user connecté
    // -------------------------------------------
    if (!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $user = $repo->findById((int) $_SESSION['user']);

    echo json_encode([
        'id' => $user->getId(),
        'nom' => $user->getNom(),
        'email' => $user->getEmail(),
        'avatar' => $user->getAvatar()
    ]);

} catch (\Exception $e) {
    error_log("Erreur users : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}