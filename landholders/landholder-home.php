<?php
include '../components/connection.php';

session_start();

// Check if the update_success flag is set
$updateSuccess = isset($_SESSION['update_success']) && $_SESSION['update_success'];

// Unset the flag to prevent the modal from displaying multiple times
unset($_SESSION['update_success']);

// Fetch house types and their counts
$landholder_id = $_SESSION['landholder_id'];
$query = $conn->prepare("SELECT houseType, COUNT(*) as count FROM properties_tb WHERE landholder_id = ? GROUP BY houseType");
$query->execute([$landholder_id]);
$houseTypes = $query->fetchAll(PDO::FETCH_ASSOC);

$dataPointsQuery = $conn->prepare("SELECT dateListed, COUNT(*) as count FROM properties_tb WHERE landholder_id = ? GROUP BY dateListed");
$dataPointsQuery->execute([$landholder_id]);
$dataPoints = $dataPointsQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch user ratings and comments
$ratingsQuery = $conn->prepare("
    SELECT 
        u.profile_picture, 
        u.full_name as user_name, 
        r.rating, 
        r.comment 
    FROM 
        user_ratings r 
    JOIN 
        users_tb u 
    ON 
        r.user_id = u.user_id 
    WHERE 
        r.landholder_id = ?
");
$ratingsQuery->execute([$landholder_id]);
$ratings = $ratingsQuery->fetchAll(PDO::FETCH_ASSOC);
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


</head>

<body class="bg-gray-100 font-family-karla flex">

    <?php include 'landholder-header.php' ?>


    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex flex-wrap mt-6">
                <div class="w-full lg:w-1/2 pr-0 lg:pr-2">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-plus mr-3"></i> Your House Type Distribution
                    </p>
                    <div class="p-6 bg-white">
                        <canvas id="houseTypeChart" width="400" height="300"></canvas>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-check mr-3"></i> Property Date Listed
                    </p>
                    <div class="p-6 bg-white">
                        <canvas id="listingChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <h2 class="text-2xl pb-3">User Ratings and Comments</h2>
                <div class="grid grid-cols-1 gap-4">
                    <?php if (empty($ratings)): ?>
                        <p class="text-center text-gray-500">No ratings yet.</p>
                    <?php else: ?>
                        <?php foreach ($ratings as $rating): ?>
                            <div class="bg-white p-4 rounded-lg shadow-lg flex flex-col md:flex-row">
                                <img src="../uploaded_image/<?= htmlspecialchars($rating['profile_picture']); ?>" alt="User Image" class="w-16 h-16 rounded-full">
                                <div class="flex-1 p-4">
                                    <p class="text-lg font-bold"><?= htmlspecialchars($rating['user_name']); ?></p>
                                    <div class="flex items-center mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $rating['rating']): ?>
                                                <i class="fas fa-star text-yellow-500"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-gray-400"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-gray-600"><?= htmlspecialchars($rating['comment']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>


    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('listingChart').getContext('2d');
    var dataPoints = <?= json_encode($dataPoints) ?>;
    var dates = dataPoints.map(point => point.dateListed);
    var counts = dataPoints.map(point => point.count);

    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Listings Over Time',
                data: counts,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    beginAtZero: true
                }]
            }
        }
    });
});
</script>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('houseTypeChart').getContext('2d');
    var houseTypes = <?= json_encode($houseTypes) ?>;

    var labels = houseTypes.map(function(item) { return item.houseType; });
    var data = houseTypes.map(function(item) { return item.count; });

    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'House Type Distribution',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)', // Red
                    'rgba(54, 162, 235, 0.8)', // Blue
                    'rgba(255, 206, 86, 0.8)', // Yellow
                    'rgba(75, 100, 192, 0.8)', // Green
                    'rgba(153, 102, 255, 0.8)', // Purple
                    'rgba(255, 159, 64, 0.8)', // Orange
                    'rgba(199, 199, 199, 0.8)', // Grey
                    'rgba(83, 102, 255, 0.8)'  // Different shade of blue
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
            }
        }
    });
});
</script>

    <script>
        var chartOne = document.getElementById('chartOne');
        var myChart = new Chart(chartOne, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var chartTwo = document.getElementById('chartTwo');
        var myLineChart = new Chart(chartTwo, {
            type: 'line',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>

    <!-- JavaScript to display confirmation modal -->
    <?php if ($updateSuccess) : ?>
        <script>
            alert("Profile updated successfully!");
        </script>
    <?php endif; ?>
</body>

</html>