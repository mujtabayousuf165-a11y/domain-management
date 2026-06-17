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

// Fetch distinct unit head names from history
$sql = "SELECT DISTINCT unit_head_name FROM unit_head_history ORDER BY unit_head_name ASC";
$result = $conn->query($sql);

$unitHeads = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $unitHeads[] = $row['unit_head_name'];
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'unit_heads' => $unitHeads]);

$conn->close();
?>
