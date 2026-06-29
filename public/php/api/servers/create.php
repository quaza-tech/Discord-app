<?php


require_once __DIR__ . '/../../../../src/bootstrap.php';

use App\Database;
use App\Repositories\ServerRepository;
use App\Repositories\UploadHistoriqueRepository;
use App\Services\S3StorageService;
use App\Services\MediaService;

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['status' => 'error', 'message' => 'Non connecté']);
        exit;
    }

    $pdo = Database::getConnection();
    $storage = new S3StorageService();
    $historiqueRepo = new UploadHistoriqueRepository($pdo);
    $mediaService = new MediaService($storage, $historiqueRepo);
    $serverRepo = new ServerRepository($pdo);

    // -------------------------------------------
    // POST → créer un serveur
    // -------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $description = $_POST['description'] ?? '';

        if (trim($nom) === "") {
            echo json_encode(['status' => 'error', 'message' => 'ID serveur manquant']);
            exit;
        }

        //Test si une bannière est fourni avec la creation du serveur
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $bannerFileName = $mediaService->remplacerMediaServeur($_FILES['banner'], null);
        } else {
            $bannerFileName = 'default.jpg';
        }

        //Test si une icon est fourni avec la creation du serveur
        if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            $iconFileName = $mediaService->remplacerMediaServeur($_FILES['icon'], null);
        } else {
            $iconFileName = 'default.png';
        }

        $newId = $serverRepo->createServer(
            (int) $_SESSION['user'],
            $_SESSION['username'],
            $nom,
            $description,
            $iconFileName,
            $bannerFileName
        );

        if ($newId === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Ce nom de serveur est déjà pris']);
        } else {
            echo json_encode(['status' => 'success', 'server_id' => $newId]);
        }
    }

} catch (\Exception $e) {
    error_log("Erreur servers : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}

?>