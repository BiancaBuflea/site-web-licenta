<?php
$pageTitle = "Autentificare";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirecționează dacă utilizatorul este deja autentificat
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Procesează formularul de autentificare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username-ul sau email-ul este obligatoriu.";
    }
    if (empty($password)) {
        $errors[] = "Parola este obligatorie.";
    }
    
    if (empty($errors)) {
        // Verifică dacă utilizatorul există
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([
            ':username' => $username,
            ':email' => $username
        ]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Autentificare reușită
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirecționează către pagina intenționată sau acasă
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                redirect($redirect);
            } else {
                redirect(SITE_URL);
            }
        } else {
            $errors[] = "Username/email sau parola incorecte.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <a href="<?= SITE_URL ?>" class="auth-logo">
                <h1>CrazyCrew Events&More</h1>
            </a>
            
            <h2>Autentificare</h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username sau Email</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Parola</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Autentificare</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
                <p><a href="<?= SITE_URL ?>">Înapoi la pagina principală</a></p>
            </div>
        </div>
    </div>
</body>
</html>
