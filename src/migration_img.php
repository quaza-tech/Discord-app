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

$s3 = new S3StorageService();
$pdo = Database::getConnection();
$userRepo = new UserRepository($pdo);
$servRepo = new ServerRepository($pdo);

$resultUser = $userRepo->fetchAvatarsAndBanner();
$resultServer = $serverRepo->fetchAvatarsAndBanner();

foreach ($resultUser as $img) {
    if (!is_null($img["avatar"])) {
        $urlAvatar = $paths['users']['avatar'] . $img["avatar"];    ///['tmp_name' => $cheminLocal, 'name' => $nomFichier, 'type' => mime_content_type($cheminLocal)]
        file_exists($urlAvatar);
        $s3->upload(['tmp_name' => $urlAvatar, 'name' => substr($img["avatar"], 0, -4), 'type' => mime_content_type($urlAvatar)]);
        $userRepo->updateAvatar($img["id"], $urlAvatar);
    }
    if (!is_null($img["banner"])) {
        $urlbanner = $paths['users']['banner'] . $img["banner"];
        file_exists($urlbanner);
        $s3->upload(['tmp_name' => $urlbanner, 'name' => substr($img["banner"], 0, -4), 'type' => mime_content_type($urlbanner)]);
        $userRepo->updateAvatar($img["id"], $urlbanner);
    }
}
foreach ($resultUser as $img) {
    if (!is_null($img["icon"])) {
        $urlAvatar = $paths['servers']['icon'] . $img["icon"];    ///['tmp_name' => $cheminLocal, 'name' => $nomFichier, 'type' => mime_content_type($cheminLocal)]
        file_exists($urlAvatar);
        $s3->upload(['tmp_name' => $urlAvatar, 'name' => substr($img["icon"], 0, -4), 'type' => mime_content_type($urlAvatar)]);
        $userRepo->updateAvatar($img["id"], $urlAvatar);
    }
    if (!is_null($img["banner"])) {
        $urlbanner = $paths['servers']['banner'] . $img["banner"];
        file_exists($urlbanner);
        $s3->upload(['tmp_name' => $urlbanner, 'name' => substr($img["banner"], 0, -4), 'type' => mime_content_type($urlbanner)]);
        $userRepo->updateAvatar($img["id"], $urlbanner);
    }
}

?>