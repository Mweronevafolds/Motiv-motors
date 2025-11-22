<?php
require 'db.php';

// Fetch 3 featured cars
try {
    $stmt = $pdo->query("SELECT * FROM cars WHERE status = 'Available' ORDER BY id DESC LIMIT 3");
    $cars = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motiv Motors | Where Passion Finds Its Drive</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="nav-container" style="justify-content: center;">
            <div class="brand">
                <a href="index.php" class="logo" style="font-size: 2rem;">MOTIV MOTORS</a>
            </div>
        </div>
    </header>

    <section class="hero" style="height: 85vh; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
        
        <div style="position: absolute; inset: 0; z-index: -1;">
            <img src="https://images.unsplash.com/photo-1617788138017-80ad40651399?q=80&w=1920&auto=format&fit=crop" 
                 style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.4);">
        </div>

        <div class="glass-panel reveal" style="padding: 50px; max-width: 800px; text-align: center;">
            <h1 style="font-size: 3.5rem; margin-bottom: 10px; line-height: 1.1;">
                THE <span style="color: var(--accent-gold);">NAIROBI</span><br>COLLECTION
            </h1>
            <p style="font-size: 1.2rem; color: #ddd; margin-bottom: 30px; font-weight: 300;">
                Curated excellence for the discerning driver. 
                Experience the future of automotive luxury.
            </p>
            
            <div style="display: flex; gap: 20px; justify-content: center;">
                <a href="inventory.php" class="btn btn-accent" style="padding: 15px 40px; font-size: 1rem;">View Collection</a>
                <a href="#mission" class="btn" style="border: 1px solid white; color: white; padding: 15px 40px;">Our Ethos</a>
            </div>
        </div>

        <div style="position: absolute; bottom: 30px; animation: bounce 2s infinite;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M7 13l5 5 5-5M7 6l5 5 5-5"/>
            </svg>
        </div>
    </section>

    <style>
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
      40% {transform: translateY(-10px);}
      60% {transform: translateY(-5px);}
    }
    </style>

    <section class="mission reveal" id="mission">
        <h2 class="reveal">The Motiv Standard</h2>
        <p>At Motiv Motors, we believe a car is not just transportation; it is a kinetic sculpture, a piece of history, and a vessel for the soul. We curate only the finest examples of automotive engineering for the discerning collector.</p>
    </section>

    <section id="inventory" class="inventory">
        <h2 class="reveal">Curated Collection</h2>
        
        <div class="car-grid">
            <?php foreach ($cars as $car): ?>
            <div class="car-card">
                <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['model']) ?>" class="card-image">
                <div class="card-details">
                    <h3 class="card-title"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                    <div class="card-specs">
                        <span><?= number_format($car['mileage']) ?> mi</span>
                        <span><?= htmlspecialchars($car['status']) ?></span>
                    </div>
                    <div class="card-price">KES <?= number_format($car['price']) ?></div>
                    <a href="listing.php?id=<?= $car['id'] ?>" class="btn btn-primary" style="width:100%; text-align:center;">View Details</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer style="background: var(--primary-color); color: white; padding: 40px 20px; margin-top: 60px;">
    <div class="footer-content">
        <div>
            <h3 style="color: var(--accent-color); margin-bottom: 15px;">MOTIV MOTORS KENYA</h3>
            <p>Where Passion Finds Its Drive.</p>
            <br>
            <p><strong>Showroom:</strong><br>
            Karen Office Park, Lang'ata Road<br>
            Nairobi, Kenya</p>
        </div>
        <div style="text-align: right;">
            <p><strong>Contact Sales:</strong><br>
            +254 700 000 000<br>
            sales@motivmotors.co.ke</p>
            <br>
            <p>&copy; <?= date('Y') ?> Motiv Motors Ltd.</p>
        </div>
    </div>
</footer>

    <?php include 'navbar.php'; ?>

    <script>
        // Simple Intersection Observer alternative for Scroll Animations
        window.addEventListener('scroll', reveal);

        function reveal() {
            var reveals = document.querySelectorAll('.reveal, .car-card-3d');

            for (var i = 0; i < reveals.length; i++) {
                var windowheight = window.innerHeight;
                var revealtop = reveals[i].getBoundingClientRect().top;
                var revealpoint = 150;

                if (revealtop < windowheight - revealpoint) {
                    reveals[i].classList.add('active');
                }
            }
        }
        
        // Trigger once on load
        reveal();
    </script>

</body>
</html>