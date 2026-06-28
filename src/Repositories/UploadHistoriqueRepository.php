<?php

namespace App\Repositories;

use App\Models\UploadHistorique;

class UploadHistoriqueRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //FONCTION D'UPLOAD DE FICHIER DANS LA BDD
    public function addUpload(int $userId,string $file_name, $file_type) : bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO user_upload_historic (user_id,file_name,file_type,upload_at)
            VALUES (?,?,?,CURRENT_TIMESTAMP)
            RETURNING id
            "
        );
        $res = $stmt->execute([$userId,$file_name,$file_type]);
        $count = $stmt->rowCount();

        if ($res && $count != 0)
            return true;
        return false;
    }

    //FONCTION QUI LISTE TOUT LES FICHIERS D'UN user
    public function listUploadByUserId(int $userId) : ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * From user_upload_historic
            WHERE user_id = ?"
        );
        $stmt->execute([$userId]);

        $fichier = [];
        while ($data = $stmt->fetch()) {
            $fichier[] = UploadHistorique::fromArray($data);
        }

        return $fichier;
    }

    //FONCTION QUI LISTE TOUT LES FICHIERS D'UN USER EN FONCTION D'UN TYPE DE FICHIER
    public function listUploadByUserIdAndFileType(int $userId, string $file_type) : ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * From user_upload_historic
            WHERE user_id = ? AND file_type ILIKE ?"
        );
        $stmt->execute([$userId,$file_type]);

        $fichier = [];
        while ($data = $stmt->fetch()) {
            $fichier[] = UploadHistorique::fromArray($data);
        }

        return $fichier;
    }


}
?>