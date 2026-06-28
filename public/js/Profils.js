// =============================================================================
// SERVEUR.JS — Gestion des serveurs, salons et panneau membres
// =============================================================================

$(document).ready(function () {

    $('.info').on('click','.btn-cardUser',function(){
        var User = $(this).data('member-id');
        
        Promise.all([API.getUser(User, serveurActuel), API.getServerRoles(serveurActuel)])
        .then(function([reponseUser, reponseRoles]) {
            var user = reponseUser.data;

            if (!user || user.length === 0) {
                Validation.showToast("Aucun profils à ce compte", "warning", 3000);
                return;
            }

            //Calcul des Permissions de l'utilisateur actuel

            mesPerm = membresActuels.find(m => m.user_id == userActuel);
            monTotal = Permissions.calculerTotal(mesPerm.roles);
            canManageRoles = Permissions.aPermission(monTotal, Permissions.GERER_ROLES);

            //Role du membre cliqué et role du serveur
            Role = null
            RoleDispo = null
            if (canManageRoles) {
                Role = membresActuels.find(m => m.user_id == User).roles;
                RoleDispo = reponseRoles.data
            }
            
            var $card = UIComponents.InfoUser(user.id, user.nickname, user.nom, user.avatar, user.banner, user.bios,canManageRoles,RoleDispo ,Role);
            
            $('body').append($card);

            //Clique sur l'addition de role (Carte de l'utilisateur sur un serveur )

            
            
        });
    });
    $('body').on('click','.user-card-btn.third',function() {
    var $roleMember = UIComponents.createRoleModal(RoleDispo,Role,$(this).data('id'));

    $('body').append($roleMember);
    })
    $('body').on('change','.checkRole',function() {
        var roleId = $(this).data('role-id');
        var estCochee = $(this).prop('checked');
        var user = $(this).closest('.voile').data('user-id')
        if (estCochee)
            API.assignRole(user,serveurActuel,roleId)
            .then(function(reponse) {
                if (reponse.status.trim() == 'error')
                    Validation.showToast("Erreur lors de l'assignation : "+reponse.message,"error");
                else {
                    Validation.showToast(reponse.message,"success");
                    getMember();
                }
            })

            .catch(function () {
                Validation.showToast("Erreur lors de l'assignation", 'error', 3000);
            });
        else
            API.removeRole(user,serveurActuel,roleId)
            .then(function(reponse) {
                if (reponse.status.trim() == 'error')
                    Validation.showToast("Erreur lors de la destitution : "+reponse.message,"error");
                else {
                    Validation.showToast(reponse.message,"success");
                    getMember();
                }
            })
            .catch(function () {
                Validation.showToast("Erreur lors de la destitution", 'error', 3000);
            });
        })
});

