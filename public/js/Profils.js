// =============================================================================
// SERVEUR.JS — Gestion des serveurs, salons et panneau membres
// =============================================================================

$(document).ready(function () {

    $('.info').on('click','.btn-cardUser',function(){
        var User = $(this).data('member-id');
        

        API.getUser(User,serveurActuel).then(function(reponse){
            
            var user = reponse.data;

            if (!user || user.length === 0) {
                Validation.showToast("Aucun profils à ce compte", "warning", 3000);
                return;
            }

            //Calcul des Permissions de l'utilisateur actuel

            mesPerm = membresActuels.find(m => m.user_id == userActuel);
            monTotal = Permissions.calculerTotalRole(mesPerm);
            canManageRoles = Permissions.aPermission(monTotal, Permissions.GERER_ROLES);

            //Role du membre cliqué et role du serveur
            if (canManageRoles) {
                Role = membresActuels.find(m => m.user_id == User);
                RoleDispo = API.getServerRoles(serveurActuel);
            }
            
            var $card = UIComponents.InfoUser(user.id, user.nickname, user.nom, user.avatar, user.banner, user.bios,hasPermission,Role ?? null ,RoleDispo ?? null);
            $('body').append($card);
        }).catch(function(){

        })
    })
});