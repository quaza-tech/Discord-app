<?php
namespace App\Services;

use App\Interfaces\FileStorageInterface;
use Aws\S3\S3Client;

class StorageService 
{
    private S3Client $s3;

    public function __construct(){
        $this->s3 = new S3Client([
                    'version' => 'latest',
                    'region'  => $_ENV['B2_REGION'],
                    'endpoint' => $_ENV['B2_ENDPOINT'],
                    'use_path_style_endpoint' => false,
                    'credentials' => [
                        'key'    => $_ENV['B2_KEY_ID'],
                        'secret' => $_ENV['B2_APPLICATION_KEY'],
                    ],
                ]);
    }
    public function upload(array $fichier, string $prefixe = ''): string
    {
        $uniqueId = uniqid();
        $extensiond = pathinfo($fichier['name'], PATHINFO_EXTENSION);
        
        $this->s3->putObject([
            'Bucket'      => $_ENV['B2_BUCKET'],
            'Key'         => $prefixe . $uniqueId . '.' . $extensiond,
            'Body'        => fopen($fichier['tmp_name'], 'rb'),
            'ContentType' => $fichier['type'],
        ]);

        return $prefixe . $uniqueId . '.' . $extensiond;
    }
    public function delete(string $nomFichier): bool
    {
        try
        {
            $result = $s3->deleteObject([
                'Bucket' => $_ENV['B2_BUCKET'],
                'Key'    => $nomFichier
            ]);

            if ($result['DeleteMarker'])
            {
                echo $nomFichier . ' was deleted or does not exist.' . PHP_EOL;
            } else {
                exit('Error: ' . $nomFichier . ' was not deleted.' . PHP_EOL);
            }
        }
        catch (S3Exception $e) {
            exit('Error: ' . $e->getAwsErrorMessage() . PHP_EOL);
        }

        // 2. Check to see if the object was deleted.
        try
        {
            echo 'Checking to see if ' . $nomFichier . ' still exists...' . PHP_EOL;

            $result = $s3->getObject([
                'Bucket' => $_ENV['B2_BUCKET'],
                'Key'    => $nomFichier
            ]);

            echo 'Error: ' . $nomFichier . ' still exists.';
        }
        catch (S3Exception $e) {
            exit($e->getAwsErrorMessage());
        }
    }
}
?>