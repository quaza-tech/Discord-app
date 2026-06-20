<?php
namespace App\Repositories;

use PDO;

class ServerRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Serveurs de l'utilisateur
    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.id, s.nom, s.description, s.icon, s.banner
             FROM servers s
             INNER JOIN server_members sm ON s.id = sm.server_id
             WHERE sm.user_id = ?
             ORDER BY s.id ASC"
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    // Serveurs disponibles (pas encore membre)
    public function findAvailableForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.id, s.nom, s.description, s.icon, s.banner, s.created_at
             FROM servers s
             LEFT JOIN server_members sm ON s.id = sm.server_id AND sm.user_id = ?
             WHERE sm.user_id IS NULL
             ORDER BY s.created_at DESC"
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    // Vérifier si user est membre
    public function isUserMember(int $userId, int $serverId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM server_members WHERE user_id = ? AND server_id = ?"
        );
        $stmt->execute([$userId, $serverId]);

        return $stmt->fetchColumn() > 0;
    }

    // Ajouter un membre
    public function addMember(int $userId, int $serverId, string $nickname): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO server_members (user_id, server_id, nickname, joined_at) 
             VALUES (?, ?, ?, CURRENT_TIMESTAMP)"
        );

        return $stmt->execute([$userId, $serverId, $nickname]);
    }

    // Récupérer les salons d'un serveur
    public function getChannels(int $serverId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
                s.id AS server_id, s.nom AS server_nom, s.description, s.icon, s.banner,
                c.id AS channel_id, c.nom AS channel_nom, c.description AS channel_des, 
                c.type, c.position, c.section
             FROM servers s
             LEFT JOIN channels c ON s.id = c.server_id
             WHERE s.id = ?
             ORDER BY c.section ASC, c.position ASC"
        );
        $stmt->execute([$serverId]);

        return $stmt->fetchAll();
    }
    public function getMembre(int $serverid): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
            s.user_id,
            s.nickname,
            u.avatar,
            r.id AS role_id,
            r.nom AS role_nom,
            r.couleur,
            r.permissions
        FROM server_members s
        INNER JOIN users u ON s.user_id = u.id
        LEFT JOIN server_members_roles smr ON smr.member_id = s.id
        LEFT JOIN roles r ON r.id = smr.role_id
        WHERE s.server_id = ?
        ORDER BY r.permissions DESC"
        );
        $stmt->execute([$serverid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Regrouper les rôles par membre
        $membres = [];
        foreach ($rows as $row) {
            $uid = $row['user_id'];

            if (!isset($membres[$uid])) {
                $membres[$uid] = [
                    'user_id' => $uid,
                    'nickname' => $row['nickname'],
                    'avatar' => $row['avatar'],
                    'roles' => []
                ];
            }

            // Ajouter le rôle seulement si le membre en a un
            if ($row['role_id'] !== null) {
                $membres[$uid]['roles'][] = [
                    'id' => $row['role_id'],
                    'nom' => $row['role_nom'],
                    'couleur' => $row['couleur'],
                    'permissions' => $row['permissions']
                ];
            }
        }

        return array_values($membres);
    }
}