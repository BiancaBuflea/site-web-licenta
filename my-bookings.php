<?php
$pageTitle = "Rezervările Mele";
include 'includes/header.php';

// Verifică dacă utilizatorul este autentificat
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Obține rezervările utilizatorului
$bookings = getBookingsByUserId($_SESSION['user_id'], $pdo);
?>

<section class="page-header">
    <div class="container">
        <h1>Rezervările Mele</h1>
        <p>Vizualizează și gestionează rezervările tale</p>
    </div>
</section>

<section class="my-bookings-section">
    <div class="container">
        <?php if (empty($bookings)): ?>
        <div class="alert alert-info">
            <p>Nu ai nicio rezervare momentan. <a href="booking.php">Creează o rezervare nouă</a>.</p>
        </div>
        <?php else: ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $booking): 
                $services = getServicesForBooking($booking['id'], $pdo);
                $serviceNames = array_map(function($service) {
                    return $service['name'];
                }, $services);
            ?>
            <div class="booking-card">
                <div class="booking-header">
                    <h3>Rezervare #<?= $booking['id'] ?></h3>
                    <span class="status-badge status-<?= $booking['status'] ?>">
                        <?= getStatusText($booking['status']) ?>
                    </span>
                </div>
                
                <div class="booking-details">
                    <div class="detail-row">
                        <span class="detail-label">Data Eveniment:</span>
                        <span class="detail-value"><?= formatDateRo($booking['event_date']) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Locație:</span>
                        <span class="detail-value"><?= $booking['event_location'] ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Număr Persoane:</span>
                        <span class="detail-value"><?= isset($booking['guests_count']) ? $booking['guests_count'] : 'Nespecificat' ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Număr Telefon:</span>
                        <span class="detail-value"><?= isset($booking['phone_number']) ? $booking['phone_number'] : 'Nespecificat' ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Servicii:</span>
                        <span class="detail-value"><?= implode(', ', $serviceNames) ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Data Creare:</span>
                        <span class="detail-value"><?= formatDateRo($booking['created_at']) ?></span>
                    </div>
                    
                    <?php if (!empty($booking['event_description'])): ?>
                    <div class="detail-row">
                        <span class="detail-label">Descriere:</span>
                        <span class="detail-value"><?= $booking['event_description'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($booking['status'] === 'pending'): ?>
                <div class="booking-actions">
                    <form method="post" action="cancel-booking.php" onsubmit="return confirm('Sunteți sigur că doriți să anulați această rezervare?');">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <button type="submit" class="btn btn-danger">Anulează Rezervarea</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="booking-actions-container">
            <a href="booking.php" class="btn btn-primary">Creează o Rezervare Nouă</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
