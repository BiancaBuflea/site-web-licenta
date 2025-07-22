<?php
$pageTitle = "Rezervare";
include 'includes/header.php';

// Verifică dacă utilizatorul este autentificat pentru trimiterea formularului
$loginRequired = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isLoggedIn()) {
    $loginRequired = true;
    $_SESSION['redirect_after_login'] = 'booking.php';
}

// Procesează formularul dacă este trimis și utilizatorul este autentificat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $event_date = sanitize($_POST['event_date']);
    $event_location = sanitize($_POST['event_location']);
    $event_description = sanitize($_POST['event_description']);
    $guests_count = isset($_POST['guests_count']) ? intval($_POST['guests_count']) : 0;
    $phone_number = sanitize($_POST['phone_number']);
    $services = isset($_POST['services']) ? $_POST['services'] : [];
    
    // Validează datele introduse
    $errors = [];
    if (empty($event_date)) {
        $errors[] = "Data evenimentului este obligatorie.";
    }
    if (empty($event_location)) {
        $errors[] = "Locația evenimentului este obligatorie.";
    }
    if (empty($services)) {
        $errors[] = "Trebuie să selectați cel puțin un serviciu.";
    }
    if ($guests_count <= 0) {
        $errors[] = "Numărul de persoane trebuie să fie mai mare decât 0.";
    }
    if (empty($phone_number)) {
        $errors[] = "Numărul de telefon este obligatoriu.";
    } elseif (!preg_match('/^[0-9+\s()-]{8,15}$/', $phone_number)) {
        $errors[] = "Numărul de telefon nu este valid.";
    }
    
    // Verifică disponibilitatea serviciilor
    $unavailableServices = [];
    foreach ($services as $serviceId) {
        if (!isServiceAvailable($serviceId, $event_date, $pdo)) {
            $service = getServiceById($serviceId, $pdo);
            $unavailableServices[] = $service['name'];
        }
    }
    
    if (!empty($unavailableServices)) {
        $errors[] = "Următoarele servicii nu sunt disponibile pentru data selectată: " . implode(", ", $unavailableServices);
    }
    
    // Dacă nu există erori, salvează rezervarea
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Inserează rezervarea
            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_date, event_location, event_description, guests_count, phone_number) VALUES (:user_id, :event_date, :event_location, :event_description, :guests_count, :phone_number)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':event_date' => $event_date,
                ':event_location' => $event_location,
                ':event_description' => $event_description,
                ':guests_count' => $guests_count,
                ':phone_number' => $phone_number
            ]);
            
            $bookingId = $pdo->lastInsertId();
            
            // Inserează serviciile rezervate
            $stmt = $pdo->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (:booking_id, :service_id)");
            foreach ($services as $serviceId) {
                $stmt->execute([
                    ':booking_id' => $bookingId,
                    ':service_id' => $serviceId
                ]);
            }
            
            $pdo->commit();
            $success = "Rezervarea a fost înregistrată cu succes! Veți fi contactat în curând pentru confirmare.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "A apărut o eroare la procesarea rezervării. Vă rugăm să încercați din nou.";
        }
    }
}

$services = getAllServices($pdo);
?>

<section class="page-header">
    <div class="container">
        <h1>Rezervare Servicii</h1>
        <p>Completați formularul de mai jos pentru a rezerva serviciile noastre</p>
    </div>
</section>

<section class="booking-section">
    <div class="container">
        <?php if ($loginRequired): ?>
        <div class="alert alert-warning">
            <p>Trebuie să fiți autentificat pentru a trimite o cerere de rezervare. <a href="auth/login.php">Autentificați-vă</a> sau <a href="auth/register.php">Creați un cont</a>.</p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <p><?= $success ?></p>
        </div>
        <?php endif; ?>
        
        <div class="booking-form">
            <form id="booking-form" method="post" action="booking.php">
                <div class="form-group">
                    <label for="event_date">Data Evenimentului *</label>
                    <input type="date" id="event_date" name="event_date" class="form-control" min="<?= date('Y-m-d'); ?>" required>

                </div>
                
                <div class="form-group">
                    <label for="event_location">Locația Evenimentului *</label>
                    <input type="text" id="event_location" name="event_location" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="guests_count">Numărul de Persoane *</label>
                    <input type="number" id="guests_count" name="guests_count" min="1" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="phone_number">Număr de Telefon *</label>
                    <input type="tel" id="phone_number" name="phone_number" class="form-control" placeholder="Ex: 0712345678" required>
                </div>
                
                <div class="form-group">
                    <label for="event_description">Descrierea Evenimentului</label>
                    <textarea id="event_description" name="event_description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Servicii Dorite *</label>
                    <?php foreach ($services as $service): ?>
                    <div class="service-checkbox">
                        <label>
                            <input type="checkbox" name="services[]" value="<?= $service['id'] ?>">
                            <?= $service['name'] ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-group">
                    <button type="button" id="check-availability" class="btn btn-secondary">Verifică Disponibilitatea</button>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?= isLoggedIn() ? 'Trimite Cererea' : 'Autentificare pentru Rezervare' ?></button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
document.getElementById('check-availability').addEventListener('click', function () {
  const date = document.getElementById('event_date').value;
  const services = Array.from(document.querySelectorAll('input[name="services[]"]:checked'))
    .map(el => el.value);

  if (!date || services.length === 0) {
    alert("Te rugăm să selectezi o dată și cel puțin un serviciu.");
    return;
  }

  fetch('check-availability.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      date: date,
      services: JSON.stringify(services)
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.available) {
      alert("Toate serviciile sunt disponibile pentru această dată.");
    } else if (data.unavailableServices) {
      alert("Serviciile indisponibile:\n" + data.unavailableServices.join(", "));
    } else if (data.error) {
      alert("Eroare: " + data.error);
    }
  })
  .catch(err => {
    console.error(err);
    alert("A apărut o eroare la verificarea disponibilității.");
  });
});
</script>



<?php include 'includes/footer.php'; ?>
