<?php
// POST /api/auth/register.php
// Ancien fichier : inscription.php

require_once __DIR__ . '/../../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    $email = $_POST['email'] ?? '';
    $pseudo = $_POST['pseudo'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    if (empty($email) || empty($pseudo) || empty($mdp)) {
        echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new UserRepository($pdo);

    if ($repo->existsByEmailOrName($email, $pseudo)) {
        echo json_encode(['status' => 'error', 'message' => 'Email ou pseudo déjà utilisé']);
        exit;
    }

    $repo->create($pseudo, $email, $mdp);
    echo json_encode(['status' => 'success']);

} catch (\Exception $e) {
    error_log("Erreur register : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}