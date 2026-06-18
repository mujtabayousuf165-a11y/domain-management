<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone to UTC+05:00 (Pakistan)
date_default_timezone_set('Asia/Karachi');

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection directly to the existing database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error . "<br>Host: $host<br>Username: $username<br>Database: $database");
}

// Set MySQL timezone to Pakistan (UTC+05:00)
$conn->query("SET time_zone = '+05:00'");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA v2
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $secret_key = '6LdKNCEtAAAAAP6D9fY2nXPMrjS78h0_0Kn7nl4M';

    if (empty($recaptcha_response)) {
        header("Location: index.php?error=" . urlencode("Please complete the reCAPTCHA"));
        exit();
    }

    // Verify with Google's API
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($verify_url, false, $context);
    $response_keys = json_decode($response, true);

    if (!$response_keys['success']) {
        header("Location: index.php?error=" . urlencode("reCAPTCHA verification failed"));
        exit();
    }

    // Get form data
    $domain_name = $conn->real_escape_string($_POST['domain_name']);
    $registration_tenure = $conn->real_escape_string($_POST['registration_tenure']);
    $domain_for = $conn->real_escape_string($_POST['domain_for']);
    $buying_as = $conn->real_escape_string($_POST['buying_as']);
    $your_name = $conn->real_escape_string($_POST['your_name']);

    // Handle unit head name (either from select or new input)
    $unit_head_name = '';
    if (isset($_POST['unit_head_name']) && $_POST['unit_head_name'] !== 'new') {
        $unit_head_name = $conn->real_escape_string($_POST['unit_head_name']);
    } elseif (isset($_POST['new_unit_head_name'])) {
        $unit_head_name = $conn->real_escape_string($_POST['new_unit_head_name']);
    }

    $project_cost = $conn->real_escape_string($_POST['project_cost']);
    $email_address = $conn->real_escape_string($_POST['email_address']);
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    $order_id = $conn->real_escape_string($_POST['order_id']);
    $additional_comments = $conn->real_escape_string($_POST['additional_comments']);
    $client_date = $conn->real_escape_string($_POST['client_date']);

    // Check if domain already exists and use existing ID, otherwise generate new ID
    $check_domain = $conn->query("SELECT domain_id FROM domains WHERE domain_name = '$domain_name' ORDER BY id DESC LIMIT 1");
    if ($check_domain->num_rows > 0) {
        $row = $check_domain->fetch_assoc();
        $unique_id = $row['domain_id'];
    } else {
        // Generate unique domain ID with hashtag
        $unique_id = '#DOM-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    }

    // Use client date from PC instead of server date
    $current_date = $client_date;

    // Insert data into database
    $sql = "INSERT INTO domains (domain_id, domain_name, registration_tenure, domain_for, buying_as, your_name, unit_head_name, project_cost, email_address, customer_name, customer_email, order_id, additional_comments, created_at, client_date)
            VALUES ('$unique_id', '$domain_name', '$registration_tenure', '$domain_for', '$buying_as', '$your_name', '$unit_head_name', '$project_cost', '$email_address', '$customer_name', '$customer_email', '$order_id', '$additional_comments', NOW(), '$current_date')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to index with success message
        header("Location: index.php?success=1");
        exit();
    } else {
        // Redirect back with error message
        header("Location: index.php?error=" . urlencode($conn->error));
        exit();
    }
}

$conn->close();
?>
