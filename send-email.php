<?php
// Disable error display and log errors instead
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Get POST data
    $email = $_POST['email'] ?? '';
    $content = $_POST['content'] ?? '';
    $subject = $_POST['subject'] ?? 'Domain Registration Details';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Email headers
    $headers = "From: noreply@domainmanagement.com\r\n";
    $headers .= "Reply-To: noreply@domainmanagement.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Convert markdown bold to plain text for email
    $content = str_replace('**', '', $content);

    // Send email
    $success = mail($email, $subject, $content, $headers);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Please configure SMTP settings in php.ini']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
