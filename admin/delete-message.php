<?php
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

// Verifică dacă mesajul există
$stmt = $pdo->prepare("SELECT id FROM contact_messages WHERE id = :id");
$stmt->execute([':id' => $messageId]);
$message = $stmt->fetch();

if (!$message) {
    redirect('messages.php');
}

// Șterge mesajul
$stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
$stmt->execute([':id' => $messageId]);

// Redirecționează înapoi la lista de mesaje
redirect('messages.php');
?>
