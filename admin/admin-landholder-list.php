<?php

include '../components/connection.php';

session_start();

// Pagination settings
$usersPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $usersPerPage;

// Base query for counting total landholders
$countQuery = "SELECT COUNT(*) AS total FROM `landholders_tb`";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$total_landholders = $stmtCount->fetchColumn();

// Calculate total pages
$totalPages = ceil($total_landholders / $usersPerPage);

// Sorting options
$sortOptions = [
    'name_asc' => 'full_name ASC',
    'name_desc' => 'full_name DESC',
    'property_count' => 'property_count DESC'
];

// Determine sorting order
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'name_asc';
$orderBy = $sortOptions[$sort];

// Search functionality
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '';

// Query to fetch landholders with sorting, search, and pagination
$query = "SELECT l.*, COUNT(p.landholder_id) AS property_count, AVG(r.rating) AS avg_rating 
          FROM `landholders_tb` l 
          LEFT JOIN `properties_tb` p ON l.landholder_id = p.landholder_id
          LEFT JOIN `user_ratings` r ON l.landholder_id = r.landholder_id
          WHERE l.full_name LIKE :searchTerm
          GROUP BY l.landholder_id
          ORDER BY $orderBy
          LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($query);
$stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Fetch landholders
$landholders = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100 font-family-karla flex">

    <?php include 'admin-header.php' ?>

    <!-- Search and Sorting Controls -->
    <div class="container mx-auto px-6 pt-3">
        <form class="flex items-center justify-between">
            <input type="text" name="search" placeholder="Search by landholder name..." value="<?= htmlentities($_GET['search'] ?? '') ?>" class="px-3 py-2 border rounded-lg w-64">
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
                <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                <option value="property_count" <?= ($sort === 'property_count') ? 'selected' : '' ?>>Properties Owned (Most to Least)</option>
            </select>
        </form>
        <p class="text-md font-bold mt-2 pb-2">You have a total of <?= $total_landholders ?> landholders present</p>
    </div>

    <!-- Display landholders -->
<div class="container mx-auto p-6 overflow-y-auto">
    <div class="bg-blue-100 shadow overflow-hidden sm:rounded-lg p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
            <?php foreach ($landholders as $landholder) { ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center mb-4">
                            <?php 
                            $profileImageUrl = !empty($landholder['profile_picture']) ? '../uploaded_image/' . $landholder['profile_picture'] : 'https://via.placeholder.com/150'; // Placeholder image URL
                            ?>
                            <img src="<?= $profileImageUrl ?>" alt="<?= htmlspecialchars($landholder['full_name']); ?>" class="w-20 h-20 rounded-full">
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold"><?= htmlspecialchars($landholder['full_name']); ?></h4>
                                <p class="text-md text-gray-500"><?= $landholder['email']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2">
                            <div>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-mobile-alt"></i> Mobile:</span> <?= $landholder['mobile']; ?>
                                </p>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-birthday-cake"></i> Age:</span> <?= $landholder['age']; ?>
                                </p>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-user-check"></i> Verification Tier:</span> <?= $landholder['verification_tier']; ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-md text-gray-500 py-2">
                                    <span class="font-bold">Account Created:</span> <?= date('M d, Y', strtotime($landholder['created_at'])); ?>
                                </p>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-home"></i> Properties Owned:</span> <?= $landholder['property_count']; ?>
                                </p>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-star"></i> Rating:</span>
                                    <?php
                                    $avgRating = round($landholder['avg_rating'], 1);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo '<i class="fas fa-star' . ($i <= $avgRating ? ' text-yellow-500' : ' text-gray-300') . '"></i>';
                                    }
                                    ?>
                                    <span>(<?= $avgRating ?>)</span>
                                </p>
                            </div>
                        </div>
                        <?php if ($landholder['permit_status'] == 'Validating') { ?>
                            <div class="mt-4 text-sm text-gray-600">
                                <p><strong>Business Permit:</strong> <a href="../uploaded_image/business_permit/<?= $landholder['business_permit']; ?>" download>Download</a></p>
                                <form action="verify_permit.php" method="post" class="flex items-center mt-2">
                                    <input type="hidden" name="landholder_id" value="<?= $landholder['landholder_id']; ?>">
                                    <button type="submit" name="action" value="verified" class="btn btn-success me-2">Verify</button>
                                    <button type="submit" name="action" value="invalid" class="btn btn-danger">Invalidate</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Pagination links -->
    <nav class="mt-6 flex justify-center">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort ?><?= ($searchTerm !== '') ? '&search=' . urlencode($_GET['search']) : '' ?>">
                        <?= $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
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
