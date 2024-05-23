<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or display message
    header("Location: login.php");
    exit;
}

// Include database connection file
include 'components/connection.php';

// Get form data
$user_id = $_SESSION['user_id'];
$propertyId = $_POST['propertyId'];
$check_in_date = $_POST['checkInDate'];
$stay_duration_months = $_POST['stayDuration'];

// Calculate check-out date based on stay duration
$checkInDate = new DateTime($check_in_date);
$checkOutDate = clone $checkInDate;
$checkOutDate->add(new DateInterval("P{$stay_duration_months}M"));
$check_out_date = $checkOutDate->format('Y-m-d');

// Fetch property details (including landholder_id)
$stmt = $conn->prepare("SELECT * FROM properties_tb WHERE property_id = ?");
$stmt->execute([$propertyId]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

$rentAmount = $property['rentAmount'];
$total_amount = $rentAmount * $stay_duration_months;

// Prepare and execute INSERT statement for bookings_tb
$stmt = $conn->prepare("INSERT INTO bookings_tb (user_id, property_id, landholder_id, check_in_date, check_out_date, stay_duration_months, total_amount, payment_method, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, 'Gcash', 'Pending', NULL)");
$stmt->execute([$user_id, $property_id, $property['landholder_id'], $check_in_date, $check_out_date, $stay_duration_months, $total_amount]);

// Close the database connection
$conn = null;

// Redirect to a confirmation page or display success message
header("Location: booking_confirmation.php");
exit;
?>
