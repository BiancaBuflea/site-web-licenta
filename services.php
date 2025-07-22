<?php
$pageTitle = "Servicii";
include 'includes/header.php';

$services = getAllServices($pdo);
?>

<section class="page-header">
    <div class="container">
        <h1>Serviciile Noastre</h1>
        <p>Descoperă gama noastră variată de servicii pentru evenimente</p>
    </div>
</section>

<section class="services-detailed">
    <div class="container">
        <?php 
        foreach ($services as $index => $service): 
            // Definim un array cu imagini pentru fiecare serviciu bazat pe ID
            $serviceImages = [
                1 => 'assets/images/service1.jpg',
                2 => 'assets/images/service2.jpg',
                3 => 'assets/images/service3.jpg',
                4 => 'assets/images/service4.jpg',
                5 => 'assets/images/service5.jpg',
                6 => 'assets/images/service6.jpg',
                // Adăugați mai multe imagini pentru alte servicii dacă este necesar
            ];
            
            // Obținem imaginea corespunzătoare sau folosim o imagine implicită
            $imagePath = isset($serviceImages[$service['id']]) 
                ? $serviceImages[$service['id']] 
                : 'assets/images/service-placeholder.jpg';
        
            // Obținem caracteristicile serviciului
            $service['features'] = getServiceFeatures($service['id']);
            
            // Determinăm dacă este un index par sau impar pentru a alterna layout-ul
            $isEven = $index % 2 === 0;
        ?>
        <div class="service-item" id="<?= strtolower(str_replace(' ', '-', $service['name'])) ?>">
            <div class="service-content <?= $isEven ? 'service-content-left' : 'service-content-right' ?>">
                <?php if ($isEven): ?>
                <div class="service-image">
                    <img src="<?= $imagePath ?>" alt="<?= $service['name'] ?>">
                </div>
                <div class="service-details">
                    <h2><?= $service['name'] ?></h2>
                    <p><?= $service['description'] ?></p>
                    
                    <?php if (!empty($service['features'])): ?>
                    <div class="service-features">
                        <h3>Ce include:</h3>
                        <ul>
                            <?php foreach ($service['features'] as $feature): ?>
                            <li><?= $feature ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <a href="booking.php" class="btn btn-primary">Rezervă Acum</a>
                </div>
                <?php else: ?>
                <div class="service-details">
                    <h2><?= $service['name'] ?></h2>
                    <p><?= $service['description'] ?></p>
                    
                    <?php if (!empty($service['features'])): ?>
                    <div class="service-features">
                        <h3>Ce include:</h3>
                        <ul>
                            <?php foreach ($service['features'] as $feature): ?>
                            <li><?= $feature ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <a href="booking.php" class="btn btn-primary">Rezervă Acum</a>
                </div>
                <div class="service-image">
                    <img src="<?= $imagePath ?>" alt="<?= $service['name'] ?>">
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
