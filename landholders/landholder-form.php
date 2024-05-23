<?php
include '../components/connection.php';
session_start();

$landholder_id = $_SESSION['landholder_id'];

if (isset($_POST['add_product'])) {
    // Sanitize and validate input data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
    $zipCode = filter_var($_POST['zipCode'], FILTER_SANITIZE_STRING);
    $houseType = filter_var($_POST['houseType'], FILTER_SANITIZE_STRING);
    $bedroomNum = filter_var($_POST['bedroomNum'], FILTER_SANITIZE_NUMBER_INT);
    $bathroomNum = filter_var($_POST['bathroomNum'], FILTER_SANITIZE_NUMBER_INT);
    $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $rentAmount = filter_var($_POST['rentAmount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
    $landholder_id = filter_var($_POST['landholder_id'], FILTER_VALIDATE_INT);
    $latitude = filter_var($_POST['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_var($_POST['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Image upload handling
    $images = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    for ($i = 1; $i <= 5; $i++) {
        $imageKey = 'image0' . $i;
        if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] == 0) {
            $imageType = $_FILES[$imageKey]['type'];
            $imageSize = $_FILES[$imageKey]['size'];
            if (in_array($imageType, $allowedTypes) && $imageSize <= $maxSize) {
                $imagePath = '../uploaded_image/' . basename($_FILES[$imageKey]['name']);
                if (move_uploaded_file($_FILES[$imageKey]['tmp_name'], $imagePath)) {
                    $images[$imageKey] = $imagePath;
                } else {
                    $errors[] = "Failed to upload image $i.";
                }
            } else {
                $errors[] = "Invalid file type or size for image $i.";
            }
        }
    }

    if (empty($errors)) {
        $insertQuery = $conn->prepare("INSERT INTO `properties_tb` (landholder_id, name, address, city, state, zipCode, houseType, bedroomNum, bathroomNum, size, rentAmount, status, details, latitude, longitude, image01, image02, image03, image04, image05) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->execute([$landholder_id, $name, $address, $city, $state, $zipCode, $houseType, $bedroomNum, $bathroomNum, $size, $rentAmount, 'Pending', $details, $latitude, $longitude, $images['image01'] ?? null, $images['image02'] ?? null, $images['image03'] ?? null, $images['image04'] ?? null, $images['image05'] ?? null]);

        if ($insertQuery) {
            $message = "New property added successfully!";
        } else {
            $message = "Failed to add new property.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Property</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css">
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>



    <style>
        .file-input {
            height: 3.5rem;
            /* Adjust the height as needed */
            padding: 0.5rem 0.75rem;
            /* This is equivalent to Tailwind's px-3 py-2 */
            background-color: #f3f4f6;
            /* Tailwind's bg-gray-200 */
            border-radius: 0.375rem;
            /* Tailwind's rounded-md */
            border: 1px solid #d1d5db;
            /* Tailwind's border-gray-300 */
        }
    </style>
</head>

<body class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php'; ?>
    <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Add New Property</h1>
            <form action="" method="post" enctype="multipart/form-data" class="p-10 bg-white rounded shadow-xl">
                <input type="hidden" name="landholder_id" value="<?php echo $_SESSION['landholder_id']; ?>">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="name">Property Name:</label>
                        <input class="w-full px-5 py-4 text-gray-700 bg-gray-200 rounded" id="name" name="name" type="text" required placeholder="Name">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block text-sm text-gray-600" for="address">Address:</label>
                        <input class="w-full px-5 py-4 text-gray-700 bg-gray-200 rounded" id="address" name="address" type="text" required placeholder="Street Address">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="city">City:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="city" name="city" type="text" required placeholder="City">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="state">State:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="state" name="state" type="text" required placeholder="State">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="zipCode">Zip Code:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="zipCode" name="zipCode" type="text" required placeholder="Zip Code">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="houseType">House Type:</label>
                        <select class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="houseType" name="houseType" required>
                            <option value="">Select House Type</option>
                            <option value="House">House</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Dorm">Dorm</option>
                            <option value="Bedspace">Bedspace</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="bedroomNum">Number of Bedrooms:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="bedroomNum" name="bedroomNum" type="number" required placeholder="Number of Bedrooms">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="bathroomNum">Number of Bathrooms:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="bathroomNum" name="bathroomNum" type="number" required placeholder="Number of Bathrooms">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="size">Size (sqm):</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="size" name="size" type="text" required placeholder="Size in square meters">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="rentAmount">Rent Amount ($):</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="rentAmount" name="rentAmount" type="text" required placeholder="Rent Amount">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">

                    </div>
                </div>



                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-full px-3">
                        <label class="block text-sm text-gray-600" for="details">Property Description:</label>
                        <textarea class="w-full px-5 py-2 text-gray-700 bg-gray-200 rounded" id="details" name="details" rows="6" required placeholder="Describe the property"></textarea>
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="image01">Upload Image 1:</label>
                        <input type="file" name="image01" accept="image/*" class="file-input w-full text-gray-700" required>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="image02">Upload Image 2:</label>
                        <input type="file" name="image02" accept="image/*" class="file-input w-full text-gray-700" required>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="image03">Upload Image 3:</label>
                        <input type="file" name="image03" accept="image/*" class="file-input w-full text-gray-700" required>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="image04">Upload Image 4:</label>
                        <input type="file" name="image04" accept="image/*" class="file-input w-full text-gray-700" required>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="image05">Upload Image 5:</label>
                        <input type="file" name="image05" accept="image/*" class="file-input w-full text-gray-700" required>
                    </div>
                </div>
                <!-- Images and other inputs continue here -->
                <div class="mt-2">

                    <div id="map" style="height: 400px;"></div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>
                <div class="mt-6">
                    <button class="px-4 pt-3 pb-3 text-white font-light tracking-wider bg-blue-900 rounded" type="submit" name="add_product">Submit</button>
                    <button class="px-4 pt-3 pb-3 text-gray-900 font-light tracking-wider bg-gray-200 rounded" type="button" onclick="window.location.href='landholder-table.php'">Cancel</button>
                </div>
            </form>
            <!-- Button to pin current location -->
            <div class="flex justify-end mt-2">
                <button onclick="pinCurrentLocation()" class="text-white px-4 py-2 bg-blue-500 rounded hover:bg-blue-600">
                    Pin My Current Location
                </button>
            </div>
        </main>
    </div>

    <script>
        var map = L.map('map').setView([14.412903, 120.864249], 20);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initialize Geocoder control
        var geocoder = L.Control.geocoder({
            defaultMarkGeocode: false, // Do not add a marker automatically
            placeholder: 'Search for a location...', // Placeholder text in search input
            collapsed: false // Open search control by default
        }).on('markgeocode', function(e) {
            var latlng = e.geocode.center;
            map.setView(latlng, 14); // Set map view to the geocoded location
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }
        }).addTo(map);

        var marker;

        // Handle map click event
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });

        // Function to handle pinning current location
        function pinCurrentLocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latlng = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    if (marker) {
                        marker.setLatLng(latlng);
                    } else {
                        marker = L.marker(latlng).addTo(map);
                    }
                    map.setView(latlng, 16); // Zoom to the current location
                    document.getElementById('latitude').value = latlng.lat;
                    document.getElementById('longitude').value = latlng.lng;
                }, function(error) {
                    console.error("Error getting current location:", error.message);
                    alert("Unable to retrieve your location. Please try again.");
                });
            } else {
                alert("Geolocation is not supported by your browser.");
            }
        }
    </script>

    <!-- AlpineJS and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
</body>

</html>