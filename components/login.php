<?php
include 'components/connection.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration Form</title>
    <link rel="icon" type="image/x-icon" href="images/logoer.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <style>
        /* Custom CSS for logo and text */
        #logo-text {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            /* Remove underline */
            color: black;
            /* Set text color to black */
        }

        #logo-text img {
            max-width: 50px;
            height: auto;
            margin-right: 10px;
        }
        .modal-overlay {
  pointer-events: none;
}
.input-group-append {
  z-index: 1;
}

    </style>

</head>

<body class="bg-gray-100 flex items-center justify-center h-screen" style="background-color: var(--secondary-color);">
<?php

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_user'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users_tb (full_name, username, email, mobile, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $username, $email, $mobile, $password]);

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
                title: "User Registration Done",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "login.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
      exit;
}

// Handle user login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users_tb WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role']; // Store the user's role in the session

        if ($_SESSION['role'] == 1) {
            // Redirect admin to admin-home.php
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
                title: "Admin Login Successful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "admin/admin-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
            exit;
        } elseif ($_SESSION['role'] == 0) {
            // Redirect regular users to user-home.php
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
                title: "User Login Successful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "user-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';

            exit;
        } else {
            // Handle unknown role
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
                icon: "error",
                title: "User Login Unsuccessful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "login.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
            exit;
        }
    } else {
        // User not found or password incorrect
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
                icon: "error",
                title: "User Login Unsuccessful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "login.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
        exit;
    }
}





// Handle landholder registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_landholder'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO landholders_tb (full_name, username, email, mobile, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $username, $email, $mobile, $password]);

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
                title: "User Registration Done",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "login.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
      exit;
}

// Handle landholder login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_landholder'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM landholders_tb WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $landholder = $stmt->fetch();

    if ($landholder && password_verify($password, $landholder['password'])) {
        $_SESSION['landholder_id'] = $landholder['landholder_id'];
        // Redirect admin to admin-home.php
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
                title: "Landholder Login Successful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "landholders/landholder-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
      exit;
    } else {
        // Landholder not found or password incorrect
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
                icon: "error",
                title: "Landholder Login Unsuccessful",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "landholders/landholder-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
      exit;
    }
}
?>





    <!-- Logo and Text -->
    <a href="loading-page-in.php" id="logo-text">
        <!-- Logo -->
        <img src="images/logoer.png" alt="Logo">
        <!-- Text -->
        <h1 class="text-lg font-bold">Tejero Rentals</h1>
    </a>
    <div class="container max-w-4xl mx-auto p-8 rounded-lg shadow-lg" style="background-color: var(--white-color); color: var(--dark-color)">
        <div class="grid grid-cols-2 gap-4">
            <!-- User Login and Registration -->
            <div class=" border-r-2 p-8">
                <h2 class="font-bold text-lg mb-4">Login to see tailored and detailed properties.</h2>
                <form action="login.php" method="post" autocomplete="on">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                        <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="username" name="username">
                    </div>
                    <div class="mb-6">
                        <label for="password">Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="input-group-append">
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="submit" name="login_user" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</button>
                        <a href="forgot-password/forgot_password.php" class="text-sm text-blue-500 hover:text-blue-700">Forgot Password?</a>
                    </div>
                </form>
                <p class="text-sm">Don't have account? <button type="button" class="mt-4 text-blue-500 hover:text-blue-700 text-sm" data-toggle="modal" data-target="#registerModal">Register Here.</button></p>
            </div>

            <!-- Landholder Registration -->
            <div class="p-4">
                <h2 class="font-bold text-lg mb-4">Got properties available for rent? Consider listing your properties here.</h2>
                <p class="mb-4">Maximize your property's visibility by listing it on our website, reaching a large audience of potential renters. <a href="#" class="text-blue-500 hover:text-blue-700" data-toggle="modal" data-target="#landholderModal">Signup as landholder. </a></p>
                <p class="mb-4">Already have an account? <a href="#" class="text-blue-500 hover:text-blue-700" data-toggle="modal" data-target="#landholderLoginModal">Log in here. </a></p>
            </div>
        </div>
    </div>

    <!-- User Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
        <!-- Modal content here -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">User Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" autocomplete="on" id="registrationForm">
                        <div class="form-group">
                            <label for="fullname">Full Name:</label>
                            <input type="text" class="form-control" id="full_name" name="fullname" placeholder="e.g. Juan Basilyo Delacruz" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="e.g. Don Juan" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="e.g. Juan.Delacruz@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile:</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="e.g. 09887654321" required>
                        </div>
                        <div class="form-group">
                            <label for="reg_password">Password:</label>
                            <input type="password" class="form-control" id="reg_password" name="password" required>
                        </div>
                        <button type="submit" name="register_user" class="btn btn-primary">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!-- Landholder Registration Modal -->
    <div class="modal fade" id="landholderModal" tabindex="-1" role="dialog" aria-labelledby="landholderModalLabel" aria-hidden="true">
        <!-- Modal content here -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="landholderModalLabel">Landholder Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" autocomplete="on">
                        <div class="form-group">
                            <label for="seller_fullname">Full Name:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="e.g. Juan Basilyo Delacruz">
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="e.g. Don Juan">
                        </div>
                        <div class="form-group">
                            <label for="seller_email">Email:</label>
                            <input type="email" class="form-control" id="landholder_email" name="email" placeholder="e.g. Juan.Delacruz@gmail.com">
                        </div>
                        <div class="form-group">
                            <label for="seller_mobile">Mobile:</label>
                            <input type="text" class="form-control" id="landholder_mobile" name="mobile" placeholder="e.g. 09887654321">
                        </div>
                        <form id="registrationForm">
                        <div class="form-group">
                            <label for="seller_password">Password:</label>
                            <input type="password" class="form-control" id="landholder_password" name="password">
                        </div>
                        <button type="submit" name="register_landholder" class="btn btn-primary">Register</button>
                        </form>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Landholder Login Modal -->
    <div class="modal fade" id="landholderLoginModal" tabindex="-1" role="dialog" aria-labelledby="landholderLoginModalLabel" aria-hidden="true">
        <!-- Modal content here -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="landholderLoginModalLabel">Landholder Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="post" autocomplete="on">
                        <div class="form-group">
                            <label for="landholder_username">Username:</label>
                            <input type="text" class="form-control" id="landholder_username" name="username" placeholder="Enter your username">
                        </div>
                        <div class="form-group">
                            <label for="landholder_password">Password:</label>
                            <div class="input-group">
                            <input type="password" class="form-control" id="landholder_password" name="password">
                            <div class="input-group-append" data-dismiss="modal">
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="eye"></i>
                                </span>
                            </div>
                        </div>
                        </div>
                        <button type="submit" name="login_landholder" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = data.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        
        function togglePassword(id) {
  var x = document.getElementById(id);
  var eye = x.nextElementSibling.querySelector('i');
  if (x.type === "password") {
    x.type = "text";
    eye.classList.add("fa-eye-slash");
  } else {
    x.type = "password";
    eye.classList.remove("fa-eye-slash");
  }
  event.stopPropagation(); // Add this line
}

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('form').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        if (password.length < 8) {
            alert('Password must be at least 8 characters long.');
            event.preventDefault(); // Prevent form submission
        } else if (!containsSpecialCharacter(password)) {
            alert('Password must contain at least one special character.');
            event.preventDefault(); // Prevent form submission
        }
    });
});

function containsSpecialCharacter(password) {
    const specialCharacters = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/;
    return specialCharacters.test(password);
}

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        const password = document.getElementById('reg_password').value;
        if (password.length < 8) {
            alert('Password must be at least 8 characters long.');
            event.preventDefault(); // Prevent form submission
        } else if (!containsSpecialCharacter(password)) {
            alert('Password must contain at least one special character.');
            event.preventDefault(); // Prevent form submission
        }
    });
});

function containsSpecialCharacter(password) {
    const specialCharacters = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/;
    return specialCharacters.test(password);
}

    </script>


</body>

</html>

