<?php
include '../components/connection.php';
session_start();

if (!isset($_SESSION['landholder_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$landholder_id = $_SESSION['landholder_id'];
$property_id = $_GET['id'] ?? '';

if (empty($property_id)) {
    echo "Property ID is missing.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `properties_tb` WHERE landholder_id = :landholder_id AND propertyId = :property_id AND is_deleted = 0");
$stmt->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);
$stmt->bindParam(':property_id', $property_id, PDO::PARAM_INT);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "Property not found or you do not have permission to edit this property.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_property'])) {
    // Retrieve form data, using existing property values if form fields are empty
    $name = $_POST['name'] ?: $property['name'];
    $address = $_POST['address'] ?: $property['address'];
    $city = $_POST['city'] ?: $property['city'];
    $state = $_POST['state'] ?: $property['state'];
    $zipCode = $_POST['zipCode'] ?: $property['zipCode'];
    $houseType = $_POST['houseType'] ?: $property['houseType'];
    $bedroomNum = $_POST['bedroomNum'] ?: $property['bedroomNum'];
    $bathroomNum = $_POST['bathroomNum'] ?: $property['bathroomNum'];
    $size = $_POST['size'] ?: $property['size'];
    $rentAmount = $_POST['rentAmount'] ?: $property['rentAmount'];
    $status = $_POST['status'] ?: $property['status'];
    $details = $_POST['details'] ?: $property['details'];
    $latitude = $_POST['latitude'] ?: $property['latitude'];
    $longitude = $_POST['longitude'] ?: $property['longitude'];

    // Update property in the database
    $update_query = $conn->prepare("UPDATE `properties_tb` SET name=?, address=?, city=?, state=?, zipCode=?, houseType=?, bedroomNum=?, bathroomNum=?, size=?, rentAmount=?, status=?, details=?, latitude=?, longitude=? WHERE propertyId=?");
    $result = $update_query->execute([$name, $address, $city, $state, $zipCode, $houseType, $bedroomNum, $bathroomNum, $size, $rentAmount, $status, $details, $latitude, $longitude, $pid]);
    
    if ($result) {
        $_SESSION['message'] = 'Property updated successfully!';
        header('Location: landholder-table.php');
        exit();
    } else {
        echo "<script>alert('Failed to update property.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Property</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php'; ?>   
    <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Update Property</h1>
            <form action="" method="POST" enctype="multipart/form-data" class="p-10 bg-white rounded shadow-xl">
                <input type="hidden" name="pid" value="<?= htmlspecialchars($pid); ?>">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="name">Property Name:</label>
                        <input type="text" class="w-full px-5 py-4 text-gray-700 bg-gray-200 rounded" id="name" name="name" value="<?= htmlspecialchars($property['name'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block text-sm text-gray-600" for="address">Address:</label>
                        <input type="text" class="w-full px-5 py-4 text-gray-700 bg-gray-200 rounded" id="address" name="address" value="<?= htmlspecialchars($property['address'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="city">City:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="city" name="city" type="text" value="<?= htmlspecialchars($property['city'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="state">State:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="state" name="state" type="text" value="<?= htmlspecialchars($property['state'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                        <label class="block text-sm text-gray-600" for="zipCode">Zip Code:</label>
                        <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="zipCode" name="zipCode" type="text" value="<?= htmlspecialchars($property['zipCode'] ?? '') ?>">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="houseType">House Type:</label>
                    <select class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="houseType" name="houseType">
                        <option value="" <?= !isset($property['houseType']) ? 'selected' : '' ?>>Select House Type</option>
                        <option value="House" <?= $property['houseType'] === 'House' ? 'selected' : '' ?>>House</option>
                        <option value="Apartment" <?= $property['houseType'] === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                        <option value="Condo" <?= $property['houseType'] === 'Condo' ? 'selected' : '' ?>>Condo</option>
                        <option value="Townhouse" <?= $property['houseType'] === 'Townhouse' ? 'selected' : '' ?>>Townhouse</option>
                    </select>
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="bedroomNum">Number of Bedrooms:</label>
                    <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="bedroomNum" name="bedroomNum" type="number" value="<?= htmlspecialchars($property['bedroomNum'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="bathroomNum">Number of Bathrooms:</label>
                    <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="bathroomNum" name="bathroomNum" type="number" value="<?= htmlspecialchars($property['bathroomNum'] ?? '') ?>">
                    </div>
                </div>

                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="size">Size (sqm):</label>
                    <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="size" name="size" type="text" value="<?= htmlspecialchars($property['size'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="rentAmount">Rent Amount ($):</label>
                    <input class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="rentAmount" name="rentAmount" type="text" value="<?= htmlspecialchars($property['rentAmount'] ?? '') ?>">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="status">Status:</label>
                    <select class="w-full px-5 py-3 text-gray-700 bg-gray-200 rounded" id="status" name="status">
                        <option value="" <?= !isset($property['status']) ? 'selected' : '' ?>>Select Status</option>
                        <option value="Available" <?= $property['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Sold Out" <?= $property['status'] === 'Sold Out' ? 'selected' : '' ?>>Sold Out</option>
                    </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-full px-3">
                        <label class="block text-sm text-gray-600" for="details">Property Description:</label>
                        <textarea class="w-full px-5 py-2 text-gray-700 bg-gray-200 rounded" id="details" name="details" rows="6" placeholder="Describe the property"><?= htmlspecialchars($property['details'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="image01">Upload Image 1:</label>
                    <input type="file" name="image01" accept="image/*" class="file-input w-full text-gray-700">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="image02">Upload Image 2:</label>
                    <input type="file" name="image02" accept="image/*" class="file-input w-full text-gray-700">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="image03">Upload Image 3:</label>
                    <input type="file" name="image03" accept="image/*" class="file-input w-full text-gray-700">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="image04">Upload Image 4:</label>
                    <input type="file" name="image04" accept="image/*" class="file-input w-full text-gray-700">
                    </div>
                    <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block text-sm text-gray-600" for="image05">Upload Image 5:</label>
                    <input type="file" name="image05" accept="image/*" class="file-input w-full text-gray-700">
                    </div>
                </div>

                <div class="mt-2">
                    <div id="map" style="height: 400px;"></div>
                    <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($property['latitude'] ?? '') ?>">
                    <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($property['longitude'] ?? '') ?>">
                </div>
                <div class="mt-6">
                    <button class="px-4 py-4 text-white font-light tracking-wider bg-gray-900 rounded" type="submit" name="update_property">Update Property</button>
                </div>
            </form>
        </main>
    </div>
    <script>
        var map = L.map('map').setView([<?= htmlspecialchars($property['latitude'] ?? 14.412903) ?>, <?= htmlspecialchars($property['longitude'] ?? 120.864249) ?>], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        var marker = L.marker([<?= htmlspecialchars($property['latitude'] ?? 14.412903) ?>, <?= htmlspecialchars($property['longitude'] ?? 120.864249) ?>]).addTo(map);

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
    </script>
    <!-- AlpineJS and Font Awesome -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
</body>
</html>
