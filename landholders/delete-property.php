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

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $update_query = $conn->prepare("UPDATE `properties_tb` SET is_deleted = 1, deleted_at = NOW() WHERE propertyId = ?");
    $update_query->execute([$property_id]);

    if ($update_query) {
        $_SESSION['message'] = "Property deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete property.";
    }
}

header("Location: landholder-table.php"); // Redirect back to the properties page
exit;
?>
