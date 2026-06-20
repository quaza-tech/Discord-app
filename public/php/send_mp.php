<?php
// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\ConvRepository;

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
    $messageRecu = $_POST['texte'];
    $convId = $_POST['convID'];
    // 5. Valider les données
    if (!$messageRecu) {
        echo json_encode(['status' => 'success', 'message' => 'Message vide']);
        exit;
    }

    if ($convId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID conversation invalide']);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $messageRepo = new ConvRepository($pdo);

    // 7. trouve le message
    $message = $messageRepo->sendMP(
        $_SESSION['user'],
        $convId,
        $messageRecu
    );

    // 8. Retourner la réponse
    echo json_encode([
        'status' => 'success'
    ]);


} catch (\Exception $e) {
    error_log("Erreur recup_message : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}
?>