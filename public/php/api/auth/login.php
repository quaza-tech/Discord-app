<?php
// POST /api/auth/login.php
// Anciens fichiers : test_connexion.php

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $email = $_POST['email'] ?? '';
    $motDePasse = $_POST['mot'] ?? '';

    if (empty($email) || empty($motDePasse)) {
        echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new UserRepository($pdo);
    $user = $repo->verifyCredentials($email, $motDePasse);

    if ($user) {
        $_SESSION['user'] = $user->getId();
        $_SESSION['username'] = $user->getNom();
        echo json_encode(['status' => 'OK']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Identifiants incorrects']);
    }

} catch (\Exception $e) {
    error_log("Erreur login : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}