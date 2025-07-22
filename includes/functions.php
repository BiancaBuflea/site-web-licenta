<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect to a specific page
function redirect($page) {
    header("Location: $page");
    exit;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if a service is available on a specific date
function isServiceAvailable($serviceId, $date, $pdo) {
    $query = "SELECT b.id FROM bookings b 
              JOIN booking_services bs ON b.id = bs.booking_id 
              WHERE bs.service_id = :service_id 
              AND b.event_date = :event_date 
              AND b.status = 'confirmed'";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':service_id' => $serviceId,
        ':event_date' => $date
    ]);
    
    return $stmt->rowCount() === 0;
}

// Get all services
function getAllServices($pdo) {
    $stmt = $pdo->query("SELECT * FROM services");
    return $stmt->fetchAll();
}

// Get service by ID
function getServiceById($id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Get all bookings
function getAllBookings($pdo) {
    $stmt = $pdo->query("SELECT b.*, u.username FROM bookings b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC");
    return $stmt->fetchAll();
}

// Get bookings by user ID
function getBookingsByUserId($userId, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

// Get services for a booking
function getServicesForBooking($bookingId, $pdo) {
    $stmt = $pdo->prepare("
        SELECT s.* FROM services s
        JOIN booking_services bs ON s.id = bs.service_id
        WHERE bs.booking_id = :booking_id
    ");
    $stmt->execute([':booking_id' => $bookingId]);
    return $stmt->fetchAll();
}

// Format date to Romanian format
function formatDateRo($date) {
    $timestamp = strtotime($date);
    return date('d.m.Y', $timestamp);
}

// Get status text in Romanian
function getStatusText($status) {
    switch($status) {
        case 'pending':
            return 'În Așteptare';
        case 'confirmed':
            return 'Confirmată';
        case 'cancelled':
            return 'Anulată';
        default:
            return ucfirst($status);
    }
}

// Obține caracteristicile pentru un serviciu
function getServiceFeatures($serviceId) {
    // Aici puteți extinde funcționalitatea pentru a prelua caracteristicile din baza de date
    // Momentan, vom folosi un array static pentru demonstrație
    $allFeatures = [
        1 => [ // Lemonade Bar
            'Minim 3 tipuri de limonadă proaspătă',
            'Pahare și accesorii',
            'Personal specializat',
            'Decor tematic pentru bar'
        ],
        2 => [ // Shisha Bar
            'Narghilele premium',
            'Diverse arome de tutun',
            'Personal specializat',
            'Zonă de relaxare amenajată'
        ],
        3 => [ // Wine Bar
            'Selecție de vinuri locale și internaționale',
            'Pahare de vin profesionale',
            'Somelier dedicat',
            'Decor elegant pentru bar'
        ],
        4 => [ // Cocktail Bar
            'Cocktailuri clasice și de specialitate',
            'Bartenderi profesioniști',
            'Show de flair bartending',
            'Bar mobil complet echipat'
        ],
        5 => [ // Tortul Mirelul
            'Un tort nonconformist, creat special pentru petreceri',
            'Zeci de shoturi dispuse artistic pe mai multe niveluri',
            'Design spectaculos, ideal pentru poze și momentul „WOW”',
            'Startul perfect pentru o seară incendiară'
        ],
        6 => [ // Efecte Speciale
            'Fum greu pentru dansul mirilor',
            'Artificii reci pentru interior',
            'Mașini de confetti',
            'Tehnician specializat'
        ]
    ];
    
    return isset($allFeatures[$serviceId]) ? $allFeatures[$serviceId] : [];
}
