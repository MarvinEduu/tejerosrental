<?php
session_start();
$_SESSION['otp']; $_SESSION['reset_email'];
include("../components/connection.php"); // Include database connection script

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize session (if not already started)


require '../vendor/autoload.php'; // Path to autoload.php from PHPMailer

// Check if form is submitted with email field
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in users_tb
    $sql = "SELECT * FROM users_tb WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Email exists, proceed to send OTP
        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Send OTP to the user's email using PHPMailer
        $subject = 'Password Reset OTP';
        $message = 'Your OTP for password reset is: ' . $otp;

        // Initialize PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noel.marvin26@gmail.com'; // Your Gmail address
            $mail->Password = 'rlfgrnohtjybuija'; // Your Gmail password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Sender and recipient settings
            $mail->setFrom('noel.marvin26@gmail.com', 'Tejeros Rental');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Send email
            $mail->send();

            // Store the OTP in the session for verification
            $_SESSION['otp'] = $otp;

            // Redirect the user to the OTP verification page
            header('Location: verify_otp.php?email=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            echo 'Failed to send OTP. Error: ' . $mail->ErrorInfo;
        }
    } else {
        // Email does not exist in users_tb
        echo '<script>alert("Email not found. Please enter a valid email address.");';
        echo 'window.location.href = "forgot_password.php";</script>';
        exit;
    }

    // Close database connection
    $conn = null; // Close the connection
} else {
    // Handle case where form is not submitted properly
    echo 'Invalid request.';
}
?>
