<?php 
if (!defined('SITE_URL')) {
    require_once __DIR__ . '/config.php';
}

// Asigură-te că functions.php este inclus
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/functions.php';
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="stylesheet" href="/crazycrew/assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?= SITE_URL ?>">
                    <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="CrazyCrew Logo" class="logo-image">
                    <h1>CrazyCrew Events&More</h1>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="<?= SITE_URL ?>">Acasă</a></li>
                    <li><a href="<?= SITE_URL ?>/services.php">Servicii</a></li>
                    <li><a href="<?= SITE_URL ?>/gallery.php">Galerie</a></li>
                    <li><a href="<?= SITE_URL ?>/booking.php">Rezervare</a></li>
                    <li><a href="<?= SITE_URL ?>/about.php">Despre Noi</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li class="dropdown">
                            <a href="#"><?= $_SESSION['username'] ?> <i class="fas fa-caret-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= SITE_URL ?>/my-bookings.php">Rezervările Mele</a></li>
                                <?php if (isAdmin()): ?>
                                    <li><a href="<?= SITE_URL ?>/admin">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a href="<?= SITE_URL ?>/auth/logout.php">Deconectare</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= SITE_URL ?>/auth/login.php">Autentificare</a></li>
                        <!-- Butonul de înregistrare a fost eliminat de aici -->
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    <main>
