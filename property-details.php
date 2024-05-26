<?php
include 'components/connection.php';

session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit; // Stop further script execution
}

// Fetch property details from the database
$property = null;
$landholder = null;
if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $select_property = $conn->prepare("SELECT * FROM `properties_tb` WHERE propertyId = ? AND status != 'Pending' AND status != 'Rejected'  AND is_deleted = 0");
    $select_property->execute([$pid]);
    $property = $select_property->fetch(PDO::FETCH_ASSOC);

    // Fetch landholder details
    if ($property && isset($property['landholder_id'])) {
        $landholderId = $property['landholder_id'];
        $select_landholder = $conn->prepare("SELECT * FROM `landholders_tb` WHERE landholder_id = ?");
        $select_landholder->execute([$landholderId]);
        $landholder = $select_landholder->fetch(PDO::FETCH_ASSOC);
    }
}

$latitude = (float) ($property['latitude'] ?? 0);
$longitude = (float) ($property['longitude'] ?? 0);

// Assuming $property['houseType'] contains the type of the current property
$similar_properties = [];
if ($property) {
    $houseType = $property['houseType'];
    $query_similar = $conn->prepare("SELECT * FROM `properties_tb` WHERE houseType = ? AND propertyId != ? AND status != 'Pending' AND status != 'Rejected'  AND is_deleted = 0 LIMIT 3");
    $query_similar->execute([$houseType, $pid]);
    $similar_properties = $query_similar->fetchAll(PDO::FETCH_ASSOC);
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_property'])) {
    $userId = $_SESSION['user_id'];
    $propertyId = $property['propertyId'];

    // Check if the user has already liked the property
    $checkLikeQuery = $conn->prepare("SELECT * FROM likes_tb WHERE user_id = ? AND propertyId = ?");
    $checkLikeQuery->execute([$userId, $propertyId]);

    if ($checkLikeQuery->rowCount() > 0) {
        // User has already liked the property, so unlike it
        $unlikeQuery = $conn->prepare("DELETE FROM likes_tb WHERE user_id = ? AND propertyId = ?");
        $unlikeQuery->execute([$userId, $propertyId]);
    } else {
        // User has not liked the property yet, so like it
        $likeQuery = $conn->prepare("INSERT INTO likes_tb (user_id, propertyId) VALUES (?, ?)");
        $likeQuery->execute([$userId, $propertyId]);
    }

    // Refresh the page to reflect the changes
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Check if the user has already liked the property
$userId = $_SESSION['user_id'];
$propertyId = $property['propertyId'];

$checkLikeQuery = $conn->prepare("SELECT * FROM likes_tb WHERE user_id = ? AND propertyId = ?");
$checkLikeQuery->execute([$userId, $propertyId]);

if ($checkLikeQuery->rowCount() > 0) {
    // User has already liked the property, display unlike button
    $likeButton = '
    <button type="submit" name="like_property" class="btn btn-success hover:bg-red-600 text-white px-4 py-2 rounded-md">
        <i class="bx bxs-like"></i> Liked
    </button>';
} else {
    // User has not liked the property yet, display like button
    $likeButton = '
    <button type="submit" name="like_property" class="btn btn-primary hover:bg-green-600 text-white px-4 py-2 rounded-md">
        <i class="bx bxs-like"></i> Like
    </button>';
}

// Count the number of likes for the property
$countLikesQuery = $conn->prepare("SELECT COUNT(*) AS like_count FROM likes_tb WHERE propertyId = ?");
$countLikesQuery->execute([$propertyId]);
$likeCountResult = $countLikesQuery->fetch(PDO::FETCH_ASSOC);
$likeCount = $likeCountResult['like_count'];

// Append the number of likes to the button
$likeButton .= " ($likeCount)";

if ($property && isset($property['landholder_id'])) {
    $landholderId = $property['landholder_id'];
    $select_landholder = $conn->prepare("
SELECT lh.*,
COALESCE(AVG(r.rating), 0) AS average_rating,
COUNT(r.rating) AS rating_count
FROM `landholders_tb` lh
LEFT JOIN `user_ratings` r ON lh.landholder_id = r.landholder_id
WHERE lh.landholder_id = ?
");
    $select_landholder->execute([$landholderId]);
    $landholder = $select_landholder->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Capstone</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/logoer.png">
    <script src="https://unpkg.com/leaflet.polyline.snakeanim/L.Polyline.SnakeAnim.js"></script>

    <style>
        #establishments-list {
            padding: 10px;

            max-height: auto;
            overflow-y: auto;
        }

        #establishments-list div {
            margin-bottom: 5px;
            cursor: pointer;
        }

        #establishments-list div:hover {
            background-color: #f0f0f0;
        }
    </style>

</head>

<body>
    <?php include 'user/user-header.php'; ?>

    <div class=" py-6 sm:px-6 lg:px-8">
        <div class="flex justify-between items-start">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-3/4">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="flex justify-between items-center">

                        <div>
                            <div class="text-md mb-4 breadcrumbs">
                                <ul>
                                    <li><a href="loading-page-in.php"><i class='bx bxs-home bx-sm'></i>&nbsp;&nbsp; Home</a></li>
                                    <li><a href="properties.php">Properties</a></li>
                                    <li><?= htmlspecialchars($property['name']); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex items-center mb-4">

                            <a href="booking.php?pid=<?= htmlspecialchars($pid); ?>" class="btn btn-neutral hover:bg-blue-600 text-white px-4 px-2 mr-2 py-2 rounded-md">Book Property</a>

                            <!-- Like Button Form -->
                            <form method="post" class="mr-4">
                                <?php echo $likeButton; ?>
                            </form>

                            <!-- Report Button -->
                            <button type="button" class="btn btn-error text-white" id="reportButton">
                                <i class='bx bx-flag'></i> <!-- Icon for reporting -->
                            </button>

                        </div>

                    </div>




                    <!-- Report Modal -->
                    <div class="fixed inset-0 z-50 overflow-auto hidden" id="reportModal">
                        <div class="flex items-center justify-center min-h-screen">
                            <div class="bg-white w-full md:w-1/2 p-8 rounded shadow-lg" x-show="openModal">
                                <div class="text-right">
                                    <button class="text-gray-500 hover:text-gray-700 close-button">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                </div>
                                <h2 class="text-xl font-bold mb-4">Report Property</h2>
                                <form method="post" action="handle_report.php">
                                    <input type="hidden" name="propertyId" value="<?= $property['propertyId'] ?>"> <!-- Replace with dynamic property ID -->
                                    <div class="mb-4">
                                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Reporting</label>
                                        <textarea id="reason" name="reason" rows="3" class="form-textarea mt-1 block w-full border border-gray-500 rounded-md" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-red w-full">Submit Report</button>
                                </form>
                            </div>

                        </div>

                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const reportButton = document.getElementById('reportButton');
                            const reportModal = document.getElementById('reportModal');
                            const closeButton = reportModal.querySelector('.close-button');

                            reportButton.addEventListener('click', function() {
                                reportModal.classList.remove('hidden');
                            });

                            closeButton.addEventListener('click', function() {
                                reportModal.classList.add('hidden');
                            });
                        });
                    </script>


                    <!-- Image Slider -->
                    <?php if ($property && isset($property['image01'])) : ?>
                        <div class="relative">
                            <!-- Main Image -->
                            <img src="uploaded_image/<?= htmlspecialchars($property['image01']); ?>" alt="<?= htmlspecialchars($property['name']); ?>" class="w-full h-auto md:h-96 object-cover mb-4 rounded-md" onclick="updateModalImage('uploaded_image/<?= htmlspecialchars($property['image01']); ?>')">

                            <!-- Thumbnails -->
                            <div class="grid grid-cols-5 gap-4 mt-8">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php $imageKey = "image0$i"; ?>
                                    <?php if (isset($property[$imageKey])) : ?>
                                        <img src="uploaded_image/<?= htmlspecialchars($property[$imageKey]); ?>" alt="Sub Image <?= $i; ?>" class="w-64 h-56 object-cover cursor-pointer rounded-md" onclick="updateModalImage('uploaded_image/<?= htmlspecialchars($property[$imageKey]); ?>')">
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- DaisyUI Modal -->
                        <!-- Modal Structure -->
                        <div id="imageModal" class="modal" tabindex="-1" aria-modal="true">
                            <div class="modal-box relative">

                                <!-- Navigation Arrows -->
                                <div class="absolute inset-y-0 left-0 flex items-center m-4">
                                    <button class="btn btn-circle " onclick="changeImage(-1)"> <i class='bx bxs-chevrons-left bx-sm'> </i></button>
                                </div>
                                <div class="absolute inset-y-0 right-0 flex items-center m-4">
                                    <button class="btn btn-circle" onclick="changeImage(1)"><i class='bx bxs-chevrons-right bx-sm'></i></button>
                                </div>

                                <!-- Image Display -->
                                <img id="modalImage" src="" alt="Property Image" class="max-h-[80vh] mx-auto">
                            </div>
                        </div>

                    <?php endif; ?>


                    <!-- Property Details -->
                    <?php if ($property) : ?>
                        <div class="mt-8">
                            <p class="text-gray-600 mb-4 text-justify "><span class="font-bold">Description: </span><br> <?= nl2br(htmlspecialchars($property['details'])); ?></p>
                            <p class="text-gray-600 mb-4"><span class="font-bold text-lg">Location:</span><br> <?= htmlspecialchars($property['address'] . ' ' . $property['city'] . ', ' . $property['state']); ?></p>
                            <div class="flex flex-wrap items-center mb-4">
                                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                                    <p class="font-bold">Type of House:</p>
                                    <p><?= htmlspecialchars($property['houseType']); ?></p>
                                </div>
                                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                                    <p class="font-bold">Bedrooms:</p>
                                    <p><?= htmlspecialchars($property['bedroomNum']); ?></p>
                                </div>
                                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                                    <p class="font-bold">Bathrooms:</p>
                                    <p><?= htmlspecialchars($property['bathroomNum']); ?></p>
                                </div>
                                <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
                                    <p class="font-bold">Size (sqm):</p>
                                    <p><?= htmlspecialchars($property['size']); ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="font-bold text-lg">Price:</p>
                                <p>₱ <?= number_format($property['rentAmount']); ?> *price may change depending on finalization.</p>

                            </div>
                            <div id="map" style="height: 500px; border-radius:20px; margin-top:20px;"></div>
                            <button id="navigateToProperty" class="btn btn-primary hover:bg-green-600 text-white px-4 py-2 mt-2 rounded-md">Navigate Property through Google Map</button>

                            <h2 class="text-lg font-bold my-8"><i class='bx bx-buildings'></i> Nearby Establishments around <?= htmlspecialchars($property['name']); ?></h2>
                            <div id="establishments-list" style="height: auto; border-radius: 20px;"></div>
                        </div>
                    <?php else : ?>
                        <p class="text-lg text-gray-600">Property not found.</p>
                    <?php endif; ?>
                </div>


            </div>

            <!-- Landholder Information -->
            <?php if ($landholder) : ?>
                <div class="bg-white p-6 rounded-lg shadow-md w-1/4 ml-4">
                    <h2 class="text-2xl font-bold mb-4">Landholder Details</h2>
                    <div class="flex items-center space-x-4">
                        <?php if (!empty($landholder['profile_picture'])) : ?>
                            <img src="uploaded_image/<?= htmlspecialchars($landholder['profile_picture']); ?>" alt="<?= htmlspecialchars($landholder['full_name']); ?>" class="w-16 h-16 rounded-full">
                        <?php endif; ?>
                        <div>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($landholder['full_name']); ?></p>
                            <!-- Display Rating -->
                            <div class="flex items-center">
                                <?php
                                $averageRating = round($landholder['average_rating']);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $averageRating) {
                                        echo '<i class="fas fa-star text-yellow-500"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-gray-400"></i>';
                                    }
                                }
                                ?>
                                <span class="ml-2 text-gray-600">(<?= htmlspecialchars($landholder['rating_count']); ?>)</span>
                            </div>
                            <!-- Display Verification Tier -->
                            <p class="mt-2 text-gray-800"> <?= htmlspecialchars($landholder['verification_tier']); ?></p>
                        </div>
                    </div>
                    <form class="mt-4">
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600">Mobile:</label>
                            <p class="text-gray-800"><?= htmlspecialchars($landholder['mobile']); ?></p>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600">Address:</label>
                            <p class="text-gray-800"><?= htmlspecialchars($landholder['address']); ?></p>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600">Email:</label>
                            <p class="text-gray-800"><?= htmlspecialchars($landholder['email']); ?></p>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-600">Bio: </label>
                            <p class="text-gray-800 text-justify"><?= htmlspecialchars($landholder['bio']); ?></p>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <a href="chat.php?receiver_id=<?= htmlspecialchars($landholder['landholder_id']); ?>" class="text-blue-500 hover:text-blue-700 flex items-center">
                                <i class="far fa-comment-dots mr-2"></i> Chat
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>




        </div>

        <!-- Similar Properties Section -->
        <?php if (!empty($similar_properties)) : ?>
            <div class="mt-8">
                <h3 class="text-2xl font-bold mb-4">Similar Properties</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($similar_properties as $sim_prop) : ?>
    <form action="" method="post" class="box bg-white rounded-lg shadow-md p-6 border border-gray-300">
        <input type="hidden" name="pid" value="<?= htmlspecialchars($sim_prop['propertyId']); ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($sim_prop['name']); ?>">
        <input type="hidden" name="price" value="<?= htmlspecialchars($sim_prop['rentAmount']); ?>">
        <input type="hidden" name="bathroomNum" value="<?= htmlspecialchars($sim_prop['bathroomNum']); ?>">
        <input type="hidden" name="image" value="<?= htmlspecialchars($sim_prop['image01']); ?>">
        <input type="hidden" name="size" value="<?= htmlspecialchars($sim_prop['size']); ?>">
        <a href="property-details.php?pid=<?= htmlspecialchars($sim_prop['propertyId']); ?>" class="block">
            <img src="uploaded_image/<?= htmlspecialchars($sim_prop['image01']); ?>" alt="<?= htmlspecialchars($sim_prop['name']); ?>" class="w-full h-64 object-cover rounded-t-lg">
            <div class="px-2 py-2">
                <div class="flex justify-between items-center">
                    <div class="price text-lg font-semibold text-gray-800"><?= htmlspecialchars($sim_prop['name']); ?></div>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <div class="name text-gray-600 flex items-center">
                        <i class='bx bx-map-alt bx-sm'></i>&nbsp; <?= htmlspecialchars($sim_prop['city']); ?>, <?= htmlspecialchars($sim_prop['houseType']); ?>
                    </div>
                    <div class="flex items-center">
                        <i class='bx bx-bath mr-1 bx-sm'></i>&nbsp;
                        <span><?= htmlspecialchars($sim_prop['bathroomNum']); ?></span>
                        <i class='bx bx-bed ml-2 mr-1 bx-sm'></i>&nbsp;
                        <span><?= htmlspecialchars($sim_prop['bedroomNum']); ?></span>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <div class="status text-gray-600 flex items-center">
                        <i class='bx bx-coin-stack mr-1 bx-sm'></i>&nbsp;₱ <?= number_format($sim_prop['rentAmount'], 0, '.', ','); ?>
                    </div>
                    <div class="size text-gray-600 flex items-center">
                        <i class='bx bx-ruler mr-1 bx-sm'></i>&nbsp;<?= htmlspecialchars($sim_prop['size']); ?> sqm
                    </div>
                </div>
            </div>
        </a>
    </form>
<?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>


    </div>
    </div>
    </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mapElement = document.getElementById('map');
            var latitude = parseFloat(<?= json_encode($latitude) ?>);
            var longitude = parseFloat(<?= json_encode($longitude) ?>);

            if (mapElement && !isNaN(latitude) && !isNaN(longitude)) {
                // Initialize Leaflet map
                var map = L.map('map').setView([latitude, longitude], 20);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Add a marker for the specified location
                var propertyMarker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup('<?= addslashes($property['name']) ?>').openPopup();

                // Fetch nearby establishments
                var overpassUrl = 'https://overpass-api.de/api/interpreter';
                var radius = 100; // Radius in meters for nearby search

                fetch(`${overpassUrl}?data=[out:json];node(around:${radius},${latitude},${longitude})[amenity];out;`)
                    .then(response => response.json())
                    .then(data => {
                        var establishmentsList = document.getElementById('establishments-list');
                        establishmentsList.innerHTML = ''; // Clear previous content

                        if (data.elements.length === 0) {
                            // No establishments nearby
                            var noEstablishmentsMessage = document.createElement('div');
                            noEstablishmentsMessage.textContent = 'No establishments nearby';
                            establishmentsList.appendChild(noEstablishmentsMessage);
                        } else {
                            // Display nearby establishments
                            data.elements.forEach(element => {
                                if (element.tags.name) {
                                    var establishmentName = element.tags.name;
                                    var latlng = [element.lat, element.lon];
                                    L.marker(latlng).addTo(map)
                                        .bindPopup(establishmentName);

                                    // Append establishment to the list
                                    var establishmentItem = document.createElement('div');
                                    establishmentItem.textContent = establishmentName;
                                    establishmentsList.appendChild(establishmentItem);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                    });

                // Function to navigate to property on Google Maps
                function navigateToProperty() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var userLatitude = position.coords.latitude;
                            var userLongitude = position.coords.longitude;

                            var googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLatitude},${userLongitude}&destination=${latitude},${longitude}&travelmode=driving`;
                            window.open(googleMapsUrl, '_blank');
                        }, function(error) {
                            alert('Error getting location: ' + error.message);
                        });
                    } else {
                        alert('Geolocation is not supported by this browser.');
                    }
                }

                // Add event listener to the button
                document.getElementById('navigateToProperty').addEventListener('click', function() {
                    navigateToProperty();
                });
            } else {
                console.log("Map element or coordinates are not available.");
            }
        });
    </script>


    <script>
        let currentImageIndex = 0;
        const imageSources = [
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <?php $imageKey = "image0$i"; ?>
                <?php if (isset($property[$imageKey])) : ?> "uploaded_image/<?= htmlspecialchars($property[$imageKey]); ?>",
                <?php endif; ?>
            <?php endfor; ?>
        ];

        function updateModalImage(src) {
            document.getElementById('modalImage').src = src;
            currentImageIndex = imageSources.indexOf(src);
            openModal('imageModal'); // Ensure the modal is opened
        }

        function changeImage(direction) {
            currentImageIndex += direction;
            if (currentImageIndex >= imageSources.length) {
                currentImageIndex = 0;
            } else if (currentImageIndex < 0) {
                currentImageIndex = imageSources.length - 1;
            }
            document.getElementById('modalImage').src = imageSources[currentImageIndex];
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('modal-open');

            // Add click listener to close modal on outside clicks
            modal.addEventListener('click', function(event) {
                // Check if the click was outside the modal image
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }

        function closeModal(modalId) {
            console.log("Closing modal:", modalId);
            document.getElementById(modalId).classList.remove('modal-open');
        }
    </script>


    <?php include 'user/user-footer.php'; ?>
</body>

</html>