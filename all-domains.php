<?php
session_start();

// Set timezone to UTC+05:00 (Pakistan)
date_default_timezone_set('Asia/Karachi');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection directly to the existing database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate domain status
function getDomainStatus($tenure, $createdAt, $emailSent)
{
    // Always use 1 year tenure
    $years = 1;

    // Calculate expiry date (1 year from creation)
    $createdDate = new DateTime($createdAt);
    $expiryDate = clone $createdDate;
    $expiryDate->modify("+{$years} years");

    // Calculate grace period start (11 months from creation)
    $graceStartDate = clone $createdDate;
    $graceStartDate->modify("+11 months");

    // Check current date
    $now = new DateTime();

    // Check if expired
    if ($now >= $expiryDate) {
        return '<span class="badge" style="background: #fee2e2; color: #dc2626;">Expired</span>';
    }
    // Check if in grace period (11th month)
    elseif ($now >= $graceStartDate && $now < $expiryDate) {
        return '<span class="badge" style="background: #fef3c7; color: #d97706;">Grace</span>';
    }
    // Check if email has been sent
    elseif ($emailSent == 1) {
        return '<span class="badge" style="background: #dcfce7; color: #16a34a;">Active</span>';
    }
    // Pending (email not sent yet)
    else {
        return '<span class="badge" style="background: #e5e7eb; color: #6b7280;">Pending</span>';
    }
}

// Function to calculate expiry date
function getExpiryDate($createdAt)
{
    $createdDate = new DateTime($createdAt);
    $expiryDate = clone $createdDate;
    $expiryDate->modify("+1 year");
    return $expiryDate->format('M d, Y');
}

// Fetch all domains from database
$sql = "SELECT * FROM domains ORDER BY id DESC";
$result = $conn->query($sql);

// No longer using time threshold, using is_viewed instead
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Domain Data - Domain Registration System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
            padding: 40px 20px;
            color: #222;
        }

        .container {
            max-width: 1400px;
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
            margin-bottom: 10px;
            font-size: 15px;
        }

        .back-link {
            display: inline-block;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .back-link:hover {
            color: #2563eb;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .data-table th,
        .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #dbe1ea;
        }

        .data-table th {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            font-weight: 600;
            font-size: 13px;
        }

        .data-table td {
            font-size: 12px;
            color: #374151;
        }

        .data-table tbody tr {
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: #f9fbff;
        }

        .data-table tbody tr.new {
            background: linear-gradient(135deg, #ecfdf3, #d1fae5);
        }

        .data-table tbody tr.new::after {
            content: 'NEW';
            background: #3b82f6;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            position: absolute;
            right: 7px;
            top: 50%;
            transform: translatey(-50%);
        }

        tr.new .badge {
            background: #bfdbfe;
        }

        .data-table tbody tr.new {
            position: relative;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .tabs {
            display: flex;
            gap: 8px;
        }

        .tab-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f3f4f6;
            color: #6b7280;
        }

        .tab-btn:hover {
            background: #e5e7eb;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .emails-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            transition: all 0.3s ease;
        }

        .emails-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .email-credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f9fbff;
            border: 1px solid #dbe1ea;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .email-credential-info {
            flex: 1;
        }

        .email-credential-email {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 2px;
        }

        .email-credential-password {
            font-size: 11px;
            color: #6b7280;
        }

        .delete-credential-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 500;
            cursor: pointer;
            background: #ef4444;
            color: #fff;
            transition: all 0.3s ease;
        }

        .delete-credential-btn:hover {
            background: #dc2626;
        }

        .card-value strong {
            font-weight: 600;
            color: #111827;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 55%;
        }

        .modal-data-container {
            display: flex;
            gap: 20px;
            margin-bottom: 16px;
        }

        .modal-data-box {
            flex: 1;
            background: #f9fbff;
            border: 1px solid #dbe1ea;
            border-radius: 12px;
            padding: 20px;
        }

        .modal-data-box h3 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dbe1ea;
        }

        .modal-data-item {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }

        .modal-data-label {
            width: 150px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            flex-shrink: 0;
        }

        .modal-data-value {
            font-size: 12px;
            color: #374151;
            flex: 1;
            word-break: break-word;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f3f4f6;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            color: #6b7280;
            transition: 0.3s ease;
        }

        .modal-close:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .modal-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111827;
        }

        .modal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .modal-table th,
        .modal-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #dbe1ea;
        }

        .modal-table th {
            background: #f9fbff;
            font-weight: 600;
            color: #374151;
            width: 200px;
        }

        .modal-table td {
            color: #111827;
        }

        .modal-table tr:last-child th,
        .modal-table tr:last-child td {
            border-bottom: none;
        }

        .email-input {
            width: 100%;
            height: 50px;
            border: 1px solid #dbe1ea;
            border-radius: 12px;
            padding: 0 16px;
            font-size: 14px;
            background: #f9fbff;
            transition: all 0.3s ease;
            outline: none;
        }

        .email-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .send-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        }

        .send-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .send-btn.sent {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .pagination-btn {
            background: #fff;
            border: 1px solid #dbe1ea;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #374151;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #3b82f6;
            color: #fff;
            border-color: #3b82f6;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            border-color: #3b82f6;
        }

        .pagination-dots {
            padding: 8px 12px;
            color: #9ca3af;
            font-size: 14px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-size: 16px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            background: #dbeafe;
            color: #3b82f6;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 16px;
        }

        .header-search {
            flex: 1;
            max-width: 400px;
        }

        .headerRight {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 6 0%;
        }

        .card.new .badge {
            background: #7fffb3;
        }

        .tabTops {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0;
        }

        .tabsRight {
            display: flex;
            align-items: center;
            gap: 30px;
        }


        .emailBtn {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
        }

        .expBtn {
            width: 210px;
            padding: 13px 10px;
        }

        #deleteSelectedBtn {
            border: none;
            padding: 12px 20px;
        }

        @media(max-width: 768px) {
            .form-box {
                padding: 30px 22px;
            }

            .title {
                font-size: 30px;
            }

            .cards-container {
                grid-template-columns: 1fr;
            }

            .card-row {
                flex-direction: column;
            }

            .card-label {
                width: 100%;
                margin-bottom: 4px;
            }
        }
    </style>
</head>

<body>

    <!-- Notification Sound -->
    <audio id="notificationSound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <div class="container">
        <div class="form-box">

            <div class="header-actions">
                <a href="index.php" class="back-link">← Back to Form</a>
                <div class="headerRight">
                    <div class="header-search">
                        <input type="text" id="searchInput" class="email-input" placeholder="🔍 Search domains..." onkeyup="filterCards()" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                    </div>
                    <button id="deleteSelectedBtn" class="logout-btn emailBtn" style="display: none; background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);" onclick="deleteSelected()">Delete Selected</button>
                    <a href="emails.php" class="logout-btn emailBtn">All Emails</a>
                    <a href="download-pdf.php" class="logout-btn emailBtn">Download PDF</a>
                    <a href="export-csv.php" class="logout-btn emailBtn">Export to Sheets</a>
                    <a href="logout.php" class="logout-btn ">Logout</a>
                </div>
            </div>

            <h1 class="title">All Domain Data</h1>
            <p class="subtitle">
                View all submitted domain registration requests from the database.
            </p>

            <div class="tabTops">
                <div class="tabs">
                    <button class="tab-btn active" onclick="switchTab('all')" id="tab-all">All</button>
                    <button class="tab-btn" onclick="switchTab('client')" id="tab-client">Client</button>
                    <button class="tab-btn" onclick="switchTab('brand')" id="tab-brand">Brand</button>
                </div>

                <div class="tabsRight">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="date" id="dateFrom" class="email-input" style="height: 40px; padding: 0 12px; font-size: 13px;">
                        <input type="date" id="dateTo" class="email-input" style="height: 40px; padding: 0 12px; font-size: 13px;">
                        <button class="emails-btn expBtn" onclick="exportSelected('csv')">Export CSV</button>
                        <button class="emails-btn expBtn" onclick="exportSelected('pdf')">Export PDF</button>
                    </div>

                    <div id="pagination" style=" display: flex; gap: 8px; flex-wrap: wrap; justify-content: end;"></div>

                </div>
            </div>
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll" onclick="toggleAllCheckboxes()"></th>
                        <th>#</th>
                        <th>Domain Name</th>
                        <th>Customer</th>
                        <th>Tenure</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php
                        // Reset result pointer to use it again
                        $result->data_seek(0);
                        $serialNumber = 1;
                        while ($row = $result->fetch_assoc()):
                            // Store data as JSON for modal
                            $rowData = json_encode($row);
                        ?>
                            <tr class="<?php echo $row['is_viewed'] == 0 ? 'new' : ''; ?>" onclick="openModal(<?php echo htmlspecialchars($rowData, ENT_QUOTES); ?>)" data-domain-for="<?php echo strtolower($row['domain_for']); ?>" data-domain-id="<?php echo $row['id']; ?>">
                                <td onclick="event.stopPropagation();"><input type="checkbox" class="row-checkbox" value="<?php echo $row['id']; ?>" onchange="updateDeleteButton()"></td>
                                <td><?php echo $serialNumber++; ?></td>
                                <td style="font-size: 16px;"><b><?php echo htmlspecialchars($row['domain_name']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($row['registration_tenure']); ?></span></td>
                                <td><?php echo getDomainStatus($row['registration_tenure'], $row['created_at'], $row['email_sent']); ?></td>
                                <td><?php echo getExpiryDate($row['created_at']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td onclick="event.stopPropagation();">
                                    <?php if (strtolower($row['domain_for']) === 'brand'): ?>
                                        <a href="emails.php?domain_id=<?php echo $row['id']; ?>" class="emails-btn" style="text-decoration: none">Emails</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="no-data">No domain data found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Email Credentials Modal -->
            <div class="modal" id="emailModal">
                <div class="modal-content" style="max-width: 500px;">
                    <button class="modal-close" onclick="closeEmailModal()">&times;</button>
                    <h2 class="modal-title" id="emailModalTitle">Email Credentials</h2>

                    <div id="emailList" style="margin-bottom: 20px; max-height: 200px; overflow-y: auto;">
                        <!-- Email list will be populated by JavaScript -->
                    </div>

                    <div style="border-top: 1px solid #dbe1ea; padding-top: 16px; margin-top: 16px;">
                        <input type="email" class="email-input" id="newEmail" placeholder="Enter email address" style="margin-bottom: 12px;">
                        <input type="password" class="email-input" id="newPassword" placeholder="Enter password" style="margin-bottom: 12px;">
                        <button class="send-btn" onclick="saveEmailCredential()">Add Credential</button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal" id="detailModal">
                <div class="modal-content">
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                    <h2 class="modal-title" id="modalTitle">Domain Details</h2>

                    <div class="modal-data-container">
                        <div class="modal-data-box" id="modalBox1">
                            <!-- First box content will be populated by JavaScript -->
                        </div>
                        <div class="modal-data-box" id="modalBox2">
                            <!-- Second box content will be populated by JavaScript -->
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 8px;">Domain Type:</label>
                        <select class="email-input" id="domainType" style="margin-bottom: 12px;">
                            <option value="">Select Domain Type</option>
                            <option value="Namecheap New">Namecheap New</option>
                            <option value="Namecheap Old">Namecheap Old</option>
                            <option value="renewal">Renewal (Third Party)</option>
                        </select>
                    </div>

                    <div style="">
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 8px;">Created Date:</label>
                        <input type="date" class="email-input" id="createdDateInput" style="margin-bottom: 12px;" onchange="updateExpiryDate()">
                    </div>

                    <input type="email" class="email-input" style="margin-top: 16px; margin-bottom: 20px;" id="emailInput" placeholder="Enter email address to send data">
                    <button class="send-btn" id="sendBtn" onclick="sendEmail()">Send to Email</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function updateExpiryDate(domainId = null) {
            const createdDateInput = document.getElementById('createdDateInput');

            if (createdDateInput.value) {
                // Get domain ID from parameter or from box 1
                let id = domainId;
                if (!id) {
                    const box1 = document.getElementById('modalBox1');
                    if (box1) {
                        id = box1.querySelector('.modal-data-item:nth-child(2) .modal-data-value')?.textContent;
                    }
                }

                // Update created date in database
                if (id) {
                    fetch('update-created-date.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `domain_id=${id}&created_date=${createdDateInput.value}`
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Created date updated successfully');
                            }
                        }).catch(err => {
                            console.error('Failed to update created date:', err);
                        });
                }
            }
        }

        function openModal(data) {
            const modal = document.getElementById('detailModal');
            const box1 = document.getElementById('modalBox1');
            const box2 = document.getElementById('modalBox2');
            const title = document.getElementById('modalTitle');
            const emailInput = document.getElementById('emailInput');
            const createdDateInput = document.getElementById('createdDateInput');

            title.textContent = data.domain_name;

            // Pre-fill email input with personal information email
            emailInput.value = data.email_address || '';

            // Set created date input
            const createdDate = new Date(data.created_at);
            const formattedDate = createdDate.toISOString().split('T')[0];
            createdDateInput.value = formattedDate;

            // Split fields into two groups
            const box1Fields = [{
                    label: 'ID',
                    value: data.domain_id || data.id
                },
                {
                    label: 'Domain Name',
                    value: data.domain_name
                },
                {
                    label: 'Registration Tenure',
                    value: data.registration_tenure
                },
                {
                    label: 'Domain For',
                    value: data.domain_for
                },
                {
                    label: 'Buying As',
                    value: data.buying_as
                },
                {
                    label: 'Your Name',
                    value: data.your_name
                },
                {
                    label: 'Unit Head Name',
                    value: data.unit_head_name
                },
                {
                    label: 'Project Cost',
                    value: data.project_cost
                }
            ];

            const box2Fields = [{
                    label: 'Email Address',
                    value: data.email_address
                },
                {
                    label: 'Customer Name',
                    value: data.customer_name
                },
                {
                    label: 'Customer Email',
                    value: data.customer_email
                },
                {
                    label: 'Order ID',
                    value: data.order_id || '-'
                },
                {
                    label: 'Additional Comments',
                    value: data.additional_comments || '-'
                },
                {
                    label: 'Created At',
                    value: new Date(data.created_at).toLocaleString()
                }
            ];

            // Populate box 1
            let box1HTML = '<h3>Domain Information</h3>';
            box1Fields.forEach(field => {
                box1HTML += `
                    <div class="modal-data-item">
                        <div class="modal-data-label">${field.label}:</div>
                        <div class="modal-data-value">${field.value}</div>
                    </div>
                `;
            });
            box1.innerHTML = box1HTML;

            // Populate box 2
            let box2HTML = '<h3>Contact & Details</h3>';
            box2Fields.forEach(field => {
                box2HTML += `
                    <div class="modal-data-item">
                        <div class="modal-data-label">${field.label}:</div>
                        <div class="modal-data-value">${field.value}</div>
                    </div>
                `;
            });
            box2.innerHTML = box2HTML;

            modal.classList.add('active');

            // Mark as viewed in database
            markAsViewed(data.id);
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.remove('active');
        }

        function markAsViewed(domainId) {
            fetch('mark-viewed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `domain_id=${domainId}`
            }).catch(err => console.error('Failed to mark as viewed:', err));
        }

        function sendEmail() {
            const email = document.getElementById('emailInput').value;
            const box1 = document.getElementById('modalBox1');
            const box2 = document.getElementById('modalBox2');
            const title = document.getElementById('modalTitle').textContent;
            const domainType = document.getElementById('domainType').value;

            // Get domain ID from box 1
            const domainId = box1.querySelector('.modal-data-item:nth-child(2) .modal-data-value').textContent;

            if (!email || !email.includes('@')) {
                alert('Please enter a valid email address');
                return;
            }

            // Create email content
            let emailContent = `${title}\n`;
            emailContent += `${'='.repeat(title.length)}\n\n`;

            if (domainType) {
                emailContent += `Domain Type: ${domainType}\n\n`;
            }

            // Get data from both boxes
            const allItems = [...box1.querySelectorAll('.modal-data-item'), ...box2.querySelectorAll('.modal-data-item')];
            allItems.forEach(item => {
                const label = item.querySelector('.modal-data-label').textContent;
                const value = item.querySelector('.modal-data-value').textContent;
                emailContent += `${label} ${value}\n`;
            });

            // Mark as viewed when email is sent
            markAsViewed(domainId);

            // Mark email as sent in database
            fetch('mark-email-sent.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `domain_id=${domainId}`
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Email sent marked successfully');
                    }
                }).catch(err => {
                    console.error('Failed to mark email sent:', err);
                });

            // CC emails
            const ccEmails = 'ashar.khan@thetechrics.com,ashad.khan@thetechrics.com';

            // Open email client with mailto link
            const mailtoLink = `mailto:${email}?cc=${encodeURIComponent(ccEmails)}&subject=${encodeURIComponent(title)}&body=${encodeURIComponent(emailContent)}`;
            window.location.href = mailtoLink;

            // Clear input and show success
            document.getElementById('emailInput').value = '';
            document.getElementById('domainType').value = '';
            const btn = document.getElementById('sendBtn');
            btn.textContent = '✓ Opening Email Client...';

            setTimeout(() => {
                btn.textContent = 'Send to Email';
                // Refresh the page to update status
                location.reload();
            }, 2000);
        }

        function openEmailModal(domainId, domainName) {
            currentDomainId = domainId;
            const modal = document.getElementById('emailModal');
            const title = document.getElementById('emailModalTitle');

            title.textContent = `Email Credentials - ${domainName}`;
            modal.classList.add('active');

            // Load email credentials
            loadEmailCredentials(domainId);
        }

        function closeEmailModal() {
            const modal = document.getElementById('emailModal');
            modal.classList.remove('active');

            // Clear form
            document.getElementById('newEmail').value = '';
            document.getElementById('newPassword').value = '';
            currentDomainId = null;
        }

        function loadEmailCredentials(domainId) {
            fetch('get-email-credentials.php?domain_id=' + domainId)
                .then(response => response.json())
                .then(data => {
                    const emailList = document.getElementById('emailList');

                    if (data.success && data.credentials.length > 0) {
                        let html = '';
                        data.credentials.forEach(cred => {
                            html += `
                                <div class="email-credential-item">
                                    <div class="email-credential-info">
                                        <div class="email-credential-email">${cred.email}</div>
                                        <div class="email-credential-password">Password: ${cred.password}</div>
                                    </div>
                                    <button class="delete-credential-btn" onclick="deleteEmailCredential(${cred.id})">Delete</button>
                                </div>
                            `;
                        });
                        emailList.innerHTML = html;
                    } else {
                        emailList.innerHTML = '<p style="color: #6b7280; font-size: 13px; text-align: center; padding: 20px;">No email credentials saved yet.</p>';
                    }
                })
                .catch(err => {
                    console.error('Failed to load email credentials:', err);
                    document.getElementById('emailList').innerHTML = '<p style="color: #ef4444; font-size: 13px; text-align: center; padding: 20px;">Failed to load email credentials.</p>';
                });
        }

        function saveEmailCredential() {
            const email = document.getElementById('newEmail').value;
            const password = document.getElementById('newPassword').value;

            if (!email || !password) {
                alert('Please enter both email and password');
                return;
            }

            if (!email.includes('@')) {
                alert('Please enter a valid email address');
                return;
            }

            fetch('save-email-credential.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `domain_id=${currentDomainId}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('newEmail').value = '';
                        document.getElementById('newPassword').value = '';
                        loadEmailCredentials(currentDomainId);
                    } else {
                        alert('Failed to save email credential: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Failed to save email credential:', err);
                    alert('Failed to save email credential');
                });
        }

        function deleteEmailCredential(credentialId) {
            if (!confirm('Are you sure you want to delete this email credential?')) {
                return;
            }

            fetch('delete-email-credential.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `credential_id=${credentialId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadEmailCredentials(currentDomainId);
                    } else {
                        alert('Failed to delete email credential: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error('Failed to delete email credential:', err);
                    alert('Failed to delete email credential');
                });
        }

        function toggleAllCheckboxes() {
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            if (checkboxes.length > 0) {
                deleteBtn.style.display = 'inline-block';
            } else {
                deleteBtn.style.display = 'none';
            }
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one domain to delete');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${checkboxes.length} domain(s)? This action cannot be undone.`)) {
                return;
            }

            const domainIds = Array.from(checkboxes).map(cb => cb.value);

            fetch('delete-domain.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `domain_id=${domainIds.join('&domain_id=')}`
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Domain(s) deleted successfully');
                        location.reload();
                    } else {
                        alert('Failed to delete domain(s): ' + data.message);
                    }
                }).catch(err => {
                    console.error('Failed to delete domain(s):', err);
                    alert('Failed to delete domain(s)');
                });
        }

        function exportSelected(format) {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const selectedIds = Array.from(checkboxes).map(cb => cb.value);

            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            if (selectedIds.length === 0 && !dateFrom && !dateTo) {
                alert('Please select at least one row or specify a date range');
                return;
            }

            let url = format === 'csv' ? 'export-csv.php' : 'download-pdf.php';
            url += '?';

            if (selectedIds.length > 0) {
                url += 'ids=' + selectedIds.join(',');
            }

            if (dateFrom) {
                url += (selectedIds.length > 0 ? '&' : '') + 'date_from=' + dateFrom;
            }

            if (dateTo) {
                url += (selectedIds.length > 0 || dateFrom ? '&' : '') + 'date_to=' + dateTo;
            }

            window.open(url, '_blank');
        }

        // Pagination and search variables
        let currentPage = 1;
        const itemsPerPage = 12;
        let allCards = [];
        let currentTab = 'all';
        let currentDomainId = null;

        // Initialize pagination and search
        document.addEventListener('DOMContentLoaded', function() {
            allCards = Array.from(document.querySelectorAll('#dataTable tbody tr'));
            setupPagination();
            filterCards();
        });

        function switchTab(tab) {
            currentTab = tab;

            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');

            // Filter rows by domain type
            filterCards();
        }

        function filterCards() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const domainFor = row.getAttribute('data-domain-for');

                const matchesSearch = text.includes(searchTerm);
                const matchesTab = currentTab === 'all' || domainFor === currentTab;

                if (matchesSearch && matchesTab) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            currentPage = 1;
            setupPagination();
            showPage(currentPage);
        }

        function setupPagination() {
            const visibleRows = Array.from(document.querySelectorAll('#dataTable tbody tr')).filter(row => row.style.display !== 'none');
            const totalPages = Math.ceil(visibleRows.length / itemsPerPage);
            const pagination = document.getElementById('pagination');

            let html = '';

            // Previous button
            html += `<button class="pagination-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>← Prev</button>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="pagination-dots">...</span>`;
                }
            }

            // Next button
            html += `<button class="pagination-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Next →</button>`;

            pagination.innerHTML = html;
        }

        function changePage(page) {
            const visibleRows = Array.from(document.querySelectorAll('#dataTable tbody tr')).filter(row => row.style.display !== 'none');
            const totalPages = Math.ceil(visibleRows.length / itemsPerPage);

            if (page < 1 || page > totalPages) return;

            currentPage = page;
            showPage(currentPage);
            setupPagination();
        }

        function showPage(page) {
            const visibleRows = Array.from(document.querySelectorAll('#dataTable tbody tr')).filter(row => row.style.display !== 'none');
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            visibleRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Real-time polling for new domains
        let lastDomainId = 0;
        const tableRows = document.querySelectorAll('#dataTable tbody tr');
        if (tableRows.length > 0) {
            const firstRow = tableRows[0];
            const checkbox = firstRow.querySelector('.row-checkbox');
            if (checkbox) {
                lastDomainId = parseInt(checkbox.value);
            }
        }
        const notificationSound = document.getElementById('notificationSound');

        function checkForNewDomains() {
            const formData = new FormData();
            formData.append('last_id', lastDomainId);

            fetch('check-new-domains.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.new_domains && data.new_domains.length > 0) {
                    // Play notification sound
                    notificationSound.play().catch(e => console.log('Audio play failed:', e));

                    // Add new domains to table
                    data.new_domains.forEach(domain => {
                        addDomainToTable(domain);
                        lastDomainId = domain.id;
                    });

                    // Refresh pagination
                    filterCards();
                }
            })
            .catch(error => console.error('Error checking for new domains:', error));
        }

        function addDomainToTable(domain) {
            const tbody = document.querySelector('#dataTable tbody');
            const expiryDate = getExpiryDate(domain.created_at);
            const createdDate = new Date(domain.created_at).toLocaleString();
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="checkbox" class="row-checkbox" value="${domain.id}" onchange="updateDeleteButton()"></td>
                <td><a href="#" onclick="showDetails(${domain.id})" style="color: #1e40af; text-decoration: none; font-weight: 500;">${domain.domain_name}</a></td>
                <td>${domain.registration_tenure}</td>
                <td>${domain.domain_for}</td>
                <td>${domain.buying_as}</td>
                <td>${domain.your_name}</td>
                <td>${domain.unit_head_name}</td>
                <td>${domain.project_cost}</td>
                <td>${domain.email_address}</td>
                <td>${domain.customer_name}</td>
                <td>${domain.customer_email}</td>
                <td>${domain.order_id || '-'}</td>
                <td>${domain.additional_comments || '-'}</td>
                <td>${createdDate}</td>
            `;
            
            // Add row at the beginning of tbody
            tbody.insertBefore(row, tbody.firstChild);
        }

        // Poll every 5 seconds
        setInterval(checkForNewDomains, 5000);
    </script>

</body>

</html>

<?php
$conn->close();
?>