$(document).ready(function () {
    //===============================
    // AFFICHAGE DES MP DU USER
    //===============================

    $(".serv").on('click', '.mp', function () {
        $(".salon").empty();
        $(".salon").append(UIComponents.createHeaderServer(null, "MP", "default.jpg"));

        API.getConv()
            .then(function (conversations) {
                if (!conversations || conversations.length === 0) {
                    Validation.showToast("Aucun MP trouvé", "warning", 3000);
                    return;
                }
                
                $.each(conversations, function (i, conv) {
                    if (conv.id) {
                        var $convCard = UIComponents.createConvCard(conv.id, conv.nom, conv.avatar, conv.userid);
                        $('.salon').append($convCard);
                    }
                });

                var premierConv   = conversations.find(s => s.id);
                var premierUserId = premierConv ? premierConv.userid : null;

                if (premierConv) {
                    conversationActuelle = premierConv.id;
                    typeActuel           = 'mp';
                    serveurActuel        = null;
                    salonActuel          = null;

                    $('.btn-MP[data-conv-id="' + conversationActuelle + '"]').addClass('salon-actif');

                    nomActuel = premierConv.nom || 'MP';
                    $("#entete").empty().append(UIComponents.createHeaderChannel(nomActuel));

                    recupererNouveauxMessages();

                    if (premierUserId) {
                        API.getUser(premierUserId).then(function (reponse) {
                            var user = reponse.data;
                            if (!user) {
                                Validation.showToast("Aucun profil trouvé", "warning", 3000);
                                return;
                            }
                            var $card = UIComponents.InfoUserMp(
                                user.id, user.nickname, user.nom,
                                user.avatar, user.banner, user.bios
                            );
                            $('.info').empty().append($card);
                        }).catch(function () {
                            Validation.showToast('ERREUR : impossible de récupérer les infos du user', 'error');
                        });
                    }  
                }      
            })         
            .catch(function () {
                Validation.showToast("Erreur : impossible de récupérer les MP", "error", 3000);
            });
    });

    // ============================================
    // SÉLECTION D'UNE CONVERSATION MP
    // ============================================
    $(".salon").on('click', '.btn-MP', function () {
        $('.btn-salon').removeClass('salon-actif');
        $('.btn-conv').removeClass('salon-actif');

        conversationActuelle = $(this).data('conv-id');
        nomActuel            = $(this).data('conv-nom');
        typeActuel           = 'mp';
        serveurActuel        = null;
        salonActuel          = null;

        var userId = $(this).data('conv-userid');  
        $(this).addClass('salon-actif');

        $("#entete").empty().append(UIComponents.createHeaderChannel(nomActuel));
        recupererNouveauxMessages();

        if (userId) {
            API.getUser(userId).then(function (reponse) {
                var user = reponse.data;
                if (!user) {
                    Validation.showToast("Aucun profil à ce compte", "warning", 3000);
                    return;
                }
                var $card = UIComponents.InfoUserMp(
                    user.id, user.nickname, user.nom,
                    user.avatar, user.banner, user.bios
                );
                $('.info').empty().append($card);
            }).catch(function () {
                Validation.showToast('ERREUR : impossible de récupérer les infos du user', 'error');
            });
        }
    });

    //=============================
    // AFFICHAGE DES AMIS (SERT DANS SERVEUR (INVITE D'AMIS ) ET MP (AFFICHAGE AMIS))
    //=============================

    $('.salon').on('click','.addFriendServer',function(){
        if (conversationActuelle != null){
            
        } else {

        }
    })

});