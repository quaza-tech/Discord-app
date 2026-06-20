<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\MessageRepository;

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
    $serverId = $_POST['serverid'] ?? '';
    $salonId = $_POST['salonid'] ?? '';

    // 5. Valider les données
    if (empty($serverId) || empty($salonId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur serveur'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $messageRepo = new MessageRepository($pdo);

    // 7. trouve le message
    $message = $messageRepo->findByChannel(
        (int) $salonId,
        (int) $serverId,
    );

    // 8. Retourner la réponse
    header('Content-Type: application/json');
    echo json_encode(array_map(fn($m) => $m->toArray(), $message));

} catch (\Exception $e) {
    error_log("Erreur recup_message : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}

