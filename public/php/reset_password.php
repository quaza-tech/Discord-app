<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\UserRepository;
use App\Services\EmailNotificationService;


try {

    // 4. Récupérer les données POST
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        die("Token manquant");
    }

    $token = $_GET['token'];

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $UserRepo = new UserRepository($pdo);
    $MailService = new EmailNotificationService();

    // 7. trouve l'id du User
    $user = $UserRepo->findByValidResetToken(
        $token
    );

    if ($user) {
        // ✅ CORRECTION : Détruire COMPLETEMENT la session existante
        session_unset();     // Vider toutes les variables de session
        session_destroy();   // Détruire la session

        // ✅ Créer une NOUVELLE session pour le nouvel utilisateur
        session_start();

        // ✅ Définir les nouvelles variables de session
        $_SESSION["user"] = $user->getId();
        $_SESSION["username"] = $user->getNom();

        $resetToken = $UserRepo->clearResetToken(
            $user->getId()
        );
        header('Location: ../salon.html');
        exit;
    } else {
        error_log("=== FIN RESET PASSWORD - ECHEC (token invalide ou expiré) ===");

        echo "
    <!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <title>Lien invalide</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #313338;
                color: white;
            }
            .error-box {
                text-align: center;
                padding: 40px;
                background-color: #2b2d31;
                border-radius: 8px;
            }
            a {
                color: #5865f2;
                text-decoration: none;
            }
            .debug {
                margin-top: 20px;
                font-size: 12px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <h1>❌ Lien invalide ou expiré</h1>
            <p>Ce lien de récupération n'est plus valide.</p>
            <p><a href='../index.html'>Retour à la connexion</a></p>
            <div class='debug'>
                <p>Token reçu: " . htmlspecialchars(substr($token, 0, 10)) . "...</p>
                " . "
            </div>
        </div>
    </body>
    </html>
    ";
    }
} catch (\Exception $e) {
    echo json_encode([
        'status' => 'ERR',
        'message' => 'Erreur serveur'
    ]);
}
?>