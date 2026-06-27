const UIComponents = (function() {
    'use strict';
    function createMpCard(){
        var $button = $('<button>',{class : 'mp'});
        var $container = $('<div>',{class : 'container-icon-serv'});
        var $icon = $('<img>',{class : 'icon-serv',src : "./img/avatars/default.png"})

        $container.append($icon); $button.append($container);

        return $button;
    }
    function createMpHeader(){
        var $button = $('<button>', {
            class: 'btn-salon',
        });
        var $imgFriends = $('<img>', {
            src: "img/action/addFriends.png",
            class: 'addFriends'
        }); 
        
        var $texte = $('<h2>').text('Amis');
        
        $button.append($imgFriends)
        $button.append($texte);

        return $button;
    }
    function createConvCard(id,nom,avatars,userid){
        var $button = $('<button>', {
            class: 'btn-MP',
            'data-conv-id': id,
            'data-conv-nom': nom,
            'data-conv-userid':userid
        });
        
        var $container = $('<div>', {
            class: 'container-MP',
            id: id
        });
        const avatarSrc = avatars 
            ? 'img/avatars/' + avatars 
            : 'img/avatars/default.png';
        var $avatar = $('<img>', {
            src: avatarSrc,
            class: 'icon',
            alt: 'Avatar'
        });

        var $texte = $('<h2>',{class: 'pseudo'}).text(nom);
        $container.append($avatar);
        $container.append($texte);
        $button.append($container);
        

        return $button;
    }
    function createServerCard(id, nom, icon) {
        var $button = $('<button>', {
            class: 'btn-serv',
            'data-serv-id': id
        });

        var $container = $('<div>', {
            class: 'container-icon-serv',
            id: id
        });
        
        const iconSrc = icon 
            ? 'img/servers/icon/' + icon 
            : 'img/servers/icon/default.png';
        const $img = $('<img>', {
            src: iconSrc,
            alt: nom,
            class: 'icon-serv'
        });
        
        $container.append($img);
        $button.append($container);
        
        return $button;
    }
    function createAddServerCard(){
        var $button = $('<button>',{class : 'ajout_serv'}).text("+");

        return $button;
    }
    
    function createChannelCard(id, nom) {
        var $button = $('<button>', {
            class: 'btn-salon',
            'data-salon-id': id,
            'data-salon-nom': nom
        });
        
        var $container = $('<div>', {
            class: 'container-salon',
            id: id
        });
        
        var $texte = $('<h2>').text(nom);
        $container.append($texte);
        $button.append($container);

        return $button;
    }
    function createEmptyChannelMessage(nomSalon){
        var $empty = $('<div>',{class : 'aucunMessage'}).text("Il semblerait que le salon "+nomSalon+" soit vide, n'hésitez pas à discuter pour faire vivre le salon ;)");
        return $empty;
    }
    function createMessageCard(id,mes_users,nom,icon, texte, date,userActuel) {
        var $container = $('<div>', {
            class: 'message',
            id: 'message' + id
        });
        // Avatar avec fallback
        const avatarSrc = icon 
            ? 'img/avatars/' + icon 
            : 'img/avatars/default.png';
        var $avatar = $('<img>', {
            src: avatarSrc,
            class: 'avatar',
            alt: 'Avatar'
        });
        
        var $messageContent = $('<div>', {
            class: 'message-content'
        });
        
        var $spanAuteur = $('<span>', {
            class: 'auteur',
            id :mes_users
        }).text(nom);

        var $texte = $('<p>', {
            class: 'texte'
        }).text(texte);

        var $spanDate = $('<span>', {
            class: 'date'
        }).text(date);
        
    if (parseInt(mes_users) === parseInt(userActuel)) {
        var $action = $('<div>', { class: 'action' });

        var $buttonModif = $('<button>', {
            class: 'btn-modif',
            'data-message-id': id
        });
        var $imgPen = $('<img>', { src: "img/action/pen.png", class: 'icon-pen' });

        var $buttonSupp = $('<button>', {
            class: 'btn-supp',
            'data-message-id': id
        });
        var $imgTrash = $('<img>', { src: "img/action/trash.png", class: 'icon-trash' });

        $buttonSupp.append($imgTrash);
        $buttonModif.append($imgPen);
        $action.append($buttonModif);
        $action.append($buttonSupp);

        $messageContent.append($action); 
    }

        $messageContent.append($spanAuteur);
        $messageContent.append($texte);
        $messageContent.append($spanDate);

        $container.append($avatar);
        $container.append($messageContent);

        return $container;
    }
    
    function createHeaderChannel(nom,id) {
    var $hashtag = $('<p>').text('#');
    var $title = $('<h1>').text(nom);
    var $separator = $('<p>').text(' | ');
    const $img = $('<img>',{class : 'Friends',src : '../img/action/addFriends.png'});
    var $btnMasqueMembre = $('<button>',{class : 'btnMasque', 'data-user-id' : id, 'data-active': 'false'});
    $btnMasqueMembre.append($img);
    var $btnDeconnexion = $('<button>', {
        id: 'btn-deconnexion'
    }).text('Déconnexion');
    var $actionsRight = $('<div>', { class: 'header-actions-right' });
    $actionsRight.append($btnMasqueMembre, $btnDeconnexion);

    return [$hashtag, $title, $separator, $actionsRight];
}
    function createHeaderServer(serverid,nom,banner){
        var $container = $('<div>',{class : 'serveur_salon',id : serverid});

        const Vbanner = banner 
                ? 'img/servers/banner/' + banner 
                : 'img/servers/banner/default.jpg';

        var $bannerSrc = $('<img>',{class : 'banner-img banner-serv', alt : 'banner-server',src : Vbanner});
        var $serverContent = $('<div>',{class : 'server-content'});
        var $serverName = $('<h1>',{class : 'server-name'}).text(nom);
        var $imgFriends = $('<img>', {
            src: "img/action/addFriends.png",
            class: 'addFriends'
        }); 

        var $buttonAddFriends = $('<button>', {
            class: 'addFriendServer',
            'data-id-server': serverid
        });

        $buttonAddFriends.append($imgFriends);
        $serverContent.append($serverName);
        $serverContent.append($buttonAddFriends);
        
        
        $container.append($bannerSrc);
        $container.append($serverContent);

        return $container;
    }
    
    function createServerListItem(serverid, nom, icon, banner, desc) {
        var $container = $('<div>', {
            class: 'serveur',
            id: 'serveur' + serverid
        });
        
        const bannerSrc = banner  
            ? 'img/servers/banner/' + banner 
            : 'img/servers/banner/default.png';
        var $banner = $('<img>', {
            class: 'banner-img banner-serv',
            src: bannerSrc,
            alt: 'banner'
        });
        
        const iconSrc = icon 
            ? 'img/servers/icon/' + icon 
            : 'img/servers/icon/default.png';
        var $icon = $('<img>', {
            class: 'icon', 
            src: iconSrc,
            alt: nom
        });
        
        var $serverContent = $('<div>', {
            class: 'server-content'
        });
        
        var $servername = $('<span>', {
            class: 'server-name'
        }).text(nom);
        
        var $p = $('<p>', {
            class: 'server-description'
        }).text(desc || 'Aucune description');
        
        var $button = $('<button>', {
            class: 'btn-join',
            'data-server-id': serverid
        }).text('Rejoindre');  // ✅ Texte du bouton

        $container.append($banner);
        $container.append($icon);
        $serverContent.append($servername);
        $serverContent.append($p);
        $container.append($serverContent);
        $container.append($button);
        
        return $container;  // ✅ RETURN !
    }
    function createServerModal(servers) {
        var $voile = $('<div>', { class: 'voile' });
    
        var $modal = $('<div>', { class: 'liste-serv' });
    
        var $titre = $('<h1>').text('Vous voulez découvrir de nouveaux horizons ?');
        
        var $banner = $('<img>', {
            src: 'img/servers/banner/banner_skyfall.jpg',
            alt: 'Banner',
            class: 'banner-img'
        });
        
        var $container = $('<div>', { class: 'serveurs-container' });
        
        $.each(servers, function(index, serveur) {
            var $item = createServerListItem(
                serveur.id,
                serveur.nom,
                serveur.icon,
                serveur.banner,
                serveur.description
            );
            $container.append($item);
        });
        
        var $btnClose = $('<button>', {
            class: 'close-liste'
        }).text('✕');
        
        $modal.append($titre);
        $modal.append($banner);
        $modal.append($container);
        $modal.append($btnClose);
        $voile.append($modal);
        
        // 9. Retourner
        return $voile;
    }
    function createSuppressionModale(){
        var $voile = $('<div>', { class: 'voile' });
        var $warning = $('<div>',{class : 'warning'});
        var $titre = $('<h1>').text("Êtes-vous sur de vouloir supprimer ce message ?");
        var $h2 = $('<h2>').text("Cette action est irréversible, toute trace du message sera supprimé du salon")
        
        
        /*var $container = UIComponents.createMessageCard(MessageId,nomUser,iconUser,text,date);
        */
        var $btnValid = $('<button>', {
            class: 'confirm'
        }).text('Valider');

        var $btnAnnule = $('<button>', {
            class: 'annule'
        }).text('Annuler');
        
        $warning.append($titre);
        $warning.append($h2);
        /*$warning.append($container);*/
        $warning.append($btnValid);
        $warning.append($btnAnnule);
        $voile.append($warning);

        return $voile;
    }
    function createModifModale(){
        var $voile = $('<div>', { class: 'voile' });
        var $warning = $('<div>',{class : 'warning'});
        var $titre = $('<h1>').text("Modifier le message");
        var $textArea = $('<textarea>',{id : "newText",placeholder : "Nouveau message"});
        
        /*var $container = UIComponents.createMessageCard(MessageId,nomUser,iconUser,text,date);
        */
        var $btnValid = $('<button>', {
            class: 'confirm'
        }).text('Valider');

        var $btnAnnule = $('<button>', {
            class: 'annule'
        }).text('Annuler');
        
        $warning.append($titre);
        $warning.append($textArea);
        /*$warning.append($container);*/
        $warning.append($btnValid);
        $warning.append($btnAnnule);
        $voile.append($warning);

        return $voile;

    }
    function createSectionSeparator(sectionName) {
    return $('<div>', {
        class: 'separator-section'
    }).text(sectionName.toUpperCase());
}
function AffichageInfoServer(id,nom,avatars){
   var $button = $('<button>', {
            class: 'btn-cardUser',
            'data-member-id': id,
            'data-member-nom': nom
        });
        
        var $container = $('<div>', {
            class: 'container-MP',
            id: id
        });
        const avatarSrc = avatars 
            ? 'img/avatars/' + avatars 
            : 'img/avatars/default.png';
        var $avatar = $('<img>', {
            src: avatarSrc,
            class: 'icon',
            alt: 'Avatar'
        });

        var $texte = $('<h2>',{class: 'pseudo'}).text(nom);
        $container.append($avatar);
        $container.append($texte);
        $button.append($container);
        

        return $button;
    }
function InfoUser(userID, nickname, nom, icon, banner, bios,canManageRoles, allRoles, memberRoles) {
    const bannerSrc = banner ? 'img/banner/' + banner : 'img/banner/default.jpg';
    const iconSrc   = icon   ? 'img/avatars/' + icon          : 'img/avatars/default.png';

    var $voile     = $('<div>', { class: 'voile' });
    var $container = $('<div>', { class: 'user-card' });

    // Bannière
    var $banner = $('<div>', { class: 'user-card-banner' })
        .css('background-image', 'url(' + bannerSrc + ')');

    // Avatar + status
    var $avatarWrapper = $('<div>', { class: 'user-card-avatar-wrapper' });
    var $avatar = $('<img>', { src: iconSrc, class: 'user-card-avatar', alt: nickname });
    var $status = $('<span>', { class: 'user-card-status' });
    $avatarWrapper.append($avatar, $status);

    // Infos
    var $body = $('<div>', { class: 'user-card-body' });

    var $username = $('<h2>', { class: 'user-card-username' }).text(nickname);
    var $tag      = $('<p>',  { class: 'user-card-tag' }).text(nom);

    var $divider1 = $('<hr>', { class: 'user-card-divider' });

    var $bioLabel = $('<p>', { class: 'user-card-label' }).text('À PROPOS DE MOI');
    var $bio      = $('<p>', { class: 'user-card-bio' }).text(bios || 'Aucune description');

    var $divider2 = $('<hr>', { class: 'user-card-divider' });

    // Boutons
    var $actions  = $('<div>', { class: 'user-card-actions' });
    var $btnAmi   = $('<button>', { class: 'user-card-btn primary', 'data-id': userID }).text('+ Ami');
    var $btnBlock = $('<button>', { class: 'user-card-btn secondary', 'data-id': userID }).text('Bloquer');
    
    $actions.append($btnAmi, $btnBlock);

    if (canManageRoles){
        var $btnViewRole = $('<button>', { class: 'user-card-btn third', 'data-id': userID }).text('+');
        $actions.append($btnViewRole);

    };
    $body.append($username, $tag, $divider1, $bioLabel, $bio, $divider2, $actions);
    $container.append($banner, $avatarWrapper, $body);
    $voile.append($container);

    return $voile;
}

function createRoleModal(allRole,MemberRole,UserId){
        var $voile = $('<div>', { class: 'voile', "data-user-id" : UserId });
        var $warning = $('<div>',{class : 'warning'});
        var $titre = $('<h1>').text("Role");
        var $checkBoxContainer = $('<div>', {class : 'checkBoxContainer'})
        
        allRole.forEach(role => {
            var $checkBox = $('<input>',{class : "checkRole", type :"checkbox", "data-role-id" : role.id})
            var $labelRole = $('<label>', {class : "labelRole"}).text(role.nom)
            MemberRole.forEach(roleM => {
                if (role.id == roleM.id)
                    $checkBox.prop('checked', true)
            });
            $checkBox.append($labelRole)
            $checkBoxContainer.append($checkBox);

        });
        
        $warning.append($titre);
        $warning.append($checkBoxContainer);
        $voile.append($warning);

        return $voile;

    }
function InfoUserMp(userID, nickname, nom, icon, banner, bios) {
    const bannerSrc = banner ? 'img/banner/' + banner : 'img/banner/default.jpg';
    const iconSrc   = icon   ? 'img/avatars/' + icon          : 'img/avatars/default.png';

    var $container = $('<div>', { class: 'user-cardMP' });

    // Bannière
    var $banner = $('<div>', { class: 'user-card-banner' })
        .css('background-image', 'url(' + bannerSrc + ')');

    // Avatar + status
    var $avatarWrapper = $('<div>', { class: 'user-card-avatar-wrapper' });
    var $avatar = $('<img>', { src: iconSrc, class: 'user-card-avatar', alt: nickname });
    var $status = $('<span>', { class: 'user-card-status' });
    $avatarWrapper.append($avatar, $status);

    // Infos
    var $body = $('<div>', { class: 'user-card-body' });

    var $username = $('<h1>', { class: 'user-card-username' }).text(nickname);
    var $tag      = $('<p>',  { class: 'user-card-tag' }).text(nom);

    var $divider1 = $('<hr>', { class: 'user-card-divider' });

    var $bioLabel = $('<p>', { class: 'user-card-label' }).text('À PROPOS DE MOI');
    var $bio      = $('<p>', { class: 'user-card-bio' }).text(bios || 'Aucune description');

    var $divider2 = $('<hr>', { class: 'user-card-divider' });

    // Boutons
    var $actions  = $('<div>', { class: 'user-card-actions' });
    var $btnAmi   = $('<button>', { class: 'user-card-btn primary', 'data-id': userID }).text('+ Ami');
    var $btnBlock = $('<button>', { class: 'user-card-btn secondary', 'data-id': userID }).text('Bloquer');
    $actions.append($btnAmi, $btnBlock);

    $body.append($username, $tag, $divider1, $bioLabel, $bio, $divider2, $actions);
    $container.append($banner, $avatarWrapper, $body);

    return $container;
}

return {
    createServerCard: createServerCard,
    createChannelCard: createChannelCard,
    createConvCard : createConvCard,
    createMessageCard: createMessageCard,
    createHeaderChannel: createHeaderChannel,
    createServerListItem: createServerListItem,
    createServerModal: createServerModal,
    createHeaderServer: createHeaderServer,
    createSuppressionModale: createSuppressionModale,
    createModifModale: createModifModale,
    createMpCard: createMpCard,
    createMpHeader:createMpHeader,
    createAddServerCard: createAddServerCard,
    createEmptyChannelMessage: createEmptyChannelMessage,
    createSectionSeparator: createSectionSeparator,
    AffichageInfoServer : AffichageInfoServer,
    InfoUser : InfoUser,
    createRoleModal : createRoleModal,
    InfoUserMp : InfoUserMp
};
})();