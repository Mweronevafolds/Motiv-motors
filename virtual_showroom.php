<?php

// Force browser to cache this page/resources for 30 days
$seconds_to_cache = 2592000;
$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: max-age=$seconds_to_cache");
require 'db.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
error_log("Virtual Showroom - Car ID: " . $id); // Debug log

// Fetch all cars for the selection grid
$allCars = [];
if (isset($pdo)) {
    $stmt = $pdo->query("SELECT id, make, model, year, image_url FROM cars ORDER BY year DESC, make, model");
    $allCars = $stmt->fetchAll();
} elseif (isset($mysqli)) {
    $result = $mysqli->query("SELECT id, make, model, year, image_url FROM cars ORDER BY year DESC, make, model");
    if ($result) {
        $allCars = $result->fetch_all(MYSQLI_ASSOC);
    }
}

$defaultModel = 'uploads/models/red-sportscar-model.glb';

// If no ID provided, show default model and car selection
if ($id === 0) {
    $car = [
        'id' => 0,
        'make' => 'Motiv',
        'model' => 'Showroom',
        'year' => date('Y'),
        'image_url' => 'logo.jpg',
        'mileage' => 0,
        'fuel_type' => 'Electric',
        'color' => 'Custom',
        'transmission' => 'Automatic',
        'price' => 0,
        'engine' => 'N/A',
        'drivetrain' => 'AWD'
    ];
    $modelToUse = $defaultModel;
    $showCarSelection = true;
} else {
    // Fetch specific car details
    $car = null;
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
    
    if (!$car) {
        // If car not found but we have other cars, show default model with selection
        if (!empty($allCars)) {
            $car = [
                'id' => 0,
                'make' => 'Motiv',
                'model' => 'Showroom',
                'year' => date('Y'),
                'image_url' => 'logo.jpg',
                'mileage' => 0,
                'fuel_type' => 'Electric',
                'color' => 'Custom',
                'transmission' => 'Automatic',
                'price' => 0,
                'engine' => 'N/A',
                'drivetrain' => 'AWD'
            ];
            $modelToUse = $defaultModel;
            $showCarSelection = true;
        } else {
            // No cars in database, show error
            die("No vehicles found in inventory. Please check back later or <a href='contact.php'>contact us</a>.");
        }
    } else {
        // Valid car found, set up its model
        $modelFile = strtolower(str_replace(' ', '-', $car['make'] . '-' . $car['model'])) . '.glb';
        $modelPath = 'uploads/models/' . $modelFile;
        $modelToUse = file_exists($modelPath) ? $modelPath : $defaultModel;
        $showCarSelection = false;
    }
}

if (!$car) { 
    error_log("No car found with ID: " . $id); // Debug log
    // Temporarily show error for debugging
    die("Error: Could not find car with ID: " . htmlspecialchars($id) . ". <a href='inventory.php'>Back to Inventory</a>");
    // header("Location: inventory.php"); 
    // exit; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['year'] . ' ' . $car['make']) ?> | Motiv Motors</title>
    
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preload" href="<?= htmlspecialchars($modelToUse) ?>" as="fetch" crossorigin="anonymous">
    <style>
        :root {
            --primary: #050b14;
            --accent: #deb86d; /* Gold */
            --accent-hover: #b5952f;
            --glass: rgba(5, 11, 20, 0.8);
            --text-light: #ffffff;
        }

        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; font-family: 'Inter', sans-serif; background-color: var(--primary); }

        /* 3D VIEWER */
        model-viewer {
            width: 100%; height: 100vh;
            background: radial-gradient(circle at 50% 50%, #1a2332 0%, #050b14 100%);
            --poster-color: transparent;
        }

        /* --- UI ELEMENTS --- */
        .nav-btn {
            position: fixed; z-index: 1000;
            background: var(--glass); color: white;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 10px 20px; border-radius: 50px;
            cursor: pointer; text-decoration: none;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.3s ease; backdrop-filter: blur(10px);
            font-size: 0.9rem; font-weight: 500;
        }
        .back-button { top: 30px; left: 30px; }
        .nav-btn:hover { background: var(--accent); color: var(--primary); border-color: var(--accent); }

        /* CAR INFO PANEL */
        .car-info {
            position: fixed; 
            bottom: 30px; 
            left: 30px;
            background: rgba(5, 11, 20, 0.85); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white; 
            padding: 20px; 
            border-radius: 12px;
            z-index: 900; 
            max-width: 320px;
            width: calc(100% - 60px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
        }
        
        .car-info h2 {
            margin: 0 0 8px 0; 
            color: var(--accent); 
            font-size: 1.4rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .car-specs { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 12px; 
            margin-top: 12px; 
            padding-top: 12px; 
            border-top: 1px solid rgba(255,255,255,0.1); 
        }
        
        .spec-item { 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            font-size: 0.85rem; 
            color: #e2e8f0; 
            white-space: nowrap;
        }

        /* FAB Button */
        .fab-button {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            z-index: 1000;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        
        .fab-button:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            background: var(--accent-hover);
        }
        
        .fab-button:active {
            transform: translateY(-1px) scale(0.98);
        }

        /* RESPONSIVE */
        @media only screen and (max-width: 768px) {
            .fab-button {
                bottom: 100px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
            .car-info {
                left: 50%;
                transform: translateX(-50%);
                bottom: 100px;
                width: 90%;
                max-width: 400px;
                padding: 15px;
            }
            
            .car-specs {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            
            .spec-item {
                font-size: 0.8rem;
            }
        }
        
        @media only screen and (max-width: 480px) {
            .car-info {
                width: calc(100% - 40px);
                bottom: 90px;
                padding: 15px;
            }
            
            .car-specs {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            
            .spec-item {
                font-size: 0.75rem;
            }
            
            .car-info h2 {
                font-size: 1.2rem;
            }
        }
            .car-info { 
                bottom: 100px; 
                width: calc(100% - 40px); 
                left: 20px;
                right: 20px;
                margin: 0 auto;
                padding: 15px; 
                max-width: 100%;
                box-sizing: border-box;
            }
            .fab-button { 
                bottom: 20px; 
                left: 20px; 
                right: 20px;
                width: auto;
                height: auto;
                border-radius: 50px;
                padding: 12px 20px;
                margin: 0 auto;
                max-width: 300px;
                text-align: center;
                justify-content: center;
            }
            .fab-button .left-side {
                margin-right: 10px;
            }
            .back-button span { display: none; }
            
            /* Ensure model viewer takes full height */
            model-viewer {
                height: calc(100vh - 200px);
            }
        }
        
        /* LOADER */
        /* Like Button Styles */
        .like-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        .like-label {
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 15px 10px 10px;
            cursor: pointer;
            user-select: none;
            border-radius: 10px;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            color: var(--primary);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .like-label:hover {
            transform: translateY(-2px);
            box-shadow: rgba(149, 157, 165, 0.3) 0px 10px 28px;
        }

        .like-checkbox {
            display: none;
        }

        .like-checkbox:checked + .like-label svg {
            fill: #ff4757;
            stroke: #ff4757;
            animation: heartButton 1s;
        }

        .like-action {
            position: relative;
            overflow: hidden;
            display: grid;
            font-weight: 500;
        }

        .like-action span {
            grid-column-start: 1;
            grid-column-end: 1;
            grid-row-start: 1;
            grid-row-end: 1;
            transition: all 0.5s;
            white-space: nowrap;
        }

        .like-action span.option-1 {
            transform: translate(0px, 0%);
            opacity: 1;
        }

        .like-checkbox:checked + .like-label .like-action span.option-1 {
            transform: translate(0px, -100%);
            opacity: 0;
        }

        .like-action span.option-2 {
            transform: translate(0px, 100%);
            opacity: 0;
        }

        .like-checkbox:checked + .like-label .like-action span.option-2 {
            transform: translate(0px, 0%);
            opacity: 1;
            color: #ff4757;
        }

        @keyframes heartButton {
            0% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1); }
            75% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .car-selection-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(5, 11, 20, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3000;
            padding: 20px;
            box-sizing: border-box;
        }

        .car-selection-container {
            background: var(--glass);
            padding: 30px;
            border-radius: 15px;
            max-width: 1200px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .car-selection-container h2 {
            color: var(--accent);
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px;
        }

        .car-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: white;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .car-image {
            height: 150px;
            background-size: cover;
            background-position: center;
        }

        .car-info {
            padding: 15px;
        }

        .car-info h3 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: white;
        }

        .view-button {
            display: inline-block;
            background: var(--accent);
            color: var(--primary);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .car-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .car-grid {
                grid-template-columns: 1fr;
            }
        }

        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--primary); display: flex; justify-content: center; align-items: center; z-index: 2000; flex-direction: column; transition: opacity 0.5s; }
        .brand-loader { color: var(--accent); font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; letter-spacing: 2px; }
        .progress-bar { width: 200px; height: 2px; background: rgba(255,255,255,0.1); position: relative; overflow: hidden; }
        .progress-bar::after { content: ''; position: absolute; top: 0; left: 0; height: 100%; width: 50%; background: var(--accent); animation: load 1.5s infinite ease-in-out; }
        @keyframes load { 0% { left: -50%; } 100% { left: 100%; } }
    </style>
</head>
<body>

    <?php if ($showCarSelection): ?>
    <div class="car-selection-overlay">
        <div class="car-selection-container">
            <h2>Select a Vehicle</h2>
            <div class="car-grid">
                <?php foreach ($allCars as $vehicle): ?>
                    <a href="virtual_showroom.php?id=<?= $vehicle['id'] ?>" class="car-card">
                        <div class="car-image" style="background-image: url('<?= htmlspecialchars($vehicle['image_url']) ?>');"></div>
                        <div class="car-info">
                            <h3><?= htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']) ?></h3>
                            <span class="view-button">View in 3D</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="loading-overlay" id="loadingScreen">
        <div class="brand-loader">MOTIV MOTORS</div>
        <div class="progress-bar"></div>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 10px;">Loading Experience<?= $showCarSelection ? ' - <a href="#" onclick="document.querySelector(\'.car-selection-overlay\').style.display=\'flex\'; return false;">Select Vehicle</a>' : '' ?></p>
    </div>

    <a href="inventory.php" class="nav-btn back-button">
        <i class="fas fa-arrow-left"></i> <span>Inventory</span>
    </a>

    <div class="like-container">
        <input type="checkbox" id="favorite" class="like-checkbox">
        <label for="favorite" class="like-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <div class="like-action">
                <span class="option-1">Add to Favorites</span>
                <span class="option-2">Added to Favorites</span>
            </div>
        </label>
    </div>

    <div class="model-viewer-container">
        <model-viewer 
            id="viewer"
            src="<?= htmlspecialchars($modelToUse) ?>" 
            poster="<?= htmlspecialchars($car['image_url']) ?>" 
            alt="<?= htmlspecialchars($car['make']) ?>"
            camera-controls 
            auto-rotate 
            shadow-intensity="1.5" 
            shadow-softness="0.8" 
            exposure="1" 
            environment-image="neutral" 
            tone-mapping="commerce" 
            ar 
            loading="eager" 
            reveal="interaction"
            camera-orbit="45deg 75deg 105%"
            field-of-view="30deg"
            min-camera-orbit="-Infinity 0deg 100%"
            max-camera-orbit="Infinity 180deg 150%"
            camera-controls-pan-speed="0.5">
            <style>
              model-viewer::part(default-poster) {
                background-size: contain;
                background-position: center;
                background-repeat: no-repeat;
                background-color: #0f0f17;
              }
              .model-viewer-container {
                width: 100%;
                height: 100vh;
                position: relative;
              }
              .ar-button {
                background-color: var(--accent);
                border-radius: 4px;
                border: none;
                position: absolute;
                top: 16px;
                right: 16px;
                padding: 8px 16px;
                font-family: 'Inter', sans-serif;
                font-weight: 600;
                color: var(--primary);
                cursor: pointer;
                z-index: 100;
                display: flex;
                align-items: center;
                gap: 8px;
              }
              .ar-button:active {
                background-color: var(--accent-hover);
              }
            </style>
            <button slot="ar-button" class="ar-button">
                <i class="fas fa-mobile-alt"></i> View in AR
            </button>
        </model-viewer>
    </div>

    <div class="car-info">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
            <span style="font-size: 0.7rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.5px;">REF #<?= htmlspecialchars($car['id'] ?? 'N/A') ?></span>
        </div>
        <h2><?= htmlspecialchars(($car['year'] ?? '') . ' ' . ($car['make'] ?? '') . ' ' . ($car['model'] ?? '')) ?></h2>
        <div class="car-specs">
            <div class="spec-item" title="Mileage">
                <i class="fas fa-tachometer-alt" style="color: var(--accent);"></i>
                <span><?= isset($car['mileage']) ? number_format($car['mileage']) . ' km' : 'N/A' ?></span>
            </div>
            <div class="spec-item" title="Fuel Type">
                <i class="fas fa-gas-pump" style="color: var(--accent);"></i>
                <span><?= htmlspecialchars($car['fuel_type'] ?? 'N/A') ?></span>
            </div>
            <div class="spec-item" title="Color">
                <i class="fas fa-palette" style="color: var(--accent);"></i>
                <span><?= htmlspecialchars($car['color'] ?? 'N/A') ?></span>
            </div>
            <div class="spec-item" title="Transmission">
                <i class="fas fa-cog" style="color: var(--accent);"></i>
                <span><?= htmlspecialchars($car['transmission'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>
    
    <?php if (($car['id'] ?? 0) > 0): ?>
    <a href="payment.php?car_id=<?= $car['id'] ?>" class="fab-button" title="Finance This Vehicle">
        <i class="fas fa-credit-card"></i>
    </a>
    <?php endif; ?>

    <script>
        const viewer = document.querySelector('#viewer');
        const loader = document.getElementById('loadingScreen');

        viewer.addEventListener('load', () => {
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 500);
        });

        viewer.addEventListener('camera-change', () => { viewer.autoRotate = false; });
    </script>
</body>
</html>