// =============================================================================
// SERVEUR.JS — Gestion des serveurs, salons et panneau membres
// =============================================================================

$(document).ready(function () {

    // ============================================
    // AFFICHAGE DES SERVEURS DE L'UTILISATEUR
    // ============================================
    function affichageDesServeurs() {
        API.getUserServers()
            .then(function (servers) {
                $('.serv').empty();

                var $mpBtn = UIComponents.createMpCard();
                $('.serv').append($mpBtn);

                $.each(servers, function (index, serveur) {
                    var $server = UIComponents.createServerCard(serveur.id, serveur.nom, serveur.icon);
                    $('.serv').append($server);
                });

                var $plusBtn = UIComponents.createAddServerCard();
                $('.serv').append($plusBtn);

                if (premierChargement) {
                    premierChargement = false;
                    $mpBtn.trigger('click');
                }
            })
            .catch(function () {
                Validation.showToast("Erreur récupération serveurs", "error", 3000);
            });
    }

    affichageDesServeurs();
    setInterval(affichageDesServeurs, 5000);

    // ============================================
    // REJOINDRE UN NOUVEAU SERVEUR (bouton +)
    // ============================================
    $('.serv').on('click', '.ajout_serv', function () {
        API.getAvailableServers()
            .then(function (servers) {
                var $modal = UIComponents.createServerModal(servers);
                $('body').append($modal);

                $('.liste-serv').on('click', '.btn-join', function () {
                    var serverId = $(this).data('server-id');

                    API.joinServer(serverId)
                        .then(function (response) {
                            if (response.status === 'success' || response.trim() === 'success') {
                                Validation.showToast('Vous avez rejoint le serveur !', 'success', 3000);
                                affichageDesServeurs();
                                $('.voile').remove();
                            } else {
                                Validation.showToast('Impossible de rejoindre le serveur', 'error', 3000);
                            }
                        })
                        .catch(function () {
                            Validation.showToast('Erreur de connexion', 'error', 3000);
                        });
                });

                $('.liste-serv').on('click', '.close-liste', function () {
                    $('.voile').remove();
                });
            })
            .catch(function () {
                Validation.showToast("Impossible de charger les serveurs disponibles", 'error', 3000);
            });
    });

    // ============================================
    // AFFICHAGE DES SALONS DU SERVEUR
    // ============================================
    $(".serv").on('click', '.btn-serv', function () {
        serveurActuel = $(this).data('serv-id');
        getMember();
        API.getChannels(serveurActuel)
            .then(function (response) {
                if (!response || response.length === 0) {
                    Validation.showToast("Aucun salon trouvé", "warning", 3000);
                    return;
                }

                $(".salon").empty();

                var $serverHeader = UIComponents.createHeaderServer(
                    response[0].server_id,
                    response[0].server_nom,
                    response[0].banner
                );
                $('.salon').append($serverHeader);

                // Organiser par section
                var sections = {};
                $.each(response, function (index, salon) {
                    var nomSection = salon.section || '';
                    if (!sections[nomSection]) sections[nomSection] = [];
                    sections[nomSection].push(salon);
                });

                $.each(sections, function (nomDeLaSection, salonsDeCetteSection) {
                    $('.salon').append(UIComponents.createSectionSeparator(nomDeLaSection));
                    $.each(salonsDeCetteSection, function (i, salon) {
                        if (salon.channel_id) {
                            $('.salon').append(UIComponents.createChannelCard(salon.channel_id, salon.channel_nom));
                        }
                    });
                });

                // Auto-sélectionner le premier salon
                var premierSalon = response.find(s => s.channel_id);
                if (premierSalon) {
                    salonActuel = premierSalon.channel_id;
                    typeActuel  = 'server';
                    nomActuel   = premierSalon.channel_nom || premierSalon.nom || 'Salon';

                    $('.btn-salon[data-salon-id="' + salonActuel + '"]').addClass('salon-actif');

                    var headerElements = UIComponents.createHeaderChannel(nomActuel);
                    $("#entete").empty().append(headerElements);
                    $("#affichage").empty();

                    recupererNouveauxMessages();
                }
            })
            .catch(function () {
                Validation.showToast("Erreur lors du chargement des salons", "error", 3000);
            });
    });

    // ============================================
    // SÉLECTION D'UN SALON
    // ============================================
    $(".salon").on('click', '.btn-salon', function () {
        $('.btn-salon').removeClass('salon-actif');
        $('.btn-conv').removeClass('salon-actif');

        salonActuel          = $(this).data('salon-id');
        nomActuel            = $(this).data('salon-nom');
        typeActuel           = 'server';
        conversationActuelle = null;

        $(this).addClass('salon-actif');

        var headerElements = UIComponents.createHeaderChannel(nomActuel);
        $("#entete").empty().append(headerElements);

        recupererNouveauxMessages();
    });

    // ============================================
    // PANNEAU MEMBRES (toggle + chargement)
    // ============================================
    $("#entete").on('click', '.btnMasque', function () {
        var actif = $(this).data('active');
        
        if (actif === 'false') {
            $(this).data('active', 'true');
            $('.info').removeClass('dispawn').addClass('show');
            $('.wrapper').removeClass('sans-info');
        } else {
            $(this).data('active', 'false');
            $('.info').removeClass('show').addClass('dispawn');
            $('.wrapper').addClass('sans-info');
            return; // Pas besoin de recharger si on ferme
        }
        if (conversationActuelle == null) getMember();
        
    });

    // ============================================
    // DÉCONNEXION
    // ============================================
    $("#entete").on('click', '#btn-deconnexion', function () {
        if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
            window.location.href = 'php/log_out.php';
        }
    });
    //=============================================
    // FONCTION UTILES
    //=============================================
    function getMember(){
        API.getMemberRole(null, serveurActuel)
            .then(function (response) {
                var membres = response.data;
                membresActuels = membres;
                if (!membres || membres.length === 0) {
                    Validation.showToast("Aucun membre trouvé", "warning", 3000);
                    return;
                }
                
                $(".info").empty();

                // Organiser par rôle
                var sections = {};
                $.each(membres, function (index, membre) {
                    var nomSection = (membre.roles && membre.roles.length > 0)
                        ? membre.roles[0].nom
                        : 'Membres';
                    if (!sections[nomSection]) sections[nomSection] = [];
                    sections[nomSection].push(membre);
                });

                // Trier par permission décroissante
                var sectionsArray = Object.entries(sections).map(function (entry) {
                    var permission = 0;
                    if (entry[1][0].roles && entry[1][0].roles.length > 0) {
                        permission = entry[1][0].roles[0].permissions;
                    }
                    return { nomSection: entry[0], membres: entry[1], permission: permission };
                });

                sectionsArray.sort(function (a, b) { return b.permission - a.permission; });

                $.each(sectionsArray, function (i, section) {
                    $('.info').append(UIComponents.createSectionSeparator(section.nomSection));
                    $.each(section.membres, function (j, membre) {
                        if (membre.user_id) {
                            $('.info').append(UIComponents.AffichageInfoServer(
                                membre.user_id,
                                membre.nickname,
                                membre.avatar
                            ));
                        }
                    });
                });
            })
            .catch(function () {
                Validation.showToast("Erreur : impossible d'afficher les membres", "error", 3000);
            });
    }
});
window.getMember = getMember;