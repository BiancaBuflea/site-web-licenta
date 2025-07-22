<?php
$pageTitle = "Galerie";
include 'includes/header.php';

// Definim categoriile de evenimente pentru filtrare
$categories = [
    'all' => 'Toate',
    'weddings' => 'Nuntă',
    'corporate' => 'Shots/Tortul Mirelui',
    'private' => 'Private',
    'cocktails' => 'Cocktails'
];

// Simulăm o listă de imagini din galerie
// În implementarea reală, acestea ar putea fi preluate din baza de date
$gallery_images = [
    [
        'id' => 1,
        'title' => 'Shisa Bar-Nuntă',
        'description' => 'Shisha bar la nunta Andreei și a lui Mihai',
        'image' => 'assets/images/gallery/wedding-1.jpg',
        'category' => 'weddings',
        'date' => '15.06.2023'
    ],
    [
        'id' => 2,
        'title' => 'Tortul Mirelul',
        'description' => 'Efect special cu tortul mirelui pentru nunta Alexandrei si a lui Mihai',
        'image' => 'assets/images/gallery/corporate-1.jpg',
        'category' => 'corporate',
        'date' => '22.07.2023'
    ],
    [
        'id' => 3,
        'title' => 'Petrecere privată',
        'description' => 'Wine Bar pentru botezul Georgiei',
        'image' => 'assets/images/gallery/private-1.jpg',
        'category' => 'private',
        'date' => '10.08.2023'
    ],
    [
        'id' => 4,
        'title' => 'Nunta Luanei și a lui Mihai',
        'description' => 'Cocktailuri Colorate',
        'image' => 'assets/images/gallery/cocktails-1.jpg',
        'category' => 'cocktails',
        'date' => '05.09.2023'
    ],
    [
        'id' => 5,
        'title' => 'Nuntă în aer liber',
        'description' => 'Cocktail Bar pentru nunta în tematica Crazy',
        'image' => 'assets/images/gallery/wedding-2.jpg',
        'category' => 'weddings',
        'date' => '18.06.2023'
    ],
    [
        'id' => 6,
        'title' => 'Conferință anuală',
        'description' => 'Shoturi colorate pentru conferința anuală',
        'image' => 'assets/images/gallery/corporate-2.jpg',
        'category' => 'corporate',
        'date' => '30.07.2023'
    ],
    [
        'id' => 7,
        'title' => 'Petrecere de absolvire',
        'description' => 'Cocktail Bar pentru absolvire',
        'image' => 'assets/images/gallery/private-2.jpg',
        'category' => 'private',
        'date' => '25.06.2023'
    ],
    [
        'id' => 8,
        'title' => 'Cocktail Signature',
        'description' => 'Cocktailuri personalizate pentru eveniment VIP',
        'image' => 'assets/images/gallery/cocktails-2.jpg',
        'category' => 'cocktails',
        'date' => '12.08.2023'
    ],
    [
        'id' => 9,
        'title' => 'Nuntă tematică',
        'description' => 'Bar tematic pentru nunta cu temă vintage',
        'image' => 'assets/images/gallery/wedding-3.jpg',
        'category' => 'weddings',
        'date' => '02.07.2023'
    ],
    [
        'id' => 10,
        'title' => 'Efecte speciale',
        'description' => 'Activități de mixologie pentru nunta Alexandrei si a lui Mihai',
        'image' => 'assets/images/gallery/corporate-3.jpg',
        'category' => 'corporate',
        'date' => '15.09.2023'
    ],
    [
        'id' => 11,
        'title' => 'Majorat',
        'description' => 'Lemonade Bar pentru petrecere de majorat',
        'image' => 'assets/images/gallery/private-3.jpg',
        'category' => 'private',
        'date' => '20.08.2023'
    ],
    [
        'id' => 12,
        'title' => 'Cocktail Exotic',
        'description' => ' Cocktail exotic pentru eveniment de vară',
        'image' => 'assets/images/gallery/cocktails-3.jpg',
        'category' => 'cocktails',
        'date' => '28.07.2023'
    ]
];

// Obține categoria curentă din query string sau folosește 'all' ca default
$current_category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<section class="page-header">
    <div class="container">
        <h1>Galerie</h1>
        <p>Imagini de la evenimentele noastre memorabile</p>
    </div>
</section>

<section class="gallery-section">
    <div class="container">
        <!-- Filtre pentru categorii -->
        <div class="gallery-filters">
            <ul>
                <?php foreach ($categories as $key => $name): ?>
                <li>
                    <a href="?category=<?= $key ?>" class="<?= $current_category === $key ? 'active' : '' ?>">
                        <?= $name ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Grid de imagini -->
        <div class="gallery-grid">
            <?php 
            foreach ($gallery_images as $image): 
                // Afișează imaginea doar dacă face parte din categoria selectată sau dacă categoria este 'all'
                if ($current_category === 'all' || $image['category'] === $current_category):
            ?>
            <div class="gallery-item" data-category="<?= $image['category'] ?>">
                <div class="gallery-image">
                    <img src="<?= $image['image'] ?>" alt="<?= $image['title'] ?>">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3><?= $image['title'] ?></h3>
                            <p><?= $image['description'] ?></p>
                            <span class="gallery-date"><?= $image['date'] ?></span>
                        </div>
                        <a href="<?= $image['image'] ?>" class="gallery-zoom" data-lightbox="gallery" data-title="<?= $image['title'] ?>: <?= $image['description'] ?>">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
        
        <!-- Mesaj pentru când nu există imagini în categoria selectată -->
        <?php if (!array_filter($gallery_images, function($img) use ($current_category) { 
            return $current_category === 'all' || $img['category'] === $current_category; 
        })): ?>
        <div class="no-images">
            <p>Nu există imagini în această categorie momentan.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox pentru vizualizarea imaginilor -->
<div id="gallery-lightbox" class="lightbox">
    <div class="lightbox-content">
        <span class="close-lightbox">&times;</span>
        <img id="lightbox-image" src="/placeholder.svg" alt="">
        <div class="lightbox-caption"></div>
        <a class="prev-image">&#10094;</a>
        <a class="next-image">&#10095;</a>
    </div>
</div>

<!-- Adăugăm script-ul pentru lightbox -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selectăm toate imaginile din galerie
    const galleryItems = document.querySelectorAll('.gallery-zoom');
    const lightbox = document.getElementById('gallery-lightbox');
    const lightboxImg = document.getElementById('lightbox-image');
    const lightboxCaption = document.querySelector('.lightbox-caption');
    const closeLightbox = document.querySelector('.close-lightbox');
    const prevButton = document.querySelector('.prev-image');
    const nextButton = document.querySelector('.next-image');
    
    let currentIndex = 0;
    const images = [];
    
    // Colectăm toate imaginile și titlurile lor
    galleryItems.forEach((item, index) => {
        images.push({
            src: item.getAttribute('href'),
            title: item.getAttribute('data-title')
        });
        
        // Adăugăm event listener pentru click pe imagine
        item.addEventListener('click', function(e) {
            e.preventDefault();
            currentIndex = index;
            openLightbox(currentIndex);
        });
    });
    
    // Funcție pentru deschiderea lightbox-ului
    function openLightbox(index) {
        lightboxImg.src = images[index].src;
        lightboxCaption.textContent = images[index].title;
        lightbox.style.display = 'flex';
        
        // Dezactivăm scroll-ul pe pagină
        document.body.style.overflow = 'hidden';
    }
    
    // Funcție pentru închiderea lightbox-ului
    function closeLightboxFunc() {
        lightbox.style.display = 'none';
        
        // Reactivăm scroll-ul pe pagină
        document.body.style.overflow = 'auto';
    }
    
    // Event listener pentru butonul de închidere
    closeLightbox.addEventListener('click', closeLightboxFunc);
    
    // Închide lightbox-ul când se face click în afara imaginii
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightboxFunc();
        }
    });
    
    // Navigare la imaginea anterioară
    prevButton.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        openLightbox(currentIndex);
    });
    
    // Navigare la imaginea următoare
    nextButton.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % images.length;
        openLightbox(currentIndex);
    });
    
    // Navigare cu tastatura
    document.addEventListener('keydown', function(e) {
        if (lightbox.style.display === 'flex') {
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                openLightbox(currentIndex);
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % images.length;
                openLightbox(currentIndex);
            } else if (e.key === 'Escape') {
                closeLightboxFunc();
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
