<?php

namespace App;
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
    if ($img["avatar"] == !null) {
        $urlAvatar = $paths['users']['avatar'] . $img["avatar"];
    }
    if ($imgBanner == !null)
        $urlbanner = $paths['users']['banner'] . $img["banner"];
    $s3->upload()
}

?>