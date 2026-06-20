<?php
namespace App\Models;
use DateTime;

class Message
{
    private int $id;
    private string $texte;
    private DateTime $date;
    private int $userId;
    private int $channelId;
    private ?string $auteurNom;
    private ?string $auteurAvatar;

    public function __construct(
        int $id,
        string $texte,
        DateTime $date,
        int $userId,
        int $channelId,
        ?string $auteurNom = null,
        ?string $auteurAvatar = null
    ) {
        $this->id = $id;
        $this->texte = $texte;
        $this->date = $date;
        $this->userId = $userId;
        $this->channelId = $channelId;
        $this->auteurNom = $auteurNom;
        $this->auteurAvatar = $auteurAvatar;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getTexte(): string
    {
        return $this->texte;
    }
    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getChannelId(): int
    {
        return $this->channelId;
    }
    public function getAuteurNom(): ?string
    {
        return $this->auteurNom;
    }
    public function getAuteurAvatar(): ?string
    {
        return $this->auteurAvatar;
    }

    // Setter
    public function setTexte(string $texte): void
    {
        $this->texte = $texte;
    }

    // Pour JSON (format attendu par JavaScript)
    public function toArray(): array
    {
        return [
            'mes_id' => $this->id,
            'mes_texte' => $this->texte,
            'mes_date' => $this->date->format('Y-m-d H:i:s'),
            'nom' => $this->auteurNom,
            'id_user' => $this->userId,
            'avatar' => $this->auteurAvatar ?? 'default.png'
        ];
    }

    // Créer depuis BDD
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['mes_id'],
            $data['mes_texte'],
            new DateTime($data['mes_date']),
            (int) $data['mes_users'],
            (int) $data['mes_channel'],
            $data['nom'] ?? null,
            $data['avatar'] ?? null
        );
    }
}