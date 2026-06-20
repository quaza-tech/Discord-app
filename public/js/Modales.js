// =============================================================================
// MODALES.JS — Gestion des modales (suppression, modification, voile)
// =============================================================================

$(document).ready(function () {

    // Fermer via bouton annuler
    $('body').on('click', '.annule', function () {
        $('.voile').remove();
    });

    // Fermer en cliquant sur le voile
    $('body').on('click', '.voile', function (e) {
        if (e.target === this) {
            $(this).remove();
        }
    });

    // ============================================
    // SUPPRESSION DE MESSAGE
    // ============================================
    $('#affichage').on('click', '.btn-supp', function () {
        var messageId = $(this).data('message-id');

        var $modal = UIComponents.createSuppressionModale();
        $('body').append($modal);

        $('.warning').on('click', '.confirm', function () {
            if (typeActuel == 'server') {
                API.deleteMessage(messageId)
                    .then(function (response) {
                        if (response && (response.status === 'success' || response.trim() === 'success')) {
                            Validation.showToast("Message supprimé !", "success", 3000);
                            recupererNouveauxMessages();
                            $('.voile').remove();
                        } else {
                            Validation.showToast("Le message n'a pas pu être supprimé.", "error", 3000);
                            $('.voile').remove();
                        }
                    })
                    .catch(function () {
                        Validation.showToast("Erreur lors de la suppression", "error", 3000);
                        $('.voile').remove();
                    });
            } else if (typeActuel == 'mp') {
                API.deleteMessage(messageId, conversationActuelle)
                    .then(function (response) {
                        if (response && (response.status === 'success' || response.trim() === 'success')) {
                            Validation.showToast("Message supprimé !", "success", 3000);
                            recupererNouveauxMessages();
                            $('.voile').remove();
                        } else {
                            Validation.showToast("Le message n'a pas pu être supprimé.", "error", 3000);
                            $('.voile').remove();
                        }
                    })
                    .catch(function () {
                        Validation.showToast("Erreur lors de la suppression", "error", 3000);
                        $('.voile').remove();
                    });
            }
        });
    });

    // ============================================
    // MODIFICATION DE MESSAGE
    // ============================================
    $('#affichage').on('click', '.btn-modif', function () {
        var messageId = $(this).data('message-id');

        var $modal = UIComponents.createModifModale();
        $('body').append($modal);

        $('.warning').on('click', '.confirm', function () {
            var messageTexte = $("#newText").val();
            if (messageTexte.trim() === '') return;

            if (typeActuel == 'server') {
                API.editMessage(messageId, messageTexte)
                    .then(function (response) {
                        if (response && (response.status === 'success' || response.trim() === 'success')) {
                            Validation.showToast("Message modifié !", "success", 3000);
                            recupererNouveauxMessages();
                            $('.voile').remove();
                        } else {
                            Validation.showToast("Le message n'a pas pu être édité.", "error", 3000);
                            $('.voile').remove();
                        }
                    })
                    .catch(function () {
                        Validation.showToast("Erreur lors de la modification", "error", 3000);
                        $('.voile').remove();
                    });
            } else if (typeActuel == 'mp') {
                API.editMessage(messageId, messageTexte, conversationActuelle)
                    .then(function (response) {
                        if (response && (response.status === 'success' || response.trim() === 'success')) {
                            Validation.showToast("Message modifié !", "success", 3000);
                            recupererNouveauxMessages();
                            $('.voile').remove();
                        } else {
                            Validation.showToast("Le message n'a pas pu être édité.", "error", 3000);
                            $('.voile').remove();
                        }
                    })
                    .catch(function () {
                        Validation.showToast("Erreur lors de la modification", "error", 3000);
                        $('.voile').remove();
                    });
            }
        });
    });

});