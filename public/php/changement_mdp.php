<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\UserRepository;
use App\Services\EmailNotificationService;

header('Content-Type: application/json');
try {

    // 4. Récupérer les données POST
    $messageRecu = $_POST['email'];

    // 5. Valider les données
    if (empty($messageRecu)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Empty data'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $UserRepo = new UserRepository($pdo);
    $MailService = new EmailNotificationService();

    // 7. trouve l'id du User
    $user = $UserRepo->findByEmail($messageRecu);
    if ($user) {
        $resetToken = $UserRepo->clearResetToken((int) $user->getId());

        $token = bin2hex(random_bytes(32));

        $insertion = $UserRepo->setResetToken(
            $user->getId(),
            $token
        );

        //Configuration de l'envoie de mail 

        $mail = $MailService->sendPasswordReset(
            $messageRecu,
            $user->getNom(),
            $token
        );
        echo json_encode(['email_sent' => true]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur serveur'
        ]);
    }

    // 8. Retourner la réponse



} catch (\Exception $e) {
    error_log("Erreur changement_mdp : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}