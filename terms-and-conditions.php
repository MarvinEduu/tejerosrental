<?php
@include 'components/connection.php';

session_start();
?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/logoer.png">

    <!-- External CSS and JavaScript libraries -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <style>
        /* Custom styles */
.overlay-container {
    position: relative;
    width: 100%;
    height: 50vh;
    overflow: hidden;
}

.overlay-container img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.overlay {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-size: 24px;
    font-weight: bold;
    padding: 20px;
    text-align: center;
}

@media (min-width: 768px) {
    .overlay {
        font-size: 32px;
    }
}

/* Additional responsive styles */
.container {
    padding: 0 8px;
}

@media (min-width: 640px) {
    .container {
        max-width: 640px;
        margin-left: auto;
        margin-right: auto;
    }
}

@media (min-width: 768px) {
    .container {
        max-width: 768px;
    }
}

@media (min-width: 1024px) {
    .container {
        max-width: 1024px;
    }
}

    </style>
</head>

<body class="bg-gray-100">

    <?php include 'user/user-header.php' ?>

    <!-- Landscape Photo with Overlay Container -->
    <div class="relative">
        <div class="overlay-container">
            <img src="images/tejeros.jpg" alt="Tejeros Landscape">
            <div class="overlay">
                Terms and Conditions
            </div>
        </div>
    </div>
    <div data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
    <!-- Terms and Conditions Content Container -->
    <div class="container mx-auto px-4 lg:px-48 py-8 text-justify relative z-10">
        <div class="bg-white p-4 lg:p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl lg:text-3xl font-bold mb-6 text-center">Terms and Conditions</h1>
            <!-- Your existing content with appropriate styling -->

            <p class="mb-6">
                Welcome to our website. If you continue to browse and use this website, you are agreeing to comply
                with and be bound by the following terms and conditions of use, which together with our privacy policy
                govern our relationship with you in relation to this website. If you disagree with any part of these
                terms and conditions, please do not use our website.
            </p>

            <h2 class="text-xl font-bold mb-4">1. Definitions</h2>
            <p class="mb-4">
                In these Terms and Conditions, "we", "us", "our" refers to the company, and "you", "your", "user"
                refers to the user or viewer of our website.
            </p>

            <h2 class="text-xl font-bold mb-4">2. Use of the Website</h2>
            <p class="mb-4">
                By accessing the website, you warrant and represent to the website owner that you are legally entitled
                to do so and to make use of information made available via the website.
            </p>

            <h2 class="text-xl font-bold mb-4">3. User Account</h2>
            <p class="mb-4">
                To access certain features of the website, you may have to register for an account. You are responsible
                for maintaining the confidentiality of your account password and for all activities that occur under
                your account.
            </p>

            <h2 class="text-xl font-bold mb-4">4. Chat System</h2>
            <p class="mb-4">
                Our website provides a chat system for communication between users and landholders. You agree to use
                the chat system only to send and receive messages that are proper and related to the particular
                communication service.
            </p>

            <h2 class="text-xl font-bold mb-4">5. Mapping and Property Details</h2>
            <p class="mb-4">
                We provide mapping and detailed property information for your convenience. While we strive to keep
                this information accurate and up-to-date, we cannot guarantee its absolute accuracy. You agree to use
                this information at your own risk.
            </p>

            <h2 class="text-xl font-bold mb-4">6. Landholder Profile</h2>
            <p class="mb-4">
                Landholder profiles are created by the landholders themselves. We do not verify the information
                provided in these profiles and are not responsible for any inaccuracies.
            </p>

            <h2 class="text-xl font-bold mb-4">7. Limitation of Liability</h2>
            <p class="mb-4">
                In no event will we be liable for any direct, indirect, special, punitive, exemplary or consequential
                losses or damages of whatsoever kind arising out of your use or access to the website.
            </p>

            <h2 class="text-xl font-bold mb-4">8. Governing Law</h2>
            <p class="mb-4">
                These Terms and Conditions shall be governed by and construed in accordance with the law of Tejeros
                Convention, Cavite and you hereby submit to the exclusive jurisdiction of the Tejeros Convention,
                Cavite courts.
            </p>

            <h2 class="text-xl font-bold mb-4">9. Changes to Terms and Conditions</h2>
            <p class="mb-4">
                We reserve the right to change these Terms and Conditions at any time. It is your responsibility to
                check regularly to determine whether the Terms and Conditions have been changed. If you do not agree
                to the changes, you should cease using the website.
            </p>

            <h2 class="text-xl font-bold mb-4">10. Acceptance of these terms</h2>
            <p class="mb-4">
                By using this website, you signify your acceptance of these Terms and Conditions. If you do not agree
                to this policy, please do not use our website.
            </p>

            <h2 class="text-xl font-bold mb-4">Contact Us</h2>
            <p>
                If you have any questions about these Terms and Conditions, please contact us.
            </p>
        </div>
        </div>
    </div>

    <!-- Initialize AOS library -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out-quart'
        });
    </script>

    <?php include 'user/user-footer.php' ?>
</body>

</html>
