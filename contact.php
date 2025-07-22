<?php
$pageTitle = "Contact";
require_once 'includes/config.php';
require_once 'includes/functions.php';

$success = false;
$errors = [];

// Procesează formularul de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Validare
    if (empty($name)) {
        $errors[] = "Numele este obligatoriu.";
    }
    
    if (empty($email)) {
        $errors[] = "Adresa de email este obligatorie.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresa de email nu este validă.";
    }
    
    if (empty($subject)) {
        $errors[] = "Subiectul este obligatoriu.";
    }
    
    if (empty($message)) {
        $errors[] = "Mesajul este obligatoriu.";
    }
    
    // Dacă nu există erori, salvează mesajul în baza de date
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':subject' => $subject,
                ':message' => $message
            ]);
            
            $success = true;
            
            // Resetează valorile formularului după trimitere
            $name = $email = $phone = $subject = $message = '';
        } catch (PDOException $e) {
            $errors[] = "A apărut o eroare la trimiterea mesajului. Vă rugăm să încercați din nou.";
        }
    }
}

include 'includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Contactează-ne</h1>
        <p>Suntem aici pentru a răspunde întrebărilor tale</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <p>Mesajul tău a fost trimis cu succes! Te vom contacta în curând.</p>
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
        
        <div class="contact-container">
            <div class="contact-info">
                <h2>Informații de Contact</h2>
                <p>Nu ezita să ne contactezi pentru orice întrebare sau solicitare. Suntem aici pentru a te ajuta!</p>
                
                <div class="contact-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Adresă</h4>
                        <p>Strada Exemplu, Nr. 123, București, România</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Telefon</h4>
                        <p>+40 712 345 678</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email</h4>
                        <p>events.crazycrew@gmail.com</p>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Program</h4>
                        <p>Luni - Vineri: 9:00 - 18:00</p>
                        <p>Sâmbătă: 10:00 - 15:00</p>
                        <p>Duminică: Închis</p>
                    </div>
                </div>
                
                <div class="social-links">
                    <h4>Urmărește-ne</h4>
                    <a href="https://www.facebook.com/EventsCrazyCrew?locale=ro_RO"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/crazycrewevents24/"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@crazycrewevents"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Trimite-ne un Mesaj</h2>
                <form method="post" action="contact.php">
                    <div class="form-group">
                        <label for="name">Nume *</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= isset($name) ? $name : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= isset($email) ? $email : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefon</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?= isset($phone) ? $phone : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subiect *</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?= isset($subject) ? $subject : '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Mesaj *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required><?= isset($message) ? $message : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Trimite Mesajul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h2>Locația Noastră</h2>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d91158.11409353752!2d26.0311541!3d44.4377401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40b1f93abf3cad4f%3A0xac0632e37c9ca628!2sBucure%C8%99ti!5e0!3m2!1sro!2sro!4v1651234567890!5m2!1sro!2sro" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
