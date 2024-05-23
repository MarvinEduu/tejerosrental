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

// Initialize error variable
$error = '';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DaisyUI CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-image: url('../images/wave2.svg');
            /* Add your background image here */
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
    <h2 class="text-2xl font-bold mb-4 text-center">Reset Your Password</h2>
    <?php
    // Handle password reset upon form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password and confirm password
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match. Please try again.';
    } else {
        // Validate password strength   
        // Validate password strength   
if (strlen($password) < 8 || !preg_match('/^(?=.*[a-zA-Z])(?=.*[\d!@#$%^&*()_+{}\[\]:;<>,.?\/\\-])[A-Za-z\d!@#$%^&*()_+{}\[\]:;<>,.?\/\\-]{8,}$/', $password)) {
    $error = 'Password must be at least 8 characters long and contain at least one special character.';
}
 else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update the user's password in the database
            $sql = "UPDATE landholders_tb SET password = :password WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                // Password updated successfully
                unset($_SESSION['otp']); // Clear the OTP session variable
                echo '<script>
        // Show loading animation
        Swal.fire({
            title: "Loading...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Delay the actual popup
        setTimeout(() => {
            Swal.fire({
                icon: "success",
                title: "Password Changed Success!",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "../login.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
                exit;
            } else {
                $error = 'Failed to reset password. Please try again.';
            }
        }
    }
}
?>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php } ?>
    <form method="post">
        <div class="mb-4">
            <label for="password" class="form-label">New Password:</label>
            <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" required pattern="^(?=.*[a-zA-Z])(?=.*[\d!@#$%^&*()_+{}\[\]:;<>,.?\/\\-])[A-Za-z\d!@#$%^&*()_+{}\[\]:;<>,.?\/\\-]{8,}$">


                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <div id="passwordHelpBlock" class="form-text">
                Password must be at least 8 characters long and contain at least one special character.
            </div>
        </div>
        <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-full">Reset Password</button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0-alpha1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Show/hide password functionality
        $('#togglePassword').click(function() {
            var passwordInput = $('#password');
            var passwordInputType = passwordInput.attr('type');
            if (passwordInputType === 'password') {
                passwordInput.attr('type', 'text');
                $(this).html('<i class="fas fa-eye"></i>');
            } else {
                passwordInput.attr('type', 'password');
                $(this).html('<i class="fas fa-eye-slash"></i>');
            }
        });
    });
</script>
</body>

</html>
