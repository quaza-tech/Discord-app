<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\ConvRepository;
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
    $messageId = $_POST['id'] ?? '';
    $newText = $_POST['texte'] ?? '';
    $Type = !empty($_POST['convid']) ? 'mp' : 'server';

    // 5. Valider les données
    if (empty($messageId) || empty($newText)) {
        echo "ERR";
        exit;
    }
    $newText .= ' (modifié)';

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    switch ($Type) {
        case 'server':
            $messageRepo = new MessageRepository($pdo);

            // 7. Modifier le message
            $success = $messageRepo->update(
                (int) $messageId,
                $_SESSION['user'],
                $newText
            );
            break;
        case 'mp':
            $messageRepo = new ConvRepository($pdo);

            // 7. Modifier le message
            $success = $messageRepo->update(
                (int) $messageId,
                $_SESSION['user'],
                $newText
            );
            break;
    }
    // 8. Retourner la réponse
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

