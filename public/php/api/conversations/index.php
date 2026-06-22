<?php
// GET  /api/conversations/index.php            → liste des conv MP    (ancien : recup_convSalon.php)
// GET  /api/conversations/index.php?id=X       → messages d'une conv  (ancien : recup_private_Message.php)
// POST /api/conversations/index.php            → envoyer un MP        (ancien : send_mp.php)
// PUT  /api/conversations/index.php            → modifier un MP       (ancien : edit_message.php type mp)
// DELETE /api/conversations/index.php          → supprimer un MP      (ancien : supp_message.php type mp)

require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ConvRepository;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $pdo = Database::getConnection();
    $repo = new ConvRepository($pdo);

    switch ($_SERVER['REQUEST_METHOD']) {

        // -------------------------------------------
        // GET → liste des conv ou messages d'une conv
        // -------------------------------------------
        case 'GET':
            if (!empty($_GET['id'])) {
                // Messages d'une conversation spécifique
                $convId = (int) $_GET['id'];
                $res = $repo->isUserInConv($_SESSION['user'], $convId);

                if ($res) {
                    $messages = $repo->recup_Mp($convId);
                    echo json_encode($messages);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
                    exit;
                }

            } else {
                // Liste de toutes les conversations
                $convs = $repo->findByUser((int) $_SESSION['user']);
                echo json_encode($convs);
            }
            break;

        // -------------------------------------------
        // POST → envoyer un MP
        // -------------------------------------------
        case 'POST':
            $texte = $_POST['texte'] ?? '';
            $convId = $_POST['convID'] ?? '';
            $res = $repo->isUserInConv($_SESSION["user"], $convId);
            if (!$res) {
                echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
                exit;
            }

            if (trim($texte) === "" || empty($convId)) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
                exit;
            }

            $repo->sendMP((int) $_SESSION['user'], (int) $convId, $texte);
            echo json_encode(['status' => 'success']);
            break;

        // -------------------------------------------
        // PUT → modifier un MP
        // -------------------------------------------
        case 'PUT':
            parse_str(file_get_contents('php://input'), $data);
            $messageId = $data['id'] ?? '';
            $newText = $data['texte'] ?? '';

            if (trim($messageId) === "" || trim($newText) === "") {
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
        // DELETE → supprimer un MP
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
    error_log("Erreur conversations : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}