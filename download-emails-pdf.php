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

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Credentials Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            color: #3b82f6;
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
            background: #3b82f6;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #3b82f6;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
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
    <h1>Email Credentials Report</h1>
    <p class="subtitle">Generated on ' . date('Y-m-d H:i:s') . '</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Domain Name</th>
                <th>Email</th>
                <th>Password</th>
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
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>' . htmlspecialchars($row['password']) . '</td>
                <td>' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" style="text-align: center;">No email credentials found.</td></tr>';
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
