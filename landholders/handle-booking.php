<?php
include '../components/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];
    $action = $_POST['action'];

    try {
        if ($action === 'accept') {
            // Update booking status to 'Accepted'
            $updateBooking = $conn->prepare("UPDATE bookings_tb SET status = 'Accepted' WHERE booking_id = ?");
            $updateBooking->execute([$bookingId]);
        } elseif ($action === 'cancel') {
            // Update booking status to 'Cancelled'
            $updateBooking = $conn->prepare("UPDATE bookings_tb SET status = 'Cancelled' WHERE booking_id = ?");
            $updateBooking->execute([$bookingId]);
        }

        // Redirect back to the pending bookings page after handling the action
        header('Location: landholder-pendings.php');
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Redirect to an error page if accessed directly
    header('Location: error.php');
    exit;
}
?>
