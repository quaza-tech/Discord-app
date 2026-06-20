// =============================================================================
// SCRIPT.JS — Point d'entrée principal (init + polling)
// =============================================================================

$(document).ready(function () {

    // ============================================
    // VÉRIFIER QUE L'UTILISATEUR EST CONNECTÉ
    // ============================================
    API.getUserInfo()
        .then(function (user) {
            userActuel = user.id;
        })
        .catch(function () {
            window.location.href = "index.html";
        });

    // ============================================
    // POLLING — Rafraîchissement automatique
    // ============================================
    setInterval(recupererNouveauxMessages, 5000);

});