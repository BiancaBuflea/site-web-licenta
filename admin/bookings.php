<?php
$pageTitle = "Administrare Rezervări";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Filtrează rezervările
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

$query = "SELECT b.*, u.username FROM bookings b JOIN users u ON b.user_id = u.id";
$params = [];

$where_clauses = [];
if (!empty($status)) {
    $where_clauses[] = "b.status = :status";
    $params[':status'] = $status;
}
if (!empty($date_from)) {
    $where_clauses[] = "b.event_date >= :date_from";
    $params[':date_from'] = $date_from;
}
if (!empty($date_to)) {
    $where_clauses[] = "b.event_date <= :date_to";
    $params[':date_to'] = $date_to;
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-bookings">
    <div class="container">
        <div class="admin-header">
            <h1>Administrare Rezervări</h1>
            <a href="<?= SITE_URL ?>/admin" class="btn btn-secondary">Înapoi la Dashboard</a>
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
            <div class="filter-form">
                <h3>Filtrare Rezervări</h3>
                <form method="get" action="bookings.php">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Toate</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>În Așteptare</option>
                                <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmate</option>
                                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Anulate</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_from">De la data</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" value="<?= $date_from ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">Până la data</label>
                            <input type="date" id="date_to" name="date_to" class="form-control" value="<?= $date_to ?>">
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">Filtrează</button>
                            <a href="bookings.php" class="btn btn-secondary">Resetează</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <h2>Lista Rezervărilor</h2>
            
            <?php if (empty($bookings)): ?>
            <p>Nu există rezervări care să corespundă criteriilor de filtrare.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilizator</th>
                            <th>Data Eveniment</th>
                            <th>Locație</th>
                            <th>Nr. Persoane</th>
                            <th>Telefon</th>
                            <th>Servicii</th>
                            <th>Status</th>
                            <th>Data Creare</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): 
                            $services = getServicesForBooking($booking['id'], $pdo);
                            $serviceNames = array_map(function($service) {
                                return $service['name'];
                            }, $services);
                        ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td><?= $booking['username'] ?></td>
                            <td><?= formatDateRo($booking['event_date']) ?></td>
                            <td><?= $booking['event_location'] ?></td>
                            <td><?= isset($booking['guests_count']) ? $booking['guests_count'] : '-' ?></td>
                            <td><?= isset($booking['phone_number']) ? $booking['phone_number'] : '-' ?></td>
                            <td><?= implode(', ', $serviceNames) ?></td>
                            <td>
                                <span class="status-badge status-<?= $booking['status'] ?>">
                                    <?= getStatusText($booking['status']) ?>
                                </span>
                            </td>
                            <td><?= formatDateRo($booking['created_at']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="manage-booking.php?id=<?= $booking['id'] ?>" class="btn btn-primary btn-sm">Detalii</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
