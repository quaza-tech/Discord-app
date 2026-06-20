<?php
namespace App\Models;
use DateTime;

class Users
{
    // Propriétés PRIVÉES (encapsulation)
    private int $id;
    private string $nom;
    private string $email;
    private ?string $avatar;
    private ?string $bios = null;
    private ?string $banner = null;
    private ?string $nickname = null;
    private string $dateCreation = '';

    // Constructeur (appelé avec "new User(...)")
    public function __construct(int $id, string $nom, string $email, ?string $avatar = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->avatar = $avatar ?? 'default.png';
    }

    // Getters (pour lire les propriétés)
    public function getId(): int
    {
        return $this->id;
    }
    public function getNom(): string
    {
        return $this->nom;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }
    public function getBios(): ?string
    {
        return $this->bios;
    }
    public function getDate(): string
    {
        return $this->dateCreation;
    }
    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    // Setters (pour modifier les propriétés)
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }
    public function setBios(string $bios): void
    {
        $this->bios = $bios;
    }

    // Méthode utile : convertir en tableau pour JSON
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'bios' => $this->bios,
            'banner' => $this->banner,
            'nickname' => $this->nickname,
        ];
    }

    // Méthode utile : créer depuis un tableau de BDD
    public static function fromArray(array $data): self
    {
        $user = new self((int) $data['id'], $data['nom'], $data['email'], $data['avatar'] ?? null);
        $user->bios = $data['bios'] ?? null;
        $user->banner = $data['banner'] ?? null;
        $user->nickname = $data['nickname'] ?? null;
        return $user;
    }
}