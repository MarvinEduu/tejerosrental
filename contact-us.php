<?php
// Include database connection
@include 'components/connection.php';

session_start();
?>



<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/logoer.png">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Custom background image style */
        .contact-section {
            background-image: url('images/wave2.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
</head>

<body class="bg-gray-100 contact-section">
    <?php include 'user/user-header.php' ?>

    <?php
// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    try {
        // Check if the user has sent a message within the last 7 days
        $sql = "SELECT created_at FROM contacts_tb WHERE email = :email ORDER BY created_at DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $lastMessageDate = $stmt->fetchColumn();

        if ($lastMessageDate && strtotime($lastMessageDate) > strtotime('-7 days')) {
            // User has sent a message within the last 7 days
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
                title: "You can only send 1 message every 7 days",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "user-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
        exit;
        } else {
            // Prepare SQL to insert into contacts_tb using PDO
            $sql = "INSERT INTO contacts_tb (name, email, message, created_at) VALUES (:name, :email, :message, NOW())";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            // Execute the query
            if ($stmt->execute()) {
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
                title: "Message sent successfully",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "user-home.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
        exit;
            } else {
                echo "Error executing query.";
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close prepared statement and database connection
    $stmt = null;
    $conn = null;
}


?>



    <!-- Contact Form Section -->
    <div class="container mx-auto px-4 py-8">
        <div data-aos="fade-down" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
            <h1 class="text-3xl font-bold mb-6 text-center text-white">Contact Us</h1>

            <!-- Contact Form -->
            <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
                <form id="contactForm" action="" method="POST">
                    <!-- Name Input -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                        <input type="text" id="name" name="name" class="mt-1 p-2 w-full border border-gray-300 rounded-md">
                    </div>

                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                        <input type="email" id="email" name="email" class="mt-1 p-2 w-full border border-gray-300 rounded-md">
                    </div>

                    <!-- Message Input -->
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="5" class="mt-1 p-2 w-full border border-gray-300 rounded-md"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded-md">
    Send Message
</button>


                    <!-- Message Status Placeholder -->
                    <div id="messageStatus" class="mt-4 text-center"></div>
                </form>
            </div>
        </div>
    </div>


    <!-- Include Tailwind CSS and any other scripts at the end -->
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>
    <?php include 'user/user-footer.php' ?>

    <!-- JavaScript for Message Submission Status -->
    <script>
        function sendMessage() {
            // Get form data
            var formData = new FormData(document.getElementById('contactForm'));

            // Send form data using Fetch API
            fetch('send-email.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Update message status
                    document.getElementById('messageStatus').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

    <script>
        AOS.init({
            duration: 1000, // values from 300 to 3000, with step 50ms
            easing: 'ease-in-out-quart', // default easing for AOS animations
        });
    </script>
</body>

</html>