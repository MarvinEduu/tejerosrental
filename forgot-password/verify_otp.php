<?php
session_start();

// Include database connection script
include("../components/connection.php");

// Ensure email is provided in the URL
if (!isset($_GET['email']) || empty($_GET['email'])) {
    header('Location: forgot_password.php');
    exit;
}

// Retrieve and sanitize email from URL parameter
$email = urldecode($_GET['email']);

// Handle OTP verification upon form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    // Validate OTP entered by the user
    $userEnteredOTP = isset($_POST['otp']) ? (int)$_POST['otp'] : 0;
    $storedOTP = isset($_SESSION['otp']) ? $_SESSION['otp'] : 0;

    if ($userEnteredOTP > 0 && $userEnteredOTP === $storedOTP) {
        // OTP is verified, redirect to reset_password.php
        header('Location: reset_password.php?email=' . urlencode($email));
        exit;
    } else {
        // Invalid OTP entered
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DaisyUI CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('../images/wave2.svg'); /* Add your background image here */
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-4">Verify OTP</h1>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <form action="verify_otp.php?email=<?php echo urlencode($email); ?>" method="post">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="mb-4">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" id="otp" name="otp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-full" name="verify_otp">Verify OTP</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0-alpha1/js/bootstrap.min.js"></script>
</body>
</html>


