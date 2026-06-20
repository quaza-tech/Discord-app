<?php
/**
 * Database.php - Connexion centralisée à la base de données
 * 
 * À QUOI ÇA SERT ?
 * - Évite de répéter le code de connexion dans chaque fichier
 * - Une seule instance de connexion (Pattern Singleton)
 * - Un seul endroit à modifier si tu changes de BDD
 * 
 * PATTERN SINGLETON = On ne peut créer qu'UNE SEULE instance
 */

namespace App;

use PDO;
use PDOException;

class Database
{
    /**
     * Instance unique de connexion PDO
     * 
     * static = partagée par toutes les instances
     * private = accessible uniquement dans cette classe
     * ?PDO = peut être null ou un objet PDO
     */
    private static ?PDO $instance = null;

    /**
     * Configuration de la base de données
     * 
     * const = constante (ne change jamais)
     * private = accessible uniquement dans cette classe
     */
    private const HOST = 'localhost';
    private const PORT = '5432';
    private const DBNAME = 'discord';
    private const USERNAME = 'noa';
    private const PASSWORD = 'leonoa09';

    /**
     * Constructeur PRIVÉ
     * 
     * Pourquoi privé ? Pour empêcher :
     * $db = new Database();  // ❌ INTERDIT
     * 
     * On DOIT utiliser :
     * $pdo = Database::getConnection();  // ✅ AUTORISÉ
     */
    private function __construct()
    {
        // Rien ici, la connexion se fait dans getConnection()
    }

    /**
     * Récupère l'instance unique de connexion
     * 
     * static = méthode de classe (appelée sans créer d'objet)
     * 
     * UTILISATION :
     * $pdo = Database::getConnection();
     */
    public static function getConnection(): PDO
    {
        // Si pas encore de connexion, on la crée
        if (self::$instance === null) {
            try {
                // Créer le DSN (Data Source Name)
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    self::HOST,
                    self::PORT,
                    self::DBNAME
                );

                // Créer la connexion PDO
                self::$instance = new PDO(
                    $dsn,
                    self::USERNAME,
                    self::PASSWORD,
                    [
                            // Lève une exception en cas d'erreur
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                            // Retourne les résultats en tableaux associatifs
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                            // Utilise les vraies requêtes préparées
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

            } catch (PDOException $e) {
                // Logger l'erreur
                error_log("Erreur de connexion BDD : " . $e->getMessage());

                // Relancer l'exception
                throw $e;
            }
        }

        // Retourner l'instance
        return self::$instance;
    }

    /**
     * Empêche le clonage de l'instance
     * 
     * Sinon on pourrait faire :
     * $db2 = clone $db1;  // ❌ INTERDIT
     */
    private function __clone()
    {
        // Interdit
    }
}

/**
 * COMMENT L'UTILISER ?
 * 
 * AVANT (dans CHAQUE fichier) :
 * $serveur = "localhost";
 * $port = "5432";
 * $utilisateur = "noa";
 * $motDePasse = "leonoa09";
 * $nomBase = "discord";
 * try {
 *     $connexion = new PDO(...);
 * } catch (...) {
 *     // ...
 * }
 * 
 * APRÈS (dans CHAQUE fichier) :
 * $pdo = Database::getConnection();
 * 
 * C'EST TOUT ! 1 ligne au lieu de 10+ !
 * 
 * AVANTAGES :
 * 1. Code répété : 0 fois au lieu de 15 fois
 * 2. Changer de BDD : Modifier 1 fichier au lieu de 15
 * 3. Une seule connexion : Économie de ressources
 */

/**
 * PATTERN SINGLETON EXPLIQUÉ :
 * 
 * Appel 1 : $pdo1 = Database::getConnection();
 * → self::$instance est null
 * → Crée la connexion
 * → Retourne la connexion
 * 
 * Appel 2 : $pdo2 = Database::getConnection();
 * → self::$instance existe déjà
 * → Retourne la MÊME connexion
 * 
 * $pdo1 === $pdo2  // true (même objet !)
 */