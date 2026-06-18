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

// Get domain_id and created_date from POST
$domainId = isset($_POST['domain_id']) ? intval($_POST['domain_id']) : 0;
$createdDate = isset($_POST['created_date']) ? $_POST['created_date'] : '';

if ($domainId > 0 && $createdDate) {
    // Update client_date in database (instead of created_at)
    $sql = "UPDATE domains SET client_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $createdDate, $domainId);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Created date updated successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update created date']);
    }
    
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}

$conn->close();
?>
