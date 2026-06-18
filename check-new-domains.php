<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection directly to the existing database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Get current domain count from GET parameter
$currentCount = isset($_GET['count']) ? intval($_GET['count']) : 0;

// Get total domain count
$totalCountResult = $conn->query("SELECT COUNT(*) as total FROM domains");
$totalCountRow = $totalCountResult->fetch_assoc();
$totalCount = $totalCountRow['total'];

// Calculate new domains
$newDomains = $totalCount - $currentCount;

header('Content-Type: application/json');
echo json_encode(['success' => true, 'new_domains' => $newDomains, 'total_count' => $totalCount]);

$conn->close();
?>
