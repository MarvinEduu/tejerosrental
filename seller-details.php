<?php
// Include connection and session start
@include 'components/connection.php';
session_start();

// Retrieve seller ID from URL parameter
$landholder_id = isset($_GET['id']) ? $_GET['id'] : null;

// Query to retrieve seller information
$sellerQuery = $conn->prepare("SELECT * FROM `landholders_tb` WHERE `landholder_id` = :landholder_id  ");
$sellerQuery->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);
$sellerQuery->execute();
$seller = $sellerQuery->fetch(PDO::FETCH_ASSOC);

// Query to retrieve seller's properties, ensuring only active properties are fetched
$propertiesQuery = $conn->prepare("SELECT * FROM `properties_tb` WHERE `landholder_id` = :landholder_id AND status != 'Pending' AND status != 'Rejected' AND is_deleted = 0");
$propertiesQuery->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);
$propertiesQuery->execute();
$properties = $propertiesQuery->fetchAll(PDO::FETCH_ASSOC);


// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$propertiesPerPage = 4; // Properties per page
$totalProperties = count($properties); // Total number of properties
$totalPages = ceil($totalProperties / $propertiesPerPage); // Total number of pages

// Calculate the starting property index for the current page
$start = ($page - 1) * $propertiesPerPage;
// Slice the properties array to display properties for the current page
$propertiesForPage = array_slice($properties, $start, $propertiesPerPage);

// Query to retrieve landholder's ratings
$landholderRatingsQuery = $conn->prepare("SELECT AVG(rating) AS average_rating, COUNT(rating) AS rating_count FROM `user_ratings` WHERE `landholder_id` = :landholder_id");
$landholderRatingsQuery->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);
$landholderRatingsQuery->execute();
$landholderRatings = $landholderRatingsQuery->fetch(PDO::FETCH_ASSOC);

// Display landholder's average rating
if ($landholderRatings) {
    $averageLandholderRating = round($landholderRatings['average_rating']);
    $landholderRatingCount = $landholderRatings['rating_count'];
} else {
    $averageLandholderRating = 0;
    $landholderRatingCount = 0;
}

?>

<!DOCTYPE html>
<html data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick view</title>
    <link rel="icon" type="image/x-icon" href="images/logoer.png">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'user/user-header.php' ?>
    <nav class="breadcrumbs flex items-center mb-4 ml-16 mt-4">
        <!-- Home -->
        <a href="loading-page-in.php" class="text-gray-500 hover:text-gray-700 transition-colors">
            Home
        </a>

        <!-- Separator -->
        <span class="mx-2 text-gray-500">></span>

        <!-- Sellers -->
        <a href="seller-list.php" class="text-gray-500 hover:text-gray-700 transition-colors">
            Sellers
        </a>

        <!-- Separator -->
        <span class="mx-2 text-gray-500">></span>

        <!-- Landholder Name -->
        <span class="text-black-700"><?= $seller['full_name']; ?></span>
    </nav>
    <div class="container mx-auto flex flex-col md:flex-row">
        <!-- Left side - Seller information -->

        <div class="box bg-white w-96 mx-4 md:mx-auto mt-4 rounded-lg shadow-md border border-gray-500 p-4 md:p-6 flex flex-col items-center justify-center lg:h-2/4">
            <!-- Seller profile image -->
            <div class="seller-profile mt-2 mb-4">
                <img src="uploaded_image/<?= $seller['profile_picture']; ?>" alt="<?= $seller['full_name']; ?>" class="w-64 h-64 md:max-w-full md:h-auto object-cover rounded-md">
            </div>
            <!-- Seller information -->
            <div class="seller-info text-center">
                <h3 class="text-lg font-semibold"><?= $seller['full_name']; ?></h3>
                <p><?= $seller['email']; ?></p>

                <!-- Display Verification Tier -->
                <p class="mt-2 text-sm text-gray-600"> <?= htmlspecialchars($seller['verification_tier']); ?></p>

                <!-- Display Rating -->
                <div class="flex items-center justify-center mt-2">
                    <?php
                    // Display landholder's rating
                    echo '<div class="flex items-center mt-2">';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $averageLandholderRating) {
                            echo '<i class="fas fa-star text-yellow-500"></i>';
                        } else {
                            echo '<i class="far fa-star text-gray-400"></i>';
                        }
                    }
                    echo "<span class='ml-2 text-gray-600'>($landholderRatingCount)</span>";
                    echo '</div>';
                    ?>
                </div>

                <!-- Additional seller information -->
                <div class="social-links mt-4 flex flex-wrap gap-2 md:gap-4 justify-center">
                    <!-- Facebook link -->
                    <a href="<?= $seller['facebook']; ?>" class="social-icon" title="Facebook" target="_blank">
                        <i class="fab fa-facebook-square text-blue-600 text-3xl md:text-5xl"></i>
                    </a>
                    <!-- LinkedIn link -->
                    <a href="<?= $seller['linkedin']; ?>" class="social-icon" title="LinkedIn" target="_blank">
                        <i class="fab fa-linkedin text-blue-800 text-3xl md:text-5xl"></i>
                    </a>
                    <!-- Instagram link -->
                    <a href="<?= $seller['instagram']; ?>" class="social-icon" title="Instagram" target="_blank">
                        <i class="fab fa-instagram text-pink-600 text-3xl md:text-5xl"></i>
                    </a>
                    <!-- Message button as icon -->
                    <a href="chat.php?seller_id=<?= $seller['landholder_id']; ?>" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                        <i class="fas fa-comment-dots text-white-600 text-xl md:text-3xl"></i> <!-- Message icon -->
                    </a>
                </div>
            </div>
        </div>



        <!-- Right side - Seller's properties -->
        <div class="w-3/4 p-8 mx-8 md:p-8 mx-auto md:mx-4">
            <p class="text-gray-600 mb-4">Showing <?= $totalProperties; ?> properties from seller.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 ">
                <?php foreach ($propertiesForPage as $property) : ?>
                    <form action="property-details.php" method="get" class="box bg-white border border-gray-300 rounded-lg shadow-md p-6">
                        <input type="hidden" name="pid" value="<?= $property['propertyId']; ?>">
                        <input type="hidden" name="name" value="<?= $property['name']; ?>">
                        <input type="hidden" name="price" value="<?= $property['rentAmount']; ?>">
                        <input type="hidden" name="bathroomNum" value="<?= $property['bathroomNum']; ?>">
                        <input type="hidden" name="image" value="<?= $property['image01']; ?>">
                        <input type="hidden" name="size" value="<?= $property['size']; ?>">
                        <a href="property-details.php?pid=<?= $property['propertyId']; ?>" class="block">
                            <img src="uploaded_image/<?= $property['image01']; ?>" alt="<?= $property['name']; ?>" class="w-full h-64 object-cover rounded-t-lg">
                            <div class="px-2 py-2">
                                <div class="flex justify-between items-center">
                                    <div class="price text-lg font-semibold text-gray-800"><?= $property['name']; ?></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="name text-gray-600 flex items-center">
                                        <i class='bx bx-map-alt bx-sm'></i>&nbsp; <?= $property['city']; ?>, <?= $property['houseType']; ?>
                                    </div>
                                    <div class="flex items-center">
                                        <i class='bx bx-bath mr-1 bx-sm'></i>&nbsp;
                                        <span><?= $property['bathroomNum']; ?></span>
                                        <i class='bx bx-bed ml-2 mr-1 bx-sm'></i>&nbsp;
                                        <span><?= $property['bedroomNum']; ?></span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="status text-gray-600 flex items-center">
                                        <i class='bx bx-coin-stack mr-1 bx-sm'></i>&nbsp;₱ <?= number_format($property['rentAmount'], 0, '.', ','); ?>
                                    </div>
                                    <div class="size text-gray-600 flex items-center">
                                        <i class='bx bx-ruler mr-1 bx-sm'></i>&nbsp;<?= $property['size']; ?> sqm
                                    </div>
                                </div>
                            </div>
                        </a>
                    </form>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 mb-8 flex justify-center">
                <!-- Pagination links -->
                <?php
                // Calculate total number of pages
                $totalPages = ceil(count($properties) / $propertiesPerPage);

                // Determine the current page and its neighbors
                $currentPage = $page;
                $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
                $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;

                // Define the range of pages to display around the current page
                $range = 2; // Number of pages to display on each side of the current page

                // Display "Previous" button
                echo '<a href="?id=' . $landholder_id . '&page=' . $prevPage . '" class="mx-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>';

                // Display triple dots before the current page if needed
                if ($currentPage - $range > 2) {
                    echo '<a href="?id=' . $landholder_id . '&page=' . max(1, $currentPage - $range - 1) . '" class="mx-2 px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">...</a>';
                }

                // Display pages around the current page
                for ($i = max(2, $currentPage - $range); $i <= min($totalPages - 1, $currentPage + $range); $i++) {
                    $activeClass = $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
                    echo '<a href="?id=' . $landholder_id . '&page=' . $i . '" class="mx-2 px-4 py-2 rounded ' . $activeClass . '">' . $i . '</a>';
                }

                // Display triple dots after the current page if needed
                if ($currentPage + $range < $totalPages - 1) {
                    echo '<a href="?id=' . $landholder_id . '&page=' . min($totalPages, $currentPage + $range + 1) . '" class="mx-2 px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">...</a>';
                }

                // Display last page link
                echo '<a href="?id=' . $landholder_id . '&page=' . $totalPages . '" class="mx-2 px-4 py-2 rounded ' . ($currentPage == $totalPages ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300') . '">' . $totalPages . '</a>';

                // Display "Next" button
                echo '<a href="?id=' . $landholder_id . '&page=' . $nextPage . '" class="mx-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>';
                ?>
            </div>
        </div>
    </div>
</body>

<?php include 'user/user-footer.php' ?>

</html>