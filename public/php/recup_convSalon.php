<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer les classes nécessaires
use App\Database;
use App\Repositories\ConvRepository;

header('Content-Type: application/json');
try {
    // 3. Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {

        echo json_encode(['error' => 'Non connecté']);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $ConvRepo = new ConvRepository($pdo);

    // 7. Récupérer les Mp du users actuel
    $conv = $ConvRepo->findByUser(
        $_SESSION['user']
    );
    // 8. Retourner le JSON
    echo json_encode($conv);

} catch (\Exception $e) {
    error_log("Erreur recup_salon : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}