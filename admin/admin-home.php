<?php

include '../components/connection.php';

session_start();

$stmt = $conn->query("SELECT latitude, longitude, name, status, image01, address, houseType, rentAmount, dateListed FROM properties_tb");

$landholders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to count properties based on status
$query = "SELECT status, COUNT(*) as count FROM properties_tb GROUP BY status";
$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize counts for new statuses
$approvedCount = 0;
$pendingCount = 0;
$rejectedCount = 0;

// Process data from database and update counts
foreach ($data as $row) {
    switch ($row['status']) {
        case 'Approved':
            $approvedCount = $row['count'];
            break;
        case 'Pending':
            $pendingCount = $row['count'];
            break;
        case 'Rejected':
            $rejectedCount = $row['count'];
            break;
        default:
            break;
    }
}

// Query to count properties based on house type and status
$query = "SELECT housetype, status, COUNT(*) as count FROM properties_tb GROUP BY housetype, status";
$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays to store data for each status
$houseTypes = [];
$countsByStatus = [
    'Approved' => ['House' => 0, 'Apartment' => 0, 'Dorm' => 0, 'Bedspace' => 0],
    'Pending' => ['House' => 0, 'Apartment' => 0, 'Dorm' => 0, 'Bedspace' => 0],
    'Rejected' => ['House' => 0, 'Apartment' => 0, 'Dorm' => 0, 'Bedspace' => 0],
];

// Process data from database
foreach ($data as $row) {
    $houseType = $row['housetype'];
    $status = $row['status'];
    $count = $row['count'];

    // Store counts based on house type and status
    if (!in_array($houseType, $houseTypes)) {
        $houseTypes[] = $houseType;
    }
    $countsByStatus[$status][$houseType] = $count;
}

// Retrieve verification tier counts
$query = "SELECT verification_tier, COUNT(*) as count FROM landholders_tb GROUP BY verification_tier";
$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize counts for each verification tier
$notVerifiedCount = 0;
$semiVerifiedCount = 0;
$fullyVerifiedCount = 0;

// Process data from database and update counts
foreach ($data as $row) {
    switch ($row['verification_tier']) {
        case 'Not Verified':
            $notVerifiedCount = $row['count'];
            break;
        case 'Semi-Verified':
            $semiVerifiedCount = $row['count'];
            break;
        case 'Fully Verified':
            $fullyVerifiedCount = $row['count'];
            break;
        default:
            break;
    }
}



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

    <div class="w-full overflow-x-hidden flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex flex-wrap mt-6">
                <div class="w-full lg:w-1/2 pr-0 lg:pr-2">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-plus mr-3"></i> Properties Status
                    </p>
                    <div class="p-6 bg-white">
                        <canvas id="propertyStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-check mr-3"></i> Sort Specification
                    </p>
                    <div class="p-7 bg-white">
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">House Type</th>
                                    <th class="px-4 py-2">Approved</th>
                                    <th class="px-4 py-2">Pending</th>
                                    <th class="px-4 py-2">Rejected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($houseTypes as $houseType) : ?>
                                    <tr>
                                        <td class="border px-4 py-3"><?php echo htmlspecialchars($houseType); ?></td>
                                        <td class="border px-4 py-3"><?php echo $countsByStatus['Approved'][$houseType]; ?></td>
                                        <td class="border px-4 py-3"><?php echo $countsByStatus['Pending'][$houseType]; ?></td>
                                        <td class="border px-4 py-3"><?php echo $countsByStatus['Rejected'][$houseType]; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>
            <div id="map" style="height: 600px; width: 100%;"></div>

            <div class="flex flex-wrap mt-6">
                <div class="w-full lg:w-1/2 pr-0 lg:pr-2">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-plus mr-3"></i> Landholder Verification Overview
                    </p>
                    <div class="p-6 bg-white">
                    <canvas id="verificationTierChart" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-check mr-3"></i> Sort Specification
                    </p>
                    <div class="p-7 bg-white">
                        
                    </div>
                </div>



            </div>
        </main>
        
        <canvas id="verificationTierChart" width="400" height="200"></canvas>

    </div>





    <script>
        // Prepare data for Chart.js
        const houseTypes = <?= json_encode($houseTypes) ?>;
        const statusLabels = ['Approved', 'Pending', 'Rejected'];
        const datasets = [];

        // Prepare datasets for each status
        statusLabels.forEach((status) => {
            const data = houseTypes.map((type) => countsByStatus[status][type] || 0);
            datasets.push({
                label: status,
                data: data,
                backgroundColor: status === 'Approved' ? 'rgba(54, 162, 235, 0.5)' : status === 'Pending' ? 'rgba(255, 206, 86, 0.5)' : 'rgba(255, 99, 132, 0.5)',
                borderColor: status === 'Approved' ? 'rgba(54, 162, 235, 1)' : status === 'Pending' ? 'rgba(255, 206, 86, 1)' : 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            });
        });

        // Create Chart.js instance for house type overview
        const ctxListing = document.getElementById('listingChart').getContext('2d');
        const myListingChart = new Chart(ctxListing, {
            type: 'bar',
            data: {
                labels: houseTypes,
                datasets: datasets
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


    <script>
        // Data for property status chart
        const propertyStatusLabels = ['Approved', 'Pending', 'Rejected'];
        const propertyStatusData = [<?= $approvedCount ?>, <?= $pendingCount ?>, <?= $rejectedCount ?>];

        // Create Chart.js instance for property status
        const ctxPropertyStatus = document.getElementById('propertyStatusChart').getContext('2d');
        const myPropertyStatusChart = new Chart(ctxPropertyStatus, {
            type: 'bar',
            data: {
                labels: propertyStatusLabels,
                datasets: [{
                    label: 'Property Status',
                    data: propertyStatusData,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)', // Blue for Approved
                        'rgba(255, 206, 86, 0.5)', // Yellow for Pending
                        'rgba(255, 99, 132, 0.5)', // Red for Rejected
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        // Leaflet Map Initialization
        var map = L.map('map').setView([14.3816, 120.8791], 11); // Initial map view (centered and zoom level)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var landholders = <?= json_encode($landholders) ?>;
        landholders.forEach(function(holder) {
            // Format date
            var dateListed = new Date(holder.dateListed);
            var formattedDate = dateListed.getDate() + ' ' + dateListed.toLocaleString('default', {
                month: 'short'
            }) + ' ' + dateListed.getFullYear();

            // Construct popup content
            var popupContent = '<div>' +
                '<b>Property Name:</b> ' + holder.name + '<br>' +
                '<b>Address:</b> ' + holder.address + '<br>' +
                '<b>House Type:</b> ' + holder.houseType + '<br>' +
                '<b>Rent Amount:</b> ' + holder.rentAmount + '<br>' +
                '<b>Status:</b> ' + holder.status + '<br>' +
                '<b>Date Listed:</b> ' + formattedDate +
                '</div>';


            // Create marker with popup
            L.marker([parseFloat(holder.latitude), parseFloat(holder.longitude)])
                .addTo(map)
                .bindPopup(popupContent);
        });
    </script>

    <!-- Chart.js Line Chart for Verification Tiers -->
    <script>
        // Data for verification tier line chart
        const verificationTierLabels = ['Not Verified', 'Semi-Verified', 'Fully Verified'];
        const verificationTierData = [<?= $notVerifiedCount ?>, <?= $semiVerifiedCount ?>, <?= $fullyVerifiedCount ?>];

        // Create Chart.js instance for verification tier line chart
        const ctxVerificationTier = document.getElementById('verificationTierChart').getContext('2d');
        const myVerificationTierChart = new Chart(ctxVerificationTier, {
            type: 'line',
            data: {
                labels: verificationTierLabels,
                datasets: [{
                    label: 'Landholders Verification Tier',
                    data: verificationTierData,
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>



    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

</body>

</html>