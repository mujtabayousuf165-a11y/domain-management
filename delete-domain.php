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

// Get domain_id(s) from POST
if (isset($_POST['domain_id'])) {
    $domainIds = is_array($_POST['domain_id']) ? $_POST['domain_id'] : [$_POST['domain_id']];
} else {
    $domainIds = [];
}

if (!empty($domainIds)) {
    // Delete domains (cascade will delete related email credentials)
    $ids = implode(',', array_map('intval', $domainIds));
    $sql = "DELETE FROM domains WHERE id IN ($ids)";
    
    if ($conn->query($sql)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Domain(s) deleted successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to delete domain(s)']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid domain ID(s)']);
}

$conn->close();
?>
