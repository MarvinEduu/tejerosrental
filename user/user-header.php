<?php

@include 'components/connection.php';

// Assuming user is not logged in by default
//$isLoggedIn = false;
$isLoggedIn = isset($_SESSION['user_id']);



// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $isLoggedIn = true; // Set to true if user is logged in
    // Fetch user's profile picture from the database
    $select_profile = $conn->prepare("SELECT * FROM `users_tb` WHERE user_id = ?");
    $select_profile->execute([$user_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    // Set the profile picture path
    $profile_picture = 'uploaded_image/' . $fetch_profile['profile_picture']; // Path to user's profile picture
}
?>

<!-- Add modal component to the HTML -->
<div id="userModal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-screen-lg w-screen h-90 overflow-y-auto">
        <!-- Modal content -->
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold"><i class='bx bxs-user-detail mx-2 bx-md'></i> User Information</h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700 focus:outline-none"><i class='bx bx-exit bx-md'></i></button>
        </div>
        <div class="flex">
            <!-- Left side (Personal information) -->
            <div class="flex flex-col mr-12 ">
                <div style="max-width: 200px; max-height: 200px; overflow: hidden;" class="mb-4">
                    <img src="<?= $profile_picture ?>" alt="Profile Picture" class="rounded-md w-full h-full object-cover h-auto">
                </div>
                <p class="text-lg font-semibold mb-2"><?= $fetch_profile['full_name'] ?></p>
                <p><i class='bx bx-envelope bx-sm'></i> <?= $fetch_profile['email'] ?></p>
                <p><i class='bx bx-phone bx-sm'></i> <?= $fetch_profile['mobile'] ?></p>
                <p class="mt-4"><strong>Address:</strong> <?= $fetch_profile['address'] ?></p>
                <?php
                    // Calculate age based on date of birth
                    if (!empty($fetch_profile['date_of_birth'])) {
                        $birthDate = new DateTime($fetch_profile['date_of_birth']);
                        $today = new DateTime();
                        $age = $birthDate->diff($today)->y;
                        echo "<p><i class='bx bx-time bx-sm'></i> $age years old</p>";
                    }
                ?>
            </div>
            <!-- Right side (Social media links and bio) -->
            <div class="flex flex-col">
                <div class="flex items-center mb-4">
                    <i class="bx bxl-facebook-circle text-blue-600 mr-2"></i>
                    <?php if (!empty($fetch_profile['facebook'])) : ?>
                        <a href="<?= $fetch_profile['facebook'] ?>" target="_blank" class="text-blue-600 hover:underline">Facebook</a>
                    <?php else : ?>
                        <p class="text-gray-500">No social media link yet</p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center mb-4">
                    <i class="bx bxl-linkedin-square text-blue-800 mr-2"></i>
                    <?php if (!empty($fetch_profile['linkedin'])) : ?>
                        <a href="<?= $fetch_profile['linkedin'] ?>" target="_blank" class="text-blue-800 hover:underline">LinkedIn</a>
                    <?php else : ?>
                        <p class="text-gray-500">No social media link yet</p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center mb-4">
                    <i class="bx bxl-instagram-alt text-pink-600 mr-2"></i>
                    <?php if (!empty($fetch_profile['instagram'])) : ?>
                        <a href="<?= $fetch_profile['instagram'] ?>" target="_blank" class="text-pink-600 hover:underline">Instagram</a>
                    <?php else : ?>
                        <p class="text-gray-500">No social media link yet</p>
                    <?php endif; ?>
                </div>
                <!-- Bio -->
                <?php if (!empty($fetch_profile['bio'])) : ?>
                    <div class="mb-4">
    <p><strong>Bio:</strong></p>
    <p class="text-justify"><?= strlen($fetch_profile['bio']) > 300 ? substr($fetch_profile['bio'], 0, 300) . '...' : $fetch_profile['bio'] ?></p>
</div>

                <?php endif; ?>
                <!-- Account Created At -->
                <div class="flex items-center">
                    <i class="bx bx-time text-gray-600 mr-2"></i>
                    <p class="text-gray-600">Account created at <?= date('F j, Y', strtotime($fetch_profile['created_at'])) ?></p>
                </div>
            </div>
        </div>
        <!-- Update Profile button -->
        <a href="user/update-profile.php" class="btn btn-primary mt-6 w-full">Update Profile</a>
    </div>
</div>




<div class="navbar bg-white sticky top-0 z-50" >
    <div class="flex-1">
    <img src="images/logoer.png" alt="Tejeros Rental Logo" class="m-2 h-14 w-14">
        <a href="loading-page-in.php" class="btn btn-ghost text-lg">Tejeros Rental</a>
        <link rel="icon" type="image/x-icon" href="/images/logoer.png">
    </div>
    <div class="navbar-center hidden lg:flex flex-2">
        <ul class="menu menu-horizontal px-1">
            <li><a href="loading-page-in.php">Home</a></li>
            <li><a href="properties.php">Properties</a></li>
            <li><a href="seller-list.php">Landholders</a></li>
            <li>
                <details class="w-40 z-10">
                    <summary>Resources</summary>
                    <ul class="p-1 bg-white">
                        <!--<li><a>Loan Calculator</a></li>-->
                        <li><a href="announcements.php">Announcements</a></li>
                        <li><a href="guide.php">Property Guide</a></li>
                    </ul>
                </details>
            </li>
            <li>
                <details class="w-40 z-10">
                    <summary>Bookings</summary>
                    <ul class="p-1 bg-white">
                        <!--<li><a>Loan Calculator</a></li>-->
                        <li><a href="own-booking.php">Current Bookings</a></li>
                        <li><a href="previous-rents.php">Previous Rents</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </div>
    <div class="flex-none gap-6">
        <?php if ($isLoggedIn) : ?>
            <!-- Panel for logged-in users -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    
                    <div class="w-20 rounded-full">
                        <!-- Display user's profile picture if logged in -->
                        <img src="<?= $profile_picture ?>" alt="Profile Picture">
                    </div>
                </div>
                <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-white rounded-box w-52">
                    <li>
                        <a href="#" id="openModal" class="justify-between">
                            Profile
                        </a>
                    </li>
                    <li><a href="chat.php">Messages</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        <?php else : ?>
            <!-- Panel for users who have not logged in -->
            <div class="lg:hidden flex items-center" id="responsive-menu">
                <button class="block px-2 focus:outline-none" id="menu-toggle">
                    <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16m-7 6h7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="hidden lg:flex mx-6" id="menu-items">
                <a href="login.php" class="btn btn-primary mr-2">Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Menu -->
<div class="hidden lg:hidden" id="mobile-menu">
    <ul class="menu menu-compact bg-white p-2 rounded-box shadow">
        <li><a href="loading-page-in.php">Home</a></li>
        <li><a href="properties.php">Properties</a></li>
        <li><a href="seller-list.php">Sellers</a></li>
        <li>
            <details class="w-40 z-10">
                <summary>Resources</summary>
                <ul class="p-1">
                    <!--<li><a>Loan Calculator</a></li>-->
                    <li><a href="announcements.php">Announcements</a></li>
                    <li><a href="guide.php">Property Guide</a></li>
                </ul>
            </details>
        </li>
        <li><a href="own-booking.php">My Own Booking</a></li>
        <li class="lg:hidden flex justify-center mt-2">
            <a href="login.php" class="btn btn-primary">Login</a>
        </li>
    </ul>
</div>

<script>
    // Toggle the visibility of the responsive menu when clicking on the hamburger icon
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

<script>
    // JavaScript to toggle the modal
    const openModalButton = document.getElementById('openModal');
    const closeModalButton = document.getElementById('closeModal');
    const modal = document.getElementById('userModal');

    openModalButton.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    closeModalButton.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
</script>
