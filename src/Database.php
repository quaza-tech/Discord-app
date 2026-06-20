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

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

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
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
                    $_ENV['DB_HOST'],
                    $_ENV['DB_PORT'],
                    $_ENV['DB_NAME'],
                    $_ENV['DB_SSLMODE'] ?? 'require'
                );

                self::$instance = new PDO(
                    $dsn,
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASSWORD'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

            } catch (PDOException $e) {
                error_log("Erreur de connexion BDD : " . $e->getMessage());
                throw $e;
            }
        }

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