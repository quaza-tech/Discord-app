$(document).ready(function() {
    $("button[type='submit']").click(function(e) {
        e.preventDefault();
    
        const emailValue = $('#email').val();
        const motValue = $('#mdp').val();

         API.login(emailValue,motValue)
            .then(function (reponse) {
                if (reponse.status == 'OK') {
                    window.location.href = "salon.html";
                } else {
                    Validation.showToast("Erreur : mot de passe ou email incorrect", 'error');
                }
            })
            .catch(function () {
                Validation.showToast("Erreur de connexion", 'error', 3000);
            });
    });

    $("#mdp-oublie").click(function(e) {
        e.preventDefault();  // ✅ Ajoute ceci pour éviter le comportement par défaut du lien
        
        var emailValue = $("#email").val().trim();
                
        if (!Validation.validateEmail(emailValue).valid){
            return Validation.showToast('Error : Aucun email dans le champ Email !');
        } else {
            API.requestPasswordReset(emailValue)
            .then(function (reponse) {
                if (reponse.status == 'OK') {
                            Validation.showToast("Veuillez vérifier votre boîte mail, un lien vous a été envoyé pour modifier votre mot de passe",'info',3000);
                        } else {
                            Validation.showToast("Une erreur est survenue lors de l'envoi du mail","error",3000);
                        }
            })
            .catch(function() {
                Validation.showToast("Erreur lors de l'envoie de l'email","error",3000);
            })
        }
    }); 
});