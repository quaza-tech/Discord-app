<?php

namespace App;
class Permissions
{
    const LIRE_MESSAGES = 1;
    const ENVOYER_MESSAGES = 2;
    const GERER_MESSAGES = 4;
    const INVITER_MEMBRES = 8;
    const GERER_SALONS = 16;
    const GERER_SERVEUR = 32;
    const MENTIONNER_TOUS = 64;
    const SUPPRIMER_MESSAGES = 128;
    const EXCLURE_MEMBRES = 256;
    const BANNIR_MEMBRES = 512;
    const ADMINISTRATEUR = 1024;

    public static function hasPermission($totalPermissions, $permissionRequis): bool
    {
        if (($totalPermissions & self::ADMINISTRATEUR) === self::ADMINISTRATEUR) {
            return true;
        }

        return ($totalPermissions & $permissionRequis) === $permissionRequis;
    }
}
?>