<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Metodă de cerere invalidă']);
    exit;
}

$date = isset($_POST['date']) ? sanitize($_POST['date']) : '';
$services = isset($_POST['services']) ? json_decode($_POST['services'], true) : [];

if (empty($date) || empty($services)) {
    echo json_encode(['error' => 'Parametri lipsă']);
    exit;
}

$unavailableServices = [];
foreach ($services as $serviceId) {
    if (!isServiceAvailable($serviceId, $date, $pdo)) {
        $service = getServiceById($serviceId, $pdo);
        $unavailableServices[] = $service['name'];
    }
}

if (empty($unavailableServices)) {
    echo json_encode(['available' => true]);
} else {
    echo json_encode([
        'available' => false,
        'unavailableServices' => $unavailableServices
    ]);
}
