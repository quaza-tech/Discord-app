<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/bootstrap.php';

use App\Database;
use App\Repositories\ConvRepository;

try {
    // Vérifier session
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    // Vérifier paramètre
    $convId = $_POST['convid'] ?? null;

    if (!$convId) {
        echo json_encode(['status' => 'error', 'message' => 'ID conversation manquant']);
        exit;
    }

    // Récupérer les messages
    $pdo = Database::getConnection();
    $convRepo = new ConvRepository($pdo);
    $messages = $convRepo->recup_Mp($convId);

    echo json_encode($messages);

} catch (\Exception $e) {
    error_log("Erreur recup_private_Message : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}