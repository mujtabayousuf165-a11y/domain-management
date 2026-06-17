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

// Get last known domain ID from POST
$lastId = isset($_POST['last_id']) ? intval($_POST['last_id']) : 0;

// Fetch domains with ID greater than lastId
$sql = "SELECT * FROM domains WHERE id > ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lastId);
$stmt->execute();
$result = $stmt->get_result();

$newDomains = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $newDomains[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'new_domains' => $newDomains]);

$stmt->close();
$conn->close();
?>
