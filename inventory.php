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
    <style>
        /* Like Button Styles for Inventory Cards */
        .like-container {
            display: inline-block;
            margin-left: 10px;
        }

        .like-label {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            cursor: pointer;
            user-select: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .like-label:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .like-checkbox {
            display: none;
        }

        .like-checkbox:checked + .like-label {
            background: rgba(255, 71, 87, 0.1);
            border-color: rgba(255, 71, 87, 0.3);
        }

        .like-checkbox:checked + .like-label svg {
            fill: #ff4757;
            stroke: #ff4757;
            animation: heartButton 0.6s;
        }

        .like-action {
            position: relative;
            overflow: hidden;
            display: grid;
            font-weight: 500;
            height: 18px;
        }

        .like-action span {
            grid-column-start: 1;
            grid-column-end: 1;
            grid-row-start: 1;
            grid-row-end: 1;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 0.8rem;
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
            color: #ff4757;
        }

        .like-checkbox:checked + .like-label .like-action span.option-2 {
            transform: translate(0px, 0%);
            opacity: 1;
        }

        @keyframes heartButton {
            0% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1); }
            75% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
    </style>
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
                
                <div class="car-card-wrapper reveal">
                    <div class="car-card" 
                         data-tilt 
                         data-tilt-max="10" 
                         data-tilt-speed="400" 
                         data-tilt-glare 
                         data-tilt-max-glare="0.3">

                        <div class="card-image-box">
                            <?php if ($car['status'] == 'Sold'): ?>
                                <div class="sold-badge">SOLD</div>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['model']) ?>">
                        </div>

                        <div class="card-content">
                            <div>
                                <h3 class="card-title"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                                <div class="card-specs">
                                    <span><?= number_format($car['mileage']) ?> km</span>
                                    <?php if ($car['status'] == 'Sold'): ?>
                                        <span style="color: #ff6b6b; font-weight: bold;">Sold Out</span>
                                    <?php else: ?>
                                        <span style="color: #2ecc71;">Verified Import</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-price">KSh <?= number_format($car['price']) ?></div>
                            </div>

                            <div class="card-actions">
                                <?php if ($car['status'] != 'Sold'): ?>
                                    <a href="listing.php?id=<?= $car['id'] ?>" class="btn-details">
                                        View Details <span style="font-size:1.2rem;">→</span>
                                    </a>
                                <?php else: ?>
                                    <span class="btn-details" style="opacity:0.5; cursor:not-allowed;">Unavailable</span>
                                <?php endif; ?>

                                <div class="like-container" onclick="event.stopPropagation();">
                                    <input type="checkbox" id="fav-<?= $car['id'] ?>" class="like-checkbox" onchange="toggleFavorite(<?= $car['id'] ?>)">
                                    <label for="fav-<?= $car['id'] ?>" class="like-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                        <div class="like-action">
                                            <span class="option-1">Like</span>
                                            <span class="option-2">Liked</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
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
            </div>
            <div style="text-align: right;">
                <p><strong>Contact Sales:</strong><br>+254 700 000 000<br>sales@motivmotors.co.ke</p>
            </div>
        </div>
    </footer>

    <?php include 'navbar.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

    <script>
        // 2. Favorites Logic
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll('.fav-input');
            inputs.forEach(input => {
                const isSaved = localStorage.getItem(input.id);
                if (isSaved === 'true') {
                    input.checked = true;
                }
            });
        });

        function toggleFavorite(carId) {
            const checkbox = document.getElementById('fav-' + carId);
            const key = 'fav-' + carId;
            if (checkbox.checked) {
                localStorage.setItem(key, 'true');
            } else {
                localStorage.removeItem(key);
            }
        }

        // 3. Scroll Animations
        window.addEventListener('scroll', reveal);
        function reveal() {
            var reveals = document.querySelectorAll('.reveal, .car-card-wrapper');
            for (var i = 0; i < reveals.length; i++) {
                var windowheight = window.innerHeight;
                var revealtop = reveals[i].getBoundingClientRect().top;
                var revealpoint = 150;
                if (revealtop < windowheight - revealpoint) {
                    reveals[i].classList.add('active');
                }
            }
        }
        reveal();
    </script>
    <div class="chat-toggle" onclick="toggleChat()">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0f0c29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
    </div>

    <div class="chat-window" id="motivChat">
        <div class="chat-header">
            <div class="ai-avatar">AI</div>
            <div>
                <strong style="color: white; display: block;">Motiv Advisor</strong>
                <span style="font-size: 0.7rem; color: #2ecc71;">● Online</span>
            </div>
            <span style="margin-left: auto; cursor: pointer; color: #aaa;" onclick="toggleChat()">&times;</span>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                Hello! I'm your Motiv AI assistant. Looking for something fast, luxury, or family-friendly for the Nairobi roads?
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <div class="dot"></div><div class="dot"></div><div class="dot"></div>
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="userMsg" class="chat-input" placeholder="Ask about cars..." onkeypress="handleEnter(event)">
            <button class="chat-send" onclick="sendMessage()">➤</button>
        </div>
    </div>

    <script>
        // Toggle Chat
        function toggleChat() {
            const chat = document.getElementById('motivChat');
            chat.classList.toggle('active');
            if(chat.classList.contains('active')) {
                document.getElementById('userMsg').focus();
            }
        }

        // Handle Enter Key
        function handleEnter(e) {
            if (e.key === 'Enter') sendMessage();
        }

        // Send Message Logic
        async function sendMessage() {
            const input = document.getElementById('userMsg');
            const message = input.value.trim();
            const chatBox = document.getElementById('chatMessages');
            const typing = document.getElementById('typingIndicator');

            if (!message) return;

            // 1. Add User Message
            appendMessage(message, 'user');
            input.value = '';

            // 2. Show Typing Indicator
            typing.style.display = 'flex';
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                // 3. Call Backend API
                const response = await fetch('chat_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                // 4. Remove Typing & Add Bot Message
                typing.style.display = 'none';
                appendMessage(data.reply, 'bot');

            } catch (error) {
                typing.style.display = 'none';
                appendMessage("Sorry, I'm having trouble connecting right now.", 'bot');
                console.error(error);
            }
        }

        function appendMessage(text, sender) {
            const chatBox = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.classList.add('message', sender);
            
            // Convert simple markdown bolding **text** to <b>text</b>
            const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
            div.innerHTML = formattedText;

            // Insert before typing indicator
            const typing = document.getElementById('typingIndicator');
            chatBox.insertBefore(div, typing);
            
            // Scroll to bottom
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>

</body>
</html>