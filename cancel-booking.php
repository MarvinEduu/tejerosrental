<?php
include 'components/connection.php';

// Check if the form is submitted and the booking ID is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    // Get the booking ID from the form
    $bookingId = $_POST['booking_id'];

    try {
        // Delete the booking from the database
        $deleteBooking = $conn->prepare("DELETE FROM bookings_tb WHERE booking_id = ?");
        $deleteBooking->execute([$bookingId]);

        // Redirect the user back to the bookings page after cancellation
        header('Location: own-booking.php');
        exit;
    } catch (Exception $e) {
        // Handle any errors if the cancellation fails
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // If the form is not submitted or booking ID is not set, redirect to an error page or handle it as necessary
    header('Location: error.php');
    exit;
}
?>
