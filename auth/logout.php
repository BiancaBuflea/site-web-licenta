<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Distruge sesiunea
session_destroy();

// Redirecționează către pagina principală
redirect(SITE_URL);
