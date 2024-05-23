<?php
include("components/connection.php");

$email = $_POST['value']; // Value of the email input field

// Check if email exists in the database
$sql = "SELECT * FROM landholders_tb WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);
$landholder = $stmt->fetch();

if ($landholder) {
    // Email already exists, return false
    echo json_encode(false);
} else {
    // Email does not exist, return true
    echo json_encode(true);
}
?>
