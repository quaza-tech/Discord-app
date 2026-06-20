<?php
// POST /api/auth/password.php  → demande de reset (ancien : changement_mdp.php)
// GET  /api/auth/password.php  → validation du token (ancien : reset_password.php)

require_once __DIR__ . '/../../../src/bootstrap.php';

use App\Database;
use App\Repositories\UserRepository;
use App\Services\EmailNotificationService;

header('Content-Type: application/json');

try {
    $pdo = Database::getConnection();
    $repo = new UserRepository($pdo);

    // ----------------------------------------
    // GET → validation du token de reset
    // ----------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            die("Token manquant");
        }

        $user = $repo->findByValidResetToken($token);

        if ($user) {
            session_unset();
            session_destroy();
            session_start();

            $_SESSION['user'] = $user->getId();
            $_SESSION['username'] = $user->getNom();

            $repo->clearResetToken($user->getId());
            header('Location: ../../../salon.html');
            exit;
        } else {
            // Token invalide — page d'erreur
            header('Content-Type: text/html; charset=UTF-8');
            echo "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><title>Lien invalide</title>
            <style>body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;
            height:100vh;background:#313338;color:white}.box{text-align:center;padding:40px;
            background:#2b2d31;border-radius:8px}a{color:#5865f2;text-decoration:none}</style></head>
            <body><div class='box'><h1>❌ Lien invalide ou expiré</h1>
            <p><a href='../../../index.html'>Retour à la connexion</a></p></div></body></html>";
        }
        exit;
    }

    // ----------------------------------------
    // POST → demande de reset par email
    // ----------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email manquant']);
            exit;
        }

        $user = $repo->findByEmail($email);

        if ($user) {
            $repo->clearResetToken($user->getId());
            $token = bin2hex(random_bytes(32));
            $repo->setResetToken($user->getId(), $token);

            $mail = new EmailNotificationService();
            $mail->sendPasswordReset($email, $user->getNom(), $token);

            echo json_encode(['status' => 'success', 'email_sent' => true]);
        } else {
            // On ne révèle pas si l'email existe ou non (sécurité)
            echo json_encode(['status' => 'success', 'email_sent' => true]);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);

} catch (\Exception $e) {
    error_log("Erreur password reset : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}