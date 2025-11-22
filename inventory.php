<?php
require 'db.php';

// --- 1. GET FILTER VALUES ---
$make_filter  = isset($_GET['make']) ? $_GET['make'] : '';
$price_filter = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$keyword      = isset($_GET['search']) ? $_GET['search'] : '';
$sort         = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// --- 2. BUILD QUERY ---
$sql = "SELECT * FROM cars WHERE 1=1";
$params = [];

if (!empty($make_filter)) {
    $sql .= " AND make = ?";
    $params[] = $make_filter;
}
if ($price_filter > 0) {
    $sql .= " AND price <= ?";
    $params[] = $price_filter;
}
if (!empty($keyword)) {
    $sql .= " AND (model LIKE ? OR description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

// Sort Logic
switch ($sort) {
    case 'price_asc':  $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), price DESC"; break;
    default:           $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), id DESC"; break; 
}

// Execute
if (isset($pdo)) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $cars = $stmt->fetchAll();
} elseif (isset($mysqli)) {
    $sql = "SELECT * FROM cars WHERE 1=1"; 
    if (!empty($make_filter)) $sql .= " AND make = '$make_filter'";
    if ($price_filter > 0)    $sql .= " AND price <= $price_filter";
    if (!empty($keyword))     $sql .= " AND (model LIKE '%$keyword%' OR description LIKE '%$keyword%')";
    if ($sort == 'price_asc') $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), price ASC";
    elseif ($sort == 'price_desc') $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), price DESC";
    else $sql .= " ORDER BY FIELD(status, 'Available', 'Pending', 'Sold'), id DESC";
    $result = $mysqli->query($sql);
    $cars = $result->fetch_all(MYSQLI_ASSOC);
}

// Get Makes
if (isset($pdo)) {
    $makes = $pdo->query("SELECT DISTINCT make FROM cars ORDER BY make")->fetchAll(PDO::FETCH_COLUMN);
} elseif (isset($mysqli)) {
    $m_res = $mysqli->query("SELECT DISTINCT make FROM cars ORDER BY make");
    $makes = [];
    while($row = $m_res->fetch_assoc()) { $makes[] = $row['make']; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Inventory | Motiv Motors Kenya</title>
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

    <section class="inventory">
        <h2 class="reveal">Find Your Drive</h2>
        
        <div class="filter-section glass-panel reveal" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
            <form method="GET" action="inventory.php" class="filter-form">
                <div class="filter-group">
                    <label>Search Model</label>
                    <input type="text" name="search" placeholder="e.g. Prado, Benz..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="filter-group">
                    <label>Make</label>
                    <select name="make">
                        <option value="">All Makes</option>
                        <?php foreach ($makes as $m): ?>
                            <option value="<?= $m ?>" <?= ($make_filter == $m) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Max Budget</label>
                    <select name="max_price">
                        <option value="0">Any Price</option>
                        <option value="3000000" <?= ($price_filter == 3000000) ? 'selected' : '' ?>>Under KSh 3M</option>
                        <option value="5000000" <?= ($price_filter == 5000000) ? 'selected' : '' ?>>Under KSh 5M</option>
                        <option value="10000000" <?= ($price_filter == 10000000) ? 'selected' : '' ?>>Under KSh 10M</option>
                        <option value="20000000" <?= ($price_filter == 20000000) ? 'selected' : '' ?>>Under KSh 20M</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                        <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                    </select>
                </div>
                <button type="submit" class="btn-search">Search</button>
                <a href="inventory.php" style="font-size: 0.9rem; text-decoration: underline; margin-bottom: 5px;">Reset</a>
            </form>
        </div>
        
        <?php if (count($cars) > 0): ?>
            <div class="car-grid">
                <?php foreach ($cars as $car): ?>
                
                <div class="car-card-3d <?= ($car['status'] == 'Sold') ? 'is-sold' : '' ?> reveal">
                    <div class="canvas">
                        <?php for($i=1; $i<=25; $i++): ?>
                            <div class="tracker tr-<?= $i ?>"></div>
                        <?php endfor; ?>

                        <div class="car-card-inner">
                            
                            <div class="card-image-wrapper">
                                <?php if ($car['status'] == 'Sold'): ?>
                                    <div class="sold-badge">SOLD</div>
                                <?php endif; ?>
                                <img src="<?= htmlspecialchars($car['image_url']) ?>" class="card-image">
                            </div>

                            <div class="card-details">
                                <h3 class="card-title"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                                
                                <div class="card-specs">
                                    <span><?= number_format($car['mileage']) ?> km</span>
                                    <?php if ($car['status'] == 'Sold'): ?>
                                        <span style="color: #d32f2f; font-weight: bold;">Sold Out</span>
                                    <?php else: ?>
                                        <span style="color: green;">Verified Import</span>
                                    <?php endif; ?>
                                </div>

                                <div class="card-price">KSh <?= number_format($car['price']) ?></div>
                                
                                <?php if ($car['status'] == 'Sold'): ?>
                                    <button class="btn" style="width:100%; background:#ccc; cursor:not-allowed;" disabled>No Longer Available</button>
                                <?php else: ?>
                                    <a href="listing.php?id=<?= $car['id'] ?>" class="btn btn-primary" style="width:100%; text-align:center;">View Details</a>
                                <?php endif; ?>

                                <input type="checkbox" id="fav-<?= $car['id'] ?>" class="fav-input" onchange="toggleFavorite(<?= $car['id'] ?>)">
                                <label for="fav-<?= $car['id'] ?>" class="fav-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <div class="action">
                                        <span class="option-1">Add to Favorites</span>
                                        <span class="option-2">Added to Favorites</span>
                                    </div>
                                </label>

                            </div>
                        </div> </div> </div> <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <h3>No vehicles found.</h3>
                <p>Try adjusting your filters.</p>
            </div>
        <?php endif; ?>
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
        // 1. On Page Load: Check LocalStorage and set buttons to "checked"
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.fav-input');
            
            inputs.forEach(input => {
                // The ID is formatted like "fav-5", we use this as the key
                const isSaved = localStorage.getItem(input.id);
                
                if (isSaved === 'true') {
                    input.checked = true;
                }
            });
        });

        // 2. Function Triggered when Clicking a Heart
        function toggleFavorite(carId) {
            const checkbox = document.getElementById('fav-' + carId);
            const key = 'fav-' + carId;
            
            if (checkbox.checked) {
                localStorage.setItem(key, 'true');
            } else {
                localStorage.removeItem(key);
            }
        }
    </script>

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