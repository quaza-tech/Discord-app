<?php
namespace App\Services;


use App\Interfaces\FileStorageInterface;
use App\Repositories\UploadHistoriqueRepository;

class MediaService
{
    public function __construct(
        private FileStorageInterface $storage,
        private UploadHistoriqueRepository $historiqueRepo
    ) {
    }

    // Pour les médias de SERVEUR : remplace, supprime l'ancien fichier du bucket
    public function remplacerMediaServeur(array $fichier, ?string $ancienNomFichier): string
    {
        // 1. uploader le nouveau via $this->storage->upload($fichier)
        $newName = $this->storage->upload($fichier);

        // 2. si $ancienNomFichier n'est pas null, appeler $this->storage->delete($ancienNomFichier)
        if ($ancienNomFichier != null)
            $this->storage->delete($ancienNomFichier);

        // 3. retourner le nouveau nom de fichier
        return $newName;
    }

    // Pour les médias UTILISATEUR : conserve l'historique, ne supprime jamais
    public function ajouterMediaUtilisateur(array $fichier, int $userId, string $type): string
    {
        // 1. uploader le nouveau via $this->storage->upload($fichier)
        $newName = $this->storage->upload($fichier);

        // 2. enregistrer une ligne dans l'historique via $this->historiqueRepo
        $this->historiqueRepo->addUpload($userId, $newName, $type);

        // 3. retourner le nouveau nom de fichier
        return $newName;
    }
}
?>