<?php
@include 'components/connection.php';

session_start();

// Pagination variables
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page from query string, default to 1
$limit = 10; // Number of announcements per page
$offset = ($page - 1) * $limit; // Offset calculation for SQL query

// Filter by announcement type if button clicked
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

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

    <div class="px-6 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl sm:text-3xl font-bold my-6 sm:my-8">Announcements</h1>

            <!-- Filter buttons -->
            <div class="flex space-x-4 mb-4">
                <a href="?type=All" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">All</a>
                <a href="?type=Blogs" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Blogs</a>
                <a href="?type=Updates" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Updates</a>
                <a href="?type=Others" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Others</a>
            </div>

            <?php
            // SQL query with pagination and filtering by type
            $query = "SELECT * FROM announcements_tb";
            if ($typeFilter && $typeFilter != 'All') {
                $query .= " WHERE type = '$typeFilter'";
            }
            $query .= " ORDER BY posted_at DESC LIMIT $limit OFFSET $offset";

            $stmt = $conn->prepare($query);
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if announcements are found
            if ($announcements) {
                foreach ($announcements as $announcement) {
                    echo '<div class="bg-white rounded-md shadow-md p-4 mb-6">';
                    echo '<h2 class="text-xl sm:text-2xl font-bold my-2">' . htmlspecialchars($announcement['title']) . '</h2>';
                    echo '<p class="text-gray-700 mb-2 text-justify">' . htmlspecialchars($announcement['details']) . '</p>';
                    echo '<p class="text-sm text-gray-500">Posted on ' . date('M d, Y H:i', strtotime($announcement['posted_at'])) . '</p>';
                    echo '</div>';
                }
            } else {
                // If no announcements found
                echo '<div class="bg-white rounded-md shadow-md p-4 mb-6">';
                echo '<p class="text-gray-700 mb-2 text-center">No announcements found.</p>';
                echo '<img src="images/empty1.png" alt="Empty Illustration" class="mx-auto mt-4" style="max-width: 500px;">';
                echo '</div>';
            }

            // Pagination links
            $total_query = "SELECT COUNT(*) FROM announcements_tb";
            if ($typeFilter && $typeFilter != 'All') {
                $total_query .= " WHERE type = '$typeFilter'";
            }
            $total_stmt = $conn->query($total_query);
            $total_rows = $total_stmt->fetchColumn();
            $total_pages = ceil($total_rows / $limit);

            echo '<div class="flex justify-center mt-4">';
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="?page=' . $i . ($typeFilter ? '&type=' . $typeFilter : '') . '" class="mx-1 my-2 px-4 py-2 rounded-lg bg-gray-200 hover:bg-blue-300">' . $i . '</a>';
            }
            echo '</div>';
            ?>
        </div>
    </div>

    <?php include 'user/user-footer.php' ?>
</body>

</html>
