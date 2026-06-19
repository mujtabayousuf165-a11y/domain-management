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

// Fetch receipt data if success=1
$receipt_data = null;
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM domains WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $receipt_data = $result->fetch_assoc();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Registration System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 60px 20px;
            color: #222;
        }

        .container {
            max-width: 1050px;
            margin: auto;
        }

        .form-box {
            background: #fff;
            padding: 50px;
            border-radius: 24px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.06),
                0 2px 8px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .form-box::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 280px;
            height: 280px;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border-radius: 50%;
            opacity: 0.1;
            z-index: -1;
        }
        .tikimg {
    width: 142px;
    height: 112px;
    overflow: hidden;
    margin: 0 auto;
}

.tikimg img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    scale: 2.4;
}

        .title {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 40px;
            font-size: 15px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #111827;
            position: relative;
            padding-left: 18px;
        }

        .section-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 4px;
            width: 6px;
            height: 28px;
            border-radius: 20px;
            background: #3b82f6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 20px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            width: 100%;
            height: 58px;
            border: 1px solid #dbe1ea;
            border-radius: 14px;
            padding: 0 18px;
            font-size: 15px;
            background: #f9fbff;
            transition: all 0.3s ease;
            outline: none;
        }

        textarea.form-control {
            height: 140px;
            resize: none;
            padding-top: 18px;
        }

        .form-control:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .captcha-box {
            width: 320px;
            height: 85px;
            border-radius: 14px;
            background: #f9fbff;
            border: 1px solid #dbe1ea;
            display: flex;
            align-items: center;
            padding: 18px;
            margin-top: 10px;
            margin-bottom: 35px;
        }

        .captcha-box input {
            width: 22px;
            height: 22px;
            margin-right: 12px;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .captcha-box span {
            font-size: 14px;
            color: #444;
        }

        .submit-btn {
            border: none;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            height: 58px;
            padding: 0 45px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.25);
            margin: 15px 0 0 ;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(59, 130, 246, 0.35);
        }

        .success-message {
            display: none;
            margin-top: 25px;
            padding: 18px;
            background: #eff6ff;
            border: 1px solid #93c5fd;
            color: #3b82f6;
            border-radius: 14px;
            font-weight: 500;
        }

        /* Receipt Popup Styles */
        .receipt-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .receipt-popup.active {
            display: flex;
        }

        .receipt-content {
    background: white;
    border-radius: 15px;
    padding: 10px 30px 30px;
    max-width: 550px;
    width: 100%;
    position: relative;
}

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .receipt-header h2 {
            color: #3b82f6;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .receipt-header .success {
            color: #10b981;
            font-size: 13px;
            font-weight: 600;
        }

        .receipt-section {
            margin-bottom: 12px;
        }

        .receipt-section h3 {
            color: #3b82f6;
            font-size: 11px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        .receipt-label {
            color: #666;
            font-weight: 500;
            font-size: 12px;
        }

        .receipt-value {
            color: #333;
            font-weight: 600;
            text-align: right;
            font-size: 12px;
        }

        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #ddd;
            padding-top: 12px;
            margin-top: 12px;
        }

        .receipt-footer p {
            font-size: 11px;
        }

        .receipt-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 12px;
        }

        .receipt-btn {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .receipt-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
        }

        .receipt-btn-secondary {
            background: linear-gradient(135deg, #d90e0e, #7f0404);
            text-decoration: none;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ef4444;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media(max-width: 768px) {
            .form-box {
                padding: 30px 22px;
            }

            .title {
                font-size: 30px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .captcha-box {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-box">
            <div class="success-message" id="successMessage">
                ✅ Your domain registration request has been submitted successfully!
            </div>
            <h1 class="title">Domain Registration System</h1>
            <p class="subtitle">
                Fill out the details below to request a domain registration quickly and securely.
            </p>
            <!-- <a href="all-domains.php" style="display: inline-block; margin-bottom: 30px; color: #3b82f6; text-decoration: none; font-weight: 500; transition: 0.3s ease;">View All Domain Data →</a> -->

            <form id="domainForm" method="POST" action="submit.php" autocomplete="off">
                <input type="hidden" name="client_date" id="clientDate" readonly style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%;">

                <!-- DOMAIN INFORMATION -->
                <h2 class="section-title">Domain Information</h2>

                <div class="form-grid">

                    <div class="form-group full-width">
                        <label>Domain Name *</label>
                        <input type="text" class="form-control" name="domain_name" placeholder="Enter Domain Name" required>
                    </div>

                    <input type="hidden" name="registration_tenure" value="1 Year">

                    <div class="form-group">
                        <label>Domain For *</label>
                        <select class="form-control" name="domain_for" required>
                            <option value="">Select Domain Type</option>
                            <option value="client">Client</option>
                            <option value="brand">Brand</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Buying As *</label>
                        <select class="form-control" name="buying_as" required>
                            <option value="">Select Option</option>
                            <option>New Purchase</option>
                            <option>Renewal</option>
                            <option>Transfer</option>
                        </select>
                    </div>

                </div>

                <!-- PERSONAL INFORMATION -->
                <h2 class="section-title">Personal Information</h2>

                <div class="form-grid">

                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" class="form-control" name="your_name" placeholder="Enter Your Name" required>
                    </div>

                    <div class="form-group">
                        <label>Unit Head Name *</label>
                        <select class="form-control" id="unitHeadName" name="unit_head_name" required>
                            <option value="">Select Unit Head</option>
                            <option value="Aamir Bin Qasim">Aamir Bin Qasim</option>
                            <option value="Emmad">Emmad</option>
                            <option value="Saif Bin Qasim">Saif Bin Qasim</option>
                            <option value="Syed Ashhad Ali">Syed Ashhad Ali</option>
                            <option value="Yousuf">Yousuf</option>
                            <option value="Zain Rehan Siddiqui">Zain Rehan Siddiqui</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Project Cost *</label>
                        <input type="number" step="0.01" class="form-control" name="project_cost" placeholder="Enter Project Cost" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" class="form-control" name="email_address" placeholder="Enter Email Address" required>
                    </div>

                </div>

                <!-- CUSTOMER INFORMATION -->
                <h2 class="section-title">Customer Information</h2>

                <div class="form-grid">

                    <div class="form-group">
                        <label>Customer Name *</label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Customer Name" required>
                    </div>

                    <div class="form-group">
                        <label>Customer Email *</label>
                        <input type="email" class="form-control" name="customer_email" placeholder="Customer Email" required>
                    </div>

                    <div class="form-group full-width">
                        <label>Order ID (DU)</label>
                        <input type="text" class="form-control" name="order_id" placeholder="Enter Order ID">
                    </div>

                    <div class="form-group full-width">
                        <label>Additional Comments</label>
                        <textarea class="form-control" name="additional_comments"
                            placeholder="Write your additional comments here..."></textarea>
                    </div>

                </div>

                <!-- reCAPTCHA v2 -->
                <div class="form-group full-width">
                    <div class="g-recaptcha" data-sitekey="6LdKNCEtAAAAADZZB91oynq1DOjAYZZsWnKYLhXz"></div>
                </div>

                <button type="submit" class="submit-btn">
                    Submit Request
                </button>



            </form>

        </div>
    </div>

    <script>
        const form = document.getElementById('domainForm');
        const successMessage = document.getElementById('successMessage');
        const captcha = document.getElementById('captcha');

        // Function to update client date input
        function updateClientDate() {
            const now = new Date();
            const clientDate = now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0') + ' ' +
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clientDate').value = clientDate;
        }

        // Update client date on page load
        updateClientDate();

        // Update client date every second
        setInterval(updateClientDate, 1000);

        // Save unit head name when form is submitted
        form.addEventListener('submit', function(e) {
            // Update client date just before submission
            updateClientDate();
        });

        // Check for success parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            successMessage.style.display = "block";
            setTimeout(() => {
                successMessage.style.display = "none";
            }, 5000);
        }

        function showReceiptPopup() {
            const popup = document.getElementById('receiptPopup');
            if (popup) {
                popup.classList.add('active');
            }
        }

        // Show receipt popup if receipt data is available after page loads
        window.onload = function() {
            <?php if ($receipt_data): ?>
                setTimeout(() => {
                    showReceiptPopup();
                }, 500); // Small delay to ensure DOM is ready
            <?php endif; ?>
        };

        function closeReceiptPopup() {
            const popup = document.getElementById('receiptPopup');
            if (popup) {
                popup.classList.remove('active');
            }
        }

        function takeReceiptScreenshot() {
            const receiptContent = document.getElementById('receiptContent');

            html2canvas(receiptContent, {
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
    </script>

    <!-- Receipt Popup -->
    <?php if ($receipt_data): ?>
    <div class="receipt-popup" id="receiptPopup">
        <div class="receipt-content" id="receiptContent">
            <div class="receipt-header">
                <div class="tikimg"><img src="tik.gif" alt=""></div>
                <h2>Domain Registration Receipt</h2>    
                <p class="success">Registration Successful!</p>
                <p style="color: #666; margin-top: 10px;">Date: <?php echo date('M d, Y h:i A', strtotime($receipt_data['client_date'] ?? $receipt_data['created_at'])); ?></p>
            </div>

            <div class="receipt-body">
                <div class="receipt-section">
                    <h3>Domain Information</h3>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain ID:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['domain_id']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['domain_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Registration Tenure:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['registration_tenure']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Domain For:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['domain_for']); ?></span>
                    </div>
                </div>

                <div class="receipt-section">
                    <h3>Personal Information</h3>
                    <div class="receipt-row">
                        <span class="receipt-label">Your Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['your_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Unit Head Name:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['unit_head_name']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Project Cost:</span>
                        <span class="receipt-value">$<?php echo htmlspecialchars($receipt_data['project_cost']); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="receipt-label">Email Address:</span>
                        <span class="receipt-value"><?php echo htmlspecialchars($receipt_data['email_address']); ?></span>
                    </div>
                </div>
            </div>

            <div class="receipt-footer">
                <p>Thank you for your domain registration!</p>
                <p style="font-size: 12px; margin-top: 5px;">This receipt is automatically generated</p>
            </div>

            <div class="receipt-actions">
                <button class="receipt-btn" onclick="takeReceiptScreenshot()">📸 Take Screenshot</button>
                <a href="https://domainrequestportal.com/" class="receipt-btn receipt-btn-secondary">Close</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

</body>

</html>