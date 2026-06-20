$(document).ready(function() {
    $("button[type='submit']").click(function(e) {
        e.preventDefault();
    
        const emailValue = $('#email').val();
        const motValue = $('#mdp').val();

        $.ajax({ 
            type: 'POST', 
            url: '../php/test_connexion.php', 
            data: {email: emailValue, mot: motValue},
            success: function(response) {
                if (response.status == 'OK') {
                    window.location.href = "salon.html";
                } else {
                    Validation.showToast("Erreur : mot de passe ou email incorrect", 'error');
                }
            },
            error: () => Validation.showToast("Erreur de connexion", 'error')
        });
    });
    $("#mdp-oublie").click(function(e) {
        e.preventDefault();  // ✅ Ajoute ceci pour éviter le comportement par défaut du lien
        
        var emailValue = $("#email").val().trim();
                
        if (!Validation.validateEmail(emailValue).valid){
            return Validation.showToast('Error : Aucun email dans le champ Email !');
        } else {
            $.ajax({
                type: 'POST',
                url: 'php/changement_mdp.php',
                data: {email: emailValue},
                success: function(response) {
                        if (response != "ERR") {
                            Validation.showToast("Veuillez vérifier votre boîte mail, un lien vous a été envoyé pour modifier votre mot de passe",'info',3000);
                        } else {
                            Validation.showToast("Une erreur est survenue lors de l'envoi du mail","error",3000);
                        }
                }
            });
        }
    }); 
});