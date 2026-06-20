<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Importer UNIQUEMENT les classes utilisées
use App\Database;
use App\Repositories\UserRepository;

header('Content-Type: application/json');
try {


    // 4. Récupérer les données POST

    $messageRecu = $_POST['email'];
    $pseudoRecu = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    // 5. Valider les données
    if (empty($messageRecu) || empty($mdp) || empty($pseudoRecu)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Valeur non renseigné'
        ]);

        exit;
    }

    // 6. Initialiser le repository
    $pdo = Database::getConnection();
    $UserRepo = new UserRepository($pdo);

    // 7. verifie l'email et le mot de passe
    $success = $UserRepo->existsByEmailOrName(
        $messageRecu,
        $pseudoRecu
    );

    if (!$success) {
        $insertion = $UserRepo->create(
            $pseudoRecu,
            $messageRecu,
            $mdp
        );
        if ($insertion) {

            echo json_encode([
                'status' => 'success',
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur serveur'
        ]);
    }

} catch (\Exception $e) {
    error_log("Erreur edit_message : " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur serveur'
    ]);
}

