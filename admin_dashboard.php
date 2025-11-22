<?php
session_start();
require 'db.php';

// Security Check: Kick out if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch Inquiries (Newest first)
$inq_stmt = $pdo->query("
    SELECT inquiries.*, cars.year, cars.make, cars.model 
    FROM inquiries 
    JOIN cars ON inquiries.car_id = cars.id 
    ORDER BY inquiries.created_at DESC
");
$inquiries = $inq_stmt->fetchAll();

// Fetch Current Inventory Summary
$car_stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $car_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Motiv Motors</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eee; }
        
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: var(--shadow-sm); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: var(--primary-color); color: white; }
        tr:hover { background-color: #f9f9f9; }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .status-Available { background: #e6f4ea; color: #1e7e34; }
        .status-Sold { background: #fbecec; color: #bd2130; }
    </style>
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="brand"><span class="logo">ADMIN DASHBOARD</span></div>
            <nav>
                <ul>
                    <li><a href="index.php" target="_blank">View Site</a></li>
                    <li><a href="logout.php" style="color: red;">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        
        <div class="section-header">
            <h2>Customer Inquiries</h2>
        </div>
        
        <?php if (count($inquiries) > 0): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Interested In</th>
                        <th>Message/Bid</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inq): ?>
                    <tr>
                        <td><?= date('M j, Y', strtotime($inq['created_at'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($inq['customer_name']) ?></strong><br>
                            <span style="font-size:0.85rem; color:#666;"><?= htmlspecialchars($inq['customer_email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($inq['year'] . ' ' . $inq['make'] . ' ' . $inq['model']) ?></td>
                        <td style="max-width: 300px;"><?= htmlspecialchars(substr($inq['message'], 0, 100)) ?>...</td>
                        <td><a href="mailto:<?= htmlspecialchars($inq['customer_email']) ?>" class="btn btn-accent" style="padding: 5px 10px; font-size: 0.7rem;">Reply</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p>No new inquiries.</p>
        <?php endif; ?>

        <br><br><br>

        <div class="section-header">
            <h2>Fleet Inventory</h2>
            <a href="admin_add.php" class="btn btn-primary">+ Add New Vehicle</a>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>#<?= $car['id'] ?></td>
                        <td><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></td>
                        <td>KES <?= number_format($car['price']) ?></td>
                        <td>
                            <?php if ($car['status'] == 'Available'): ?>
                                <a href="status_toggle.php?id=<?= $car['id'] ?>&status=Sold" class="status-badge status-Available" title="Click to Mark Sold">Available</a>
                            <?php else: ?>
                                <a href="status_toggle.php?id=<?= $car['id'] ?>&status=Available" class="status-badge status-Sold" title="Click to Mark Available">Sold</a>
                            <?php endif; ?>
                        </td>
                        </td>                        <td>
                        <td>
                            <a href="listing.php?id=<?= $car['id'] ?>" target="_blank" style="color: var(--primary-color); font-weight: bold; margin-right: 10px;">View</a>
                            
                            <a href="delete_car.php?id=<?= $car['id'] ?>" 
                            onclick="return confirm('Are you sure you want to delete this vehicle? This cannot be undone.');" 
                            style="color: red; font-size: 0.9rem;">Delete</a>
                        </td>                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>