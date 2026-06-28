<?php

namespace App\Models;

use DateTime;

//{id, userId, type, nomFichier, uploadedAt}

class UploadHistorique
{
    private int $id;
    private int $userId;
    private string $type;
    private string $nomFichier;
    private DateTime $uploadAt;

    public function __construct(
        int $id,
        int $userId,
        string $type,
        string $nomFichier,
        DateTime $uploadAt,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->type = $type;
        $this->nomFichier = $nomFichier;
        $this->uploadAt = $uploadAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getNomFichier(): string
    {
        return $this->nomFichier;
    }
    public function getUploadAt(): DateTime
    {
        return $this->uploadAt;
    }

    // Setter
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setNomFichier(string $name): void
    {
        $this->nomFichier = $name;
    }

    // Pour JSON (format attendu par JavaScript)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_user' => $this->userId,
            'type' => $this->type,
            'nomFichier' => $this->nomFichier,
            'uploadAt' => $this->uploadAt->format('Y-m-d H:i:s'),
        ];
    }

    // Créer depuis BDD
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            (int) $data['user_id'],
            (string) $data['file_name'],
            (string) $data['file_type'],
            new DateTime($data['upload_at'])
        );
    }
}
?>