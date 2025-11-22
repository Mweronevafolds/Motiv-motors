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

    <section class="hero">
        <div class="hero-content">
            <h1>Where Passion Finds Its Drive</h1>
            <p>Curated excellence. Timeless machines. Your journey begins here.</p>
            <a href="#inventory" class="btn btn-accent">Explore Inventory</a>
        </div>
    </section>

    <section class="mission" id="mission-section">
        <h2>The Motiv Standard</h2>
        <p>At Motiv Motors, we believe a car is not just transportation; it is a kinetic sculpture, a piece of history, and a vessel for the soul. We curate only the finest examples of automotive engineering for the discerning collector.</p>
    </section>

    <section id="inventory" class="inventory">
        <h2>Curated Collection</h2>
        
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

</body>
</html>