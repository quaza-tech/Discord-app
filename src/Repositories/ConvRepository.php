<?php

namespace App\Repositories;

use App\Models\Conv;
use PDO;
class ConvRepository
{

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

    }
    public function findOrCreateConv(int $userID_1, int $userID_2)
    {
        // 1. Chercher la conversation existante
        $stmt = $this->pdo->prepare(
            "SELECT * FROM conversations 
         WHERE (user1_id = ? AND user2_id = ?)
         OR (user1_id = ? AND user2_id = ?)"
        );
        $stmt->execute([$userID_1, $userID_2, $userID_2, $userID_1]);
        $data = $stmt->fetch();

        // 2. Si elle n'existe pas, la créer
        if (!$data) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO conversations (user1_id, user2_id) VALUES(?, ?)"
            );
            $stmt->execute([$userID_1, $userID_2]);

            //Récupérer l'ID de la conversation créée
            $newId = $this->pdo->lastInsertId();

            // Récupérer la conversation complète
            $stmt = $this->pdo->prepare("SELECT * FROM conversations WHERE id = ?");
            $stmt->execute([$newId]);
            $data = $stmt->fetch();
        }

        return $data;
    }
    public function findByUser($userid)
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
        c.id,
        c.user1_id,
        c.user2_id,
        c.created_at,
        u.id as userid,
        u.nom as nom,
        u.avatar as avatar,
        COUNT(CASE WHEN pm.sender_id != ? AND pm.is_read = FALSE THEN 1 END) as unread_count
     FROM conversations c
     JOIN users u ON (
        CASE 
            WHEN c.user1_id = ? THEN c.user2_id 
            ELSE c.user1_id 
        END = u.id
     )
     LEFT JOIN private_messages pm ON pm.conversation_id = c.id
     WHERE c.user1_id = ? OR c.user2_id = ?
     GROUP BY c.id, c.user1_id, c.user2_id, c.created_at, u.id, u.nom, u.avatar
     ORDER BY MAX(pm.date) DESC"
        );
        $stmt->execute([$userid, $userid, $userid, $userid]);
        return $stmt->fetchAll();
    }

    public function isUserInConv(int $userId, int $convID): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM conversations 
         WHERE (user1_id = ? AND id = ?)
         OR (user2_id = ? AND id = ?)"
        );
        $stmt->execute([$userId, $convID]);
        return !empty($stmt->fetchAll());
    }
    public function markAsRead(int $convId, int $userId)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE private_messages 
         SET is_read = TRUE 
         WHERE conversation_id = ? 
         AND sender_id != ? 
         AND is_read = FALSE"
        );
        $stmt->execute([$convId, $userId]);
    }
    public function sendMP(int $userid, int $convID, string $texte)
    {
        $stmt = $this->pdo->prepare("INSERT INTO private_messages(conversation_id,sender_id,texte,date) VALUES (?,?,?,CURRENT_TIMESTAMP)");
        $result = $stmt->execute([$convID, $userid, $texte]);


        return $result;
    }
    public function recup_Mp($idconv)
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.id as id,
                p.conversation_id,
                p.sender_id,
                p.texte,
                p.date,
                p.is_read,
                u.nom as nom,
                u.avatar as avatar
            FROM private_messages p
            INNER JOIN users u ON p.sender_id = u.id 
            INNER JOIN conversations c ON p.conversation_id = c.id
            WHERE c.id = ?
            ORDER BY p.date ASC"
        );
        $stmt->execute([$idconv]);

        $resultat = $stmt->fetchAll();
        return $resultat;
    }
    // Modifier un message
    public function update(int $messageId, int $userId, string $nouveauTexte): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE private_messages SET texte = ? WHERE id = ? AND sender_id = ?"
        );
        $stmt->execute([$nouveauTexte, $messageId, $userId]);

        return $stmt->rowCount() > 0;
    }

    // Supprimer un message
    public function delete(int $messageId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM private_messages WHERE id = ? AND sender_id = ?"
        );
        $stmt->execute([$messageId, $userId]);

        return $stmt->rowCount() > 0;
    }

}

?>