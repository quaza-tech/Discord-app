<?php
/**
 * Channel.php - VERSION CORRIGÉE
 */

namespace App\Models;

class Channel
{
    private int $id;
    private string $nom;
    private int $serverId;
    private string $type;
    private string $description;
    private string $section;
    private int $position;

    public function __construct(
        int $id,
        string $nom,
        int $serverId,
        string $type,
        string $description,
        string $section,
        int $position  // ✅ Pas de virgule après le dernier paramètre
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->serverId = $serverId;
        $this->type = $type;
        $this->description = $description;
        $this->section = $section;
        $this->position = $position;
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

    public function getServerId(): int
    {
        return $this->serverId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    // Setters
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function setType(string $type): void
    {
        $this->type = $type ?? 'text';
    }

    public function setSection(string $section): void
    {
        $this->section = $section;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    // Vérifications
    public function isTextChannel(): bool
    {
        return $this->type === 'text';  // ✅ Utilise === au lieu de ==
    }

    public function isVoiceChannel(): bool
    {
        return $this->type === 'voice';
    }

    // Pour JSON (format attendu par JavaScript)
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'channel_id' => $this->id,  // ✅ Alias pour compatibilité
            'nom' => $this->nom,
            'channel_nom' => $this->nom,  // ✅ Alias
            'description' => $this->description,
            'channel_des' => $this->description,  // ✅ Alias
            'server_id' => $this->serverId,
            'type' => $this->type,
            'section' => $this->section,
            'position' => $this->position
        ];
    }

    // Créer depuis BDD
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['nom'],
            (int) ($data['server_id'] ?? $data['serverId'] ?? 0),  // ✅ Gère les deux formats
            $data['type'] ?? 'text',
            $data['description'] ?? $data['channel_des'] ?? '',  // ✅ STRING, pas INT !
            $data['section'] ?? 'TEXTE',
            (int) ($data['position'] ?? 0)
        );
    }
}