<?php

// 1. Charger bootstrap (fait session_start() automatiquement)
require_once __DIR__ . '/../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');
// 6. Initialiser le repository
$pdo = Database::getConnection();
$UserRepo = new UserRepository($pdo);

// 2. Vérifier si l'utilisateur est connecté
if (isset($_SESSION["username"])) {
    $info = $UserRepo->findById(
        $_SESSION['user']
    );


    // 8. Retourner le JSON
    header('Content-Type: application/json');
    echo json_encode([
        'id' => $info->getId(),
        'nom' => $info->getNom(),
        'email' => $info->getEmail(),
        'avatar' => $info->getAvatar()
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}

