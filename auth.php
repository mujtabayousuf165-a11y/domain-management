<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection directly to the existing database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // Hash the password using MD5 (same as in database)
    $hashed_password = md5($password);
    
    // Check if user exists
    $sql = "SELECT * FROM admin_users WHERE username = '$username' AND password = '$hashed_password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to all-domains page
        header("Location: all-domains.php");
        exit();
    } else {
        // Redirect back to login with error
        header("Location: login.php?error=1");
        exit();
    }
}

$conn->close();
?>
