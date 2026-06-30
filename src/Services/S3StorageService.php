<?php
namespace App\Services;

use App\Interfaces\FileStorageInterface;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class S3StorageService implements FileStorageInterface
{
    private S3Client $s3;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['B2_REGION'],
            'endpoint' => $_ENV['B2_ENDPOINT'],
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key' => $_ENV['B2_KEY_ID'],
                'secret' => $_ENV['B2_APPLICATION_KEY'],
            ],
        ]);
    }
    public function upload(array $fichier, string $prefixe = ''): string
    {
        $uniqueId = uniqid();
        $extensiond = pathinfo($fichier['name'], PATHINFO_EXTENSION);

        $this->s3->putObject([
            'Bucket' => $_ENV['B2_BUCKET'],
            'Key' => $prefixe . $uniqueId . '.' . $extensiond,
            'Body' => fopen($fichier['tmp_name'], 'rb'),
            'ContentType' => $fichier['type'],
        ]);

        return "https://s3.eu-central-003.backblazeb2.com/" + $prefixe . $uniqueId . '.' . $extensiond;
    }
    public function delete(string $nomFichier): bool
    {
        try {
            $result = $this->s3->deleteObject([
                'Bucket' => $_ENV['B2_BUCKET'],
                'Key' => $nomFichier
            ]);
            return true;
        } catch (S3Exception $e) {
            return false;
        }
    }
}
?>