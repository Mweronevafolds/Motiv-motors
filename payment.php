<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Vehicle
if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();
} elseif (isset($mysqli)) {
    $stmt = $mysqli->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $car = $res->fetch_assoc();
}

if (!$car) { header("Location: inventory.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | Motiv Motors</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --primary: #050b14;
            --secondary: #1e293b;
            --accent: #deb86d;
            --text-light: #ffffff;
            --border: #334155;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--primary); 
            color: white; 
            margin: 0; 
            line-height: 1.6;
        }

        .checkout-container {
            max-width: 1100px; 
            margin: 40px auto; 
            display: grid; 
            grid-template-columns: 1.2fr 0.8fr; 
            gap: 40px; 
            padding: 20px;
        }

        /* LEFT COLUMN */
        .payment-methods { 
            background: var(--secondary); 
            padding: 30px; 
            border-radius: 12px; 
            border: 1px solid var(--border); 
        }
        
        .method-tabs { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 25px; 
            border-bottom: 1px solid var(--border); 
            padding-bottom: 15px; 
        }
        
        .tab { 
            padding: 10px 20px; 
            cursor: pointer; 
            border-radius: 6px; 
            font-weight: 600; 
            opacity: 0.6; 
            transition: 0.3s;
            display: flex; 
            align-items: center; 
            gap: 8px;
        }
        
        .tab.active { 
            background: var(--accent); 
            color: var(--primary); 
            opacity: 1; 
        }
        
        .tab:hover { 
            opacity: 1; 
        }

        .form-group { 
            margin-bottom: 20px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 0.9rem; 
            color: #94a3b8; 
        }
        
        .form-control {
            width: 100%; 
            padding: 14px; 
            background: #0f172a; 
            border: 1px solid var(--border);
            color: white; 
            border-radius: 6px; 
            font-size: 1rem;
        }
        
        .form-control:focus { 
            border-color: var(--accent); 
            outline: none; 
        }

        .mpesa-box { 
            background: #4caf50; 
            color: white; 
            padding: 15px; 
            border-radius: 6px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 15px;
        }

        /* RIGHT COLUMN (Summary) */
        .order-summary { 
            background: white; 
            color: var(--primary); 
            padding: 30px; 
            border-radius: 12px; 
            height: fit-content;
        }
        
        .car-preview { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 20px; 
            border-bottom: 1px solid #e2e8f0; 
            padding-bottom: 20px; 
        }
        
        .car-thumb { 
            width: 80px; 
            height: 80px; 
            object-fit: cover; 
            border-radius: 8px; 
        }
        
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            font-size: 1.2rem; 
            font-weight: 700; 
            margin-top: 20px; 
        }

        .btn-pay {
            width: 100%; 
            background: var(--primary); 
            color: var(--accent); 
            padding: 18px;
            border: none; 
            border-radius: 8px; 
            font-size: 1.1rem; 
            font-weight: 700;
            cursor: pointer; 
            margin-top: 25px; 
            transition: 0.3s; 
            text-transform: uppercase;
        }
        
        .btn-pay:hover { 
            background: #0f172a; 
            transform: translateY(-2px); 
        }

        .secure-badge { 
            text-align: center; 
            margin-top: 15px; 
            font-size: 0.8rem; 
            color: #64748b; 
            display: flex; 
            justify-content: center; 
            gap: 5px; 
        }

        /* M-Pesa Specific */
        .mpesa-instruction { 
            font-size: 0.9rem; 
            color: #cbd5e1; 
            line-height: 1.6; 
        }
        
        .steps-list { 
            margin: 15px 0; 
            padding-left: 20px; 
            color: #cbd5e1; 
            font-size: 0.9rem; 
        }
        
        .steps-list li { 
            margin-bottom: 8px; 
        }

        @media (max-width: 768px) {
            .checkout-container { 
                grid-template-columns: 1fr; 
            }
            
            .order-summary { 
                order: -1; 
            }
        }
        
        /* Header Styles */
        .checkout-header {
            padding: 20px 40px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="checkout-header">
        <a href="listing.php?id=<?= $car['id'] ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Vehicle
        </a>
        <img src="logo.jpg" alt="Motiv Motors" style="height: 40px; border-radius: 4px;">
    </div>

    <div class="checkout-container">
        
        <div class="payment-methods">
            <h2 style="color: var(--accent); margin-top: 0;">Payment Method</h2>
            
            <div class="method-tabs">
                <div class="tab active" onclick="switchTab('mpesa')"><i class="fas fa-mobile-alt"></i> M-Pesa</div>
                <div class="tab" onclick="switchTab('card')"><i class="fas fa-credit-card"></i> Card</div>
                <div class="tab" onclick="switchTab('bank')"><i class="fas fa-university"></i> Bank</div>
            </div>

            <form id="mpesa-form" action="process_payment.php" method="POST">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                <div class="mpesa-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/1200px-M-PESA_LOGO-01.svg.png" style="height: 30px; background: white; padding: 5px; border-radius: 4px;">
                    <div>
                        <strong>M-Pesa Express</strong><br>
                        <span style="font-size: 0.8rem;">Instant payment prompt will be sent to your phone.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>M-Pesa Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="07XX XXX XXX" required>
                </div>
                
                <input type="hidden" name="method" value="mpesa">
                <button type="submit" class="btn-pay" style="background: #4caf50; color: white;">Pay with M-Pesa</button>
            </form>

            <form id="card-form" style="display:none;" action="process_payment.php" method="POST">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                <div class="form-group">
                    <label>Cardholder Name</label>
                    <input type="text" name="card_name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Expiry</label>
                        <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY" required>
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" name="card_cvv" class="form-control" placeholder="123" required>
                    </div>
                </div>
                <input type="hidden" name="method" value="card">
                <button type="submit" class="btn-pay">Pay Securely</button>
            </form>

            <form id="bank-form" style="display:none;" action="process_payment.php" method="POST">
                <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                <p class="mpesa-instruction">Please transfer the deposit to the following account:</p>
                <div style="background: #0f172a; padding: 20px; border-radius: 6px; border: 1px solid var(--accent);">
                    <p style="margin: 5px 0;"><strong>Bank:</strong> KCB Bank</p>
                    <p style="margin: 5px 0;"><strong>Account Name:</strong> Motiv Motors Ltd</p>
                    <p style="margin: 5px 0;"><strong>Account No:</strong> 123 456 7890</p>
                    <p style="margin: 5px 0;"><strong>Branch:</strong> Karen</p>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label>Enter Transaction Reference Code</label>
                    <input type="text" name="reference" class="form-control" placeholder="e.g. TRX8293..." required>
                </div>
                <input type="hidden" name="method" value="bank">
                <button type="submit" class="btn-pay">Confirm Transfer</button>
            </form>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="car-preview">
                <img src="<?= htmlspecialchars($car['image_url']) ?>" class="car-thumb">
                <div>
                    <h4 style="margin: 0; color: var(--primary);"><?= $car['year'] ?> <?= $car['make'] ?></h4>
                    <p style="margin: 5px 0; font-size: 0.9rem; color: #64748b;"><?= $car['model'] ?></p>
                </div>
            </div>

            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Vehicle Price</span>
                    <span>KES <?= number_format($car['price']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #64748b;">
                    <span>Booking Deposit (20%)</span>
                    <span>KES <?= number_format($car['price'] * 0.2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #64748b;">
                    <span>Processing Fee</span>
                    <span>KES 2,500</span>
                </div>
            </div>

            <div class="total-row">
                <span>Pay Now</span>
                <span style="color: #4caf50;">KES <?= number_format(($car['price'] * 0.2) + 2500) ?></span>
            </div>
            
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 20px; line-height: 1.5;">
                * This amount serves as a commitment fee to reserve the vehicle. The balance is due upon delivery or finance approval.
            </p>

            <div class="secure-badge">
                <i class="fas fa-lock"></i> SSL Encrypted Transaction
            </div>
        </div>
    </div>

    <script>
        function switchTab(method) {
            // Hide all forms
            document.getElementById('mpesa-form').style.display = 'none';
            document.getElementById('card-form').style.display = 'none';
            document.getElementById('bank-form').style.display = 'none';
            
            // Reset tabs
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            
            // Show selected form and activate tab
            document.getElementById(method + '-form').style.display = 'block';
            event.currentTarget.classList.add('active');
        }
    </script>

</body>
</html>
