<?php
include '../components/connection.php'; // Include your database connection file

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $landholderId = $_POST['landholder_id']; // Get landholder ID from form input
    $action = $_POST['action']; // Get action (verified or invalid)

    if ($action == 'verified') {
        // Update permit_status to 'Verified' and verification_tier to 'Fully Verified'
        $query = "UPDATE landholders_tb SET permit_status = 'Verified', verification_tier = 'Fully Verified' WHERE landholder_id = :landholder_id";
    } elseif ($action == 'invalid') {
        // Update permit_status to 'Invalid' and keep verification_tier unchanged
        $query = "UPDATE landholders_tb SET permit_status = 'Invalid' WHERE landholder_id = :landholder_id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":landholder_id", $landholderId);
    $stmt->execute();

    header("Location: admin-landholder-list.php");
    exit;
} else {
    header("Location: admin-landholder-list.php");
    exit;
}
?>
