<?php
session_start(); // Start the session
require 'db.php';

// ADD THIS SECURITY CHECK:
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $mileage = $_POST['mileage'];
    $desc = $_POST['description'];
    
    // Image Upload Logic
    $target_dir = "uploads/";
    // Ensure uploads directory exists
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $target_file = $target_dir . basename($_FILES["car_image"]["name"]);
    $uploadOk = 1;
    
    // Check if image file is a actual image
    if(move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;

        // Insert into DB (PDO version)
        $sql = "INSERT INTO cars (make, model, year, price, mileage, description, image_url) VALUES (?,?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$make, $model, $year, $price, $mileage, $desc, $image_url])) {
            $message = "Vehicle added successfully!";
        } else {
            $message = "Database error.";
        }
    } else {
        $message = "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Motiv Motors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="brand"><span class="logo">MOTIV ADMIN</span></div>
            <nav><ul><li><a href="index.php">Back to Site</a></li></ul></nav>
        </div>
    </header>

    <div class="mission" style="max-width: 600px; margin-top: 40px;">
        <h2>Add New Vehicle</h2>
        <?php if($message): ?>
            <p style="color: green; font-weight: bold; margin-bottom: 20px;"><?= $message ?></p>
        <?php endif; ?>

        <form action="admin_add.php" method="POST" enctype="multipart/form-data" class="contact-form" style="text-align: left;">
            
            <label>Make</label>
            <input type="text" name="make" placeholder="e.g. Aston Martin" required>

            <label>Model</label>
            <input type="text" name="model" placeholder="e.g. DB11" required>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <div>
                    <label>Year</label>
                    <input type="number" name="year" required>
                </div>
                <div>
                    <label>Price (KES)</label>
                    <input type="number" name="price" required>
                </div>
                <div>
                    <label>Mileage</label>
                    <input type="number" name="mileage" required>
                </div>
            </div>

            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Vehicle Image</label>
            <input type="file" name="car_image" required style="border: none; padding-left: 0;">

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Add Vehicle</button>
        </form>
    </div>
</body>
</html>