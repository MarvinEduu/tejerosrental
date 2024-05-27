<?php
include '../components/connection.php';
session_start();

// Pagination settings
$usersPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $usersPerPage;

// Base query for counting total users
$countQuery = "SELECT COUNT(*) AS total FROM `users_tb`";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute();
$total_users = $stmtCount->fetchColumn();

// Sorting options
$sortOptions = [
    'name_asc' => 'full_name ASC',
    'name_desc' => 'full_name DESC',
    'registration_asc' => 'created_at ASC',
    'registration_desc' => 'created_at DESC'
];

// Determine sorting order
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'name_asc';
$orderBy = $sortOptions[$sort];

// Search functionality
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '';

// Query to fetch users with sorting, search, and pagination
$query = "SELECT * FROM `users_tb` 
          WHERE full_name LIKE :searchTerm
          ORDER BY $orderBy
          LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($query);
$stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
$stmt->bindValue(':limit', $usersPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Fetch users
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="container mx-auto px-6 pt-3 ">
        <form class=" flex items-center justify-between">
            <input type="text" name="search" placeholder="Search by user name..." value="<?= htmlentities($_GET['search'] ?? '') ?>" class="px-3 py-2 border rounded-lg w-64">
            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
                <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                <option value="registration_asc" <?= ($sort === 'registration_asc') ? 'selected' : '' ?>>Registration Date (Oldest to Newest)</option>
                <option value="registration_desc" <?= ($sort === 'registration_desc') ? 'selected' : '' ?>>Registration Date (Newest to Oldest)</option>
            </select>
        </form>
        <p class="text-md font-bold mt-2 pb-2">You have a total of <?= $total_users ?> users present</p>
    </div>
    
    
    <!-- Display users container -->
<div class="container mx-auto p-6 overflow-y-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
            <?php foreach ($users as $user) { ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-400">
                    <div class="p-4">
                        <div class="flex items-center mb-4">
                            <?php 
                            $profileImageUrl = !empty($user['profile_picture']) ? '../uploaded_image/' . $user['profile_picture'] : 'https://via.placeholder.com/300'; // Placeholder image URL
                            ?>
                            <img src="<?= $profileImageUrl ?>" alt="<?= htmlspecialchars($user['full_name']); ?>" class="w-20 h-20 rounded-full">
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold"><?= htmlspecialchars($user['full_name']); ?></h4>
                                <p class="text-md text-gray-500"><?= $user['email']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2">
                            <div>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-mobile-alt"></i> Mobile:</span> <?= $user['mobile']; ?>
                                </p>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-birthday-cake"></i> Age:</span> <?= $user['age']; ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-md text-gray-500 py-2">
                                    <span class="font-bold">Account Created:</span> <?= date('M d, Y', strtotime($user['created_at'])); ?>
                                </p>
                                <!-- Bookings information -->
                                <?php
                                // Count number of bookings for this user
                                $countBookingsQuery = "SELECT COUNT(*) FROM `bookings_tb` WHERE user_id = :user_id";
                                $stmtCountBookings = $conn->prepare($countBookingsQuery);
                                $stmtCountBookings->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                                $stmtCountBookings->execute();
                                $bookingCount = $stmtCountBookings->fetchColumn();
                                ?>
                                <p class="text-md text-gray-600 py-2">
                                    <span class="font-bold"><i class="fas fa-bookmark"></i> Bookings:</span> <?= $bookingCount; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Pagination links -->
    <nav class="mt-6 flex justify-center">
        <ul class="pagination">
            <?php
            // Calculate total pages
            $totalPages = ceil($total_users / $usersPerPage);
            
            for ($i = 1; $i <= $totalPages; $i++) : ?>
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