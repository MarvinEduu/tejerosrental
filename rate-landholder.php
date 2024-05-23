<?php
include 'components/connection.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit;
}

// Get data from form submission
$rating = $_POST['rating'];
$comment = $_POST['comment'];
$landholderId = $_POST['landholderId'];
$bookingId = $_POST['bookingId']; // Correctly passing the bookingId
$userId = $_SESSION['user_id'];

try {
    // Insert rating into database
    $insertRating = $conn->prepare("
        INSERT INTO user_ratings (user_id, landholder_id, booking_id, rating, comment, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $insertRating->execute([$userId, $landholderId, $bookingId, $rating, $comment]);

    // Redirect back to the previous page after rating is submitted
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
} catch (Exception $e) {
    // Handle error if rating insertion fails
    echo "Error: " . $e->getMessage();
    exit;
}
?>
