<?php
session_start();
require 'config.php'; // Include the database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Get the email input from the form
    $password = $_POST['password']; // Get the password input from the form

    // Prepare and execute SQL query to check if the user exists in the database by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Bind the email to the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with the provided email exists
    if ($result->num_rows == 1) {
        // Fetch the user's data from the database
        $user = $result->fetch_assoc();

        // Verify the provided password against the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // If password is correct, set session variables
            $_SESSION['user_id'] = $user['id']; // Store the user's ID in the session
            $_SESSION['user_email'] = $user['email']; // Store the user's email in the session

            // Redirect the user to the dashboard or main page
            header("Location: login1.php");
            exit(); // Stop script execution after redirecting the user
        } else {
            // If the password is incorrect, redirect back to index.html with an error message
            $_SESSION['error'] = "Incorrect password. Please try again.";
            header("Location: login.php"); // Redirect back to the login page
            exit(); // Stop script execution
        }
    } else {
        // If the user does not exist in the database, redirect back to index.html with an error message
        $_SESSION['error'] = "Email not found. Please try again.";
        header("Location: login.php"); // Redirect back to the login page
        exit(); // Stop script execution
    }
}
?>
