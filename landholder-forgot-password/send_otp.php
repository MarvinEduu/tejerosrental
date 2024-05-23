<?php
session_start();
$_SESSION['otp'] = null;
$_SESSION['reset_email'] = null;

include("../components/connection.php"); // Include database connection script

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Path to autoload.php from PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if email exists in users_tb
    $sql = "SELECT * FROM landholders_tb WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Email exists, proceed to send OTP
        $otp = rand(100000, 999999);

        $subject = 'Password Reset OTP';
        $message = 'Your OTP for password reset is: ' . $otp;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noel.marvin26@gmail.com'; // Your Gmail address
            $mail->Password = 'rlfgrnohtjybuija'; // Your Gmail password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('noel.marvin26@gmail.com', 'Tejeros Rental');
            $mail->addAddress($email);

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();

            $_SESSION['otp'] = $otp;
            $_SESSION['reset_email'] = $email;

            header('Location: verify_otp.php?email=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            echo 'Failed to send OTP. Error: ' . $mail->ErrorInfo;
        }
    } else {
        echo '<script>alert("Email not found. Please enter a valid email address.");';
        echo 'window.location.href = "forgot_password.php";</script>';
        exit;
    }

    $conn = null; // Close the connection
} else {
    echo 'Invalid request.';
}
?>
