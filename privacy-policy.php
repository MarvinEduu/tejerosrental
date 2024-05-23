<?php
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

.policy-section {
    display: grid;
    grid-template-columns: 1fr; /* Single column by default */
    gap: 20px;
}

@media (min-width: 768px) {
    .overlay {
        font-size: 32px; /* Larger font size for desktop */
    }

    .policy-section {
        grid-template-columns: repeat(2, 1fr); /* Two columns on larger screens */
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
                Privacy Policy
            </div>
        </div>
    </div>

    <!-- Privacy Policy Content Container -->
    <div data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
    <div class="container mx-auto px-4 lg:px-48 py-8 text-justify relative z-10">
        <div class="bg-white p-4 lg:p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl lg:text-3xl font-bold mb-6 text-center">Privacy Policy</h1>
            <!-- Your existing content with appropriate styling -->
            <p class="mb-6">
                Welcome to Tejeros Rental. This privacy policy sets out how we use and protect any information that
                you provide when using this website. This Privacy Policy governs the manner in which our website collects,
                uses, maintains, and discloses information collected from users (each, a "User") of the website. At our rental
                property website targeting Tejeros Convention, Cavite, we are committed to protecting the privacy and security
                of our users. This Privacy Policy outlines how we collect, use, and safeguard the personal information you
                provide to us.
            </p>

            <!-- Information We Collect Section -->
            <h2 class="text-xl font-bold mb-4">Information We Collect</h2>
            <div class="policy-section">
                <div>
                    <h3 class="text-md font-bold mb-2">1. User Accounts:</h3>
                    <p class="mb-4">
                        If you choose to create a user account on our website, we will collect your name, email address,
                        and any other information you provide during the registration process.
                    </p>
                    <h3 class="text-md font-bold mb-2">2. Chat System:</h3>
                    <p class="mb-4">
                        When you use our chat system to communicate with landholders, we may collect the content of your
                        messages and any other information you choose to share.
                    </p>
                </div>
                <div>
                    <h3 class="text-md font-bold mb-2">3. Property Viewing:</h3>
                    <p class="mb-4">
                        When you view property listings on our website, we may collect information about your browsing
                        activity, such as the properties you view, the time spent on each page, and any searches you perform.
                    </p>
                    <h3 class="text-md font-bold mb-2">4. Landholder Profiles:</h3>
                    <p class="mb-4">
                        The information displayed in landholder profiles is provided by the landholders themselves. We do not
                        verify or take responsibility for the accuracy of this information.
                    </p>
                </div>
            </div>

            <!-- How We Use Your Information Section -->
            <h2 class="text-xl font-bold mb-4">How We Use Your Information</h2>
            <div class="policy-section">
                <div>
                    <h3 class="text-md font-bold mb-2">1. User Accounts:</h3>
                    <p class="mb-4">
                        We use the information collected during the registration process to create and manage your user
                        account, and to provide you with personalized services and updates.
                    </p>
                    <h3 class="text-md font-bold mb-2">2. Chat System:</h3>
                    <p class="mb-4">
                        We use the chat system data to facilitate communication between users and landholders, and to
                        monitor for any inappropriate or abusive content.
                    </p>
                </div>
                <div>
                    <h3 class="text-md font-bold mb-2">3. Property Viewing:</h3>
                    <p class="mb-4">
                        We use the information about your property viewing activity to improve our website's functionality,
                        enhance the user experience, and provide you with relevant property recommendations.
                    </p>
                    <h3 class="text-md font-bold mb-2">4. Landholder Profiles:</h3>
                    <p class="mb-4">
                        We display the landholder profile information to help users make informed decisions about the
                        properties they are interested in.
                    </p>
                </div>
            </div>

            <!-- Data Security Section -->
            <h2 class="text-xl font-bold mb-4">Data Security</h2>
            <p class="mb-4">
                We take reasonable measures to protect the personal information you provide to us from loss, misuse,
                unauthorized access, disclosure, alteration, and destruction. However, no method of transmission over
                the internet or method of electronic storage is 100% secure, and we cannot guarantee the absolute
                security of your data.
            </p>

            <!-- Third-Party Sharing Section -->
            <h2 class="text-xl font-bold mb-4">Third-Party Sharing</h2>
            <p class="mb-4">
                We do not sell, trade, or otherwise transfer your personal information to third parties, except in the
                following circumstances:
                <br>- When required by law or to comply with a legal process
                <br>- To protect the rights, property, or safety of our company, our users, or the public
                <br>- With service providers who assist us in operating the website and providing our services
            </p>

            <!-- Changes to this Privacy Policy Section -->
            <h2 class="text-xl font-bold mb-4">Changes to this Privacy Policy</h2>
            <p class="mb-4">
                We reserve the right to update or modify this Privacy Policy at any time. If we make changes, we will
                post the updated policy on our website and indicate the date of the last revision. Your continued use
                of our website after any changes constitutes your acceptance of the new Privacy Policy.
            </p>

            <!-- Contact Us Section -->
            <h2 class="text-xl font-bold mb-4">Contact Us</h2>
            <p>
                If you have any questions about this Privacy Policy, please contact us.
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
