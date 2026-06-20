<?php
namespace App\Services;

use App\Interfaces\NotificationInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService implements NotificationInterface
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = '0cc9fe8d9e064a';  // TON USERNAME MAILTRAP
        $this->mailer->Password = 'ad6831cee0a63c';  // TON PASSWORD MAILTRAP
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 2525;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom('noreply@discordclone.com', 'Discord Clone');
    }

    public function send(string $to, string $subject, string $message): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Erreur envoi email : " . $e->getMessage());
            return false;
        }
    }

    // Méthode spécifique pour reset password
    public function sendPasswordReset(string $email, string $userName, string $token): bool
    {
        $resetLink = 'http://localhost:8000/php/reset_password.php?token=' . $token;

        $message = "<style>
            body {
                margin: 0;
                padding: 0;
                font-family: 'Whitney', 'Helvetica Neue', Helvetica, Arial, sans-serif;
                background-color: #313338;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            } 
            .login-box {
                background-color: #313338;
                color: #f2f3f5;
                padding: 32px;
                border-radius: 8px;
                box-shadow: 0 2px 10px 0 rgba(0,0,0,0.2);
                width: 400px;
                text-align: center;
            }
            h2 {
                font-size: 24px;
                font-weight: 600;
                margin-bottom: 8px;
                color: #ffffff;
            }
            p {
                color: #b5bac1;
                font-size: 16px;
                margin-bottom: 20px;
            }
        </style>
        <body>
            <div class='login-box'>
                <h2>Lien d'accès au compte</h2>
                <p>Bonjour <strong>" . htmlspecialchars($userName) . "</strong>,</p>
                <p>Suite à une demande de changement de mot de passe, notre équipe vous envoie ce mail afin de vous octroyer la possibilité de vous connecter à votre compte. Nous vous conseillons de changer votre mot de passe dans vos paramètres de compte.</p>
                <p style='text-align: center;'>
                    <a href='" . $resetLink . "' style='display: inline-block; background-color: #5865f2; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold;'>
                        Accéder à mon compte
                    </a>
                </p>
                <p style='color: #72767d; font-size: 12px;'>Ce lien expire dans 1 heure.</p>
                <p style='color: #72767d; font-size: 11px;'>⚠️ Attention : ce lien invalide automatiquement tous les autres liens de récupération.</p>
            </div>
        </body>
        ";
        return $this->send($email, "Lien d'accès au compte", $message);
    }
}