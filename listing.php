<?php
require 'db.php';

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Vehicle Details
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

// WhatsApp Message
$wa_number = "254700000000"; 
$wa_message = "Hello, I am interested in the " . $car['year'] . " " . $car['make'] . " " . $car['model'] . " (Ref #" . $car['id'] . ")";
$wa_link = "https://wa.me/" . $wa_number . "?text=" . urlencode($wa_message);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?> | Motiv Motors</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    <style>
        .model-viewer-container {
            width: 100%;
            height: 400px;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        model-viewer {
            width: 100%;
            height: 100%;
            background-color: #f5f5f5;
        }
        .model-controls {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }
        .model-controls button {
            padding: 8px 15px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .model-controls button:hover {
            background: #555;
        }
    </style>
</head>
<body class="listing-page">

    <div class="listing-container">
        <a href="inventory.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>

        <div class="listing-content">
            <div class="listing-gallery">
                <div class="model-viewer-container">
                    <?php 
                    $modelFile = strtolower(str_replace(' ', '-', $car['make'] . '-' . $car['model'])) . '.glb';
                    $modelPath = 'uploads/models/' . $modelFile;
                    $defaultModel = 'uploads/models/red-sportscar-model.glb';
                    $modelToUse = file_exists($modelPath) ? $modelPath : $defaultModel;
                    ?>
                    <model-viewer 
                        src="<?php echo htmlspecialchars($modelToUse); ?>" 
                        alt="3D model of <?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?>"
                        auto-rotate 
                        camera-controls 
                        shadow-intensity="1"
                        camera-orbit="45deg 60deg 2.5m"
                        environment-image="neutral"
                        exposure="0.8"
                        shadow-softness="0.6">
                        
                        <div class="model-controls" slot="ar-button">
                            <button id="ar-button">View in AR</button>
                            <button id="ar-failure">AR not available</button>
                        </div>
                        
                        <button class="ar-button" slot="ar-button" id="ar-button">
                            View in your space
                        </button>
                        
                        <div id="ar-prompt">
                            <img src="https://modelviewer.dev/shared-assets/icons/hand.png" alt="Hand icon">
                        </div>
                        
                        <button slot="hotspot-1" class="hotspot" data-position="0.5 0.5 0.5" data-normal="0 1 0">
                            <div class="hotspot-tooltip">Engine</div>
                        </button>
                    </model-viewer>
                </div>
                
                <?php if ($car['status'] == 'Sold'): ?>
                    <div class="sold-badge">SOLD</div>
                <?php endif; ?>
                
                <img src="<?= htmlspecialchars($car['image_url']) ?>" 
                     alt="<?= htmlspecialchars($car['model']) ?>" 
                     class="main-image"
                     style="<?= ($car['status'] == 'Sold') ? 'filter: grayscale(100%); opacity: 0.7;' : '' ?>">

                <a href="virtual_showroom.php?id=<?= $car['id'] ?>" class="btn-360-trigger">
                    <i class="fas fa-cube"></i>
                    <span>360° TOUR</span>
                </a>
            </div>

            <div class="listing-details">
                <div>
                    <div class="ref-number">REF #<?= $car['id'] ?></div>
                    <h1 class="car-title"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h1>
                </div>

                <div class="price-tag">
                    KES <?= number_format($car['price']) ?>
                    <div class="status-badge status-<?= strtolower($car['status']) ?>">
                        <?= htmlspecialchars($car['status']) ?>
                    </div>
                </div>

                <div class="specs-grid">
                    <div class="spec-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span><?= number_format($car['mileage']) ?> km</span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-gas-pump"></i>
                        <span><?= htmlspecialchars($car['fuel_type'] ?? 'Petrol') ?></span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-palette"></i>
                        <span><?= htmlspecialchars($car['color'] ?? 'N/A') ?></span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-cog"></i>
                        <span><?= htmlspecialchars($car['transmission'] ?? 'Automatic') ?></span>
                    </div>
                </div>

                <div class="description-section" style="margin-top: 0; padding: 20px;">
                    <h3><i class="fas fa-list-ul"></i> Highlights</h3>
                    <div class="features-grid">
                        <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Premium Sound</span></div>
                        <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Leather Interior</span></div>
                        <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Navigation</span></div>
                        <div class="feature-item"><i class="fas fa-check-circle"></i> <span>Keyless Entry</span></div>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="<?= $wa_link ?>" class="btn btn-whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-calendar-check"></i> Book Test Drive
                    </button>
                </div>

                <a href="payment.php?id=<?= $car['id'] ?>" class="pay-container" style="margin-top: 20px;">
                    <div class="left-side">
                        <div class="card-icon">
                            <div class="card-strip"></div>
                            <div class="card-chip"></div>
                        </div>
                        <div class="pos-machine">
                            <div class="pos-screen"></div>
                        </div>
                    </div>
                    <div class="right-side">
                        <div class="pay-label">Start Purchase</div>
                        <svg viewBox="0 0 451.846 451.847" class="arrow-icon"><path d="M345.441 248.292L151.154 442.573c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744L278.318 225.92 106.409 54.017c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.287 194.284c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373z"></path></svg>
                    </div>
                </a>
            </div>
        </div>

        <div class="finance-calculator">
            <h2><i class="fas fa-calculator"></i> Finance Calculator</h2>
            <p class="calculator-subtitle">Estimated repayment based on 13% interest p.a.</p>

            <div class="calculator-grid">
                <div class="calculator-input">
                    <label>Deposit Amount</label>
                    <div class="input-group">
                        <span class="input-prefix">KES</span>
                        <input type="number" id="deposit" value="<?= $car['price'] * 0.2 ?>" oninput="calculateLoan()">
                    </div>
                    <div class="input-hint">Minimum 20% recommended</div>
                </div>

                <div class="calculator-input">
                    <label>Loan Duration</label>
                    <div class="input-group">
                        <span class="input-prefix"><i class="fas fa-clock"></i></span>
                        <select id="months" onchange="calculateLoan()">
                            <option value="12">12 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                            <option value="48" selected>48 Months</option>
                            <option value="60">60 Months</option>
                        </select>
                    </div>
                </div>

                <div class="calculator-result">
                    <div class="result-label">Monthly Repayment</div>
                    <div class="result-amount" id="monthly-payment">-</div>
                </div>
            </div>
        </div>

        <div class="description-section">
            <h2><i class="fas fa-file-alt"></i> Vehicle Description</h2>
            <p class="description-text"><?= nl2br(htmlspecialchars($car['description'])) ?></p>
        </div>

        <?php if ($car['status'] == 'Available'): ?>
            <div class="description-section">
                <h2><i class="fas fa-envelope"></i> Send Inquiry</h2>
                <form action="process_contact.php" method="POST" class="contact-form">
                    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Name</label>
                            <input type="text" name="name" required placeholder="Your Name">
                        </div>
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" required placeholder="your@email.com">
                        </div>
                    </div>
                    <label>Message</label>
                    <textarea name="offer" rows="3">Hi, is this <?= htmlspecialchars($car['model']) ?> still available?</textarea>
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Send Message</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal-overlay" id="testDriveModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Book Test Drive</h2>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <form id="testDriveForm" class="modal-form">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px;">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom:5px;">Phone</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:5px;">Preferred Date</label>
                    <div class="input-with-icon">
                        <i class="far fa-calendar-alt"></i>
                        <input type="datetime-local" name="preferred_date" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Booking</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div>
                <h3 style="color: var(--accent); margin-bottom: 15px;">MOTIV MOTORS KENYA</h3>
                <p>Where Passion Finds Its Drive.</p>
                <br>
                <p><strong>Showroom:</strong><br>Karen Office Park, Lang'ata Road<br>Nairobi, Kenya</p>
            </div>
            <div style="text-align: right;">
                <p><strong>Contact Sales:</strong><br>+254 700 000 000<br>sales@motivmotors.co.ke</p>
                <br>
                <p>&copy; <?= date('Y') ?> Motiv Motors Ltd.</p>
            </div>
        </div>
    </footer>

    <script>
        // Modal Logic
        function openModal() {
            document.getElementById('testDriveModal').classList.add('active');
        }
        function closeModal() {
            document.getElementById('testDriveModal').classList.remove('active');
        }
        document.getElementById('testDriveModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // Loan Calculator
        document.addEventListener('DOMContentLoaded', calculateLoan);

        function calculateLoan() {
            const carPrice = <?= json_encode((float)$car['price']); ?>;
            const depositInput = document.getElementById('deposit');
            const monthsInput = document.getElementById('months');
            
            let deposit = parseFloat(depositInput.value) || 0;
            let months = parseInt(monthsInput.value, 10) || 48;
            const interestRate = 0.13;

            // Simple validation to prevent negative numbers
            if(deposit < 0) deposit = 0;
            if(deposit > carPrice) deposit = carPrice;

            const principal = carPrice - deposit;
            
            if (principal <= 0) {
                document.getElementById('monthly-payment').innerText = "Fully Paid";
                return;
            }

            const years = months / 12;
            const totalInterest = principal * interestRate * years;
            const totalAmount = principal + totalInterest;
            const monthly = totalAmount / months;

            // Format with commas (KES 50,000)
            document.getElementById('monthly-payment').innerText = "KES " + Math.round(monthly).toLocaleString();
        }
    </script>

</body>
</html>