<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "mohita_forwarders_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Sanitize input
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Check against the `users` table now
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    echo "success";
} else {
    echo "fail";
}

$conn->close();
?>
