<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }

// Fetch Inquiries
$inq_stmt = $pdo->query("SELECT inquiries.*, cars.make, cars.model FROM inquiries JOIN cars ON inquiries.car_id = cars.id ORDER BY inquiries.created_at DESC");
$inquiries = $inq_stmt->fetchAll();

// Fetch Orders
$ord_stmt = $pdo->query("SELECT orders.*, cars.make, cars.model FROM orders JOIN cars ON orders.car_id = cars.id ORDER BY orders.created_at DESC");
$orders = $ord_stmt->fetchAll();

// Fetch Inventory
$car_stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $car_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Command Center | Motiv Motors</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: radial-gradient(circle at top, #1b1f36 0%, #0f1324 60%, #070910 100%);
            color: #f5f5f5;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.5);
        }

        .glass-panel table {
            width: 100%;
            border-collapse: collapse;
        }

        .glass-panel th, .glass-panel td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            color: #e4e6f2;
        }

        .glass-panel th {
            background-color: rgba(255,255,255,0.04);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            color: #fdfdfd;
        }

        .glass-panel tr:hover {
            background-color: rgba(255,255,255,0.03);
        }

        .status-badge {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-Available {
            background: rgba(46, 213, 115, 0.15);
            color: #2ed573;
            border: 1px solid rgba(46, 213, 115, 0.4);
        }

        .status-Sold {
            background: rgba(255, 82, 82, 0.15);
            color: #ff5252;
            border: 1px solid rgba(255, 82, 82, 0.4);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-accent {
            background: var(--accent-gold);
            color: #fff;
        }

        .btn-primary {
            background: #4CAF50;
            color: #fff;
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div style="max-width: 1200px; margin: 0 auto; padding: 20px; padding-bottom: 100px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h1 style="margin: 0;">COMMAND CENTER</h1>
            <a href="logout.php" class="btn" style="border: 1px solid red; color: red;">Logout</a>
        </div>

        <div class="glass-panel" style="padding: 30px; margin-bottom: 50px; border: 1px solid var(--accent-gold);">
            <h3 style="color: var(--accent-gold); margin-bottom: 20px;"> Recent Sales & Deposits</h3>
            
            <?php if (count($orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>M-Pesa Ref</th>
                        <th>Amount Paid</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= date('M j, H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <?= htmlspecialchars($order['customer_name']) ?><br>
                            <small style="opacity:0.7;"><?= htmlspecialchars($order['customer_phone']) ?></small>
                        </td>
                        <td style="color: #2ecc71; font-family: monospace;"><?= htmlspecialchars($order['mpesa_code']) ?></td>
                        <td style="font-weight: bold;">KSh <?= number_format($order['amount_paid']) ?></td>
                        <td><?= htmlspecialchars($order['make'] . ' ' . $order['model']) ?></td>
                        <td><span class="status-badge status-<?= $order['order_status'] ?>"><?= $order['order_status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p style="color: #888;">No active orders yet.</p>
            <?php endif; ?>
        </div>

        <div class="glass-panel" style="padding: 30px; margin-bottom: 50px;">
            <h3 style="color: var(--accent-gold); margin-bottom: 20px;">Recent Inquiries</h3>
            <?php if (count($inquiries) > 0): ?>
            <table>
                <thead>
                    <tr><th>Date</th><th>Client</th><th>Vehicle</th><th>Message</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inq): ?>
                    <tr>
                        <td><?= date('M j', strtotime($inq['created_at'])) ?></td>
                        <td><?= htmlspecialchars($inq['customer_name']) ?></td>
                        <td><?= htmlspecialchars($inq['make'] . ' ' . $inq['model']) ?></td>
                        <td style="opacity: 0.7; font-size: 0.9rem;"><?= htmlspecialchars(substr($inq['message'], 0, 50)) ?>...</td>
                        <td><a href="mailto:<?= $inq['customer_email'] ?>" class="btn btn-accent" style="padding: 5px 15px; font-size: 0.8rem;">Reply</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p style="color: #888;">No pending inquiries.</p>
            <?php endif; ?>
        </div>

        <div class="glass-panel" style="padding: 30px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="color: var(--accent-gold);">Fleet Inventory</h3>
                <a href="admin_add.php" class="btn btn-primary">+ Add Vehicle</a>
            </div>
            
            <table>
                <thead><tr><th>ID</th><th>Vehicle</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>#<?= $car['id'] ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($car['image_url']) ?>" style="width: 40px; height: 40px; border-radius: 50%; vertical-align: middle; margin-right: 10px; object-fit: cover;">
                            <?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?>
                        </td>
                        <td style="font-family: 'Playfair Display'; color: var(--accent-gold);">KSh <?= number_format($car['price']) ?></td>
                        <td>
                            <a href="status_toggle.php?id=<?= $car['id'] ?>&status=<?= ($car['status']=='Available')?'Sold':'Available' ?>" 
                               class="status-badge status-<?= $car['status'] ?>" style="text-decoration: none;">
                               <?= $car['status'] ?>
                            </a>
                        </td>
                        <td>
                            <a href="delete_car.php?id=<?= $car['id'] ?>" onclick="return confirm('Delete this car?')" style="color: #ff6b6b; font-size: 1.2rem;">&times;</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>