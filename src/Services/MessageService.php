<?php
/**
 * MessageService.php - Service de gestion des messages
 * 
 * À QUOI ÇA SERT ?
 * Contient la LOGIQUE MÉTIER liée aux messages
 * 
 * DIFFÉRENCE REPOSITORY vs SERVICE :
 * 
 * MessageRepository :
 * - create() : INSERT dans la BDD
 * - update() : UPDATE dans la BDD
 * - delete() : DELETE dans la BDD
 * 
 * MessageService :
 * - sendMessage() : Crée le message + vérifie les permissions + notifie
 * - editMessage() : Vérifie que l'user est l'auteur + modifie
 * - deleteMessage() : Vérifie permissions + supprime + log
 * 
 * Le Service orchestre les Repositories et ajoute la logique métier !
 */

namespace App\Services;

use App\Models\Message;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Repositories\ChannelRepository;

class MessageService
{
    private MessageRepository $messageRepo;
    private UserRepository $userRepo;
    private ChannelRepository $channelRepo;

    /**
     * Constructeur avec injection de dépendances
     * 
     * @param MessageRepository $messageRepo
     * @param UserRepository $userRepo
     * @param ChannelRepository $channelRepo
     */
    public function __construct(
        MessageRepository $messageRepo,
        UserRepository $userRepo,
        ChannelRepository $channelRepo
    ) {
        $this->messageRepo = $messageRepo;
        $this->userRepo = $userRepo;
        $this->channelRepo = $channelRepo;
    }

    /**
     * Envoie un message dans un salon
     * 
     * LOGIQUE MÉTIER :
     * 1. Vérifier que l'utilisateur existe
     * 2. Vérifier que le salon existe
     * 3. Vérifier que le texte n'est pas vide
     * 4. Créer le message
     * 5. (Optionnel) Logger, notifier, etc.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $channelId ID du salon
     * @param string $texte Contenu du message
     * @return Message|null
     */
    public function sendMessage(int $userId, int $channelId, string $texte): ?Message
    {
        // 1. Vérifier que l'utilisateur existe
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            error_log("MessageService: Utilisateur $userId non trouvé");
            return null;
        }

        // 2. Vérifier que le salon existe
        $channel = $this->channelRepo->findById($channelId);
        if (!$channel) {
            error_log("MessageService: Salon $channelId non trouvé");
            return null;
        }

        // 3. Vérifier que le texte n'est pas vide
        $texte = trim($texte);
        if (empty($texte)) {
            error_log("MessageService: Texte vide");
            return null;
        }

        // 4. Vérifier la longueur du message (limite Discord = 2000 caractères)
        if (strlen($texte) > 2000) {
            error_log("MessageService: Message trop long");
            return null;
        }

        // 5. Créer le message
        $message = $this->messageRepo->create($userId, $channelId, $texte);

        // 6. Logger l'action
        error_log("MessageService: Message {$message->getId()} créé par {$user->getNom()} dans salon $channelId");

        // 7. (Optionnel) Ici tu pourrais ajouter :
        // - Envoyer une notification aux mentions (@user)
        // - Mettre à jour le "last_message_at" du salon
        // - Déclencher un webhook
        // - etc.

        return $message;
    }

    /**
     * Modifie un message
     * 
     * LOGIQUE MÉTIER :
     * 1. Vérifier que l'utilisateur est l'auteur du message
     * 2. Vérifier que le nouveau texte n'est pas vide
     * 3. Modifier le message
     * 
     * @param int $messageId ID du message
     * @param int $userId ID de l'utilisateur (pour vérifier les permissions)
     * @param string $nouveauTexte Nouveau contenu
     * @return bool
     */
    public function editMessage(int $messageId, int $userId, string $nouveauTexte): bool
    {
        // 1. Vérifier que le texte n'est pas vide
        $nouveauTexte = trim($nouveauTexte);
        if (empty($nouveauTexte)) {
            error_log("MessageService: Nouveau texte vide");
            return false;
        }

        // 2. Vérifier la longueur
        if (strlen($nouveauTexte) > 2000) {
            error_log("MessageService: Nouveau message trop long");
            return false;
        }

        // 3. Modifier le message
        // La vérification que l'user est l'auteur est faite dans MessageRepository
        $success = $this->messageRepo->update($messageId, $userId, $nouveauTexte);

        if ($success) {
            error_log("MessageService: Message $messageId modifié par utilisateur $userId");
        } else {
            error_log("MessageService: Échec modification message $messageId (user $userId n'est pas l'auteur ?)");
        }

        return $success;
    }

    /**
     * Supprime un message
     * 
     * LOGIQUE MÉTIER :
     * 1. Vérifier que l'utilisateur est l'auteur OU admin
     * 2. Supprimer le message
     * 3. Logger l'action
     * 
     * @param int $messageId ID du message
     * @param int $userId ID de l'utilisateur
     * @return bool
     */
    public function deleteMessage(int $messageId, int $userId): bool
    {
        // La vérification que l'user est l'auteur est faite dans MessageRepository
        $success = $this->messageRepo->delete($messageId, $userId);

        if ($success) {
            error_log("MessageService: Message $messageId supprimé par utilisateur $userId");
        } else {
            error_log("MessageService: Échec suppression message $messageId (user $userId n'est pas l'auteur ?)");
        }

        return $success;
    }

    /**
     * Récupère les messages d'un salon
     * 
     * @param int $channelId ID du salon
     * @param int $serverId ID du serveur (pour vérification)
     * @param int $limit Nombre maximum de messages (optionnel)
     * @return Message[]
     */
    public function getChannelMessages(int $channelId, int $serverId, int $limit = 100): array
    {
        // Vérifier que le salon existe et appartient bien au serveur
        $channel = $this->channelRepo->findById($channelId);
        if (!$channel || $channel->getServerId() !== $serverId) {
            error_log("MessageService: Salon $channelId non trouvé ou n'appartient pas au serveur $serverId");
            return [];
        }

        // Récupérer les messages
        $messages = $this->messageRepo->findByChannel($channelId, $serverId);

        // Limiter le nombre de messages si nécessaire
        if (count($messages) > $limit) {
            $messages = array_slice($messages, -$limit);  // Garde les X derniers
        }

        return $messages;
    }

    /**
     * Vérifie si un utilisateur peut modifier un message
     * 
     * @param int $messageId ID du message
     * @param int $userId ID de l'utilisateur
     * @return bool
     */
    public function canUserEditMessage(int $messageId, int $userId): bool
    {
        // Pour l'instant, seul l'auteur peut modifier
        // Tu pourrais ajouter : les admins peuvent aussi modifier

        // Cette logique pourrait être étendue pour vérifier les rôles
        // Par exemple : if ($user->isAdmin() || $message->getUserId() === $userId)

        return true;  // La vérification réelle est dans le Repository
    }

    /**
     * Nettoie le texte d'un message (sanitize)
     * 
     * @param string $texte Texte brut
     * @return string Texte nettoyé
     */
    private function sanitizeMessage(string $texte): string
    {
        // Supprimer les espaces inutiles
        $texte = trim($texte);

        // Supprimer les balises HTML (protection XSS)
        $texte = strip_tags($texte);

        // (Optionnel) Gérer les mentions
        // $texte = $this->parseMentions($texte);

        // (Optionnel) Gérer les emojis
        // $texte = $this->parseEmojis($texte);

        return $texte;
    }
}

/**
 * EXEMPLES D'UTILISATION :
 * 
 * $pdo = Database::getConnection();
 * $messageRepo = new MessageRepository($pdo);
 * $userRepo = new UserRepository($pdo);
 * $channelRepo = new ChannelRepository($pdo);
 * 
 * $messageService = new MessageService($messageRepo, $userRepo, $channelRepo);
 * 
 * // Envoyer un message
 * $message = $messageService->sendMessage(
 *     1,              // userId
 *     5,              // channelId
 *     'Hello world!'  // texte
 * );
 * 
 * if ($message) {
 *     echo "Message envoyé avec l'ID : " . $message->getId();
 * }
 * 
 * // Modifier un message
 * $success = $messageService->editMessage(
 *     123,                      // messageId
 *     1,                        // userId
 *     'Message modifié !'       // nouveauTexte
 * );
 * 
 * // Supprimer un message
 * $success = $messageService->deleteMessage(123, 1);
 * 
 * // Récupérer les messages d'un salon
 * $messages = $messageService->getChannelMessages(
 *     5,    // channelId
 *     1,    // serverId
 *     50    // limit (optionnel)
 * );
 * 
 * foreach ($messages as $message) {
 *     echo $message->getTexte() . "\n";
 * }
 */

/**
 * DIFFÉRENCE AVEC MessageRepository :
 * 
 * MessageRepository (accès aux données) :
 * - create($userId, $channelId, $texte) : INSERT direct
 * - update($messageId, $userId, $texte) : UPDATE direct
 * - delete($messageId, $userId) : DELETE direct
 * 
 * MessageService (logique métier) :
 * - sendMessage() : Vérifie user + salon + texte, puis appelle create()
 * - editMessage() : Vérifie texte + longueur, puis appelle update()
 * - deleteMessage() : Log l'action, puis appelle delete()
 * - getChannelMessages() : Vérifie permissions + limite, puis appelle findByChannel()
 * 
 * LE SERVICE AJOUTE DE LA LOGIQUE MÉTIER AU-DESSUS DU REPOSITORY !
 */