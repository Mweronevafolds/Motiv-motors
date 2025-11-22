<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from DB
    // (Using PDO syntax based on your first request. If using MySQLi, let me know)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        header("Location: admin_dashboard.php"); // Redirect to Dashboard
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Motiv Motors</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-box { max-width: 400px; margin: 100px auto; padding: 40px; background: white; box-shadow: var(--shadow-lg); border-radius: 8px; text-align: center; }
        input { width: 100%; margin-bottom: 15px; }
    </style>
</head>
<body style="background-color: #eee;">

    <div class="login-box">
        <h2 style="margin-bottom: 20px;">Staff Login</h2>
        <?php if($error): ?>
            <p style="color: red; margin-bottom: 10px;"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Access Dashboard</button>
        </form>
        <p style="margin-top: 15px; font-size: 0.8rem;"><a href="index.php">← Back to Website</a></p>
    </div>

</body>
</html>