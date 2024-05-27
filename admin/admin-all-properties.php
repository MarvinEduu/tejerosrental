<?php

include '../components/connection.php';

session_start();

// Pagination settings
$propertiesPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $propertiesPerPage;

// Base query for counting total landholders
$countQuery = "SELECT COUNT(*) AS total FROM `properties_tb`";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$total_properties = $stmtCount->fetchColumn();

// Sorting options
$sortOptions = [
    'name_asc' => 'name ASC',
    'name_desc' => 'name DESC',
    'price_asc' => 'rentAmount ASC',
    'price_desc' => 'rentAmount DESC'
];

// Determine sorting order
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'name_asc';
$orderBy = $sortOptions[$sort];

// Search functionality
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '';

// Filters
$houseType = isset($_GET['houseType']) ? $_GET['houseType'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Base query
$query = "SELECT * FROM `properties_tb` WHERE is_deleted = 0";

// Filter conditions
$params = [];

if (!empty($houseType)) {
    $query .= " AND houseType = :houseType";
    $params['houseType'] = $houseType;
}

if (!empty($status)) {
    $query .= " AND status = :status";
    $params['status'] = $status;
}

if (!empty($searchTerm)) {
    $query .= " AND name LIKE :searchTerm";
    $params['searchTerm'] = $searchTerm;
}

// Prepare and execute the statement to count total properties
$stmt = $conn->prepare($query);
$stmt->execute($params);
$total_properties = $stmt->rowCount();

// Add sorting, LIMIT and OFFSET to the query
$query .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";
$params['limit'] = $propertiesPerPage;
$params['offset'] = $offset;

// Prepare and execute the query to fetch properties
$stmt = $conn->prepare($query);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Admin Template</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100 font-family-karla flex">

<?php include 'admin-header.php' ?>

<div class="container mx-auto px-6 pt-3">
    <!-- Combined Search, Sorting, and Filters Form -->
    <form class=" flex items-center justify-between">
        <input type="text" name="search" placeholder="Search by name..." value="<?= htmlentities($_GET['search'] ?? '') ?>" class="px-3 py-2 border rounded-lg w-64">
        
        <div class="flex items-center gap-2">
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
                <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Price (Low to High)</option>
                <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>Price (High to Low)</option>
            </select>
            
            <select id="houseType" name="houseType" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
                <option value="">House Type</option>
                <option value="House" <?= ($houseType == 'House') ? 'selected' : '' ?>>House</option>
                <option value="Apartment" <?= ($houseType == 'Apartment') ? 'selected' : '' ?>>Apartment</option>
                <option value="Dorm" <?= ($houseType == 'Dorm') ? 'selected' : '' ?>>Dorm</option>
                <option value="Bedspace" <?= ($houseType == 'Bedspace') ? 'selected' : '' ?>>Bedspace</option>
            </select>
            
            <select id="status" name="status" onchange="this.form.submit()" class="px-2 py-2 border rounded-lg">
                <option value="">Status</option>
                <option value="Approved" <?= ($status == 'Approved') ? 'selected' : '' ?>>Approved</option>
                <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Rejected" <?= ($status == 'Rejected') ? 'selected' : '' ?>>Rejected</option>
            </select>
            
            <a href="?" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Clear</a>
        </div>
    </form>
    <p class="text-md font-bold mt-2 pb-2">You have a total of <?= $total_properties ?> properties present</p>
    </div>


    
    <div class="container mx-auto p-6 overflow-y-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
<!-- Display properties -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2">
    <?php foreach ($properties as $property) { ?>
        <div class="bg-white rounded-md shadow-md overflow-hidden flex border border-gray-400">
            <!-- Display property image -->
            <img src="../uploaded_image/<?php echo htmlspecialchars($property['image01']); ?>" alt="Property Image" class="w-80 h-80 object-cover rounded-lg p-3">
            <div class="py-4 flex flex-col justify-between">
                <h4 class="text-lg font-semibold"><?= htmlspecialchars($property['name']); ?></h4>
                <p class="text-md text-gray-500"><i class="fas fa-home"> </i> <span class="font-bold">Type:</span> <?= htmlspecialchars($property['houseType']); ?></p>
                <p class="text-md text-gray-600"><i class="fas fa-bed"></i> <span class="font-bold">Bedrooms:</span> <?= htmlspecialchars($property['bedroomNum']); ?></p>
                <p class="text-md text-gray-600"><i class="fas fa-bath"></i> <span class="font-bold">Bathrooms:</span> <?= htmlspecialchars($property['bathroomNum']); ?></p>
                <p class="text-md text-gray-600"><i class="fas fa-ruler-combined"></i> <span class="font-bold">Size:</span> <?= htmlspecialchars($property['size']); ?> sqm</p>
                <p class="text-md text-gray-600"><i class="fas fa-coins"></i> <span class="font-bold">Price:</span> ₱ <?= htmlspecialchars($property['rentAmount']); ?></p>
                <p class="text-md text-gray-500"><i class="fas fa-info-circle"></i> <span class="font-bold">Status:</span> <?= htmlspecialchars($property['status']); ?></p>
            </div>
        </div>
    <?php } ?>
</div>
</div>

    <!-- Pagination -->
    <nav class="mt-6 flex justify-center">
        <ul class="pagination">
            <?php
            $totalPages = ceil($total_properties / $propertiesPerPage);
            for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i ?>&houseType=<?= urlencode($houseType) ?>&status=<?= urlencode($status) ?>&sort=<?= urlencode($sort) ?>&search=<?= urlencode($_GET['search'] ?? '') ?>"><?= $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>

<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
<!-- ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

</body>

</html>
