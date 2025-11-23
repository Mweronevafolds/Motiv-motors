<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id = $_POST['car_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    
    $mpesa_code = "QKH" . rand(10000, 99999) . "X"; 
    
    $stmt = $pdo->prepare("SELECT price FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();
    $amount = $car['price'] * 0.10;

    try {
        $sql = "INSERT INTO orders (car_id, customer_name, customer_phone, amount_paid, mpesa_code, order_status) 
                VALUES (?, ?, ?, ?, ?, 'Confirmed')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$car_id, $name, $phone, $amount, $mpesa_code]);

        $updateCar = $pdo->prepare("UPDATE cars SET status = 'Pending' WHERE id = ?");
        $updateCar->execute([$car_id]);

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Payment Successful</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body style="display: flex; align-items: center; justify-content: center; height: 100vh; text-align: center; background: #0f0c29;">
            <div class="glass-panel" style="padding: 50px; max-width: 500px;">
                <div style="font-size: 4rem; color: #2ecc71; margin-bottom: 20px;">✓</div>
                <h1 style="color: white;">Payment Received</h1>
                <p style="color: #ddd;">M-Pesa Code: <strong style="color: var(--accent-gold);"><?= $mpesa_code ?></strong></p>
                <p style="margin-bottom: 30px; opacity: 0.7; color: #aaa;">Your reservation deposit of <strong>KSh <?= number_format($amount) ?></strong> has been confirmed.</p>
                <a href="index.php" class="btn btn-primary">Return Home</a>
            </div>
        </body>
        </html>
        <?php

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
