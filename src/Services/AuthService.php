<?php
namespace App\Services;

use App\Models\Users;
use App\Repositories\UserRepository;
use App\Repositories\ServerRepository;
use App\Services\EmailNotificationService;
use App\Interfaces\NotificationInterface;

class AuthService
{
    private UserRepository $userRepo;
    private ServerRepository $serverRepo;
    private ?NotificationInterface $notificationService;

    // Injection de dépendances
    public function __construct(
        UserRepository $userRepo,
        ServerRepository $serverRepo,
        ?NotificationInterface $notificationService = null
    ) {
        $this->userRepo = $userRepo;
        $this->serverRepo = $serverRepo;
        $this->notificationService = $notificationService;
    }

    // Connexion
    public function login(string $email, string $password): ?Users
    {
        // 1. Vérifier les identifiants
        $user = $this->userRepo->verifyCredentials($email, $password);

        if (!$user)
            return null;

        // 2. Créer la session
        $this->createSession($user);

        // 3. Ajouter au serveur par défaut si nécessaire
        $this->ensureUserInDefaultServer($user->getId(), $user->getNom());

        return $user;
    }

    // Inscription
    public function register(string $nom, string $email, string $password): ?Users
    {
        // Vérifier si existe déjà
        if ($this->userRepo->existsByEmailOrName($email, $nom)) {
            return null;
        }

        // Créer l'utilisateur
        return $this->userRepo->create($nom, $email, $password);
    }

    // Demande de reset password
    public function requestPasswordReset(string $email): bool
    {
        // Chercher l'utilisateur
        $user = $this->userRepo->findByEmail($email);

        if (!$user)
            return false;

        // Invalider les anciens tokens
        $this->userRepo->invalidateAllResetTokens();

        // Générer un nouveau token
        $token = bin2hex(random_bytes(32));

        // Stocker le token
        $this->userRepo->setResetToken($user->getId(), $token);

        // Envoyer l'email
        if ($this->notificationService && $this->notificationService instanceof EmailNotificationService) {
            return $this->notificationService->sendPasswordReset(
                $user->getEmail(),
                $user->getNom(),
                $token
            );
        }

        return true;
    }

    // Reset avec token
    public function resetPasswordWithToken(string $token): ?Users
    {
        // Chercher l'utilisateur
        $user = $this->userRepo->findByValidResetToken($token);

        if (!$user)
            return null;

        // Détruire l'ancienne session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        session_start();

        // Créer la nouvelle session
        $this->createSession($user);

        // Supprimer le token
        $this->userRepo->clearResetToken($user->getId());

        return $user;
    }

    // Déconnexion
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    // Créer la session
    private function createSession(Users $user): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user'] = $user->getId();
        $_SESSION['username'] = $user->getNom();
    }

    // Ajouter au serveur par défaut
    private function ensureUserInDefaultServer(int $userId, string $username): void
    {
        if (!$this->serverRepo->isUserMember($userId, 1)) {
            $this->serverRepo->addMember($userId, 1, $username);
        }
    }
}