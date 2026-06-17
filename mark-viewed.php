<?php
// Disable error display
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Database configuration
    $host = 'localhost';
    $username = 'domainrequestpor_domainrequest';
    $password = 'SPHDMA}J9ymihaKG';
    $database = 'domainrequestpor_management';

    // Create connection directly to the existing database
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed");
    }

    // Get domain ID
    $domainId = $_POST['domain_id'] ?? 0;

    if (!$domainId) {
        throw new Exception("Invalid domain ID");
    }

    // Update is_viewed status
    $sql = "UPDATE domains SET is_viewed = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $domainId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows affected']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
