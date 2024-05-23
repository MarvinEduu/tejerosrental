<?php
@include 'components/connection.php';

session_start();


?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rental Capstone</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/x-icon" href="images/logoer.png">

  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100 flex flex-col">

  <?php include 'user/user-header.php' ?>

  <div class="px-6 sm:px-6 lg:px-8"> <!-- Adjust padding based on screen size -->
    <div class="max-w-4xl mx-auto"> <!-- Center content and limit width -->
        <h1 class="text-2xl sm:text-3xl font-bold my-6 sm:my-8">Announcements</h1> <!-- Increase heading size on larger screens -->

        <?php
        $query = "SELECT * FROM announcements_tb ORDER BY posted_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($announcements as $announcement) {
            echo '<div class="bg-white rounded-md shadow-md p-4 mb-6">'; // Remove unnecessary text-justify class
            echo '<h2 class="text-xl sm:text-2xl font-bold my-2">' . htmlspecialchars($announcement['title']) . '</h2>'; // Increase heading size on larger screens
            echo '<p class="text-gray-700 mb-2 text-justify">' . htmlspecialchars($announcement['details']) . '</p>';
            echo '<p class="text-sm text-gray-500">Posted on ' . date('M d, Y H:i', strtotime($announcement['posted_at'])) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</div>


</body>
<?php include 'user/user-footer.php' ?>
</html>
