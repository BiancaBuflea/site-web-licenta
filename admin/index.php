<?php
$pageTitle = "Admin Dashboard";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Obține statistici pentru dashboard
$stats = [];

// Numărul total de rezervări
$stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
$stats['total_bookings'] = $stmt->fetchColumn();

// Numărul de rezervări în așteptare
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = :status");
$stmt->execute([':status' => 'pending']);
$stats['pending_bookings'] = $stmt->fetchColumn();

// Numărul total de utilizatori
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$stats['total_users'] = $stmt->fetchColumn();

// Numărul total de servicii
$stmt = $pdo->query("SELECT COUNT(*) FROM services");
$stats['total_services'] = $stmt->fetchColumn();

// Numărul de mesaje necitite
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$stats['unread_messages'] = $stmt->fetchColumn();

// Activitate recentă
$recentActivity = [];

// Rezervări recente
$stmt = $pdo->query("SELECT b.id, u.username, b.event_date, b.status, b.created_at 
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.id 
                     ORDER BY b.created_at DESC LIMIT 5");
$recentBookings = $stmt->fetchAll();

// Mesaje recente
$stmt = $pdo->query("SELECT id, name, subject, created_at, is_read 
                     FROM contact_messages 
                     ORDER BY created_at DESC LIMIT 5");
$recentMessages = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <h1>Dashboard Admin</h1>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="bookings.php">Rezervări</a></li>
                <li><a href="users.php">Utilizatori</a></li>
                <li><a href="services.php">Servicii</a></li>
                <li><a href="messages.php">Mesaje</a></li>
            </ul>
        </div>
        
        <div class="admin-panel">
            <h2>Statistici Generale</h2>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Rezervări Totale</h3>
                    <div class="stat-value"><?= $stats['total_bookings'] ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Rezervări în Așteptare</h3>
                    <div class="stat-value"><?= $stats['pending_bookings'] ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Utilizatori</h3>
                    <div class="stat-value"><?= $stats['total_users'] ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Servicii</h3>
                    <div class="stat-value"><?= $stats['total_services'] ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Mesaje Necitite</h3>
                    <div class="stat-value"><?= $stats['unread_messages'] ?></div>
                </div>
            </div>
            
            <div class="recent-activity">
                <h2>Activitate Recentă</h2>
                
                <div class="activity-section">
                    <h3>Rezervări Recente</h3>
                    <?php if (empty($recentBookings)): ?>
                    <p>Nu există rezervări recente.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Utilizator</th>
                                    <th>Data Eveniment</th>
                                    <th>Status</th>
                                    <th>Data Creare</th>
                                    <th>Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td><?= $booking['id'] ?></td>
                                    <td><?= $booking['username'] ?></td>
                                    <td><?= formatDateRo($booking['event_date']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $booking['status'] ?>">
                                            <?= getStatusText($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= formatDateRo($booking['created_at']) ?></td>
                                    <td>
                                        <a href="manage-booking.php?id=<?= $booking['id'] ?>" class="btn btn-primary btn-sm">Detalii</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="activity-section">
                    <h3>Mesaje Recente</h3>
                    <?php if (empty($recentMessages)): ?>
                    <p>Nu există mesaje recente.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nume</th>
                                    <th>Subiect</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMessages as $message): ?>
                                <tr>
                                    <td><?= $message['id'] ?></td>
                                    <td><?= $message['name'] ?></td>
                                    <td><?= $message['subject'] ?></td>
                                    <td><?= formatDateRo($message['created_at']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $message['is_read'] ? 'confirmed' : 'pending' ?>">
                                            <?= $message['is_read'] ? 'Citit' : 'Necitit' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view-message.php?id=<?= $message['id'] ?>" class="btn btn-primary btn-sm">Vizualizare</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
