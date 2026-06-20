<?php
namespace App\Repositories;

use App\Models\Message;
use PDO;
use DateTime;

class MessageRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Créer un message
    public function create(int $userId, int $channelId, string $texte): Message
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO dis_messages (mes_texte, mes_date, mes_users, mes_channel) 
             VALUES (?, CURRENT_TIMESTAMP, ?, ?) 
             RETURNING mes_id, mes_date"
        );
        $stmt->execute([$texte, $userId, $channelId]);

        $result = $stmt->fetch();

        return new Message(
            (int) $result['mes_id'],
            $texte,
            new DateTime($result['mes_date']),
            $userId,
            $channelId
        );
    }

    // Récupérer les messages d'un salon
    public function findByChannel(int $channelId, int $serverId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dis_messages.mes_id, dis_messages.mes_texte, dis_messages.mes_date, 
                    dis_messages.mes_users, dis_messages.mes_channel,users.id,
                    users.nom, users.avatar
             FROM dis_messages 
             INNER JOIN users ON dis_messages.mes_users = users.id 
             INNER JOIN channels ON dis_messages.mes_channel = channels.id
             WHERE channels.id = ? AND channels.server_id = ?
             ORDER BY dis_messages.mes_date ASC"
        );
        $stmt->execute([$channelId, $serverId]);

        $messages = [];
        while ($data = $stmt->fetch()) {
            $messages[] = Message::fromArray($data);
        }

        return $messages;
    }

    // Modifier un message
    public function update(int $messageId, int $userId, string $nouveauTexte): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE dis_messages SET mes_texte = ? WHERE mes_id = ? AND mes_users = ?"
        );
        $stmt->execute([$nouveauTexte, $messageId, $userId]);

        return $stmt->rowCount() > 0;
    }

    // Supprimer un message
    public function delete(int $messageId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM dis_messages WHERE mes_id = ? AND mes_users = ?"
        );
        $stmt->execute([$messageId, $userId]);

        return $stmt->rowCount() > 0;
    }
}