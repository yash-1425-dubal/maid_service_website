<?php
require 'config.php'; // Include your database connection file
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password != $confirm_password) {
        echo "<p>Passwords do not match. Try again.</p>";
        exit();
    }

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p>Email already registered. Please log in.</p>";
    } else {
        // Hash password and store new user in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            // Store the user_id in the session
            $_SESSION['user_id'] = $conn->insert_id; // Get the last inserted ID
            echo "<p>Sign up successful. <a href='index.php'>Login now</a></p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
    }
}
?>
