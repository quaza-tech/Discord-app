<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');
try {
    // 3. Vérifier que l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Non connecté'
        ]);
        exit;
    }

    // 4. Récupérer les données POST
    $serverId = $_POST['server_id'];

    // 5. Valider les données
    if (empty($serverId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur serveur'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $serverRepo = new ServerRepository($pdo);
    $success = $serverRepo->addMember(
        $_SESSION['user'],
        $serverId,
        $_SESSION['username']
    );

    echo $success ? json_encode([
        'status' => 'success',
        'message' => 'Erreur serveur'
    ]) : json_encode([
            'status' => 'ERR',
            'message' => 'Erreur serveur'
        ]);


} catch (\Exception $e) {
    error_log("Erreur edit_message : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}

