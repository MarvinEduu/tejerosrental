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
        r.comment,
        r.created_at,
        (SELECT COUNT(*) FROM bookings_tb WHERE user_id = r.user_id) AS userBookingCount,
        (SELECT p.name 
         FROM bookings_tb b 
         JOIN properties_tb p ON b.propertyId = p.propertyId 
         WHERE b.user_id = r.user_id 
         ORDER BY b.created_at DESC 
         LIMIT 1) AS latest_property_name
    FROM 
        user_ratings r 
    JOIN 
        users_tb u 
    ON 
        r.user_id = u.user_id 
    WHERE 
        r.landholder_id = ?
    ORDER BY r.created_at DESC
");
$ratingsQuery->execute([$landholder_id]);
$ratings = $ratingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate rating distribution
$ratingDistribution = array_fill(1, 5, 0);
foreach ($ratings as $rating) {
    $ratingDistribution[$rating['rating']]++;
}

function format_date($date)
{
    return date("F j, Y", strtotime($date));
}

// Fetch likes data for each property along with property names
$likesQuery = $conn->prepare("SELECT p.name AS propertyName, COUNT(*) as likes 
    FROM likes_tb l
    INNER JOIN properties_tb p ON l.propertyId = p.propertyId 
    WHERE l.propertyId IN (SELECT propertyId FROM properties_tb WHERE landholder_id = ?) 
    GROUP BY l.propertyId");
$likesQuery->execute([$landholder_id]);
$likesData = $likesQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate total likes count across all properties
$totalLikesQuery = $conn->prepare("SELECT COUNT(*) as totalLikes FROM likes_tb WHERE propertyId IN (SELECT propertyId FROM properties_tb WHERE landholder_id = ?)");
$totalLikesQuery->execute([$landholder_id]);
$totalLikesResult = $totalLikesQuery->fetch(PDO::FETCH_ASSOC);
$totalLikes = $totalLikesResult['totalLikes'];

// Fetch bookings data for each property along with property names
$bookingsQuery = $conn->prepare("SELECT p.name AS propertyName, COUNT(*) as bookings 
    FROM bookings_tb b
    INNER JOIN properties_tb p ON b.propertyId = p.propertyId 
    WHERE b.propertyId IN (SELECT propertyId FROM properties_tb WHERE landholder_id = ?) 
    GROUP BY b.propertyId");
$bookingsQuery->execute([$landholder_id]);
$bookingsData = $bookingsQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate total bookings count across all properties
$totalBookingsQuery = $conn->prepare("SELECT COUNT(*) as totalBookings FROM bookings_tb WHERE propertyId IN (SELECT propertyId FROM properties_tb WHERE landholder_id = ?)");
$totalBookingsQuery->execute([$landholder_id]);
$totalBookingsResult = $totalBookingsQuery->fetch(PDO::FETCH_ASSOC);
$totalBookings = $totalBookingsResult['totalBookings'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .rating-item {
            margin: 1rem 0;
        }
    </style>
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
                        <div id="houseTypeChart" class="w-full h-96"></div>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-check mr-3"></i> Property Date Listed
                    </p>
                    <div class="p-6 bg-white">
                        <div id="listingChart" class="w-full h-96"></div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap mt-6">
                <div class="w-full lg:w-1/2 pr-0 lg:pr-2">

                    <!-- Table for bookings for every properties -->
                    <!-- New Chart for Property Bookings -->
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-calendar-alt mr-3"></i> Property Bookings
                    </p>
                    <!-- Display total bookings -->
                    <div class="p-6 mb-2 bg-white">
                        <p class="text-md">Total Bookings Across Properties: <?= $totalBookings ?></p>
                    </div>
                    <div class="p-2 bg-white">
                        <div id="bookingsChart" class="w-full h-96"></div>
                    </div>

                </div>
                <div class="w-full lg:w-1/2 pl-0 lg:pl-2 mt-12 lg:mt-0">
                    <!-- Existing code for Property Date Listed chart -->

                    <!-- New Chart for Property Likes -->
                    <p class="text-2xl pb-3 flex items-center">
                        <i class="fas fa-thumbs-up mr-3"></i> Property Likes
                    </p>
                    <!-- Display total likes -->
                    <div class=" p-6 mb-2 bg-white">
                        <p class="text-md">Total Likes Across Properties: <?= $totalLikes ?></p>
                    </div>
                    <div class="p-2 bg-white">
                        <div id="likesChart" class="w-full h-96"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-2xl pb-3"><i class="fas fa-star text-yellow-500 mr-2"></i> User Ratings and Comments</h2>
                <div class="flex flex-wrap">
                    <div class="w-full lg:w-1/2 pr-0 lg:pr-2">
                        <div class="p-6 bg-white">
                            <div id="ratingsChart" class="w-full h-96"></div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 pl-0 lg:pl-2">
                        <div class="grid grid-cols-1 gap-4">
                            <?php if (empty($ratings)) : ?>
                                <p class="text-center text-gray-500">No ratings yet.</p>
                            <?php else : ?>
                                <?php for ($i = 0; $i < min(2, count($ratings)); $i++) : ?>
                                    <div class="bg-white p-4 rounded-lg shadow-md flex flex-col md:flex-row">
                                        <img src="../uploaded_image/<?= htmlspecialchars($ratings[$i]['profile_picture']); ?>" alt="User Image" class="w-20 h-20 rounded-full">
                                        <div class="flex-1 p-4">
                                            <div class="flex justify-between">
                                                <p class="text-lg font-bold"><?= htmlspecialchars($ratings[$i]['user_name']); ?></p>
                                                <p class="text-sm text-gray-500"><?= format_date($ratings[$i]['created_at']); ?></p>
                                            </div>
                                            <div class="flex items-center mb-2">
                                                <?php for ($j = 1; $j <= 5; $j++) : ?>
                                                    <?php if ($j <= $ratings[$i]['rating']) : ?>
                                                        <i class="fas fa-star text-yellow-500"></i>
                                                    <?php else : ?>
                                                        <i class="far fa-star text-gray-400"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                            <p class="text-gray-600"><?= htmlspecialchars($ratings[$i]['comment']); ?></p>
                                            <p class="text-sm text-gray-500">Bookings: <?= htmlspecialchars($ratings[$i]['userBookingCount']); ?> (Recent: <?= htmlspecialchars($ratings[$i]['latest_property_name']); ?>)</p>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                                <div class="text-right">
                                    <button class="bg-blue-500 text-white py-2 px-4 rounded" data-bs-toggle="modal" data-bs-target="#allRatingsModal">View All Ratings</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal for All Ratings -->
    <div class="modal fade" id="allRatingsModal" tabindex="-1" aria-labelledby="allRatingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allRatingsModalLabel">All Ratings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="allRatingsContainer">
                        <!-- Ratings will be loaded here by JavaScript with pagination -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var bookingsData = <?= json_encode($bookingsData) ?>;
            var propertyNames = bookingsData.map(item => item.propertyName);
            var bookingsCount = bookingsData.map(item => item.bookings);

            var bookingsChartOptions = {
                chart: {
                    type: 'bar',
                    height: '100%'
                },
                series: [{
                    name: 'Bookings',
                    data: bookingsCount
                }],
                xaxis: {
                    categories: propertyNames
                }
            };

            var bookingsChart = new ApexCharts(document.querySelector('#bookingsChart'), bookingsChartOptions);
            bookingsChart.render();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var likesData = <?= json_encode($likesData) ?>;
            var propertyNames = likesData.map(item => item.propertyName);
            var likesCount = likesData.map(item => item.likes);

            var likesChartOptions = {
                chart: {
                    type: 'bar',
                    height: '100%'
                },
                series: [{
                    name: 'Likes',
                    data: likesCount
                }],
                xaxis: {
                    categories: propertyNames
                }
            };

            var likesChart = new ApexCharts(document.querySelector('#likesChart'), likesChartOptions);
            likesChart.render();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var houseTypes = <?= json_encode($houseTypes) ?>;
            var labels = houseTypes.map(function(item) {
                return item.houseType;
            });
            var data = houseTypes.map(function(item) {
                return item.count;
            });

            var houseTypeChartOptions = {
                chart: {
                    type: 'pie',
                    height: '100%'
                },
                series: data,
                labels: labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var houseTypeChart = new ApexCharts(document.querySelector('#houseTypeChart'), houseTypeChartOptions);
            houseTypeChart.render();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var dataPoints = <?= json_encode($dataPoints) ?>;
            var dates = dataPoints.map(point => point.dateListed);
            var counts = dataPoints.map(point => point.count);

            var listingChartOptions = {
                chart: {
                    type: 'area',
                    height: '100%'
                },
                series: [{
                    name: 'Listings Over Time',
                    data: counts
                }],
                xaxis: {
                    categories: dates
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        }
                    }
                }]
            };

            var listingChart = new ApexCharts(document.querySelector('#listingChart'), listingChartOptions);
            listingChart.render();
        });

        document.addEventListener('DOMContentLoaded', function() {
            var ratings = <?= json_encode($ratings) ?>;
            var ratingsChartData = Array.from({
                length: 5
            }, () => 0); // Initialize array for ratings count

            // Count the number of ratings for each value (1 to 5)
            ratings.forEach(rating => {
                ratingsChartData[rating.rating - 1]++;
            });

            var ratingsChartOptions = {
                chart: {
                    type: 'bar',
                    height: '100%'
                },
                series: [{
                    name: 'Ratings',
                    data: ratingsChartData
                }],
                xaxis: {
                    categories: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars']
                }
            };

            var ratingsChart = new ApexCharts(document.querySelector('#ratingsChart'), ratingsChartOptions);
            ratingsChart.render();
        });

        // JavaScript to display all ratings in the modal with pagination
        document.addEventListener('DOMContentLoaded', function() {
            var ratings = <?= json_encode($ratings) ?>;
            var modalBody = document.getElementById('allRatingsContainer');
            var ratingsPerPage = 5;
            var totalPages = Math.ceil(ratings.length / ratingsPerPage);
            var currentPage = 1;

            function displayRatings(page) {
                var startIndex = (page - 1) * ratingsPerPage;
                var endIndex = Math.min(startIndex + ratingsPerPage, ratings.length);
                var ratingsHTML = '';
                for (var i = startIndex; i < endIndex; i++) {
                    ratingsHTML += `
                        <div class=" my-3 bg-white p-4 rounded-lg shadow-lg flex flex-col md:flex-row">
                            <img src="../uploaded_image/${ratings[i].profile_picture}" alt="User Image" class="w-16 h-16 rounded-full">
                            <div class="flex-1 p-4">
                                <div class="flex justify-between">
                                    <p class="text-lg font-bold">${ratings[i].user_name}</p>
                                    <p class="text-sm text-gray-500">${formatDate(ratings[i].created_at)}</p>
                                </div>
                                <div class="flex items-center mb-2">
                                    ${Array.from({ length: 5 }, (_, index) => ` <i class = "fas fa-star ${index < ratings[i].rating ? 'text-yellow-500' : 'far fa-star text-gray-400'}" > </i>
                    `).join('')}
                                </div>
                                <p class="text-gray-600">${ratings[i].comment}</p>
                                <p class="text-sm text-gray-500">Bookings: ${ratings[i].userBookingCount} (Recent: ${ratings[i].latest_property_name})</p>
                            </div>
                        </div>
                    `;
                }
                modalBody.innerHTML = ratingsHTML;
                updatePagination(page);
            }

            function updatePagination(currentPage) {
                var paginationHTML = '';
                for (var i = 1; i <= totalPages; i++) {
                    paginationHTML += `<li class="page-item ${currentPage === i ? 'active' : ''}"><button class="page-link" onclick="displayRatings(${i})">${i}</button></li>`;
                }
                var paginationContainer = document.querySelector('.pagination');
                if (!paginationContainer) {
                    paginationContainer = document.createElement('ul');
                    paginationContainer.className = 'pagination justify-content-center mt-4';
                    modalBody.insertAdjacentElement('afterend', paginationContainer);
                }
                paginationContainer.innerHTML = paginationHTML;
            }

            displayRatings(currentPage);
        });

        function formatDate(dateString) {
            var options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        // JavaScript to display confirmation modal
        <?php if ($updateSuccess) : ?>
            alert("Profile updated successfully!");
        <?php endif; ?>
    </script>
</body>

</html>