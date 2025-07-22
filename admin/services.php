<?php
$pageTitle = "Administrare Servicii";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Procesează editarea serviciului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $serviceId = (int)$_POST['service_id'];
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $image_path = sanitize($_POST['image_path']); // Adăugat
    
    // Validare
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Numele serviciului este obligatoriu.";
    }
    
    if (empty($description)) {
        $errors[] = "Descrierea serviciului este obligatorie.";
    }
    
    // Dacă nu există erori, actualizează serviciul
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE services SET name = :name, description = :description, image_path = :image_path WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image_path' => $image_path, // Adăugat
            ':id' => $serviceId
        ]);
        
        $success = "Serviciul a fost actualizat cu succes.";
        
        // Resetează modul de editare
        unset($_GET['edit']);
    }
}

// Procesează adăugarea serviciului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $image_path = sanitize($_POST['image_path']); // Adăugat
    
    // Validare
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Numele serviciului este obligatoriu.";
    }
    
    if (empty($description)) {
        $errors[] = "Descrierea serviciului este obligatorie.";
    }
    
    // Dacă nu există erori, adaugă serviciul
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO services (name, description, image_path) VALUES (:name, :description, :image_path)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':image_path' => $image_path // Adăugat
        ]);
        
        $success = "Serviciul a fost adăugat cu succes.";
        
        // Resetează modul de adăugare
        unset($_GET['add']);
    }
}

// Procesează ștergerea serviciului
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $serviceId = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute([':id' => $serviceId]);
    
    $success = "Serviciul a fost șters cu succes.";
}

// Obține serviciul pentru editare
$editService = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $serviceId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute([':id' => $serviceId]);
    $editService = $stmt->fetch();
    
    if (!$editService) {
        redirect('services.php');
    }
}

// Obține toate serviciile
$stmt = $pdo->query("SELECT * FROM services ORDER BY name");
$services = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-services">
    <div class="container">
        <div class="admin-header">
            <h1>Administrare Servicii</h1>
            <a href="<?= SITE_URL ?>/admin" class="btn btn-secondary">Înapoi la Dashboard</a>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="bookings.php">Rezervări</a></li>
                <li><a href="users.php">Utilizatori</a></li>
                <li><a href="services.php" class="active">Servicii</a></li>
                <li><a href="messages.php">Mesaje</a></li>
            </ul>
        </div>
        
        <div class="admin-panel">
            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <p><?= $success ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['add'])): ?>
            <!-- Formular pentru adăugare serviciu -->
            <div class="admin-form">
                <h2>Adăugare Serviciu Nou</h2>
                <form method="post" action="services.php">
                    <div class="form-group">
                        <label for="name">Nume Serviciu:</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descriere:</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_path">Calea Imaginii:</label>
                        <input type="text" id="image_path" name="image_path" class="form-control" value="<?= isset($_POST['image_path']) ? $_POST['image_path'] : (isset($editService['image_path']) ? $editService['image_path'] : '') ?>">
                        <small class="form-text">Exemplu: assets/images/nume-serviciu.jpg</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="add_service" class="btn btn-primary">Adaugă Serviciu</button>
                        <a href="services.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
            </div>
            <?php elseif ($editService): ?>
            <!-- Formular pentru editare serviciu -->
            <div class="admin-form">
                <h2>Editare Serviciu</h2>
                <form method="post" action="services.php">
                    <input type="hidden" name="service_id" value="<?= $editService['id'] ?>">
                    
                    <div class="form-group">
                        <label for="name">Nume Serviciu:</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= isset($_POST['name']) ? $_POST['name'] : $editService['name'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descriere:</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= isset($_POST['description']) ? $_POST['description'] : $editService['description'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_path">Calea Imaginii:</label>
                        <input type="text" id="image_path" name="image_path" class="form-control" value="<?= isset($_POST['image_path']) ? $_POST['image_path'] : (isset($editService['image_path']) ? $editService['image_path'] : '') ?>">
                        <small class="form-text">Exemplu: assets/images/nume-serviciu.jpg</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="edit_service" class="btn btn-primary">Actualizează Serviciu</button>
                        <a href="services.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- Lista serviciilor -->
            <h2>Lista Serviciilor</h2>
            
            <?php if (empty($services)): ?>
            <p>Nu există servicii înregistrate.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume</th>
                            <th>Descriere</th>
                            <th>Imagine</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td><?= $service['name'] ?></td>
                            <td><?= substr($service['description'], 0, 100) ?>...</td>
                            <td><?= !empty($service['image_path']) ? $service['image_path'] : 'Nicio imagine' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="services.php?edit=<?= $service['id'] ?>" class="btn btn-primary btn-sm">Editează</a>
                                    <a href="services.php?delete=<?= $service['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sigur doriți să ștergeți acest serviciu?')">Șterge</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="admin-actions">
                <a href="services.php?add=1" class="btn btn-primary">Adaugă Serviciu Nou</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .admin-form {
        background-color: var(--container-bg);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .admin-form h2 {
        margin-bottom: 20px;
        color: var(--secondary-color);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: #fff;
    }
    
    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .alert ul {
        margin: 0;
        padding-left: 20px;
    }
</style>

<?php include '../includes/footer.php'; ?>
