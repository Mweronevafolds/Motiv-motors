<?php
require 'db.php';
$ids_string = isset($_GET['ids']) ? $_GET['ids'] : '';
$ids_array = array_filter(array_map('intval', explode(',', $ids_string)));

if (!empty($ids_array)) {
    $placeholders = implode(',', array_fill(0, count($ids_array), '?'));
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id IN ($placeholders)");
    $stmt->execute($ids_array);
    $cars = $stmt->fetchAll();
} else {
    $cars = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h1>MY GARAGE</h1>
            <a href="inventory.php" class="btn">Back to Showroom</a>
        </div>
        
        <div class="car-grid" style="margin-top: 40px;">
            <?php if (count($cars) === 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: rgba(255,255,255,0.05); border-radius: 16px;">
                    <h3>No favorites found.</h3>
                    <a href="inventory.php" class="btn btn-accent" style="margin-top:20px;">Browse Showroom</a>
                </div>
            <?php else: ?>
                <?php foreach($cars as $car): ?>
                    <div class="car-card-3d">
                        <div class="canvas">
                            <?php for($i=1; $i<=25; $i++): ?><div class="tracker tr-<?= $i ?>"></div><?php endfor; ?>
                            <div class="car-card-inner">
                                <div class="card-image-wrapper">
                                    <img src="<?= htmlspecialchars($car['image_url']) ?>" class="card-image">
                                    <?php if ($car['status'] !== 'Available'): ?>
                                        <div class="sold-badge"><?= htmlspecialchars($car['status']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-details">
                                    <h3 class="card-title"><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></h3>
                                    <div class="card-price">KSh <?= number_format($car['price']) ?></div>
                                    <a href="listing.php?id=<?= $car['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center;">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
