<?php
include 'components/connection.php';

session_start();

// Number of items per page
$itemsPerPage = 4;

// Get the current page number
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset
$offset = ($currentPage - 1) * $itemsPerPage;

// Construct the base SQL query
// Construct the base SQL query
$sql = "
    SELECT p.*
    FROM properties_tb p
    LEFT JOIN bookings_tb b ON p.propertyId = b.propertyId
        AND b.status = 'Accepted'
        AND b.endDate > NOW()
    WHERE p.status != 'Pending'
      AND p.status != 'Rejected'
      AND p.is_deleted = 0
      AND b.booking_id IS NULL
";

// Append conditions based on filters
$conditions = [];
$params = [];

// Rent Amount filter
if (!empty($_GET['rent'])) {
    switch ($_GET['rent']) {
        case '2000':
            $conditions[] = "`rentAmount` < 2000";
            break;
        case '3000':
            $conditions[] = "`rentAmount` = 3000";
            break;
        case '4000':
            $conditions[] = "`rentAmount` >= 4000";
            break;
    }
}

// Size filter
if (!empty($_GET['size'])) {
    switch ($_GET['size']) {
        case '50-100':
            $conditions[] = "(`size` >= 50 AND `size` <= 100)";
            break;
        case '100-200':
            $conditions[] = "(`size` > 100 AND `size` <= 200)";
            break;
        case '200-up':
            $conditions[] = "`size` > 200";
            break;
    }
}


// Other filters
if (!empty($_GET['search'])) {
    $conditions[] = "`name` LIKE :search";
    $params[':search'] = "%{$_GET['search']}%";
}


// Handle multiple filter values for bedrooms
if (!empty($_GET['bedrooms'])) {
    $bedroomFilters = explode(',', $_GET['bedrooms']);
    $bedroomConditions = [];
    foreach ($bedroomFilters as $bedroomFilter) {
        if ($bedroomFilter == '5') {
            $bedroomConditions[] = "`bedroomNum` NOT IN (0, 1, 2, 3, 4)";
        } else {
            $bedroomConditions[] = "`bedroomNum` = :bedroom{$bedroomFilter}";
            $params[":bedroom{$bedroomFilter}"] = (int)$bedroomFilter;
        }
    }
    $conditions[] = '(' . implode(' OR ', $bedroomConditions) . ')';
}

// Handle multiple filter values for bathrooms
if (!empty($_GET['bathrooms'])) {
    $bathroomFilters = explode(',', $_GET['bathrooms']);
    $bathroomConditions = [];
    foreach ($bathroomFilters as $bathroomFilter) {
        if ($bathroomFilter == '5') {
            $bathroomConditions[] = "`bathroomNum` NOT IN (0, 1, 2, 3, 4)";
        } else {
            $bathroomConditions[] = "`bathroomNum` = :bathroom{$bathroomFilter}";
            $params[":bathroom{$bathroomFilter}"] = (int)$bathroomFilter;
        }
    }
    $conditions[] = '(' . implode(' OR ', $bathroomConditions) . ')';
}

if (!empty($conditions)) {
    $sql .= ' AND ' . implode(' AND ', $conditions);
}


// Sort by
$orderBy = '';
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'new_listings':
            $orderBy = 'propertyId DESC';
            break;
        case 'old_listings':
            $orderBy = 'propertyId ASC';
            break;
        case 'low_price':
            $orderBy = 'rentAmount ASC';
            break;
        case 'high_price':
            $orderBy = 'rentAmount DESC';
            break;
    }
}

// Add sorting condition to the SQL query
if (!empty($orderBy)) {
    $sql .= " ORDER BY $orderBy";
}

// Add pagination to the SQL query
$sql .= " LIMIT :offset, :itemsPerPage";

// Prepare and bind parameters to the SQL query
$select_products = $conn->prepare($sql);
foreach ($params as $key => &$val) {
    $select_products->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$select_products->bindParam(':offset', $offset, PDO::PARAM_INT);
$select_products->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

// Execute the query
$select_products->execute();

// Count total number of properties with filters applied
$total_properties_query = "
    SELECT COUNT(*) AS total
    FROM properties_tb p
    LEFT JOIN bookings_tb b ON p.propertyId = b.propertyId
        AND b.status = 'Accepted'
        AND b.endDate > NOW()
    WHERE p.status != 'Pending'
      AND p.status != 'Rejected'
      AND p.is_deleted = 0
      AND b.booking_id IS NULL
";

if (!empty($conditions)) {
    $total_properties_query .= ' AND ' . implode(' AND ', $conditions);
}
$count_stmt = $conn->prepare($total_properties_query);
foreach ($params as $key => &$val) {
    $count_stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$count_stmt->execute();
$total_properties = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        html,
        body {
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            .filter-section {
                width: 100%;
                margin-right: 0;
            }

            .property-listing {
                width: 100%;
            }
        }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="bg-white">
    <?php include 'user/user-header.php' ?>

    <div class="flex justify-center items-center mx-10 mt-2 py-4 mb-2 px-8 shadow-md">
        <div class="join">
            <form action="" method="GET" class="w-96 flex">
                <input type="text" name="search" class="input input-bordered join-item w-full" placeholder="Search by name" />
                <button type="submit" class="btn join-item">Search</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mx-10 justify-between">
        <a href="category.php?category=house" class="bg-blue-200 hover:bg-blue-300 rounded-lg p-4 flex flex-col items-center justify-center">
            <img src="images/icon-1.png" alt="House" class="w-16 h-16 mb-2">
            <h3 class="text-lg font-semibold">House</h3>
        </a>
        <a href="category.php?category=apartment" class="bg-blue-200 hover:bg-blue-300 rounded-lg p-4 flex flex-col items-center justify-center">
            <img src="images/icon-2.png" alt="Apartment" class="w-16 h-16 mb-2">
            <h3 class="text-lg font-semibold">Apartment</h3>
        </a>
        <a href="category.php?category=dorm" class="bg-blue-200 hover:bg-blue-300 rounded-lg p-4 flex flex-col items-center justify-center">
            <img src="images/icon-3.png" alt="Dorm" class="w-16 h-16 mb-2">
            <h3 class="text-lg font-semibold">Dorm</h3>
        </a>
        <a href="category.php?category=bedspace" class="bg-blue-200 hover:bg-blue-300 rounded-lg p-4 flex flex-col items-center justify-center">
            <img src="images/icon-4.png" alt="Bedspace" class="w-16 h-16 mb-2">
            <h3 class="text-lg font-semibold">Bedspace</h3>
        </a>
    </div>

    <nav class="breadcrumbs flex items-center ml-11">
        <a href="loading-page-in.php" class="text-gray-500 hover:text-gray-700 transition-colors">Home</a>
        <span class="mx-2 text-gray-500">></span>
        <a href="properties.php" class="text-black-500 hover:text-gray-700 transition-colors">Properties</a>
    </nav>

    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start mx-6">
            <!-- Filter Section -->
            <!-- Filter Section -->
            <div class="filter-section py-4 px-6 mt-6 border border-gray-300 bg-blue-100 rounded-md md:w-1/4 mr-4">
                <h2 class="text-lg font-semibold mb-4">Filter Options</h2>
                <form action="" method="GET">
                    <!-- Existing Filters -->
                    <!-- Status -->
                    <!-- Size of House -->
                    <div class="form-group border-gray-300 pb-4 mb-4">
                        <label for="size"></label>
                        <select id="size" name="size" class="select select-bordered w-full max-w-xs" onchange="this.form.submit()">
                            <option value="">Select Size</option>
                            <option value="50-100">50-100 sqm</option>
                            <option value="100-200">100-200 sqm</option>
                            <option value="200-up">200 sqm and up</option>
                        </select>
                    </div>

                    <!-- Bedrooms -->
                    <div class="form-group border-gray-300 pb-4 mb-4">
                        <label for="bedrooms"></label>
                        <select id="bedrooms" name="bedrooms" class="select select-bordered w-full max-w-xs" onchange="this.form.submit()">
                            <option value="">Select Bedrooms</option>
                            <option value="1">1 Bedroom</option>
                            <option value="2">2 Bedrooms</option>
                            <option value="3">3 Bedrooms</option>
                            <option value="4">4 Bedrooms</option>
                            <option value="5">5+ Bedrooms</option>
                        </select>
                    </div>
                    <!-- Bathrooms -->
                    <div class="form-group border-gray-300 pb-4 mb-4">
                        <label for="bathrooms"></label>
                        <select id="bathrooms" name="bathrooms" class="select select-bordered w-full max-w-xs" onchange="this.form.submit()">
                            <option value="">Select Bathrooms</option>
                            <option value="1">1 Bathroom</option>
                            <option value="2">2 Bathrooms</option>
                            <option value="3">3 Bathrooms</option>
                            <option value="4">4 Bathrooms</option>
                            <option value="5">5+ Bathrooms</option>
                        </select>
                    </div>
                    <!-- Rent Amount -->
                    <div class="form-group border-gray-300 pb-4 mb-4">
                        <label for="rent"></label>
                        <select id="rent" name="rent" class="select select-bordered w-full max-w-xs" onchange="this.form.submit()">
                            <option value="">Select Rent Amount</option>
                            <option value="2000">Lower 2000</option>
                            <option value="3000">3000</option>
                            <option value="4000">4000 and up</option>
                        </select>
                    </div>
                    <!-- Clear Filters Button -->
                    <div class="flex justify-center">
                        <a href="properties.php" class="btn btn-secondary ml-2 w-full">Reset Filter</a>
                    </div>
                </form>
            </div>


            <!-- Properties Listing Section -->
            <div class="property-listing flex-grow mt-6 ">
                <div class="flex justify-between items-center mb-4 mx-4 border border-gray-300 py-4 px-2">
                    <p class="text-gray-600">Showing <?php echo min($total_properties, $offset + 1); ?> - <?php echo min($total_properties, $offset + $itemsPerPage); ?> out of <?php echo $total_properties; ?> results</p>
                    <form action="" method="GET" class="flex justify-end">
                        <select name="sort" class="select select-bordered w-full max-w-xs" onchange="this.form.submit()">
                            <option value="">Sort by</option>
                            <option value="new_listings">Newest Listings</option>
                            <option value="old_listings">Oldest Listings</option>
                            <option value="low_price">Lowest Price</option>
                            <option value="high_price">Highest Price</option>
                        </select>
                    </form>
                </div>
                <div class="property-listing flex-grow mt-6">
                    <!-- Property Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4 mt-4 ml-4 mr-4">
                        <?php
                        // Check if there are any matching properties
                        if ($select_products->rowCount() > 0) {
                            // Loop through each property and generate the grid item
                            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                                <form action="" method="post" class="box bg-white rounded-lg shadow-md p-6 border border-gray-300">
                                    <input type="hidden" name="pid" value="<?= $fetch_product['propertyId']; ?>">
                                    <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                                    <input type="hidden" name="price" value="<?= $fetch_product['rentAmount']; ?>">
                                    <input type="hidden" name="bathroomNum" value="<?= $fetch_product['bathroomNum']; ?>">
                                    <input type="hidden" name="image" value="<?= $fetch_product['image01']; ?>">
                                    <input type="hidden" name="size" value="<?= $fetch_product['size']; ?>">
                                    <a href="property-details.php?pid=<?= $fetch_product['propertyId']; ?>" class="block">
                                        <img src="uploaded_image/<?= $fetch_product['image01']; ?>" alt="<?= $fetch_product['name']; ?>" class="w-full h-64 object-cover rounded-t-lg">
                                        <div class="px-2 py-2">
                                            <div class="flex justify-between items-center">
                                                <div class="price text-lg font-semibold text-gray-800"><?= $fetch_product['name']; ?></div>
                                            </div>
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="name text-gray-600 flex items-center">
                                                    <i class='bx bx-map-alt bx-sm'></i>&nbsp; <?= $fetch_product['city']; ?>, <?= $fetch_product['houseType']; ?>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class='bx bx-bath mr-1 bx-sm'></i>&nbsp;
                                                    <span><?= $fetch_product['bathroomNum']; ?></span>
                                                    <i class='bx bx-bed ml-2 mr-1 bx-sm'></i>&nbsp;
                                                    <span><?= $fetch_product['bedroomNum']; ?></span>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center mt-2">
                                                <div class="status text-gray-600 flex items-center">
                                                    <i class='bx bx-coin-stack mr-1 bx-sm'></i>&nbsp;₱ <?= number_format($fetch_product['rentAmount'], 0, '.', ','); ?>
                                                </div>
                                                <div class="size text-gray-600 flex items-center">
                                                    <i class='bx bx-ruler mr-1 bx-sm'></i>&nbsp;<?= $fetch_product['size']; ?> sqm
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </form>


                        <?php
                            }
                        } else {
                            // If no matching properties found, display a message
                            echo '<p class="empty">No products found!</p>';
                        }
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 mb-8 flex justify-center">
                        <?php
                        // Calculate total number of pages
                        $totalPages = ceil($total_properties / $itemsPerPage);

                        // Previous page link
                        $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
                        echo '<a href="?page=' . $prevPage . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="btn btn-outline hover:bg-blue-300 bg-gray-200 mx-2">Previous</a>';

                        // Page numbers
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $activeClass = $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-blue-200 text-gray-700 hover:bg-blue-300';
                            echo '<a href="?page=' . $i . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="btn ' . $activeClass . ' mx-2">' . $i . '</a>';
                        }

                        // Next page link
                        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
                        echo '<a href="?page=' . $nextPage . '&sort=' . (isset($_GET['sort']) ? $_GET['sort'] : '') . '" class="btn btn-outline hover:bg-blue-300 bg-gray-200 mx-2">Next</a>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this JavaScript code at the end of your HTML body -->
    <script>
        // Function to submit the form without refreshing the page
        function submitForm() {
            document.getElementById("filterForm").submit();
        }
    </script>

    <?php include 'user/user-footer.php'; ?>
</body>

</html>