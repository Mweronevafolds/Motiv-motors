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
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            color: #fff;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            padding: 45px 40px 50px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
            backdrop-filter: blur(14px);
            text-align: center;
        }

        .login-box h2 { letter-spacing: 2px; margin-bottom: 25px; color: #fff; }

        .login-box input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(0,0,0,0.25);
            color: #fff;
            margin-bottom: 18px;
        }

        .login-box input::placeholder { color: rgba(255,255,255,0.6); }

        .error-banner {
            background: rgba(255, 99, 132, 0.2);
            border: 1px solid rgba(255, 99, 132, 0.4);
            color: #ff99ab;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 0.9rem;
        }

        .login-box button {
            width: 100%;
            padding: 12px 0;
            border-radius: 999px;
            border: none;
            background: var(--accent-gold, #d4af37);
            color: #0f0c29;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.35);
            cursor: pointer;
        }

        .login-box a { color: rgba(255,255,255,0.8); text-decoration: none; }
        .login-box a:hover { color: var(--accent-gold, #d4af37); }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Staff Login</h2>
        <?php if($error): ?>
            <div class="error-banner"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Access Dashboard</button>
        </form>
        <p style="margin-top: 20px; font-size: 0.85rem;"><a href="index.php">← Back to Website</a></p>
    </div>

</body>
</html>