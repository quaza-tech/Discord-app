<?php
/**
 * ChannelRepository.php - VERSION CORRIGÉE
 */

namespace App\Repositories;

use App\Models\Channel;
use PDO;

class ChannelRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Créer un salon
     */
    public function create(
        int $serverId,
        string $nom,
        string $type,
        string $description,
        string $section,
        int $position
    ): Channel {
        $stmt = $this->pdo->prepare(
            "INSERT INTO channels (nom, description, server_id, type, position, section) 
             VALUES (?, ?, ?, ?, ?, ?)
             RETURNING id"  // ✅ PostgreSQL RETURNING
        );
        $stmt->execute([$nom, $description, $serverId, $type, $position, $section]);

        $id = $stmt->fetchColumn();

        return new Channel(
            (int) $id,
            $nom,
            $serverId,
            $type,
            $description,
            $section,
            $position
        );
    }

    /**
     * Modifier un salon
     */
    public function update(
        int $id,
        string $nom,
        string $description,
        int $position
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE channels SET nom = ?, description = ?, position = ? WHERE id = ?"
            // ✅ Virgules entre les colonnes, pas AND
            // ✅ 'position' bien orthographié
        );
        $stmt->execute([$nom, $description, $position, $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Supprimer un salon
     */
    public function delete(int $channelId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM channels WHERE id = ?"
        );
        $stmt->execute([$channelId]);

        return $stmt->rowCount() > 0;
    }


    /**
     * Trouver un salon par ID
     */
    public function findById(int $id): ?Channel
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, description, server_id, type, position, section 
             FROM channels 
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Channel::fromArray($data);
    }

    /**
     * Trouver TOUS les salons d'un serveur
     */
    public function findByServer(int $serverId): array  // ✅ Retourne un array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, description, server_id, type, position, section 
             FROM channels 
             WHERE server_id = ?
             ORDER BY section ASC, position ASC"
        );
        $stmt->execute([$serverId]);

        $channels = [];
        while ($data = $stmt->fetch()) {  // ✅ Boucle while
            $channels[] = Channel::fromArray($data);
        }

        return $channels;  // ✅ Tableau de Channel
    }

    /**
     * Trouver les salons par serveur ET section
     */
    public function findByServerAndSection(int $serverId, string $section): array  // ✅ array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, description, server_id, type, position, section 
             FROM channels 
             WHERE server_id = ? AND section = ?
             ORDER BY position ASC"
        );
        $stmt->execute([$serverId, $section]);

        $channels = [];
        while ($data = $stmt->fetch()) {
            $channels[] = Channel::fromArray($data);
        }

        return $channels;
    }

    /**
     * Trouver les salons textuels d'un serveur
     */
    public function findTextChannelsByServer(int $serverId): array  // ✅ array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, description, server_id, type, position, section 
             FROM channels 
             WHERE server_id = ? AND type = 'text'
             ORDER BY section ASC, position ASC"
        );
        $stmt->execute([$serverId]);

        $channels = [];
        while ($data = $stmt->fetch()) {
            $channels[] = Channel::fromArray($data);
        }

        return $channels;
    }

    /**
     * Compter les salons d'un serveur
     */
    public function countByServer(int $serverId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM channels WHERE server_id = ?"
        );
        $stmt->execute([$serverId]);

        return (int) $stmt->fetchColumn();  // ✅ fetchColumn() + cast en int
    }

    /**
     * Récupérer les sections d'un serveur
     */
    public function getSectionsByServer(int $serverId): array  // ✅ Nom au pluriel
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT section 
             FROM channels 
             WHERE server_id = ? AND section IS NOT NULL
             ORDER BY section ASC"  // ✅ DISTINCT
        );
        $stmt->execute([$serverId]);

        $sections = [];
        while ($row = $stmt->fetch()) {
            $sections[] = $row['section'];  // ✅ Juste les valeurs
        }

        return $sections;  // ✅ ['TEXTE', 'VOCAL', 'ANNONCES']
    }
}