<?php
session_start();
require 'db.php';

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // 1. (Optional) Delete the image file from the folder to save space
    // We fetch the image path first
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT image_url FROM cars WHERE id = ?");
        $stmt->execute([$id]);
        $car = $stmt->fetch();
    } elseif (isset($mysqli)) {
        $stmt = $mysqli->prepare("SELECT image_url FROM cars WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $car = $res->fetch_assoc();
    }

    // Delete file if it exists and is not an external link
    if ($car && file_exists($car['image_url'])) {
        unlink($car['image_url']);
    }

    // 2. Delete dependent records (inquiries/orders) to satisfy FK constraints
    try {
        if (isset($pdo)) {
            $pdo->prepare("DELETE FROM inquiries WHERE car_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM orders WHERE car_id = ?")->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
            $stmt->execute([$id]);
        } elseif (isset($mysqli)) {
            $stmt = $mysqli->prepare("DELETE FROM inquiries WHERE car_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt = $mysqli->prepare("DELETE FROM orders WHERE car_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt = $mysqli->prepare("DELETE FROM cars WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        
        // Redirect back to dashboard
        header("Location: admin_dashboard.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        die("Error deleting record: " . $e->getMessage());
    }
} else {
    header("Location: admin_dashboard.php");
}
?>