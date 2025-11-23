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
    
    /* Collection Section Styles */
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .section-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    
    .collection-filters {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .filter-btn {
        background: transparent;
        border: 1px solid var(--accent-gold);
        color: var(--text-color);
        padding: 0.5rem 1.5rem;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: var(--accent-gold);
        color: var(--primary-color);
    }
    
    .car-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        padding: 1rem;
    }
    
    .car-card {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .car-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .card-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .car-card:hover .card-image {
        transform: scale(1.05);
    }
    
    .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .car-card:hover .card-overlay {
        opacity: 1;
    }
    
    .btn-view {
        background: var(--accent-gold);
        color: var(--primary-color);
        padding: 0.75rem 1.5rem;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-view:hover {
        background: #fff;
        transform: translateY(-2px);
    }
    
    .card-details {
        padding: 1.5rem;
    }
    
    .card-badge {
        display: inline-block;
        background: rgba(212, 175, 55, 0.2);
        color: var(--accent-gold);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
    }
    
    .card-title {
        font-size: 1.25rem;
        margin: 0.5rem 0;
        color: var(--text-color);
    }
    
    .card-specs {
        display: flex;
        gap: 1rem;
        margin: 1rem 0;
        color: var(--text-light);
        font-size: 0.9rem;
    }
    
    .card-specs .icon {
        margin-right: 0.25rem;
    }
    
    .status-available {
        color: #2ecc71;
    }
    
    .status-sold {
        color: #e74c3c;
    }
    
    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .card-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--accent-gold);
    }
    
    .btn-3d {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .btn-3d:hover {
        background: var(--accent-gold);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    .collection-cta {
        text-align: center;
        margin-top: 3rem;
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid var(--accent-gold);
        color: var(--accent-gold);
        padding: 0.75rem 2rem;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
        background: var(--accent-gold);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .car-grid {
            grid-template-columns: 1fr;
        }
        
        .collection-filters {
            flex-direction: column;
            align-items: center;
        }
        
        .filter-btn {
            width: 80%;
        }
    }
    </style>

    <section class="mission reveal" id="mission">
        <h2 class="reveal">The Motiv Standard</h2>
        <p>At Motiv Motors, we believe a car is not just transportation; it is a kinetic sculpture, a piece of history, and a vessel for the soul. We curate only the finest examples of automotive engineering for the discerning collector.</p>
    </section>

    <section id="inventory" class="inventory">
        <div class="section-header">
            <h2 class="reveal">Curated Collection</h2>
            <p class="section-subtitle">Handpicked selection of premium vehicles</p>
            
            <div class="collection-filters">
                <button class="filter-btn active" data-filter="all">All Vehicles</button>
                <button class="filter-btn" data-filter="luxury">Luxury</button>
                <button class="filter-btn" data-filter="suv">SUV</button>
                <button class="filter-btn" data-filter="sports">Sports</button>
            </div>
        </div>
        
        <div class="car-grid">
            <?php foreach ($cars as $car): 
                // Determine car category based on make/model
                $category = 'luxury';
                if (stripos($car['model'], 'Cayenne') !== false || stripos($car['model'], 'Prado') !== false) {
                    $category = 'suv';
                } elseif (stripos($car['model'], 'WRX') !== false) {
                    $category = 'sports';
                }
            ?>
            <div class="car-card" data-category="<?= $category ?>">
                <div class="card-image-container">
                    <img src="<?= htmlspecialchars($car['image_url']) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>" class="card-image">
                    <div class="card-overlay">
                        <a href="listing.php?id=<?= $car['id'] ?>" class="btn-view">View Details</a>
                        <a href="virtual_showroom.php?id=<?= $car['id'] ?>" class="btn-view" style="margin-top: 10px; background: rgba(255, 255, 255, 0.9); color: #0f0c29;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 5px;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            3D View
                        </a>
                    </div>
                </div>
                <div class="card-details">
                    <div class="card-badge"><?= ucfirst($category) ?></div>
                    <h3 class="card-title"><?= htmlspecialchars($car['year']) ?> <?= htmlspecialchars($car['make']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                    <div class="card-specs">
                        <span><i class="icon">🚗</i> <?= number_format($car['mileage']) ?> km</span>
                        <span><i class="icon">⚡</i> <?= htmlspecialchars($car['fuel_type'] ?? 'Petrol') ?></span>
                        <span class="status-<?= strtolower($car['status']) ?>"><?= htmlspecialchars($car['status']) ?></span>
                    </div>
                    <div class="card-footer">
                        <div class="card-price">KES <?= number_format($car['price']) ?></div>
                        <a href="listing.php?id=<?= $car['id'] ?>" class="btn-3d" title="View Details">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="collection-cta">
            <a href="inventory.php" class="btn btn-outline">View Full Inventory</a>
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