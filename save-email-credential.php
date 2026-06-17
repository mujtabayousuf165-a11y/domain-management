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

    // Create connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed");
    }

    // Get POST data
    $domainId = $_POST['domain_id'] ?? 0;
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$domainId || !$email || !$password) {
        throw new Exception("Missing required fields");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address");
    }

    // Insert email credential
    $sql = "INSERT INTO brand_email_credentials (domain_id, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $domainId, $email, $password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save email credential']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
