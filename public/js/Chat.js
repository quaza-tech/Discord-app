// =============================================================================
// CHAT.JS — Récupération et envoi de messages (serveur + MP)
// =============================================================================

// Appelée aussi par setInterval dans script.js
function recupererNouveauxMessages() {
    if (typeActuel === null) return;

    // ============================================
    // CAS 1 : Messages de SERVEUR
    // ============================================
    if (typeActuel === 'server') {
        if (!serveurActuel || !salonActuel) return;

        API.getMessages(serveurActuel, salonActuel)
            .then(function (messages) {
                $('#affichage').empty();

                if (!Array.isArray(messages) || messages.length === 0) {
                    $('#affichage').append(UIComponents.createEmptyChannelMessage(nomActuel));
                    return;
                }

                $.each(messages, function (index, message) {
                    var $msg = UIComponents.createMessageCard(
                        message.mes_id,
                        message.id_user,
                        message.nom,
                        message.avatar,
                        message.mes_texte,
                        message.mes_date,
                        userActuel
                    );
                    $('#affichage').append($msg);
                });

                $('#affichage').scrollTop($('#affichage')[0].scrollHeight);
            })
            .catch(function () {
                Validation.showToast("Erreur lors de la récupération des messages", 'error', 3000);
            });
    }

    // ============================================
    // CAS 2 : Messages PRIVÉS
    // ============================================
    else if (typeActuel === 'mp') {
        if (!conversationActuelle) return;

        API.getMP(conversationActuelle)
            .then(function (messages) {
                $('#affichage').empty();

                if (!Array.isArray(messages) || messages.length === 0) {
                    $('#affichage').append(UIComponents.createEmptyChannelMessage(nomActuel));
                    return;
                }

                $.each(messages, function (index, message) {
                    var $msg = UIComponents.createMessageCard(
                        message.id,
                        message.sender_id,
                        message.nom,
                        message.avatar,
                        message.texte,
                        message.date,
                        userActuel
                    );
                    $('#affichage').append($msg);
                });

                $('#affichage').scrollTop($('#affichage')[0].scrollHeight);
            })
            .catch(function () {
                Validation.showToast("Erreur lors de la récupération des MP", 'error', 3000);
            });
    }
}

$(document).ready(function () {

    // ============================================
    // ENVOI DE MESSAGE (touche Entrée)
    // ============================================
    $('#publie').keypress(function (event) {
        if (event.which !== 13) return;
        event.preventDefault();

        var messageTexte = $("#publie").val();
        if (messageTexte.trim() === '') return;

        // CAS 1 : Serveur
        if (typeActuel === 'server') {
            if (!serveurActuel || !salonActuel) {
                Validation.showToast("Veuillez d'abord sélectionner un salon", "error", 3000);
                return;
            }

            API.sendMessage(serveurActuel, salonActuel, messageTexte)
                .then(function (response) {
                    if (response && response !== "ERR") {
                        $('#publie').val('');
                        recupererNouveauxMessages();
                    }
                })
                .catch(function () {
                    Validation.showToast("Erreur lors de l'envoi", "error", 3000);
                });
        }

        // CAS 2 : MP
        else if (typeActuel === 'mp') {
            if (!conversationActuelle) {
                Validation.showToast("Veuillez d'abord sélectionner une conversation", "error", 3000);
                return;
            }

            API.sendMP(conversationActuelle, messageTexte)
                .then(function (response) {
                    if (response && response.status == 'success') {
                        $('#publie').val('');
                        recupererNouveauxMessages();
                    }
                })
                .catch(function () {
                    Validation.showToast("Erreur lors de l'envoi du MP", "error", 3000);
                });
        }
    });

});