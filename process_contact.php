<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id = $_POST['car_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $offer = $_POST['offer'];

    try {
        $sql = "INSERT INTO inquiries (car_id, customer_name, customer_email, message) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$car_id, $name, $email, $offer]);

        // Redirect back to listing with success message
        echo "<script>alert('Thank you! Your inquiry has been sent.'); window.location.href='listing.php?id=$car_id';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
}
?>