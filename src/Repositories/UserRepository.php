<?php
namespace App\Repositories;

use App\Models\Users;
use PDO;

class UserRepository
{
    private PDO $pdo;

    // Le PDO est injecté (passé dans le constructeur)
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Trouver un user par email
    public function findByEmail(string $email): ?Users
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, email, avatar FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data)
            return null;

        return Users::fromArray($data);
    }

    // Trouver un user par ID
    // UserRepository.php
    public function findById(int $id, ?int $serverId = null): ?Users
    {
        if ($serverId) {
            $stmt = $this->pdo->prepare(
                "SELECT u.id, u.nom, u.email, u.avatar, u.banner, u.bios, s.nickname
             FROM users u
             LEFT JOIN server_members s ON u.id = s.user_id AND s.server_id = ?
             WHERE u.id = ?"
            );
            $stmt->execute([$serverId, $id]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT id, nom, email, avatar, banner, bios
             FROM users WHERE id = ?"
            );
            $stmt->execute([$id]);
        }
        $data = $stmt->fetch();
        return Users::fromArray($data);
    }

    // Vérifier email + password
    public function verifyCredentials(string $email, string $password): ?Users
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, email, mdp, avatar FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        if (!$data || !password_verify($password, $data['mdp'])) {
            return null;
        }

        return Users::fromArray($data);
    }
    //Create a new user
    public function create(string $nom, string $email, string $password): Users
    {
        // Hash the password using PHP's default algorithm (bcrypt), never store plain-text passwords
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Use a prepared statement to safely insert the user, prevents SQL injection attacks
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (nom, email, mdp) VALUES (?, ?, ?) RETURNING id"
        );

        // Bind and execute with user-provided values
        $stmt->execute([$nom, $email, $hashedPassword]);

        // Retrieve the auto-generated ID returned by the RETURNING clause
        $id = $stmt->fetchColumn();

        return new Users($id, $nom, $email);
    }

    //modifier la bannière de l'utilisateur
    public function updateBanner(int $user_id, string $banner): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
            SET banner = ?
            WHERE id = ?
            "
        );
        $stmt->execute([$banner, $user_id]);
    }

    //modifier l'avatars de l'utilisateur
    public function updateAvatar(int $user_id, string $avatar): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
            SET avatar = ?
            WHERE id = ?
            "
        );
        $stmt->execute([$avatar, $user_id]);
    }

    //fonction temporaire de fetch banner et avatars
    public function fetchAvatarsAndBanner(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id ,avatar, banner FROM users
            WHERE (avatar IS NOT NULL 
            AND avatar NOT ILIKE 'http%' )
            OR (banner IS NOT NULL 
            AND banner NOT ILIKE 'http%' )
            "
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Vérifier si email ou nom existe
    public function existsByEmailOrName(string $email, string $nom): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM users WHERE email = ? OR nom = ?"
        );
        $stmt->execute([$email, $nom]);

        return $stmt->fetchColumn() > 0;
    }

    // Méthodes pour reset password
    public function setResetToken(int $userId, string $token): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET reset_token = ?, reset_expiration = CURRENT_TIMESTAMP + INTERVAL '1 hour' WHERE id = ?"
        );
        return $stmt->execute([$token, $userId]);
    }

    public function findByValidResetToken(string $token): ?Users
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, email, avatar FROM users WHERE reset_token = ? AND reset_expiration > CURRENT_TIMESTAMP"
        );
        $stmt->execute([$token]);
        $data = $stmt->fetch();

        if (!$data)
            return null;

        return Users::fromArray($data);
    }

    public function clearResetToken(int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET reset_token = NULL, reset_expiration = NULL WHERE id = ?"
        );
        return $stmt->execute([$userId]);
    }

    public function invalidateAllResetTokens(): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET reset_token = NULL, reset_expiration = NULL WHERE reset_token IS NOT NULL"
        );
        return $stmt->execute();
    }
    public function findFriends(int $userid): ?Users
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.id as id, u.nom as nom, u.email as email, u.avatar as avatar,a.statut as statut FROM users u INNER JOIN amis a on u.id=a.ami_id WHERE a.user_id = ?"
        );
        $stmt->execute([$userid]);
        $data = $stmt->fetch();

        if (!$data) {

            $stmt = $this->pdo->prepare(
                "SELECT u.id as id, u.nom as nom, u.email as email, u.avatar as avatar FROM users u INNER JOIN amis a on u.id=a.user_id WHERE a.ami_id = ?"
            );
            $stmt->execute([$userid]);
            $data = $stmt->fetch();
            if (!$data) {
                return null;
            }
        }
        return Users::fromArray($data);
    }
}