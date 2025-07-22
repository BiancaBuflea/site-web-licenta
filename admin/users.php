<?php
$pageTitle = "Administrare Utilizatori";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Procesează editarea utilizatorului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $userId = (int)$_POST['user_id'];
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Validare
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Numele de utilizator este obligatoriu.";
    }
    
    if (empty($email)) {
        $errors[] = "Adresa de email este obligatorie.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresa de email nu este validă.";
    }
    
    // Verifică dacă numele de utilizator sau email-ul există deja (exceptând utilizatorul curent)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':id' => $userId
    ]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        $errors[] = "Numele de utilizator sau adresa de email există deja.";
    }
    
    // Dacă nu există erori, actualizează utilizatorul
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, is_admin = :is_admin WHERE id = :id");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':is_admin' => $is_admin,
            ':id' => $userId
        ]);
        
        $success = "Utilizatorul a fost actualizat cu succes.";
        
        // Resetează modul de editare
        unset($_GET['edit']);
    }
}

// Procesează adăugarea utilizatorului
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Validare
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Numele de utilizator este obligatoriu.";
    }
    
    if (empty($email)) {
        $errors[] = "Adresa de email este obligatorie.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresa de email nu este validă.";
    }
    
    if (empty($password)) {
        $errors[] = "Parola este obligatorie.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Parola trebuie să aibă cel puțin 6 caractere.";
    }
    
    // Verifică dacă numele de utilizator sau email-ul există deja
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        $errors[] = "Numele de utilizator sau adresa de email există deja.";
    }
    
    // Dacă nu există erori, adaugă utilizatorul
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (:username, :email, :password, :is_admin)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':is_admin' => $is_admin
        ]);
        
        $success = "Utilizatorul a fost adăugat cu succes.";
        
        // Resetează modul de adăugare
        unset($_GET['add']);
    }
}

// Procesează ștergerea utilizatorului
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = (int)$_GET['delete'];
    
    // Nu permite ștergerea propriului cont
    if ($userId != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        
        $success = "Utilizatorul a fost șters cu succes.";
    } else {
        $errors = ["Nu puteți șterge propriul cont."];
    }
}

// Obține utilizatorul pentru editare
$editUser = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $userId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $editUser = $stmt->fetch();
    
    if (!$editUser) {
        redirect('users.php');
    }
}

// Obține toți utilizatorii
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

include '../includes/header.php';
?>

<section class="admin-users">
    <div class="container">
        <div class="admin-header">
            <h1>Administrare Utilizatori</h1>
            <a href="<?= SITE_URL ?>/admin" class="btn btn-secondary">Înapoi la Dashboard</a>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="bookings.php">Rezervări</a></li>
                <li><a href="users.php" class="active">Utilizatori</a></li>
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
            <!-- Formular pentru adăugare utilizator -->
            <div class="admin-form">
                <h2>Adăugare Utilizator Nou</h2>
                <form method="post" action="users.php">
                    <div class="form-group">
                        <label for="username">Nume Utilizator:</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Parolă:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="is_admin">
                            <input type="checkbox" id="is_admin" name="is_admin" <?= isset($_POST['is_admin']) ? 'checked' : '' ?>>
                            Administrator
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="add_user" class="btn btn-primary">Adaugă Utilizator</button>
                        <a href="users.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
            </div>
            <?php elseif ($editUser): ?>
            <!-- Formular pentru editare utilizator -->
            <div class="admin-form">
                <h2>Editare Utilizator</h2>
                <form method="post" action="users.php">
                    <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
                    
                    <div class="form-group">
                        <label for="username">Nume Utilizator:</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?= isset($_POST['username']) ? $_POST['username'] : $editUser['username'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? $_POST['email'] : $editUser['email'] ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="is_admin">
                            <input type="checkbox" id="is_admin" name="is_admin" <?= (isset($_POST['is_admin']) || $editUser['is_admin']) ? 'checked' : '' ?>>
                            Administrator
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="edit_user" class="btn btn-primary">Actualizează Utilizator</button>
                        <a href="users.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- Lista utilizatorilor -->
            <h2>Lista Utilizatorilor</h2>
            
            <?php if (empty($users)): ?>
            <p>Nu există utilizatori înregistrați.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume Utilizator</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Data Înregistrare</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['is_admin'] ? 'Administrator' : 'Utilizator' ?></td>
                            <td><?= formatDateRo($user['created_at']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="users.php?edit=<?= $user['id'] ?>" class="btn btn-primary btn-sm">Editează</a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sigur doriți să ștergeți acest utilizator?')">Șterge</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="admin-actions">
                <a href="users.php?add=1" class="btn btn-primary">Adaugă Utilizator Nou</a>
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
    
    .form-group label input[type="checkbox"] {
        margin-right: 5px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background-color: #fff;
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
