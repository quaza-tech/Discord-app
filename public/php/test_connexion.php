<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;

header('Content-Type: application/json');
try {

    // 4. Récupérer les données POST
    $email = $_POST['email'] ?? '';
    $motDePasse = $_POST['mot'] ?? '';

    // 5. Valider les données
    if (empty($email) || empty($motDePasse)) {
        echo json_encode([
            'status' => 'ERR',
            'message' => 'Erreur serveur'
        ]);
        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $UserRepo = new UserRepository($pdo);
    $ServerInit = new ServerRepository($pdo);

    // 7. verifie l'email et le mot de passe
    $success = $UserRepo->verifyCredentials(
        $email,
        $motDePasse,
    );

    if (!empty($success)) {

        // 8. Retourner la réponse
        $_SESSION['user'] = $success->getId();
        $_SESSION['username'] = $success->getNom();

        echo json_encode([
            'status' => 'OK',
        ]);
    } else {
        echo json_encode([
            'status' => 'ERR',
            'message' => 'Erreur serveur'
        ]);
    }

} catch (\Exception $e) {
    error_log("Erreur edit_message : " . $e->getMessage());
    echo json_encode([
        'status' => 'ERR',
        'message' => 'Erreur serveur'
    ]);
}

