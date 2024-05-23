<?php

include '../components/connection.php';

session_start();

// Number of properties per page
$propertiesPerPage = 5;

// Get total count of pending properties
$pendingPropertiesQuery = $conn->prepare("SELECT COUNT(*) AS total FROM `properties_tb` WHERE status = 'Pending'");
$pendingPropertiesQuery->execute();
$totalProperties = $pendingPropertiesQuery->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate total pages
$totalPages = ceil($totalProperties / $propertiesPerPage);

// Determine current page (default to 1 if not set)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $totalPages)); // Ensure page is within valid range

// Calculate offset for SQL query
$offset = ($page - 1) * $propertiesPerPage;

// Retrieve pending properties for the current page
$pendingPropertiesQuery = $conn->prepare("SELECT * FROM `properties_tb` WHERE status = 'Pending' LIMIT $offset, $propertiesPerPage");
$pendingPropertiesQuery->execute();
$pendingProperties = $pendingPropertiesQuery->fetchAll(PDO::FETCH_ASSOC);

// Message based on the number of pending properties
$numPendingProperties = count($pendingProperties);
$pendingPropertiesMessage = "You have $numPendingProperties pending " . ($numPendingProperties === 1 ? "property" : "properties");

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-gray-100 font-family-karla flex">

    <?php include 'admin-header.php' ?>

    <?php
    // Calculate number of pending properties
    $numPendingProperties = count($pendingProperties);
    $pendingPropertiesMessage = "You have $numPendingProperties pending properties";

    // Adjust heading based on the number of pending properties
    $heading = "<h1 class='text-2xl font-bold mb-4'>Pending Properties</h1>";
    if ($numPendingProperties === 1) {
        $heading = "<h1 class='text-2xl font-bold mb-4'>Pending Property</h1>";
    } elseif ($numPendingProperties === 0) {
        $pendingPropertiesMessage = "You have no pending properties";
    }
    ?>


    <!-- Main Content Section -->
<div class="container mx-auto overflow-y-auto py-8 px-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Pending Properties</h1>

    <!-- Messages Container -->
    <div class="bg-blue-100 shadow overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Pending Properties</h3>
        </div>

        <!-- Display pending properties message -->
        <div class="p-4">
            <?php echo $pendingPropertiesMessage; ?>
        </div>

        <!-- Properties -->
        <!-- Properties -->
<!-- Properties -->
<div class="p-4 space-y-4">
    <!-- Display each property in a container -->
    <?php foreach ($pendingProperties as $property) : ?>
        <div class="border border-gray-200 p-4 rounded-lg bg-white shadow-sm flex items-stretch">
            <!-- Property Image -->
            <img src="../uploaded_image/<?php echo htmlspecialchars($property['image01']); ?>" alt="Property Image" class="w-96 h-72 object-cover rounded-lg shadow-md">

            <!-- Property Description and Landholder Details -->
            <div class="flex-1 ml-8">
                <!-- Property Name -->
                <h4 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($property['name']); ?></h4>

                <!-- Flex Container for Property and Landholder Details -->
                <div class="grid grid-cols-2 gap-4 text-base text-gray-700">
                    <!-- Property Details -->
                    <div>
                        <!-- Location -->
                        <div class="flex items-center mb-2">
                            <i class="fas fa-map mr-2 text-gray-500"></i>
                            <p><?php echo htmlspecialchars($property['address']); ?></p>
                        </div>

                        <!-- House Type -->
                        <div class="flex items-center mb-2">
                            <i class="fas fa-home mr-2 text-gray-500"></i>
                            <p><?php echo htmlspecialchars($property['houseType']); ?></p>
                        </div>

                        <!-- Rent Amount -->
                        <div class="flex items-center mb-2">
                            <i class="fas fa-coins mr-2 text-gray-500"></i>
                            <p>₱ <?php echo htmlspecialchars($property['rentAmount']); ?></p>
                        </div>

                        <!-- Date Listed -->
                        <div class="flex items-center mb-2">
                            <i class="far fa-calendar-alt mr-2 text-gray-500"></i>
                            <p><?php echo htmlspecialchars($property['dateListed']); ?></p>
                        </div>
                    </div>

                    <!-- Landholder Information -->
                    <div>
                        <?php
                        // Fetch landholder details based on landholder_id
                        $landholder_id = $property['landholder_id'];
                        $select_landholder = $conn->prepare("SELECT full_name, email, mobile, address FROM `landholders_tb` WHERE landholder_id = ?");
                        $select_landholder->execute([$landholder_id]);
                        $landholder = $select_landholder->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div>
                            <!-- Landholder Name -->
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user mr-2 text-gray-500"></i>
                                <p><?php echo $landholder ? htmlspecialchars($landholder['full_name']) : 'Unknown Landholder'; ?></p>
                            </div>
                            
                            <!-- Landholder Email -->
                            <div class="flex items-center mb-2">
                                <i class="fas fa-envelope mr-2 text-gray-500"></i>
                                <p><?php echo $landholder ? htmlspecialchars($landholder['email']) : 'N/A'; ?></p>
                            </div>
                            
                            <!-- Landholder Mobile -->
                            <div class="flex items-center mb-2">
                                <i class="fas fa-phone-alt mr-2 text-gray-500"></i>
                                <p><?php echo $landholder ? htmlspecialchars($landholder['mobile']) : 'N/A'; ?></p>
                            </div>
                            
                            <!-- Landholder Address -->
                            <div class="flex items-center mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                                <p><?php echo $landholder ? htmlspecialchars($landholder['address']) : 'N/A'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions (Approve/Reject Buttons) -->
                <div class="mt-auto flex justify-end">
                    <form action="process-approve-property.php" method="post">
                        <input type="hidden" name="propertyId" value="<?php echo $property['propertyId']; ?>">
                        <button type="submit" name="approve" class="px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600">Approve</button>
                        <button type="submit" name="reject" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 ml-2">Reject</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Display message if no records found -->
    <?php if (empty($pendingProperties)) : ?>
        <div class="border border-gray-200 p-4 rounded-lg bg-white shadow-sm text-center">
            <p class="text-sm text-gray-500">No pending properties found.</p>
        </div>
    <?php endif; ?>
</div>


    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center">
        <?php if ($totalPages > 1) : ?>
            <?php if ($page > 1) : ?>
                <a href="?page=<?= ($page - 1) ?>" class="mx-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-md">
                    <i class="fas fa-arrow-left"></i> Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?= $i ?>" class="mx-1 px-3 py-1 <?= $i === $page ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-md">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?= ($page + 1) ?>" class="mx-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-md">
                    Next <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>





    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

</body>

</html>