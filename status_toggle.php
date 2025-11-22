<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)$_GET['id'];
$new_status = $_GET['status']; // 'Available' or 'Sold'

if (isset($pdo)) {
    $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
} elseif (isset($mysqli)) {
    $stmt = $mysqli->prepare("UPDATE cars SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
exit;
?>