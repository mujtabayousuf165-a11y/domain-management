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

// Get unit_head_name from POST
$unitHeadName = isset($_POST['unit_head_name']) ? trim($_POST['unit_head_name']) : '';

if ($unitHeadName) {
    // Check if already exists
    $checkSql = "SELECT id FROM unit_head_history WHERE unit_head_name = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $unitHeadName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insert new unit head name
        $sql = "INSERT INTO unit_head_history (unit_head_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $unitHeadName);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Unit head name saved']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to save unit head name']);
        }
        
        $stmt->close();
    } else {
        // Already exists, return success
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Unit head name already exists']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid unit head name']);
}

$conn->close();
?>
