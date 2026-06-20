

$(document).ready(function() {
    $("button[type='submit']").click(function(e) {
        e.preventDefault();
    
        const emailValue = $('#email').val();
        const pseudoValue = $('#Pseudo').val();
        const mdpValue = $('#mdp').val();
        const password2 = $('#Cmdp').val();
        
        const vEmail = Validation.validateEmail(emailValue);
        const vMdp = Validation.validatePassword(mdpValue);
        const vNom = Validation.validateUsername(pseudoValue);
        const vMatch = Validation.validatePasswordMatch(mdpValue, password2);
        
        // Chaînage propre des erreurs
        if (!vEmail.valid) return Validation.showToast(vEmail.error, 'error');
        if (!vNom.valid) return Validation.showToast(vNom.error, 'error');
        if (!vMdp.valid) return Validation.showToast(vMdp.error, 'error');
        if (!vMatch.valid) return Validation.showToast(vMatch.error, 'error');

        $.ajax({ 
            type: 'POST', 
            url: '../php/inscription.php', 
            data: {email: emailValue, pseudo: pseudoValue, mdp: mdpValue},
            success: function(response) {
                if (response.status === "success") {
                    Validation.showToast("Compte créé !", 'info');
                    setTimeout(() => window.location.href = "index.html", 2000);
                } else {
                    Validation.showToast("Erreur : Compte déjà existant ou données invalides", 'warning');
                }
            },
            error: () => Validation.showToast("Erreur de connexion", 'error')
        });
    });
});