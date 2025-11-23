<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $car_id = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');

    if (!$car_id || $name === '' || $phone === '' || $date === '' || $time === '') {
        die('Invalid submission.');
    }

    $special_message = "[TEST DRIVE REQUEST]\nPhone: $phone\nDate: $date\nTime: $time";
    $dummy_email = preg_replace('/[^0-9]/', '', $phone) . "@testdrive.com";

    try {
        $sql = "INSERT INTO inquiries (car_id, customer_name, customer_email, message) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$car_id, $name, $dummy_email, $special_message]);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header('Location: inventory.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="display:flex; align-items:center; justify-content:center; height:100vh;">
    <div class="glass-panel" style="padding:50px; text-align:center; max-width:500px;">
        <h1 style="color:var(--accent-gold); margin-bottom: 10px;">Booking Confirmed</h1>
        <p>We have received your request for <strong><?= htmlspecialchars($date) ?></strong>.</p>
        <p>Our concierge will call you at <strong><?= htmlspecialchars($phone) ?></strong> shortly to finalize details.</p>
        <br>
        <a href="inventory.php" class="btn btn-primary">Back to Showroom</a>
    </div>
</body>
</html>
