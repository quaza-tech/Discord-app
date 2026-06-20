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
            var $card = UIComponents.InfoUser(user.id, user.nickname, user.nom, user.avatar, user.banner, user.bios);
            $('body').append($card);
        }).catch(function(){

        })
    })
});