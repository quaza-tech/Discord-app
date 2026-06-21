<?php
// GET    /api/channels/messages.php?server_id=X&channel_id=Y → récupérer messages (ancien : recup_message.php)
// POST   /api/channels/messages.php                          → envoyer message    (ancien : ajax.php)
// PUT    /api/channels/messages.php                          → modifier message   (ancien : edit_message.php)
// DELETE /api/channels/messages.php                          → supprimer message  (ancien : supp_message.php)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\MessageRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new MessageRepository($pdo);

    switch ($_SERVER['REQUEST_METHOD']) {

        // -------------------------------------------
        // GET → récupérer les messages d'un salon
        // -------------------------------------------
        case 'GET':
            $serverId = $_GET['server_id'] ?? '';
            $channelId = $_GET['channel_id'] ?? '';

            if (empty($serverId) || empty($channelId)) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
                exit;
            }

            $messages = $repo->findByChannel((int) $channelId, (int) $serverId);
            echo json_encode(array_map(fn($m) => $m->toArray(), $messages));
            break;

        // -------------------------------------------
        // POST → envoyer un message
        // -------------------------------------------
        case 'POST':
            $texte = $_POST['texte'] ?? '';
            $salonId = $_POST['salonid'] ?? '';

            if (empty($texte) || empty($salonId)) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
                exit;
            }

            $repo->create((int) $_SESSION['user'], (int) $salonId, $texte);
            echo json_encode(['status' => 'success']);
            break;

        // -------------------------------------------
        // PUT → modifier un message
        // -------------------------------------------
        case 'PUT':
            parse_str(file_get_contents('php://input'), $data);
            $messageId = $data['id'] ?? '';
            $newText = $data['texte'] ?? '';

            if (empty($messageId) || empty($newText)) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
                exit;
            }

            $success = $repo->update((int) $messageId, $_SESSION['user'], $newText . ' (modifié)');
            echo json_encode(
                $success
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'Modification échouée']
            );
            break;

        // -------------------------------------------
        // DELETE → supprimer un message
        // -------------------------------------------
        case 'DELETE':
            parse_str(file_get_contents('php://input'), $data);
            $messageId = $data['id'] ?? '';

            if (empty($messageId)) {
                echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
                exit;
            }

            $success = $repo->delete((int) $messageId, $_SESSION['user']);
            echo json_encode(
                $success
                ? ['status' => 'success']
                : ['status' => 'error', 'message' => 'Suppression échouée']
            );
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    }

} catch (\Exception $e) {
    error_log("Erreur channel messages : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}