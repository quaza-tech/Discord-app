<?php

// 1. Charger bootstrap
require_once __DIR__ . '/../../src/bootstrap.php';

// 2. Détruire toutes les variables de session
session_unset();

// 3. Détruire la session
session_destroy();

// 4. Rediriger vers la page de connexion
header('Location: ../../index.html');
exit;
