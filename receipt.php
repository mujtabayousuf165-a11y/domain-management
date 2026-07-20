<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone to UTC+05:00 (Pakistan)
date_default_timezone_set('Asia/Karachi');

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID from URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: index.php");
    exit();
}

// Fetch the domain data
$sql = "SELECT * FROM domains WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$data = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Registration Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .receipt {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .receipt-header h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .receipt-header .success {
            color: #10b981;
            font-size: 18px;
            font-weight: 600;
        }

        .receipt-body {
            margin-bottom: 20px;
        }

        .receipt-section {
            margin-bottom: 20px;
        }

        .receipt-section h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        .receipt-label {
            color: #666;
            font-weight: 500;
        }

        .receipt-value {
            color: #333;
            font-weight: 600;
            text-align: right;
        }

        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #ddd;
            padding-top: 20px;
            margin-top: 20px;
        }

        .receipt-footer p {
            color: #666;
            font-size: 14px;
        }

        .actions {
            text-align: center;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin: 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt" id="receipt">
            <div class="receipt-header">
                <h1>🎉 Domain Registration Receipt</h1>
                <p class="success">✓ Registration Successful!</p>
                <p style="color: #666; margin-top: 10px;">Date: <?php echo date('M d, Y h:i A', strtotime($data['client_date'] ?? $data['created_at'])); ?></p>
            </div>

            <div class="receipt-body">
                <div class="receipt-section">
                    <h3>Domain Information</h3>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain ID:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['domain_id']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['domain_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Registration Tenure:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['registration_tenure']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain For:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['domain_for']); ?></span>
                    </div>
                </div>

                <div class="receipt-section">
                    <h3>Personal Information</h3>
                    <div class="receipt-row">
                        <span class="receipt-label">Your Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['your_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Unit Head Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['unit_head_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain Price:</span>
                        <span class="receipt-value">$<?php echo htmlspecialchars($data['project_cost']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Email Address:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['email_address']); ?></span>
                    </div>
                </div>

                <?php if (!empty($data['customer_name']) || !empty($data['customer_email']) || !empty($data['order_id']) || !empty($data['additional_comments'])): ?>
                <div class="receipt-section">
                    <h3>Contact Details</h3>
                    <?php if (!empty($data['customer_name'])): ?>
                    <div class="receipt-row">
                        <span class="receipt-label">Customer Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['customer_name']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['customer_email'])): ?>
                    <div class="receipt-row">
                        <span class="receipt-label">Customer Email:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['customer_email']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['order_id'])): ?>
                    <div class="receipt-row">
                        <span class="receipt-label">Order ID:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['order_id']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($data['additional_comments'])): ?>
                    <div class="receipt-row">
                        <span class="receipt-label">Additional Comments:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($data['additional_comments']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="receipt-section">
                    <h3>Timestamp</h3>
                    <div class="receipt-row">
                        <span class="receipt-label">Client Date:</span>
                        <span class="receipt-value"><?php echo date('M d, Y h:i A', strtotime($data['client_date'] ?? $data['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Thank you for your domain registration!</p>
                <p style="font-size: 12px; margin-top: 5px;">This receipt is automatically generated</p>
            </div>
        </div>

        <div class="actions">
            <button class="btn" onclick="takeScreenshot()">📸 Take Screenshot</button>
            <button class="btn btn-secondary" onclick="goToForm()">📝 New Registration</button>
        </div>
    </div>

    <script>
        function takeScreenshot() {
            const receipt = document.getElementById('receipt');
            
            html2canvas(receipt, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                canvas.toBlob(blob => {
                    try {
                        const item = new ClipboardItem({ 'image/png': blob });
                        navigator.clipboard.write([item]).then(() => {
                            alert('Screenshot copied to clipboard! You can now paste it anywhere with Ctrl+V');
                        }).catch(err => {
                            console.error('Failed to copy to clipboard:', err);
                            alert('Failed to copy to clipboard. Please try again.');
                        });
                    } catch (err) {
                        console.error('Clipboard API not supported:', err);
                        alert('Your browser does not support copying images to clipboard. Please try using a modern browser like Chrome or Edge.');
                    }
                });
            }).catch(err => {
                console.error('Failed to take screenshot:', err);
                alert('Failed to take screenshot. Please try again.');
            });
        }

        function goToForm() {
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
