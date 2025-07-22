<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Verifică dacă utilizatorul este autentificat
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Procesează anularea rezervării
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    
    if ($booking_id > 0) {
        // Verifică dacă rezervarea aparține utilizatorului curent
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $booking_id,
            ':user_id' => $_SESSION['user_id']
        ]);
        
        $booking = $stmt->fetch();
        
        if ($booking && $booking['status'] === 'pending') {
            // Anulează rezervarea
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
            $stmt->execute([':id' => $booking_id]);
            
            redirect('my-bookings.php?cancelled=1');
        }
    }
}

// Redirecționează înapoi la pagina de rezervări
redirect('my-bookings.php');
