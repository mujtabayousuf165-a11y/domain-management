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
                        <input type="text" class="form-control" id="unitHeadName" name="unit_head_name" placeholder="Enter Unit Head Name" list="unitHeadHistory" required>
                        <datalist id="unitHeadHistory">
                            <!-- Will be populated by JavaScript -->
                        </datalist>
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
        const unitHeadNameInput = document.getElementById('unitHeadName');
        const unitHeadHistory = document.getElementById('unitHeadHistory');

        // Load unit head history on page load
        function loadUnitHeadHistory() {
            fetch('get-unit-head-history.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.unit_heads) {
                        unitHeadHistory.innerHTML = '';
                        data.unit_heads.forEach(name => {
                            const option = document.createElement('option');
                            option.value = name;
                            unitHeadHistory.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error('Failed to load unit head history:', err));
        }

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

            const unitHeadName = unitHeadNameInput.value.trim();
            if (unitHeadName) {
                fetch('save-unit-head-history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `unit_head_name=${encodeURIComponent(unitHeadName)}`
                }).catch(err => console.error('Failed to save unit head name:', err));
            }
        });

        // Load unit head history on page load
        loadUnitHeadHistory();

        // Check for success parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === '1') {
            successMessage.style.display = "block";
            setTimeout(() => {
                successMessage.style.display = "none";
            }, 5000);
        }
    </script>

</body>

</html>