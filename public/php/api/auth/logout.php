<?php
// GET /api/auth/logout.php
// Ancien fichier : log_out.php

require_once __DIR__ . '/../../../../src/bootstrap.php';

session_unset();
session_destroy();

header('Location: ../../../index.html');
exit;