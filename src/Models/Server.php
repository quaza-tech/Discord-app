<?php
/**
 * Server.php - VERSION CORRIGÉE
 */

namespace App\Models;

use DateTime;

class Server
{
    private int $id;
    private string $nom;
    private string $description;
    private int $ownerId;
    private string $icon;
    private string $banner;
    private ?DateTime $createdAt;

    public function __construct(
        int $id,
        string $nom,
        string $description,
        int $ownerId,
        string $icon,
        string $banner,
        ?DateTime $createdAt = null  // ✅ Nullable + pas de virgule
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->ownerId = $ownerId;
        $this->icon = $icon ?? 'default.png';
        $this->banner = $banner ?? 'default.jpg';
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getBanner(): string
    {
        return $this->banner;
    }

    public function getCreatedAt(): ?DateTime  // ✅ Nom correct
    {
        return $this->createdAt;
    }

    // Setters
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon ?? 'default.png';
    }

    public function setBanner(string $banner): void
    {
        $this->banner = $banner ?? 'default.jpg';
    }

    // Pour JSON (format attendu par JavaScript)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'owner_id' => $this->ownerId,  // ✅ Snake_case
            'icon' => $this->icon,
            'banner' => $this->banner,
            'created_at' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null  // ✅ Formaté
        ];
    }

    // Créer depuis BDD
    public static function fromArray(array $data): self
    {
        $createdAt = null;
        if (isset($data['created_at']) && !empty($data['created_at'])) {
            $createdAt = new DateTime($data['created_at']);
        }

        return new self(
            (int) $data['id'],
            $data['nom'] ?? '',
            $data['description'] ?? '',
            (int) ($data['owner_id'] ?? $data['ownerId'] ?? 0),  // ✅ Gère les deux formats
            $data['icon'] ?? 'default.png',
            $data['banner'] ?? 'default.jpg',
            $createdAt  // ✅ DateTime ou null
        );
    }
}