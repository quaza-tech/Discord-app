const Permissions = (function () {
    'use strict';

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
    const GERER_ROLES = 2048;

    function calculerTotal(tableauRole){
        var init = 0;
        tableauRole.forEach(role => {
            init = init | role.permissions;
        });
        return init;
    }
    function aPermission(total, permissionDemandee) {
        if ((total & ADMINISTRATEUR) === ADMINISTRATEUR) {
            return true;
        }

        return (total & permissionDemandee) === permissionDemandee;
    }

    return {
        LIRE_MESSAGES : LIRE_MESSAGES,
        ENVOYER_MESSAGES : ENVOYER_MESSAGES,
        GERER_MESSAGES : GERER_MESSAGES,
        INVITER_MEMBRES : INVITER_MEMBRES,
         GERER_SALONS : GERER_SALONS,
         GERER_SERVEUR : GERER_SERVEUR,
         MENTIONNER_TOUS : MENTIONNER_TOUS,
         SUPPRIMER_MESSAGES : SUPPRIMER_MESSAGES,
         EXCLURE_MEMBRES : EXCLURE_MEMBRES,
         BANNIR_MEMBRES : BANNIR_MEMBRES,
         ADMINISTRATEUR : ADMINISTRATEUR,
         GERER_ROLES : GERER_ROLES,
         calculerTotalRole : calculerTotal,
         aPerm : aPermission
    };

})()