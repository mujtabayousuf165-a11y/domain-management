<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$username = 'domainrequestpor_domainrequest';
$password = 'SPHDMA}J9ymihaKG';
$database = 'domainrequestpor_management';

// Create connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if admin_users table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($tableCheck->num_rows == 0) {
    // Create admin_users table
    $sql = "CREATE TABLE admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    echo "Created admin_users table<br>";
}

// New admin credentials
$newUsername = 'admin';
$newPassword = 'admin123';
$hashedPassword = md5($newPassword);

// Check if user already exists
$checkSql = "SELECT * FROM admin_users WHERE username = '$newUsername'";
$result = $conn->query($checkSql);

if ($result->num_rows > 0) {
    // Update existing user
    $updateSql = "UPDATE admin_users SET password = '$hashedPassword' WHERE username = '$newUsername'";
    if ($conn->query($updateSql)) {
        echo "Updated existing admin user password<br>";
    } else {
        echo "Error updating password: " . $conn->error . "<br>";
    }
} else {
    // Insert new user
    $insertSql = "INSERT INTO admin_users (username, password) VALUES ('$newUsername', '$hashedPassword')";
    if ($conn->query($insertSql)) {
        echo "Created new admin user<br>";
    } else {
        echo "Error creating user: " . $conn->error . "<br>";
    }
}

echo "<br><strong>New Admin Credentials:</strong><br>";
echo "Username: " . $newUsername . "<br>";
echo "Password: " . $newPassword . "<br>";
echo "<br><a href='login.php'>Go to Login Page</a>";

$conn->close();
?>
