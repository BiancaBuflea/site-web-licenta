<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site settings
define('SITE_NAME', 'CrazyCrew Events&More');
define('SITE_URL', '/crazycrew');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . '/db.php';
