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

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="domain_data_' . date('Y-m-d') . '.csv"');
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
    'Registration Tenure',
    'Domain For',
    'Buying As',
    'Your Name',
    'Unit Head Name',
    'Project Cost',
    'Email Address',
    'Customer Name',
    'Customer Email',
    'Order ID',
    'Additional Comments',
    'Is Viewed',
    'Created At'
];

fputcsv($output, $headers);

// Add data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data = [
            $row['id'],
            $row['domain_name'],
            $row['registration_tenure'],
            $row['domain_for'],
            $row['buying_as'],
            $row['your_name'],
            $row['unit_head_name'],
            $row['project_cost'],
            $row['email_address'],
            $row['customer_name'],
            $row['customer_email'],
            $row['order_id'] ?: '',
            $row['additional_comments'] ?: '',
            $row['is_viewed'] ? 'Yes' : 'No',
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
