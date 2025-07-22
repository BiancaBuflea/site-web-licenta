<?php
$pageTitle = "Înregistrare";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirecționează dacă utilizatorul este deja autentificat
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Procesează formularul de înregistrare
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username-ul este obligatoriu.";
    }
    if (empty($email)) {
        $errors[] = "Email-ul este obligatoriu.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresa de email nu este validă.";
    }
    if (empty($password)) {
        $errors[] = "Parola este obligatorie.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Parola trebuie să aibă cel puțin 6 caractere.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Parolele nu coincid.";
    }
    
    // Verifică dacă username-ul sau email-ul există deja
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if ($user['username'] === $username) {
                $errors[] = "Username-ul este deja utilizat.";
            }
            if ($user['email'] === $email) {
                $errors[] = "Adresa de email este deja utilizată.";
            }
        }
    }
    
    // Înregistrează utilizatorul dacă nu există erori
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $result = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password
        ]);
        
        if ($result) {
            // Autentificare automată după înregistrare
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = 0;
            
            // Redirecționează către pagina intenționată sau acasă
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                redirect($redirect);
            } else {
                redirect(SITE_URL);
            }
        } else {
            $errors[] = "A apărut o eroare la înregistrare. Vă rugăm să încercați din nou.";
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
            
            <h2>Înregistrare</h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Parola</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmă Parola</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Înregistrare</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Ai deja cont? <a href="login.php">Autentifică-te</a></p>
                <p><a href="<?= SITE_URL ?>">Înapoi la pagina principală</a></p>
            </div>
        </div>
    </div>
</body>
</html>
