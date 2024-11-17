<?php
// Fetch the latest user details from the database and return them as JSON
session_start();
require 'config.php';

// Query to get the latest entry by assuming 'id' is the primary key
// You can also use a 'created_at' column if available
$sql = "SELECT username, email, phone FROM users ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user); // Return the latest user details as JSON
} else {
    echo json_encode(['error' => 'No users found']);
}
?>
