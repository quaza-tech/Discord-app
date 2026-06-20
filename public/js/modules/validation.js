const Validation = (function() {
    'use strict';
    const CONSTANTS = {
        MIN_PASSWORD_LENGTH: 8,
        MIN_USERNAME_LENGTH: 3,
        MAX_USERNAME_LENGTH: 30,
        PASSWORD_REGEX: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/,
        EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    };
    
    const ERR = {
        EMAIL_REQUIRED: 'Le champ email est obligatoire',
        EMAIL_INVALID: 'Adresse email invalide',
        USERNAME_REQUIRED: 'Le champ pseudo est obligatoire',
        USERNAME_TOO_SHORT: `Le pseudo doit contenir au moins ${CONSTANTS.MIN_USERNAME_LENGTH} caractères`,
        PASSWORD_REQUIRED: 'Le champ mot de passe est obligatoire',
        PASSWORD_TOO_SHORT: `Le mot de passe doit contenir au moins ${CONSTANTS.MIN_PASSWORD_LENGTH} caractères`,
        PASSWORD_WEAK: 'Le mot de passe doit contenir une majuscule, une minuscule et un chiffre',
        PASSWORD_MISMATCH: 'Les mots de passe ne correspondent pas'
    };
    
    return {
        validateUsername: (u) => {
            if (!u || u.trim() === '') 
                return {valid: false, error: ERR.USERNAME_REQUIRED};
            if (u.trim().length < CONSTANTS.MIN_USERNAME_LENGTH) 
                return {valid: false, error: ERR.USERNAME_TOO_SHORT};
            return {valid: true};
        },
        validateEmail: (e) => {
            if (!e || e.trim() === '') 
                return {valid: false, error: ERR.EMAIL_REQUIRED};
            if (!CONSTANTS.EMAIL_REGEX.test(e)) 
                return {valid: false, error: ERR.EMAIL_INVALID};
            return {valid: true};
        },
        validatePassword: (p) => {
            if (!p) 
                return {valid: false, error: ERR.PASSWORD_REQUIRED};
            if (p.length < CONSTANTS.MIN_PASSWORD_LENGTH) 
                return {valid: false, error: ERR.PASSWORD_TOO_SHORT};
            if (!CONSTANTS.PASSWORD_REGEX.test(p)) 
                return {valid: false, error: ERR.PASSWORD_WEAK};
            return {valid: true};
        },
        validatePasswordMatch: (p1, p2) => (p1 === p2) ? {valid: true} : {valid: false, error: ERR.PASSWORD_MISMATCH},
        
        showToast: function(message, type, duration = 3000) {
            // 1. Créer l'élément toast avec la classe du type
            const $toast = $('<div>', {
                class: 'toast toast-' + type,
                text: message
            });

            // 2. Ajouter au body
            $('body').append($toast);

            // 3. Déclencher l'apparition (on attend 10ms pour que le navigateur voit l'élément)
            setTimeout(() => {
                $toast.addClass('show');
            }, 5);

            // 4. Masquer et supprimer après 'duration'
            setTimeout(() => {
                $toast.removeClass('show'); // Le message repart vers la droite
                
                // On attend la fin de l'animation CSS (400ms) avant de supprimer du DOM
                setTimeout(() => {
                    $toast.remove();
                }, 400);
            }, duration);
        }
    };
})();