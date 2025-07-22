<?php
$pageTitle = "Administrare Mesaje";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Filtrare mesaje
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT * FROM contact_messages";
$params = [];

$where_clauses = [];
if ($status !== '') {
    $is_read = ($status === 'read') ? 1 : 0;
    $where_clauses[] = "is_read = :is_read";
    $params[':is_read'] = $is_read;
}
if (!empty($date_from)) {
    $where_clauses[] = "created_at >= :date_from";
    $params[':date_from'] = $date_from . ' 00:00:00';
}
if (!empty($date_to)) {
    $where_clauses[] = "created_at <= :date_to";
    $params[':date_to'] = $date_to . ' 23:59:59';
}
if (!empty($search)) {
    $where_clauses[] = "(name LIKE :search OR email LIKE :search OR subject LIKE :search OR message LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$messages = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-messages">
    <div class="container">
        <div class="admin-header">
            <h1>Administrare Mesaje</h1>
            <a href="<?= SITE_URL ?>/admin" class="btn btn-secondary">Înapoi la Dashboard</a>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="bookings.php">Rezervări</a></li>
                <li><a href="users.php">Utilizatori</a></li>
                <li><a href="services.php">Servicii</a></li>
                <li><a href="messages.php" class="active">Mesaje</a></li>
            </ul>
        </div>
        
        <div class="admin-panel">
            <div class="filter-form">
                <h3>Filtrare Mesaje</h3>
                <form method="get" action="messages.php">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">Toate</option>
                                <option value="unread" <?= $status === 'unread' ? 'selected' : '' ?>>Necitite</option>
                                <option value="read" <?= $status === 'read' ? 'selected' : '' ?>>Citite</option>
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
                        
                        <div class="filter-group">
                            <label for="search">Căutare</label>
                            <input type="text" id="search" name="search" class="form-control" value="<?= $search ?>" placeholder="Nume, email, subiect sau conținut...">
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">Filtrează</button>
                            <a href="messages.php" class="btn btn-secondary">Resetează</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <h2>Lista Mesajelor</h2>
            
            <?php if (empty($messages)): ?>
            <p>Nu există mesaje care să corespundă criteriilor de filtrare.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Subiect</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= $message['id'] ?></td>
                            <td><?= $message['name'] ?></td>
                            <td><?= $message['email'] ?></td>
                            <td><?= $message['phone'] ? $message['phone'] : '-' ?></td>
                            <td><?= $message['subject'] ?></td>
                            <td><?= formatDateRo($message['created_at']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $message['is_read'] ? 'confirmed' : 'pending' ?>">
                                    <?= $message['is_read'] ? 'Citit' : 'Necitit' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="view-message.php?id=<?= $message['id'] ?>" class="btn btn-primary btn-sm">Vizualizare</a>
                                    <a href="delete-message.php?id=<?= $message['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sigur doriți să ștergeți acest mesaj?')">Șterge</a>
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
