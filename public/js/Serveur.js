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

                var $plusBtn = UIComponents.createServerCard(0,"ajout",'compas.png');
                $('.serv').append($plusBtn);

                var $createBtn = UIComponents.createAddServerCard();
                $('.serv').append($createBtn);

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

    //=============================================
    // FORMULAIRE DE CREATION DE SERVEUR
    //=============================================
    $('.serv').on('click','.create_serv',function() {
        var modalServer = UIComponents.createServer();

        $("body").append(modalServer);
        bindServerModalEvents(modalServer);

        function bindServerModalEvents($voile) {
        
            // Ouvrir l'explorateur au clic sur les zones
            $voile.find('#banner-zone').on('click', function () {
                $voile.find('#banner-input').trigger('click');
            });
        
            $voile.find('.banner-edit-btn').on('click', function (e) {
                e.stopPropagation();
                $voile.find('#banner-input').trigger('click');
            });
        
            $voile.find('#icon-zone').on('click', function () {
                $voile.find('#icon-input').trigger('click');
            });
        
            // Prévisualisation icône
            $voile.find('#icon-input').on('change', function (e) {
                previewImage(e.target.files[0], $voile.find('#icon-preview'), $voile.find('#icon-placeholder'));
            });
        
            // Prévisualisation bannière
            $voile.find('#banner-input').on('change', function (e) {
                previewImage(e.target.files[0], $voile.find('#banner-preview'), $voile.find('#banner-placeholder'));
            });
        
            //Compteur de caractères + activation bouton Créer
            $voile.find('#server-name').on('input', function () {
                var len = $(this).val().length;
                $voile.find('#name-count').text(len);
                $voile.find('#submit-btn').prop('disabled', len === 0);
            });
        
            // Annuler : ferme la modale
            $voile.find('#cancel-btn').on('click', function () {
                closeServerModal($voile);
            });
        
            // Fermer en cliquant sur le voile (en dehors de la modale)
            $voile.on('click', function (e) {
                if (e.target === this) {
                    closeServerModal($voile);
                }
            });
        
            // Soumission
            $voile.find('#submit-btn').on('click', function () {
                submitServerForm($voile);
            });
        }
        
        /**
         * Lit un fichier image et l'affiche dans $imgEl, masque $placeholderEl.
         */
        function previewImage(file, $imgEl, $placeholderEl) {
            if (!file) return;
        
            var reader = new FileReader();
            reader.onload = function (event) {
                $imgEl.attr('src', event.target.result).show();
                $placeholderEl.hide();
            };
            reader.readAsDataURL(file);
        }
        
        /**
         * Ferme et retire la modale du DOM.
         */
        function closeServerModal($voile) {
            $voile.remove();
        }
        
        /**
         * Récupère les données du formulaire et les envoie au serveur.
         * Adapte l'URL / le traitement selon ton API (php/api/...).
         */
        function submitServerForm($voile) {
            var name = $voile.find('#server-name').val().trim();
            var iconFile = $voile.find('#icon-input')[0].files[0];
            var bannerFile = $voile.find('#banner-input')[0].files[0];
        
            if (!name) return;
        
            var formData = new FormData();
            formData.append('nom', name);
            if (iconFile) formData.append('icon', iconFile);
            if (bannerFile) formData.append('banner', bannerFile);
            
            API.createServer(formData)
            .then(function(reponse) {
                if (reponse.status === 'success') {
                    Validation.showToast('Vous avez creer le serveur !', 'success', 3000);
                    affichageDesServeurs();
                    $('.voile').remove();
                } else {
                    Validation.showToast(reponse.message, 'error', 3000);
                }
            })
            .catch(function () {
                Validation.showToast('Erreur de connexion', 'error', 3000);
            })
        }
        
        
    })
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
    window.getMember = getMember;
});
