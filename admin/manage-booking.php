<?php
$pageTitle = "Gestionare Rezervare";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Verifică dacă ID-ul rezervării este furnizat
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('bookings.php');
}

$bookingId = (int)$_GET['id'];

// Obține detaliile rezervării
$stmt = $pdo->prepare("SELECT b.*, u.username, u.email FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = :id");
$stmt->execute([':id' => $bookingId]);
$booking = $stmt->fetch();

// Verifică dacă rezervarea există
if (!$booking) {
    redirect('bookings.php');
}

// Obține serviciile rezervate
$services = getServicesForBooking($bookingId, $pdo);

// Procesează actualizarea statusului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = sanitize($_POST['status']);
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $newStatus,
        ':id' => $bookingId
    ]);
    
    $success = "Statusul rezervării a fost actualizat cu succes.";
    
    // Actualizează variabila booking pentru a reflecta schimbarea
    $booking['status'] = $newStatus;
}

include '../includes/header.php';
?>

<section class="admin-manage-booking">
    <div class="container">
        <div class="admin-header">
            <h1>Gestionare Rezervare #<?= $bookingId ?></h1>
            <a href="bookings.php" class="btn btn-secondary">Înapoi la Lista de Rezervări</a>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="bookings.php" class="active">Rezervări</a></li>
                <li><a href="users.php">Utilizatori</a></li>
                <li><a href="services.php">Servicii</a></li>
                <li><a href="messages.php">Mesaje</a></li>
            </ul>
        </div>
        
        <div class="admin-panel">
            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <p><?= $success ?></p>
            </div>
            <?php endif; ?>
            
            <div class="booking-details">
                <div class="booking-header">
                    <h2>Detalii Rezervare</h2>
                    <span class="status-badge status-<?= $booking['status'] ?>">
                        <?= getStatusText($booking['status']) ?>
                    </span>
                </div>
                
                <div class="booking-info">
                    <div class="booking-info-group">
                        <h3>Informații Client</h3>
                        <div class="booking-info-item">
                            <strong>Nume Utilizator:</strong> <?= $booking['username'] ?>
                        </div>
                        <div class="booking-info-item">
                            <strong>Email:</strong> <?= $booking['email'] ?>
                        </div>
                        <?php if (isset($booking['phone_number']) && !empty($booking['phone_number'])): ?>
                        <div class="booking-info-item">
                            <strong>Telefon:</strong> <?= $booking['phone_number'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-info-group">
                        <h3>Informații Eveniment</h3>
                        <div class="booking-info-item">
                            <strong>Data Eveniment:</strong> <?= formatDateRo($booking['event_date']) ?>
                        </div>
                        <div class="booking-info-item">
                            <strong>Locație:</strong> <?= $booking['event_location'] ?>
                        </div>
                        <?php if (isset($booking['guests_count']) && $booking['guests_count'] > 0): ?>
                        <div class="booking-info-item">
                            <strong>Număr Persoane:</strong> <?= $booking['guests_count'] ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['event_description'])): ?>
                        <div class="booking-info-item">
                            <strong>Descriere:</strong> <?= nl2br($booking['event_description']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-info-group">
                        <h3>Servicii Rezervate</h3>
                        <?php if (empty($services)): ?>
                        <p>Nu există servicii rezervate.</p>
                        <?php else: ?>
                        <ul class="booking-services-list">
                            <?php foreach ($services as $service): ?>
                            <li>
                                <div class="service-name"><?= $service['name'] ?></div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-info-group">
                        <h3>Informații Sistem</h3>
                        <div class="booking-info-item">
                            <strong>ID Rezervare:</strong> <?= $booking['id'] ?>
                        </div>
                        <div class="booking-info-item">
                            <strong>Data Creare:</strong> <?= formatDateRo($booking['created_at']) ?>
                        </div>
                        <div class="booking-info-item">
                            <strong>Status:</strong> <?= getStatusText($booking['status']) ?>
                        </div>
                    </div>
                </div>
                
                <div class="booking-actions">
                    <h3>Actualizare Status</h3>
                    <form method="post" action="manage-booking.php?id=<?= $bookingId ?>">
                        <div class="form-group">
                            <label for="status">Status Nou:</label>
                            <select id="status" name="status" class="form-control">
                                <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>În Așteptare</option>
                                <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmat</option>
                                <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Anulat</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="update_status" class="btn btn-primary">Actualizează Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .booking-details {
        margin-bottom: 30px;
    }
    
    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .booking-info {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .booking-info-group {
        margin-bottom: 20px;
    }
    
    .booking-info-group h3 {
        margin-bottom: 15px;
        color: var(--secondary-color);
        font-size: 1.2rem;
    }
    
    .booking-info-item {
        margin-bottom: 10px;
    }
    
    .booking-info-item strong {
        display: inline-block;
        min-width: 150px;
    }
    
    .booking-services-list {
        list-style: none;
        padding: 0;
    }
    
    .booking-services-list li {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .booking-services-list li:last-child {
        border-bottom: none;
    }
    
    .booking-actions {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    
    .booking-actions h3 {
        margin-bottom: 15px;
        color: var(--secondary-color);
        font-size: 1.2rem;
    }
</style>

<?php include '../includes/footer.php'; ?>
