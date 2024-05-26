<?php

include '../components/connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];
    $action = $_POST['action'];

    try {
        // Fetch the propertyId of the booking to be accepted or canceled
        $stmt = $conn->prepare("SELECT propertyId FROM bookings_tb WHERE booking_id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $propertyId = $booking['propertyId'];

            if ($action === 'accept') {
                // Accept the selected booking
                $acceptStmt = $conn->prepare("UPDATE bookings_tb SET status = 'Accepted' WHERE booking_id = ?");
                $acceptStmt->execute([$bookingId]);

                // Cancel all other pending bookings for the same property
                $cancelStmt = $conn->prepare("UPDATE bookings_tb SET status = 'Cancelled' WHERE propertyId = ? AND booking_id != ? AND status = 'Pending'");
                $cancelStmt->execute([$propertyId, $bookingId]);
                
                $_SESSION['message'] = 'Booking accepted and other pending bookings for the same property have been cancelled.';
            } elseif ($action === 'cancel') {
                // Cancel the selected booking
                $cancelStmt = $conn->prepare("UPDATE bookings_tb SET status = 'Cancelled' WHERE booking_id = ?");
                $cancelStmt->execute([$bookingId]);

                $_SESSION['message'] = 'Booking has been cancelled.';
            }
        } else {
            $_SESSION['error'] = 'Booking not found.';
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Redirect to the pending bookings page
    header('Location: landholder-pendings.php');
    exit;
} else {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: landholder-pendings.php');
    exit;
}
?>
