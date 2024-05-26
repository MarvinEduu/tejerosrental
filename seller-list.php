<?php
@include 'components/connection.php';

session_start();

// Number of items per page
$itemsPerPage = 8;

// Get the current page number
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset
$offset = ($currentPage - 1) * $itemsPerPage;

// Initialize the search query
$searchQuery = '';

// Check if the search parameter is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Sanitize the search input to prevent SQL injection
    $search = '%' . $_GET['search'] . '%';
    // Append the search condition to the SQL query
    $searchQuery = " AND s.full_name LIKE :search";
}

// Construct the base SQL query to fetch sellers along with the count of their properties
$sql = "SELECT s.*, COUNT(p.propertyId) AS propertyCount 
        FROM `landholders_tb` s 
        LEFT JOIN `properties_tb` p ON s.landholder_id = p.landholder_id 
        LEFT JOIN `bookings_tb` b ON p.propertyId = b.propertyId
                                    AND b.status = 'Accepted'
                                    AND b.endDate > NOW()
        WHERE p.status != 'Pending' 
            AND p.status != 'Rejected' 
            AND p.is_deleted = 0
            AND (b.booking_id IS NULL OR b.booking_id = '')";

// Append the search query to the SQL query
$sql .= $searchQuery;

// Group by clause
$sql .= " GROUP BY s.landholder_id";

// Sort by
if (isset($_GET['sort'])) {
    $sort_option = $_GET['sort'];
    switch ($sort_option) {
        case 'newest_seller':
            $orderBy = 's.landholder_id DESC';
            break;
        case 'oldest_seller':
            $orderBy = 's.landholder_id ASC';
            break;
        case 'name_asc':
            $orderBy = 's.full_name ASC';
            break;
        default:
            // Default sorting option
            $orderBy = ''; // Add your default sorting option here if needed
            break;
    }
} else {
    // Default sorting option if not provided in the URL
    $orderBy = ''; // Add your default sorting option here if needed
}

// Add sorting condition to the SQL query
if (!empty($orderBy)) {
    $sql .= " ORDER BY $orderBy";
}

// Add pagination to the SQL query
$sql .= " LIMIT :offset, :itemsPerPage";

// Prepare the SQL query
$select_sellers = $conn->prepare($sql);

// Bind parameters for pagination
$select_sellers->bindParam(':offset', $offset, PDO::PARAM_INT);
$select_sellers->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

// Bind search parameter if provided
if (!empty($searchQuery)) {
    $select_sellers->bindParam(':search', $search, PDO::PARAM_STR);
}

// Execute the query
$select_sellers->execute();

// Count total number of sellers
if (!empty($searchQuery)) {
    // If search parameter is provided, count total number of sellers based on search query
    $total_sellers_query = $conn->prepare("SELECT COUNT(*) AS total FROM `landholders_tb` s WHERE 1=1" . $searchQuery);
    $total_sellers_query->bindParam(':search', $search, PDO::PARAM_STR);
    $total_sellers_query->execute();
} else {
    // If no search parameter, count total number of sellers without search condition
    $total_sellers_query = $conn->query("SELECT COUNT(*) AS total FROM `landholders_tb`");
}
$total_sellers = $total_sellers_query->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html data-theme="winter">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rental Capstone</title>
    <link rel="icon" type="image/x-icon" href="images/logoer.png">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        html,
        body {
            overflow-x: hidden;
        }

        /* Add this CSS to your existing style sheet or within a <style> tag in your HTML */
        .seller-profile {
            width: 100%;
            /* Ensure the container spans the entire width of its parent */
            height: 300px;
            /* Set a fixed height for the container (adjust as needed) */
            border-radius: 8px;
            /* Add rounded corners to the container */
            overflow: hidden;
            /* Ensure that the image doesn't overflow beyond the container */
        }

        .seller-profile img {
            width: 100%;
            /* Make the image width fill the container */
            height: 100%;
            /* Make the image height fill the container */
            object-fit: cover;
            /* Scale the image to cover the entire container */
            object-position: center;
            /* Center the image within the container */
            border-radius: 8px;
            /* Apply rounded corners to the image */
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="bg-white">
    <?php include 'user/user-header.php' ?>

    <div class="flex justify-center items-center mx-4 sm:mx-10 mt-2 py-4 mb-2 px-4 sm:px-8 shadow-md">
        <div class="w-full sm:w-auto">
            <form action="" method="GET" class="flex flex-wrap justify-center">
                <input type="text" name="search" class="input input-bordered w-full sm:w-64 md:w-80 lg:w-96 mb-2 sm:mb-0 sm:mr-2" placeholder="Search by name" />
                <button type="submit" class="btn w-full sm:w-auto">Search</button>
            </form>
        </div>

    </div>
    <nav class="breadcrumbs ml-10">
        <!-- Home -->
        <a href="loading-page-in.php" class="text-gray-500 hover:text-gray-700 transition-colors">
            Home
        </a>
        <span class="mx-2 text-gray-500">></span>
        <!-- Sellers -->
        <a href="seller-list.php" class="text-black-500 hover:text-gray-700 transition-colors">
            Landholders
        </a>
    </nav>

    <div class=" rounded-lg bg-white p-4 mx-10 border border-gray-300">

        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="mb-2 ">
                Showing <?php echo min($total_sellers, $offset + 1); ?> - <?php echo min($total_sellers, $offset + $itemsPerPage); ?> out of <?php echo $total_sellers; ?> sellers
            </div>

            <form action="" method="GET" class="flex flex-col md:flex-row md:items-center">
                <label for="sort" class="mr-2 md:mr-4 whitespace-nowrap mb-2 md:mb-0">Sort by:</label>
                <select id="sort" name="sort" class="select select-bordered max-w-xs">
                    <option value="default">Default</option>
                    <option value="newest_seller" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'newest_seller') echo 'selected'; ?>>Newest Seller</option>
                    <option value="oldest_seller" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'oldest_seller') echo 'selected'; ?>>Oldest Seller</option>
                    <option value="name_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') echo 'selected'; ?>>Name (Ascending)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="container mx-auto p-6 w-4/4">
        <!-- Updated HTML structure -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
            // Check if there are any sellers
            if ($select_sellers->rowCount() > 0) {
                // Loop through each seller and generate the grid item
                while ($fetch_seller = $select_sellers->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <a href="seller-details.php?id=<?= $fetch_seller['landholder_id']; ?>" class="seller-grid-item">
                        <div class="box bg-white rounded-lg shadow-md p-6 flex flex-col items-center justify-center border border-gray-300">
                            <!-- Display seller profile image -->
                            <div class="seller-profile">
                                <img src="uploaded_image/<?= $fetch_seller['profile_picture']; ?>" alt="<?= $fetch_seller['full_name']; ?>" class="w-full h-full object-cover rounded-md">
                            </div>
                            <!-- Display seller information -->
                            <div class="seller-info mt-4 text-center">
                                <h3 class="text-lg font-semibold"><?= $fetch_seller['full_name']; ?></h3>
                                <p><?= $fetch_seller['propertyCount']; ?> Properties</p>
                            </div>
                        </div>
                    </a>
            <?php
                }
            } else {
                // If no sellers found, display a message
                echo '<p class="empty">No sellers found!</p>';
            }
            ?>
        </div>

    </div>
    <div class="mt-8 mb-8 flex justify-center">
        <!-- Pagination links -->
        <?php
        // Calculate total number of pages
        $totalPages = ceil($total_sellers / $itemsPerPage);

        // Previous page link
        $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
        echo '<a href="?page=' . $prevPage . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="join-item btn btn-outline hover:bg-blue-300 bg-gray-200">Previous</a>';

        // Page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-300';
            echo '<a href="?page=' . $i . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="mx-2 px-4 py-2 rounded ' . $activeClass . '">' . $i . '</a>';
        }

        // Next page link
        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
        echo '<a href="?page=' . $nextPage . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="join-item btn btn-outline hover:bg-blue-300 bg-gray-200">Next</a>';
        ?>
    </div>

    <?php include 'user/user-footer.php' ?>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var sortSelect = document.getElementById('sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                // Submit the form when sorting option changes
                this.form.submit();
            });
        }
    });
</script>

</html>