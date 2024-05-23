<?php
include '../components/connection.php';

session_start();

// Check if the user is logged in as a landholder
if (!isset($_SESSION['landholder_id'])) {
    header('Location: login.php');
    exit;
}

$landholderId = $_SESSION['landholder_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingId = $_POST['booking_id'];
    $conditionCheck = isset($_POST['conditionCheck']);
    $paymentsCheck = isset($_POST['paymentsCheck']);
    $keysCheck = isset($_POST['keysCheck']);
    $noticeCheck = isset($_POST['noticeCheck']);

    try {
        // Fetch the user_id associated with the booking
        $query = "SELECT user_id FROM bookings_tb WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $userId = $booking['user_id'];

            // Update the booking status to 'Ended'
            $query = "UPDATE bookings_tb SET status = 'Ended' WHERE booking_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$bookingId]);

            // Send a success response
            echo json_encode(['success' => true, 'booking_id' => $bookingId]);
            exit;
        } else {
            throw new Exception('Booking not found.');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
