<?php
namespace App\Interfaces;

interface NotificationInterface
{
    /**
     * Envoie une notification
     * 
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $message Message
     * @return bool Succès ou non
     */
    public function send(string $to, string $subject, string $message): bool;
}