<?php
/**
 * bootstrap.php - Fichier d'initialisation de l'application
 * 
 * À QUOI ÇA SERT ?
 * - Charge automatiquement les classes (autoloader)
 * - Démarre la session
 * - Inclut Composer (pour PHPMailer)
 * 
 * CE FICHIER SERA INCLUS EN PREMIER DANS TOUS TES FICHIERS PHP
 */

// ============================================
// 1. CHARGER COMPOSER (pour PHPMailer)
// ============================================
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * EXPLICATION :
 * __DIR__ = le dossier actuel (src/)
 * ../ = remonte d'un niveau (racine du projet)
 * vendor/autoload.php = fichier créé par Composer
 */

// ============================================
// 2. AUTOLOADER PERSONNALISÉ (pour NOS classes)
// ============================================
spl_autoload_register(function ($class) {
    /**
     * Cette fonction est appelée AUTOMATIQUEMENT quand tu fais :
     * $user = new App\Models\User();
     * 
     * Elle va :
     * 1. Transformer "App\Models\User" en chemin de fichier
     * 2. Charger le fichier "src/Models/User.php"
     */

    // Notre namespace de base
    $prefix = 'App\\';

    // Dossier de base de nos classes
    $base_dir = __DIR__ . '/';

    // Si la classe n'utilise pas notre namespace, on ignore
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    // Retirer le prefix "App\" et convertir les \ en /
    // Exemple : "App\Models\User" devient "Models/User"
    $relative_class = substr($class, strlen($prefix));

    // Créer le chemin complet du fichier
    // Exemple : "src/Models/User.php"
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si le fichier existe, on le charge
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * EXEMPLE D'UTILISATION :
 * 
 * Quand tu écris :
 * use App\Models\User;
 * $user = new User();
 * 
 * PHP appelle automatiquement notre fonction avec $class = "App\Models\User"
 * La fonction transforme ça en chemin : "src/Models/User.php"
 * Et charge le fichier !
 * 
 * RÉSULTAT : Tu n'as JAMAIS besoin de faire require 'Models/User.php' !
 */

// ============================================
// 3. DÉMARRER LA SESSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * EXPLICATION :
 * - session_status() vérifie si une session est déjà active
 * - Si pas active, on la démarre
 * - Maintenant tous tes fichiers PHP auront accès à $_SESSION
 */

/**
 * RÉSUMÉ DE CE FICHIER :
 * 
 * Quand tu mets en haut de ton fichier PHP :
 * require_once __DIR__ . '/../../src/bootstrap.php';
 * 
 * Il se passe :
 * 1. Composer est chargé (PHPMailer disponible)
 * 2. L'autoloader est configuré (tes classes se chargent automatiquement)
 * 3. La session est démarrée ($_SESSION disponible)
 * 
 * C'EST TOUT ! Simple et efficace !
 */