<?php
require 'config.php'; // Include your database connection file
session_start(); // Start the session

// Retrieve user_id from the session
$user_id = $_SESSION['user_id']; // Use the stored user_id

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve common payment details
    $address = $_POST['address'];
    $booking_day = $_POST['booking_day']; // Day of the booking
    $booking_date = $_POST['booking_date']; // Date of the booking
    $booking_time = $_POST['booking_time']; // Time of the booking
    $selected_plan = $_POST['selected_plan']; // Selected plan
    $service_name = isset($_POST['services']) ? implode(", ", $_POST['services']) : ''; // Ensure correct name for services array
    $payment_method = $_POST['payment_method']; // Determine which payment method was chosen

    // Validate common required fields
    if (empty($user_id) || empty($address) || empty($booking_day) || empty($booking_date) || empty($booking_time) || empty($selected_plan)) {
        echo "<p>All fields are required. Please go back and fill out the form again.</p>";
        exit();
    }

    // Prepare variables for payment data (UPI, debit/credit card fields)
    $upi_id = '';
    $name_on_debit_card = '';
    $debit_card_number = '';
    $debit_card_expiry = '';
    $debit_card_cvv = '';
    $name_on_credit_card = '';
    $credit_card_number = '';
    $credit_card_expiry = '';
    $credit_card_cvv = '';

    // Handle payment method selection and validation
    if ($payment_method == "Debit Card") {
        // Retrieve debit card details
        $name_on_debit_card = $_POST['name_on_debit_card'];
        $debit_card_number = $_POST['debit_card_number'];
        $debit_card_expiry = $_POST['debit_card_expiry'];
        $debit_card_cvv = $_POST['debit_card_cvv'];

        // Validate debit card details
        if (empty($name_on_debit_card) || empty($debit_card_number) || empty($debit_card_expiry) || empty($debit_card_cvv)) {
            echo "<p>All debit card fields are required. Please go back and fill out the form again.</p>";
            exit();
        }
    } elseif ($payment_method == "Credit Card") {
        // Retrieve credit card details
        $name_on_credit_card = $_POST['name_on_credit_card'];
        $credit_card_number = $_POST['credit_card_number'];
        $credit_card_expiry = $_POST['credit_card_expiry'];
        $credit_card_cvv = $_POST['credit_card_cvv'];

        // Validate credit card details
        if (empty($name_on_credit_card) || empty($credit_card_number) || empty($credit_card_expiry) || empty($credit_card_cvv)) {
            echo "<p>All credit card fields are required. Please go back and fill out the form again.</p>";
            exit();
        }
    } elseif ($payment_method == "UPI") {
        // Retrieve UPI ID
        $upi_id = $_POST['upi_id'];

        // Validate UPI ID
        if (empty($upi_id)) {
            echo "<p>UPI ID is required. Please go back and fill out the form again.</p>";
            exit();
        }
    } elseif ($payment_method == "QR Code") {
        // Handle QR Code payment logic if needed
        // For example, you may want to validate the QR Code (if applicable)
    } else {
        // Invalid payment method
        echo "<p>Invalid payment method selected. Please go back and try again.</p>";
        exit();
    }

    // Insert payment data into the users table
    $sql = "UPDATE users 
            SET address=?, booking_day=?, booking_date=?, booking_time=?, 
                selected_plan=?, service_name=?, payment_method=?, 
                upi_id=?, name_on_debit_card=?, debit_card_number=?, 
                debit_card_expiry=?, debit_card_cvv=?, 
                name_on_credit_card=?, credit_card_number=?, 
                credit_card_expiry=?, credit_card_cvv=?, created_at=NOW() 
            WHERE id=?";

    $stmt = $conn->prepare($sql);
    // Bind parameters (15 parameters)
    $stmt->bind_param("ssssssssssssssssi", $address, $booking_day, $booking_date, $booking_time, 
                      $selected_plan, $service_name, $payment_method, 
                      $upi_id, $name_on_debit_card, $debit_card_number, 
                      $debit_card_expiry, $debit_card_cvv, 
                      $name_on_credit_card, $credit_card_number, 
                      $credit_card_expiry, $credit_card_cvv, $user_id);

    if ($stmt->execute()) {
        echo "<p>Payment successful! Thank you for your booking.</p>";
        // Optionally, redirect the user to a confirmation page
        // header("Location: confirmation.php");
        // exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
