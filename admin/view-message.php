<?php
$pageTitle = "Vizualizare Mesaj";
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Verifică dacă ID-ul mesajului este furnizat
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('messages.php');
}

$messageId = (int)$_GET['id'];

// Obține detaliile mesajului
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = :id");
$stmt->execute([':id' => $messageId]);
$message = $stmt->fetch();

// Verifică dacă mesajul există
if (!$message) {
    redirect('messages.php');
}

// Marchează mesajul ca citit dacă nu este deja
if (!$message['is_read']) {
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = :id");
    $stmt->execute([':id' => $messageId]);
    $message['is_read'] = 1;
}

include '../includes/header.php';
?>

<section class="admin-view-message">
    <div class="container">
        <div class="admin-header">
            <h1>Vizualizare Mesaj</h1>
            <a href="messages.php" class="btn btn-secondary">Înapoi la Lista de Mesaje</a>
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
            <div class="message-details">
                <div class="message-header">
                    <h2><?= $message['subject'] ?></h2>
                    <div class="message-meta">
                        <span class="status-badge status-<?= $message['is_read'] ? 'confirmed' : 'pending' ?>">
                            <?= $message['is_read'] ? 'Citit' : 'Necitit' ?>
                        </span>
                        <span class="message-date"><?= formatDateRo($message['created_at']) ?></span>
                    </div>
                </div>
                
                <div class="message-sender-info">
                    <div class="sender-detail">
                        <strong>De la:</strong> <?= $message['name'] ?> (<?= $message['email'] ?>)
                    </div>
                    <?php if ($message['phone']): ?>
                    <div class="sender-detail">
                        <strong>Telefon:</strong> <?= $message['phone'] ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="message-content">
                    <h3>Mesaj:</h3>
                    <div class="message-body">
                        <?= nl2br(htmlspecialchars($message['message'])) ?>
                    </div>
                </div>
                
                <div class="message-actions">
                    <?php
                    // Construiește un mailto: URL cu parametri pre-completați
                    $mailtoSubject = "Re: " . rawurlencode($message['subject']);
                    $mailtoBody = "\n\n\n--------------------\nMesaj original de la: " . $message['name'] . "\nData: " . formatDateRo($message['created_at']) . "\n\n" . $message['message'];
                    $mailtoUrl = "mailto:" . $message['email'] . "?subject=" . $mailtoSubject . "&body=" . rawurlencode($mailtoBody);
                    ?>
                    <a href="<?= $mailtoUrl ?>" class="btn btn-primary">Răspunde prin Email</a>
                    <a href="delete-message.php?id=<?= $message['id'] ?>" class="btn btn-danger" onclick="return confirm('Sigur doriți să ștergeți acest mesaj?')">Șterge Mesajul</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .message-details {
        background-color: var(--container-bg);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .message-meta {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .message-date {
        color: #777;
    }
    
    .message-sender-info {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .sender-detail {
        margin-bottom: 10px;
    }
    
    .message-content {
        margin-bottom: 30px;
    }
    
    .message-body {
        background-color: rgba(255, 255, 255, 0.5);
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
        white-space: pre-line;
    }
    
    .message-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }
</style>

<?php include '../includes/footer.php'; ?>
