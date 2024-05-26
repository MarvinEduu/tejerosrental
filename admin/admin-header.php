<?php
@include '../components/connection.php';

// Check if landholder is not logged in or does not have role 1
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    // Redirect to login page
    header("Location: ../login.php");
    exit; // Stop further execution
}


// If landholder is logged in, fetch user data
$user_id = $_SESSION['user_id'];

// Query to fetch user data based on landholder_id
$query = "SELECT username, profile_picture, full_name, email, mobile, address, facebook, linkedin, instagram, bio, created_at FROM users_tb WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $_SESSION['username'] = $row['username'];
    $_SESSION['profile_picture'] = $row['profile_picture'];
    $_SESSION['full_name'] = $row['full_name'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['mobile'] = $row['mobile'];
    $_SESSION['address'] = $row['address'];
    $_SESSION['facebook'] = $row['facebook'];
    $_SESSION['linkedin'] = $row['linkedin'];
    $_SESSION['instagram'] = $row['instagram'];
    $_SESSION['bio'] = $row['bio'];
    $_SESSION['created_at'] = $row['created_at'];
}
?>

<aside class="relative bg-sidebar h-screen w-96 hidden sm:block shadow-xl ">
    <div class="p-8 flex flex-col items-center justify-center"> <!-- Use flex to align items vertically and horizontally -->
        <div class="flex items-center mb-3"> <!-- Nested flex container for vertical alignment -->
            <img src="../images/logoer.png" alt="Logo" class="h-20 w-20 mr-3"> <!-- Logo image -->
            <h1 class="text-white text-xl font-semibold">Tejeros Rental</h1>
        </div>


    </div>

    <nav class="text-white text-base font-semibold">
    <a href="admin-home.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    <a href="admin-all-properties.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-list-ul mr-3"></i>
        All Properties
    </a>
    <a href="admin-landholder-list.php?search=&sort=name_asc" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-users mr-3"></i>
        Landholder List
    </a>
    <a href="admin-user-list.php?search=&sort=name_asc" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-user mr-3"></i>
        User List
    </a>
    <a href="admin-announcements.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-bullhorn mr-3"></i>
        Post Announcement
    </a>
    <a href="admin-contact.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-envelope mr-3"></i>
        Contact Messages
    </a>
    <a href="admin-approval.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-hourglass-half mr-3"></i>
        Pending Properties
    </a>
</nav>


</aside>

<div class="w-full flex flex-col  h-screen overflow-y-hidden">
    <!-- Desktop Header -->
    <header class="w-full items-center bg-white py-3 px-6 hidden sm:flex">
        <div class="w-1/2"></div>
        <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end z-50">
            <!-- Check if the seller is logged in -->
            <?php if (isset($_SESSION['user_id'])) : ?>
                <!-- Display seller's profile image as a clickable button -->
                <a href="#" @click="isOpen = !isOpen" class="relative z-50 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none z-50">
                    <img src="../uploaded_image/<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Image" class="w-full h-full object-cover">

                </a>
                <!-- Dropdown menu -->
                <div x-show="isOpen" @click.away="isOpen = false" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                    <a href="../logout.php" class="block px-4 py-2 account-link hover:text-white">Sign Out</a>
                </div>
            <?php endif; ?>
        </div>
    </header>




    <!-- Mobile Header & Nav -->
    <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
        <div class="flex items-center justify-between">
            <a href="index.html" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">Admin</a>
            <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                <i x-show="!isOpen" class="fas fa-bars"></i>
                <i x-show="isOpen" class="fas fa-times"></i>
            </button>
        </div>

        <!-- Dropdown Nav -->
        <nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">
        <a href="admin-home.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    <a href="admin-all-properties.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-list-ul mr-3"></i>
        All Properties
    </a>
    <a href="admin-landholder-list.php?search=&sort=name_asc" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-users mr-3"></i>
        Landholder List
    </a>
    <a href="admin-user-list.php?search=&sort=name_asc" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-user mr-3"></i>
        User List
    </a>
    <a href="admin-announcements.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-bullhorn mr-3"></i>
        Post Announcement
    </a>
    <a href="admin-contact.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-envelope mr-3"></i>
        Contact Messages
    </a>
    <a href="admin-approval.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-2 pl-6 nav-item">
        <i class="fas fa-hourglass-half mr-3"></i>
        Pending Properties
    </a>
        </nav>
        <!-- <button class="w-full bg-white cta-btn font-semibold py-2 mt-5 rounded-br-lg rounded-bl-lg rounded-tr-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
                    <i class="fas fa-plus mr-3"></i> New Report
                </button> -->
    </header>