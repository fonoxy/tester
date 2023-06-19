<?php

// File Indexing System with Accounts, Account Images, and Account Sharing

// Connect to the database (Replace with your own database credentials)
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255)
)";
$conn->query($sql);

// Create files table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS files (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    user_id INT(6) UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

// Create sharing table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS sharing (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_id INT(6) UNSIGNED,
    user_id INT(6) UNSIGNED,
    FOREIGN KEY (file_id) REFERENCES files(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

// Register a new user
function registerUser($username, $password)
{
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
    if ($conn->query($sql) === true) {
        echo "Registration successful.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Login a user
function loginUser($username, $password)
{
    global $conn;
    $sql = "SELECT id, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        if (password_verify($password, $hashedPassword)) {
            echo "Login successful. Welcome!";
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }
}

// Upload a file for a user
function uploadFile($filename, $username)
{
    global $conn;
    $user_id = getUserId($username);

    $sql = "INSERT INTO files (filename, user_id) VALUES ('$filename', $user_id)";
    if ($conn->query($sql) === true) {
        echo "File uploaded successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Share a file with another user
function shareFile($fileId, $username)
{
    global $conn;
    $user_id = getUserId($username);

    $sql = "INSERT INTO sharing (file_id, user_id) VALUES ($fileId, $user_id)";
    if ($conn->query($sql) === true) {
        echo "File shared successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Get user ID by username
function getUserId($username)
{
    global $conn;
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row["id"];
    } else {
        return null;
    }
}

// Example usage

// Register a new user
registerUser("john_doe", "password123");

// Login a user
loginUser("john_doe", "password123");

// Upload a file for a user
uploadFile("file1.txt", "john_doe");

// Share a file with another user
shareFile(1, "jane_smith");

$conn->close();
?>
