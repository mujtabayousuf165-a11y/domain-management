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

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters
$selectedIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build SQL query with filters
$sql = "SELECT * FROM domains WHERE 1=1";
$params = [];
$types = '';

if (!empty($selectedIds)) {
    $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
    $sql .= " AND id IN ($placeholders)";
    $params = array_merge($params, $selectedIds);
    $types .= str_repeat('i', count($selectedIds));
}

if ($dateFrom) {
    $sql .= " AND created_at >= ?";
    $params[] = $dateFrom;
    $types .= 's';
}

if ($dateTo) {
    $sql .= " AND created_at <= ?";
    $params[] = $dateTo . ' 23:59:59';
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";

// Execute query
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Domain Data Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            color: #00c853;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #00c853;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #00c853;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .new-badge {
            background: #00c853;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>Domain Data Report</h1>
    <p class="subtitle">Generated on ' . date('Y-m-d H:i:s') . '</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Domain Name</th>
                <th>Tenure</th>
                <th>Domain For</th>
                <th>Buying As</th>
                <th>Your Name</th>
                <th>Unit Head Name</th>
                <th>Domain Cost</th>
                <th>Email</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th>Order ID</th>
                <th>Comments</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>';

if ($result->num_rows > 0) {
    $serialNumber = 1;
    while ($row = $result->fetch_assoc()) {
        $html .= '
            <tr>
                <td>' . $serialNumber++ . '</td>
                <td>' . htmlspecialchars($row['domain_name']) . '</td>
                <td>' . htmlspecialchars($row['registration_tenure']) . '</td>
                <td>' . htmlspecialchars($row['domain_for']) . '</td>
                <td>' . htmlspecialchars($row['buying_as']) . '</td>
                <td>' . htmlspecialchars($row['your_name']) . '</td>
                <td>' . htmlspecialchars($row['unit_head_name']) . '</td>
                <td>$' . htmlspecialchars($row['project_cost']) . '</td>
                <td>' . htmlspecialchars($row['email_address']) . '</td>
                <td>' . htmlspecialchars($row['customer_name']) . '</td>
                <td>' . htmlspecialchars($row['customer_email']) . '</td>
                <td>' . htmlspecialchars($row['order_id'] ?: '-') . '</td>
                <td>' . htmlspecialchars($row['additional_comments'] ?: '-') . '</td>
                <td>' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="14" style="text-align: center;">No domain data found.</td></tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>Domain Management System - Confidential Data</p>
    </div>
</body>
</html>';

$conn->close();

// Output HTML with print functionality
echo $html;
echo '<script>window.onload = function() { window.print(); }</script>';
?>
