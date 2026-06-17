<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection directly to the existing database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get domain_id from URL if provided
$domainId = isset($_GET['domain_id']) ? intval($_GET['domain_id']) : 0;

// Fetch email credentials with domain names
if ($domainId > 0) {
    // Filter by specific domain
    $sql = "SELECT bec.id, bec.domain_id, bec.email, bec.password, bec.created_at, d.domain_name 
            FROM brand_email_credentials bec 
            JOIN domains d ON bec.domain_id = d.id 
            WHERE bec.domain_id = ?
            ORDER BY bec.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $domainId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Show all email credentials
    $sql = "SELECT bec.id, bec.domain_id, bec.email, bec.password, bec.created_at, d.domain_name 
            FROM brand_email_credentials bec 
            JOIN domains d ON bec.domain_id = d.id 
            ORDER BY bec.created_at DESC";
    $result = $conn->query($sql);
}

// Get domain name for title if filtering by specific domain
$domainName = '';
if ($domainId > 0) {
    $domainSql = "SELECT domain_name FROM domains WHERE id = ?";
    $domainStmt = $conn->prepare($domainSql);
    $domainStmt->bind_param("i", $domainId);
    $domainStmt->execute();
    $domainResult = $domainStmt->get_result();
    if ($domainRow = $domainResult->fetch_assoc()) {
        $domainName = $domainRow['domain_name'];
    }
    $domainStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Credentials - Domain Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-box {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 16px;
        }

        .back-link,
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .back-link {
            background: #f3f4f6;
            color: #3b82f6;
        }

        .back-link:hover {
            color: #2563eb;
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        }

        .title {
            font-size: 36px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 10px;
            font-size: 15px;
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

        .data-table tbody tr:hover {
            background: #f9fbff;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
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

        .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            background: #dc2626;
            color: #fff;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-size: 16px;
        }

        @media(max-width: 768px) {
            .form-box {
                padding: 30px 22px;
            }

            .title {
                font-size: 30px;
            }

            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-box">

            <div class="header-actions">
                <a href="all-domains.php" class="back-link">← Back to Domains</a>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <a href="export-emails-csv.php?<?php echo $domainId > 0 ? "domain_id=$domainId" : ''; ?>" class="logout-btn emailBtn">Export CSV</a>
                    <a href="download-emails-pdf.php?<?php echo $domainId > 0 ? "domain_id=$domainId" : ''; ?>" class="logout-btn emailBtn">Download PDF</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <h1 class="title"><?php echo $domainName ? "Email Credentials - {$domainName}" : "Email Credentials"; ?></h1>
            <p class="subtitle">
                <?php echo $domainName ? "Email credentials for {$domainName}." : "View all saved email credentials for brand domains."; ?>
            </p>

            <?php if ($domainId > 0): ?>
                <div style="margin-bottom: 20px; padding: 16px; background: #f9fbff; border: 1px solid #dbe1ea; border-radius: 12px;">
                    <h3 style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px;">Add New Email Credential</h3>
                    <input type="email" id="newEmail" class="email-input" placeholder="Enter email address" style="margin-bottom: 12px; width: 100%; height: 50px; border: 1px solid #dbe1ea; border-radius: 10px; padding: 0 18px; font-size: 14px; background: #fff;">
                    <input type="password" id="newPassword" class="email-input" placeholder="Enter password" style="margin-bottom: 12px; width: 100%; height: 50px; border: 1px solid #dbe1ea; border-radius: 10px; padding: 0 18px; font-size: 14px; background: #fff;">
                    <button class="send-btn" onclick="saveEmailCredential()" style="padding: 12px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer; background: linear-gradient(135deg, #00c853, #00e676); color: #fff; transition: all 0.3s ease;">Add Credential</button>
                </div>
            <?php endif; ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Domain Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $serialNumber = 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $serialNumber++; ?></td>
                                <td><b><?php echo htmlspecialchars($row['domain_name']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['password']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button class="delete-btn" onclick="deleteCredential(<?php echo $row['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-data">No email credentials found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>

    <script>
        function deleteCredential(credentialId) {
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
                    location.reload();
                } else {
                    alert('Failed to delete email credential: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Failed to delete email credential:', err);
                alert('Failed to delete email credential');
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
                body: `domain_id=<?php echo $domainId; ?>&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('newEmail').value = '';
                    document.getElementById('newPassword').value = '';
                    location.reload();
                } else {
                    alert('Failed to save email credential: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Failed to save email credential:', err);
                alert('Failed to save email credential');
            });
        }
    </script>

</body>

</html>

<?php
$conn->close();
?>
