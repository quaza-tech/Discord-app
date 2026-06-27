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
            monTotal = Permissions.calculerTotalRole(mesPerm.roles);
            canManageRoles = Permissions.aPermission(monTotal, Permissions.GERER_ROLES);

            //Role du membre cliqué et role du serveur
            if (canManageRoles) {
                Role = membresActuels.find(m => m.user_id == User).roles;
                RoleDispo = reponseRoles.data
            }
            
            var $card = UIComponents.InfoUser(user.id, user.nickname, user.nom, user.avatar, user.banner, user.bios,canManageRoles,RoleDispo ?? null ,Role ?? null);
            
            $('body').append($card);

            //Clique sur l'addition de role (Carte de l'utilisateur sur un serveur )

            $('.user-card').on('click','.user-card-btn.third',function() {
                var $roleMember = UIComponents.createRoleModal(RoleDispo,Role,user.id);

                $('body').append($roleMember);

                $('.checkBoxContainer').on('change','.checkRole',function() {
                    var roleId = $(this).data('role-id');
                    var estCochee = $(this).prop('checked');
                    var user = $(this).closest('.voile').data('user-id')
                    if (estCochee)
                        API.assignRole(user,serveurActuel,roleId);
                    else
                        API.removeRole(user,serveurActuel,roleId);
                })
            })
            
        });
    });

    

    
});