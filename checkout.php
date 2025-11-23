<?php
require 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if(!$car) die("Car not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        
        <div class="glass-panel" style="width: 100%; max-width: 900px; display: grid; grid-template-columns: 1fr 1fr; overflow: hidden;">
            
            <div style="padding: 40px; background: rgba(0,0,0,0.2);">
                <h3 style="color: var(--accent-gold); margin-bottom: 20px;">ORDER SUMMARY</h3>
                <img src="<?= htmlspecialchars($car['image_url']) ?>" style="width: 100%; border-radius: 10px; margin-bottom: 20px;">
                <h2><?= htmlspecialchars($car['year'] . ' ' . $car['make']) ?></h2>
                <h3 style="font-weight: 300;">&nbsp;&nbsp;<?= htmlspecialchars($car['model']) ?></h3>
                
                <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Vehicle Price</span>
                        <span>KSh <?= number_format($car['price']) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Reservation Fee (10%)</span>
                        <span style="color: var(--accent-gold);">KSh <?= number_format($car['price'] * 0.1) ?></span>
                    </div>
                </div>
            </div>

            <div style="padding: 40px;">
                <h2 style="margin-bottom: 30px;">Secure Checkout</h2>
                
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                    
                    <div class="filter-group" style="margin-bottom: 20px;">
                        <label>Full Legal Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 5px;">
                    </div>

                    <div class="filter-group" style="margin-bottom: 20px;">
                        <label>M-Pesa Phone Number</label>
                        <input type="text" name="phone" placeholder="07XX XXX XXX" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 5px;">
                    </div>
                    
                    <div style="background: rgba(46, 204, 113, 0.1); border: 1px solid #2ecc71; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                        <strong style="color: #2ecc71;">PAY WITH M-PESA</strong>
                        <p style="font-size: 0.8rem; margin-top: 5px;">You will receive an STK prompt on your phone to complete the reservation payment.</p>
                    </div>

                    <button type="submit" class="btn btn-accent" style="width: 100%; padding: 15px; font-weight: bold;">COMPLETE RESERVATION</button>
                    <p style="text-align: center; margin-top: 15px; font-size: 0.8rem; opacity: 0.6;">Secure 256-bit SSL Encrypted</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
