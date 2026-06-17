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

    // Get domain ID
    $domainId = $_GET['domain_id'] ?? 0;

    if (!$domainId) {
        throw new Exception("Invalid domain ID");
    }

    // Fetch email credentials
    $sql = "SELECT id, email, password, created_at FROM brand_email_credentials WHERE domain_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $domainId);
    $stmt->execute();
    $result = $stmt->get_result();

    $credentials = [];
    while ($row = $result->fetch_assoc()) {
        $credentials[] = $row;
    }

    echo json_encode(['success' => true, 'credentials' => $credentials]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
