<?php
require 'db.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Vehicle Details
// Note: We use a simple query that works for both PDO and MySQLi
if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();
} elseif (isset($mysqli)) {
    $stmt = $mysqli->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $car = $res->fetch_assoc();
}

// Redirect if not found
if (!$car) {
    header("Location: inventory.php");
    exit;
}

// WhatsApp Message Generator
$wa_number = "254700000000"; // Replace with your real number
$wa_message = "Hello, I am interested in the " . $car['year'] . " " . $car['make'] . " " . $car['model'] . " (Ref #" . $car['id'] . ")";
$wa_link = "https://wa.me/" . $wa_number . "?text=" . urlencode($wa_message);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?> | Motiv Motors</title>
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

    <?php include 'navbar.php'; ?>

    <div class="listing-container">
        <div class="listing-image">
            <div class="card-image-wrapper">
                <?php if ($car['status'] == 'Sold'): ?>
                    <div class="sold-badge" style="font-size: 1.5rem; padding: 15px 30px;">SOLD</div>
                <?php endif; ?>
                
                <img src="<?= htmlspecialchars($car['image_url']) ?>" 
                     alt="<?= htmlspecialchars($car['model']) ?>" 
                     style="width: 100%; border-radius: 8px; box-shadow: var(--shadow-lg); <?= ($car['status'] == 'Sold') ? 'filter: grayscale(100%); opacity: 0.8;' : '' ?>">
            </div>
            
            <div style="margin-top: 30px;">
                <h3>Vehicle Description</h3>
                <p style="margin-top: 10px; color: #444; white-space: pre-line;"><?= nl2br(htmlspecialchars($car['description'])) ?></p>
            </div>
        </div>

        <div class="listing-info">
            <span style="font-size: 0.9rem; color: #666;">Ref #<?= $car['id'] ?></span>
            <h1 style="margin-top: 5px;"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?></h1>
            <h2 style="color: #555;"><?= htmlspecialchars($car['model']) ?></h2>
            
            <div class="listing-price" style="margin-top: 15px; font-size: 2.2rem; color: var(--primary-color);">
                KSh <?= number_format($car['price']) ?>
            </div>

            <div class="spec-grid">
                <div>
                    <strong>Mileage</strong><br>
                    <?= number_format($car['mileage']) ?> km
                </div>
                <div>
                    <strong>Status</strong><br>
                    <span style="color: <?= ($car['status']=='Sold') ? 'red' : 'green' ?>; font-weight:bold;">
                        <?= htmlspecialchars($car['status']) ?>
                    </span>
                </div>
                <div>
                    <strong>Year</strong><br>
                    <?= htmlspecialchars($car['year']) ?>
                </div>
                <div>
                    <strong>Transmission</strong><br>
                    Automatic
                </div>
            </div>

            <?php if ($car['status'] == 'Available'): ?>
                <a href="<?= $wa_link ?>" target="_blank" class="btn" style="background-color: #25D366; color: white; width: 100%; text-align: center; margin-bottom: 20px; display: block; border-radius: 4px;">
                    <span style="font-size: 1.2rem; vertical-align: middle;">✆</span> Chat via WhatsApp
                </a>

                <div class="contact-form">
                    <h3>Request a Viewing</h3>
                    <p style="margin-bottom: 15px; font-size: 0.9rem; color: #666;">Prefer email? Send us an inquiry below.</p>
                    
                    <form action="process_contact.php" method="POST">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>

                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>

                        <label for="offer">Message / Offer</label>
                        <textarea id="offer" name="offer" rows="3" required>I am interested in this vehicle. Is it still available?</textarea>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Inquiry</button>
                    </form>
                </div>

            <?php else: ?>
                <div style="background: #fbecec; border: 1px solid #dc3545; padding: 30px; text-align: center; border-radius: 8px; color: #721c24;">
                    <h3>⛔ This Vehicle is Sold</h3>
                    <p>This car has found a new home. Please check our inventory for similar models.</p>
                    <br>
                    <a href="inventory.php" class="btn btn-primary">View Available Stock</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

</body>
</html>