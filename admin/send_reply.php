<?php

include '../components/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $recipientEmail = $_POST['recipient_email'];
    $replyMessage = $_POST['reply_message'];

    // Insert the reply into the database
    $stmt = $conn->prepare("INSERT INTO contacts_tb (name, email, message) VALUES (:name, :email, :message)");
    $stmt->bindParam(':name', $adminName);
    $stmt->bindParam(':email', $recipientEmail);
    $stmt->bindParam(':message', $replyMessage);
    $adminName = "Your Admin Name"; // Set your admin name here
    $stmt->execute();

    // Send email to recipient
    $to = $recipientEmail;
    $subject = "Reply to your message";
    $message = "Dear User,\n\nYou have received a reply to your message:\n\n" . $replyMessage . "\n\nBest regards,\nYour Admin";
    $headers = "From: Your Admin <admin@example.com>";

    if (mail($to, $subject, $message, $headers)) {
        // Email sent successfully
        // Redirect back to landholder-contact with success message
        header("Location: landholder-contact.php?success=1");
        exit();
    } else {
        // Email sending failed
        // Redirect back to landholder-contact with error message
        header("Location: landholder-contact.php?error=1");
        exit();
    }
} else {
    // If the form was not submitted via POST method, redirect back to landholder-contact
    header("Location: landholder-contact.php");
    exit();
}
?>
