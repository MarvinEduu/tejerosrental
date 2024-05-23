<?php
@include '../components/connection.php';

// Check if landholder is not logged in
if (!isset($_SESSION['landholder_id'])) {
  // Redirect to login page
  header("Location: ../login.php");
  exit; // Stop further execution
}

// If landholder is logged in, fetch user data
$landholder_id = $_SESSION['landholder_id'];

// Query to fetch user data based on landholder_id
$query = "SELECT username, profile_picture, full_name, email, mobile, address, facebook, linkedin, instagram, bio, created_at FROM landholders_tb WHERE landholder_id = :landholder_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);
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

<!-- Modal for viewing and updating profile -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewProfileModalLabel">Landholder Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2">
          <div class="col-md-6">
            <!-- Display profile information -->
            <img src="../uploaded_image/<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; padding: 6px; margin-left: 10px;">
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fas fa-user fa-lg"></i> <?php echo $_SESSION['full_name']; ?></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fas fa-envelope fa-lg"></i> <?php echo $_SESSION['email']; ?></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fas fa-phone fa-lg"></i> <?php echo $_SESSION['mobile']; ?></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fas fa-home fa-lg"></i> <?php echo $_SESSION['address']; ?></p>
            <button class="btn btn-primary mt-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#updateProfileModal">Update Profile</button>
          </div>
          <div class="col-md-6">
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fab fa-facebook fa-lg"></i> <a href="<?php echo $_SESSION['facebook'] ?: '#'; ?>" target="_blank" style="color: blue;"><?php echo $_SESSION['facebook'] ? "Facebook" : "No social media link yet"; ?></a></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fab fa-linkedin fa-lg"></i> <a href="<?php echo $_SESSION['linkedin'] ?: '#'; ?>" target="_blank" style="color: blue;"><?php echo $_SESSION['linkedin'] ? "LinkedIn" : "No social media link yet"; ?></a></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fab fa-instagram fa-lg"></i> <a href="<?php echo $_SESSION['instagram'] ?: '#'; ?>" target="_blank" style="color: blue;"><?php echo $_SESSION['instagram'] ? "Instagram" : "No social media link yet"; ?></a></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px; text-align: justify;"><i class="fas fa-user-edit fa-lg"></i> <?php echo substr($_SESSION['bio'], 0, 300); ?></p>
            <p style="font-size: 1rem; padding: 6px; margin-left: 10px;"><i class="fas fa-calendar-alt fa-lg"></i> <?php echo $_SESSION['created_at']; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Modal for updating profile -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Update Profile Form -->
        <form id="updateProfileForm" method="post" action="update_profile.php" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="fullName" class="form-label">Profile Picture:</label>
            <img src="../uploaded_image/<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; padding: 6px; margin-left: 10px;">
            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpg, image/jpeg, image/png" class="form-control">
          </div>
          <div class="mb-3">
            <label for="fullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $_SESSION['full_name']; ?>">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email']; ?>">
          </div>
          <div class="mb-3">
            <label for="mobile" class="form-label">Mobile</label>
            <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $_SESSION['mobile']; ?>">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $_SESSION['address']; ?>">
          </div>
          <!-- Add more fields as needed -->

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  // Ensure DOM content is loaded before attaching event listener
  document.addEventListener('DOMContentLoaded', function() {
    // Log form submission to test functionality
    document.getElementById('updateProfileForm').addEventListener('submit', function(event) {
      console.log('Form submitted'); // Test: Check if form submission is triggered
    });
  });
</script>



<aside class="relative bg-sidebar h-screen w-96 hidden sm:block shadow-xl ">
  <div class="p-8 flex flex-col items-center justify-center"> <!-- Use flex to align items vertically and horizontally -->
    <div class="flex items-center mb-3"> <!-- Nested flex container for vertical alignment -->
      <img src="../images/logoer.png" alt="Logo" class="h-20 w-20 mr-3"> <!-- Logo image -->
      <h1 class="text-white text-xl font-semibold">Tejeros Rental</h1>
    </div>



  </div>

  <nav class="text-white text-base font-semibold">
    <a href="landholder-home.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
      <i class="fas fa-tachometer-alt mr-3"></i>
      Dashboard
    </a>
    <a href="landholder-table.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
      <i class="fas fa-list-ul mr-3"></i>
      All Properties
    </a>
    <nav class="text-white text-base font-semibold">
      <a href="landholder-form.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-plus-square mr-3"></i>
        Add Property
      </a>
      <a href="landholder-status.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-check-circle mr-3"></i>
        Verification
      </a>
      <a href="landholder-calendar.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-calendar-alt mr-3"></i>
        Calendar
      </a>
      <a href="landholder-pendings.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-clock mr-3"></i>
        Pending Bookings
      </a>
      <a href="landholder-active.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-calendar-check mr-3"></i>
        Active/ Cancelled Bookings
      </a>
    </nav>

  </nav>
</aside>

<div class="w-full flex flex-col  h-screen overflow-y-hidden">
  <!-- Desktop Header -->
  <header class="w-full items-center bg-white py-3 px-6 hidden sm:flex">
    <div class="w-1/2"></div>
    <div x-data="{ isOpen: false }" class="relative w-1/2 flex justify-end z-50">
      <!-- Check if the seller is logged in -->
      <?php if (isset($_SESSION['landholder_id'])) : ?>
        <!-- Display seller's profile image as a clickable button -->
        <a href="#" @click="isOpen = !isOpen" class="relative z-50 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none z-50">
          <img src="../uploaded_image/<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Image" class="w-full h-full object-cover">

        </a>
        <!-- Dropdown menu -->
        <div x-show="isOpen" @click.away="isOpen = false" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
          <a href="#" class="block px-4 py-2 account-link hover:text-white" data-bs-toggle="modal" data-bs-target="#viewProfileModal">Account</a>
          <a href="landholder-chat.php" class="block px-4 py-2 account-link hover:text-white">
            Messages
          </a>

          <a href="../logout.php" class="block px-4 py-2 account-link hover:text-white">Sign Out</a>
        </div>
      <?php endif; ?>
    </div>
  </header>






  <!-- Mobile Header & Nav -->
  <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-4 px-6 sm:hidden">
    <div class="flex items-center justify-between">
    <img src="../images/logoer.png" alt="Logo" class="h-10 w-10">
      <a href="landholder-home.php" class="text-white text-lg font-semibold uppercase hover:text-gray-300">Tejeros Rental</a>
      <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
        <i x-show="!isOpen" class="fas fa-bars"></i>
        <i x-show="isOpen" class="fas fa-times"></i>
      </button>
    </div>

    <!-- Dropdown Nav -->
    <nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">
      <a href="landholder-home.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
      </a>
      <a href="landholder-table.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-list-ul mr-3"></i>
        All Properties
      </a>
      <a href="landholder-form.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-plus-square mr-3"></i>
        Add Property
      </a>
      <a href="landholder-status.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-check-circle mr-3"></i>
        Verification
      </a>
      <a href="landholder-calendar.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-calendar-alt mr-3"></i>
        Calendar
      </a>
      <a href="landholder-pendings.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-clock mr-3"></i>
        Pending Bookings
      </a>
      <a href="landholder-active.php" class="flex items-center text-white opacity-85 hover:opacity-100 py-4 pl-6 nav-item">
        <i class="fas fa-calendar-check mr-3"></i>
        Active/ Cancelled Bookings
      </a>
      <?php if (isset($_SESSION['landholder_id'])) : ?>
        <!-- Display seller's profile image as a clickable button -->
        <a href="#" @click="isOpen = !isOpen" class="relative z-50 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none z-50">
          <img src="../uploaded_image/<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Image" class="w-full h-full object-cover">

        </a>
        <!-- Dropdown menu -->
       
          <a href="#" class="block px-4 py-2 account-link hover:text-white" data-bs-toggle="modal" data-bs-target="#viewProfileModal">Account</a>
          <a href="landholder-chat.php" class="block px-4 py-2 account-link hover:text-white">
            Messages
          </a>

          <a href="../logout.php" class="block px-4 py-2 account-link hover:text-white">Sign Out</a>
      <?php endif; ?>


    </nav>
    <!-- <button class="w-full bg-white cta-btn font-semibold py-2 mt-5 rounded-br-lg rounded-bl-lg rounded-tr-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
                    <i class="fas fa-plus mr-3"></i> New Report
                </button> -->
  </header>