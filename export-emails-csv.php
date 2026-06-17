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

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="email_credentials_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 compatibility with Excel/Google Sheets
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
$headers = [
    'ID',
    'Domain Name',
    'Email',
    'Password',
    'Created'
];

fputcsv($output, $headers);

// Add data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data = [
            $row['id'],
            $row['domain_name'],
            $row['email'],
            $row['password'],
            $row['created_at']
        ];
        fputcsv($output, $data);
    }
}

// Close output stream
fclose($output);

$conn->close();
exit;
?>
