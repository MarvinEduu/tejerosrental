<!DOCTYPE html>
<html lang="en" data-theme="winter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Update Property</title>
</head>
<body>

</body>
</html>

<?php
include '../components/connection.php';

session_start();
$landholder_id = $_SESSION['landholder_id'];

// Ensure the form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyId = $_POST['propertyId'];
    $name = $_POST['name'];
    $houseType = $_POST['houseType'];
    $bedroomNum = $_POST['bedroomNum'];
    $bathroomNum = $_POST['bathroomNum'];
    $size = $_POST['size'];
    $rentAmount = $_POST['rentAmount'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Array to store image file names
    $imageFields = ['image01', 'image02', 'image03', 'image04', 'image05'];
    $uploadedImages = [];

    foreach ($imageFields as $imageField) {
        if (!empty($_FILES[$imageField]['name'])) {
            $imageName = time() . '_' . $_FILES[$imageField]['name'];
            $imageTempName = $_FILES[$imageField]['tmp_name'];
            $imageUploadPath = '../uploaded_image/' . $imageName;

            if (move_uploaded_file($imageTempName, $imageUploadPath)) {
                $uploadedImages[$imageField] = $imageName;
            } else {
                echo "Failed to upload image: " . $_FILES[$imageField]['name'];
                exit;
            }
        }
    }

    // Update query
    $updateQuery = "UPDATE `properties_tb` SET 
                    name = :name, 
                    houseType = :houseType, 
                    bedroomNum = :bedroomNum, 
                    bathroomNum = :bathroomNum, 
                    size = :size, 
                    rentAmount = :rentAmount, 
                    latitude = :latitude,
                    longitude = :longitude";

    // Add image fields to the update query if new images are uploaded
    foreach ($uploadedImages as $imageField => $imageName) {
        $updateQuery .= ", $imageField = :$imageField";
    }

    $updateQuery .= " WHERE propertyId = :propertyId AND landholder_id = :landholder_id";

    $stmt = $conn->prepare($updateQuery);

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':houseType', $houseType);
    $stmt->bindParam(':bedroomNum', $bedroomNum);
    $stmt->bindParam(':bathroomNum', $bathroomNum);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':rentAmount', $rentAmount);
    $stmt->bindParam(':latitude', $latitude);
    $stmt->bindParam(':longitude', $longitude);
    $stmt->bindParam(':propertyId', $propertyId);
    $stmt->bindParam(':landholder_id', $landholder_id);

    // Bind image parameters if new images are uploaded
    foreach ($uploadedImages as $imageField => $imageName) {
        $stmt->bindParam(':' . $imageField, $imageName);
    }

    // Execute the update query
    if ($stmt->execute()) {
        echo '<script>
        // Show loading animation
        Swal.fire({
            title: "Loading...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Delay the actual popup
        setTimeout(() => {
            Swal.fire({
                icon: "success",
                title: "Property Successfully Updated.",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = "landholder-table.php";
            });
        }, 2000); // 3000 milliseconds (3 seconds) delay
      </script>';
        exit;
    } else {
        echo "Error updating property.";
    }
}
?>
