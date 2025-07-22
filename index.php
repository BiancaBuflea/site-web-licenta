<?php
$pageTitle = "Acasă";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('assets/images/hero-bg.jpg');">
    <div class="container">
        <div class="hero-content">
            <h1>Organizăm Evenimentele Tale Speciale</h1>
            <p>CrazyCrew Events&More oferă servicii complete pentru evenimente memorabile. De la nunți și botezuri, la petreceri corporate și aniversări, suntem aici să transformăm visele tale în realitate.</p>
            <div class="hero-buttons">
                <a href="services.php" class="btn btn-primary">Serviciile Noastre</a>
                <a href="booking.php" class="btn btn-secondary">Rezervă Acum</a>
            </div>
        </div>
    </div>
</section>

<!-- Servicii Populare -->
<section class="featured-services">
    <div class="container">
        <div class="section-header">
            <h2>Servicii Populare</h2>
            <p>Descoperă cele mai solicitate servicii pentru evenimentele tale</p>
        </div>
        
        <div class="services-grid">
            <?php
            // Obține serviciile populare
            $stmt = $pdo->query("SELECT * FROM services ORDER BY id LIMIT 3");
            $services = $stmt->fetchAll();
            
            foreach ($services as $service):
                // Definim un array cu imagini pentru fiecare serviciu bazat pe ID
                $serviceImages = [
                    1 => 'assets/images/service1.jpg', // Înlocuiți cu calea reală pentru primul serviciu
                    2 => 'assets/images/service2.jpg', // Înlocuiți cu calea reală pentru al doilea serviciu
                    3 => 'assets/images/service3.jpg', // Înlocuiți cu calea reală pentru al treilea serviciu
                ];
                
                // Obținem imaginea corespunzătoare sau folosim o imagine implicită
                $imagePath = isset($serviceImages[$service['id']]) 
                    ? $serviceImages[$service['id']] 
                    : 'assets/images/service-placeholder.jpg';
            ?>
            <div class="service-card">
                <div class="service-image">
                    <img src="<?= $imagePath ?>" alt="<?= $service['name'] ?>">
                </div>
                <div class="service-content">
                    <h3><?= $service['name'] ?></h3>
                    <p><?= substr($service['description'], 0, 100) ?>...</p>
                    <div class="service-card-footer">
                        <a href="services.php" class="btn btn-primary">Detalii</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="view-all-services">
            <a href="services.php" class="btn btn-secondary">Vezi Toate Serviciile</a>
        </div>
    </div>
</section>

<!-- Despre Noi -->
<section class="home-about">
    <div class="container">
        <div class="home-about-content">
            <div class="home-about-text">
                <h2>Despre CrazyCrew Events</h2>
                <p>Cu o experiență de peste 5 ani în organizarea evenimentelor, echipa CrazyCrew Events&More este dedicată să ofere servicii de cea mai înaltă calitate pentru clienții noștri.</p>
                <p>Ne mândrim cu atenția la detalii, profesionalismul și capacitatea de a transforma orice eveniment într-o experiență de neuitat.</p>
                <a href="about.php" class="btn btn-primary">Află Mai Multe</a>
            </div>
            <div class="home-about-image">
                <img src="assets/images/about-home.jpg" alt="Despre CrazyCrew Events">
            </div>
        </div>
    </div>
</section>

<!-- Testimoniale -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>Ce Spun Clienții Noștri</h2>
            <p>Experiențele celor care au ales serviciile noastre</p>
        </div>
        
        <div class="testimonials-slider">
            <div class="testimonial-item">
                <div class="testimonial-content">
                    <p>"Am colaborat cu CrazyCrew Events pentru nunta noastră și totul a fost perfect! Recomand cu încredere serviciile lor pentru orice eveniment special."</p>
                </div>
                <div class="testimonial-author">
                    <div class="author-name">Maria și Andrei</div>
                    <div class="author-event">Nuntă, Iulie 2023</div>
                </div>
            </div>
            
            <div class="testimonial-item">
                <div class="testimonial-content">
                    <p>"Profesionalism, punctualitate și servicii de calitate. Petrecerea corporate organizată de CrazyCrew a fost un real succes!"</p>
                </div>
                <div class="testimonial-author">
                    <div class="author-name">Alexandru Popescu</div>
                    <div class="author-event">Eveniment Corporate, Decembrie 2023</div>
                </div>
            </div>
            
            <div class="testimonial-item">
                <div class="testimonial-content">
                    <p>"Botezul fiicei noastre a fost organizat impecabil. Mulțumim echipei CrazyCrew pentru că a făcut această zi atât de specială pentru noi!"</p>
                </div>
                <div class="testimonial-author">
                    <div class="author-name">Elena și Mihai</div>
                    <div class="author-event">Botez, Septembrie 2023</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Pregătit să organizezi un eveniment de neuitat?</h2>
            <p>Contactează-ne acum pentru a discuta despre viziunea ta și pentru a primi o ofertă personalizată.</p>
            <div class="cta-buttons">
                <a href="booking.php" class="btn btn-primary">Rezervă Acum</a>
                <a href="contact.php" class="btn btn-secondary">Contactează-ne</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
