<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\S3StorageService;
use App\Database;
use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;

$paths = [
    'servers' => [
        'icon' => 'public/img/servers/icon/',
        'banner' => 'public/img/servers/banner/',
    ],
    'users' => [
        'avatar' => 'public/img/avatars/',
        'banner' => 'public/img/banners/',
    ],
];
$pdo = Database::getConnection();
$s3 = new S3StorageService();

$userRepo = new UserRepository($pdo);
$serverRepo = new ServerRepository($pdo);

$resultUser = $userRepo->fetchAvatarsAndBanner();
$resultServer = $serverRepo->fetchAvatarsAndBanner();

echo "Tu vas migrer X fichiers. Confirmer ? (oui/non) : ";
$confirmation = trim(fgets(STDIN));
if ($confirmation !== 'oui') {
    exit("Migration annulée.");
}

foreach ($resultUser as $img) {
    if ($img["avatar"] !== null) {
        $urlAvatar = $paths['users']['avatar'] . $img["avatar"];    ///['tmp_name' => $cheminLocal, 'name' => $nomFichier, 'type' => mime_content_type($cheminLocal)]
        $res = file_exists($urlAvatar);
        if ($res) {
            $url = $s3->upload(['tmp_name' => $urlAvatar, 'name' => $img["avatar"], 'type' => mime_content_type($urlAvatar)]);
            $userRepo->updateAvatar($img["id"], $url);
        }

    }
    if ($img["banner"] !== null) {
        $urlbanner = $paths['users']['banner'] . $img["banner"];
        $res = file_exists($urlbanner);
        if ($res) {
            $url = $s3->upload(['tmp_name' => $urlbanner, 'name' => $img["banner"], 'type' => mime_content_type($urlbanner)]);
            $userRepo->updateBanner($img["id"], $url);
        }
    }
}
foreach ($resultServer as $img) {
    if ($img["icon"] !== null) {
        $urlAvatar = $paths['servers']['icon'] . $img["icon"];    ///['tmp_name' => $cheminLocal, 'name' => $nomFichier, 'type' => mime_content_type($cheminLocal)]
        $res = file_exists($urlAvatar);
        if ($res) {
            $url = $s3->upload(['tmp_name' => $urlAvatar, 'name' => $img["icon"], 'type' => mime_content_type($urlAvatar)]);
            $serverRepo->updateIcon($img["id"], $url);
        }
    }
    if ($img["banner"] !== null) {
        $urlbanner = $paths['servers']['banner'] . $img["banner"];
        $res = file_exists($urlbanner);
        if ($res) {
            $url = $s3->upload(['tmp_name' => $urlbanner, 'name' => $img["banner"], 'type' => mime_content_type($urlbanner)]);
            $serverRepo->updateBanner($img["id"], $url);
        }
    }
}

?>