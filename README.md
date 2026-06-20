# TP7 — Application Web de Messagerie (Discord-like)

## Présentation du projet

Ce projet est une application web de messagerie en temps réel, inspirée de Discord, développée dans le cadre d'un TP de développement web en BUT Informatique.

L'application permet à des utilisateurs de s'inscrire, de rejoindre des serveurs, d'envoyer des messages dans des salons textuels et d'échanger des messages privés.

**Stack technique :**

- **Backend :** PHP 8 avec PDO (PostgreSQL)
- **Frontend :** HTML, CSS, JavaScript (jQuery)
- **Bibliothèque externe :** PHPMailer (via Composer)
- **Base de données :** PostgreSQL

---

## Fonctionnalités

- Inscription et connexion sécurisée (mots de passe hashés avec `password_hash`)
- Réinitialisation de mot de passe par email (PHPMailer + token sécurisé)
- Navigation entre plusieurs serveurs et salons
- Envoi et suppression de messages en temps réel (polling)
- Messagerie privée entre utilisateurs
- Gestion des rôles membres au sein des serveurs
- API REST partielle (`/api/auth`, `/api/channels`, `/api/servers`, `/api/users`...)

---

## Architecture du projet

Le projet suit une architecture **MVC** (Modèle - Vue - Contrôleur), organisée pour séparer clairement les responsabilités :

```
TP7 continue refont/
│
├── src/                          # Logique métier (backend)
│   ├── bootstrap.php             # Initialisation : autoloader, session, Composer
│   ├── Database.php              # Connexion PDO centralisée (pattern Singleton)
│   │
│   ├── Models/                   # Représentation des entités métier
│   │   ├── Users.php
│   │   ├── Server.php
│   │   ├── Channel.php
│   │   ├── Message.php
│   │   └── Conv.php
│   │
│   ├── Repositories/             # Accès à la base de données (requêtes SQL)
│   │   ├── UserRepository.php
│   │   ├── ServerRepository.php
│   │   ├── ChannelRepository.php
│   │   ├── MessageRepository.php
│   │   └── ConvRepository.php
│   │
│   ├── Services/                 # Logique applicative (règles métier)
│   │   ├── AuthService.php       # Inscription, connexion, reset de mot de passe
│   │   ├── MessageService.php
│   │   └── EmailNotificationService.php
│   │
│   └── Interfaces/
│       └── NotificationInterface.php  # Contrat pour les services de notification
│
├── public/                       # Fichiers accessibles depuis le navigateur
│   ├── index.html                # Page de connexion
│   ├── inscription.html          # Page d'inscription
│   ├── salon.html                # Page principale (serveurs + salons + messages)
│   │
│   ├── css/                      # Feuilles de style
│   ├── js/                       # Scripts JavaScript (jQuery)
│   │   ├── Chat.js               # Gestion des messages
│   │   ├── Serveur.js            # Gestion des serveurs
│   │   ├── Mp.js                 # Messages privés
│   │   └── modules/
│   │       ├── API.js            # Appels REST centralisés
│   │       └── UIComponents.js   # Composants d'interface réutilisables
│   │
│   └── php/
│       ├── api/                  # Points d'entrée de l'API REST
│       │   ├── auth/             # login, logout, register, password
│       │   ├── channels/         # Salons et messages
│       │   ├── servers/          # Serveurs
│       │   ├── users/            # Utilisateurs
│       │   ├── members/          # Membres d'un serveur
│       │   └── conversations/    # Messages privés
│       │
│       └── *.php                 # Scripts AJAX (legacy, compatibilité)
│
├── vendor/                       # Dépendances Composer (PHPMailer)
└── composer.json                 # Déclaration des dépendances
```

---

## Choix techniques et justifications

### Pattern Singleton — `Database.php`

La connexion à la base de données est gérée via un Singleton : une seule instance PDO est créée pour toute la durée de la requête. Cela évite d'ouvrir plusieurs connexions inutiles et centralise la configuration en un seul endroit.

### Requêtes préparées — tous les `Repository`

Toutes les interactions avec la base de données utilisent des **requêtes préparées PDO** (`prepare` + `execute`). Cela protège l'application contre les injections SQL, qui sont parmi les vulnérabilités les plus courantes des applications web (OWASP Top 10).

### Hashage des mots de passe — `UserRepository.php`

Les mots de passe ne sont jamais stockés en clair. La fonction PHP `password_hash($password, PASSWORD_DEFAULT)` utilise l'algorithme bcrypt, recommandé par les bonnes pratiques de sécurité.

### Réinitialisation de mot de passe sécurisée — `AuthService.php`

Le système de reset utilise un token généré avec `bin2hex(random_bytes(32))`, ce qui produit une chaîne aléatoire cryptographiquement sûre. Le token est à usage unique et invalidé après utilisation.

### Interface `NotificationInterface`

L'utilisation d'une interface PHP pour les notifications permet de respecter le **principe d'inversion de dépendances** (SOLID) : le service d'authentification ne dépend pas d'une implémentation concrète (email), mais d'un contrat abstrait. Il serait donc facile de remplacer l'envoi par email par des notifications push ou SMS sans modifier le cœur de l'application.

---

## Cycle de développement suivi

| Phase             | Description                                                                                    |
| ----------------- | ---------------------------------------------------------------------------------------------- |
| **Analyse**       | Lecture du cahier des charges, identification des entités (users, servers, channels, messages) |
| **Conception**    | Définition de l'architecture MVC, modélisation des relations entre entités                     |
| **Développement** | Implémentation itérative : authentification → serveurs → messages → API REST                   |
| **Tests**         | Vérification manuelle des endpoints API, tests de connexion/inscription                        |
| **Livraison**     | Dépôt du code source sur le gestionnaire de versions                                           |

---

## Lancer le projet

**Prérequis :**

- PHP 8.x
- PostgreSQL
- Composer

**Installation :**

```bash
# Installer les dépendances PHP
composer install

# Configurer la base de données
# Modifier les constantes dans src/Database.php

# Lancer un serveur local
php -S localhost:8080 -t public/
```

---

## Sécurité — points clés

| Menace                | Contre-mesure mise en place                         |
| --------------------- | --------------------------------------------------- |
| Injection SQL         | Requêtes préparées PDO sur toutes les opérations    |
| Vol de mot de passe   | Hashage bcrypt via `password_hash`                  |
| Usurpation de session | Token de reset à usage unique, sessions PHP natives |
| Accès non autorisé    | Vérification de session sur chaque page protégée    |
