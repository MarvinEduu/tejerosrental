<?php
include '../components/connection.php';

// Check if property ID is provided in the URL
if (isset($_GET['id'])) {
    // Retrieve property details from the database based on the provided property ID
    $propertyId = $_GET['id'];
    $query = "SELECT * FROM properties_tb WHERE propertyId = :propertyId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':propertyId', $propertyId, PDO::PARAM_INT);
    $stmt->execute();
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($property) {
        // Retrieve property owner details from the database
        $queryOwner = "SELECT * FROM landholders_tb WHERE landholder_id = :landholder_id";
        $stmtOwner = $conn->prepare($queryOwner);
        $stmtOwner->bindParam(':landholder_id', $property['landholder_id'], PDO::PARAM_INT);
        $stmtOwner->execute();
        $owner = $stmtOwner->fetch(PDO::FETCH_ASSOC);

        // Output the property details in a printable format
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Print Property Details</title>
            <!-- Include Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../css/style.css">
            <style>
                body {
                    font-family: Arial, sans-serif;
                }
                .container {
                    padding: 20px;
                }
                h1 {
                    font-size: 24px;
                    margin-bottom: 20px;
                }
                ul {
                    padding-left: 0;
                    list-style-type: none;
                }
                li {
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1 class="mb-4">Property Details</h1>
                <ul class="mb-4">
                    <li><strong>Name:</strong> <?= htmlspecialchars($property['name']) ?></li>
                    <li><strong>Type:</strong> <?= htmlspecialchars($property['houseType']) ?></li>
                    <li><strong>Bedrooms:</strong> <?= htmlspecialchars($property['bedroomNum']) ?></li>
                    <li><strong>Bathrooms:</strong> <?= htmlspecialchars($property['bathroomNum']) ?></li>
                    <li><strong>Size:</strong> <?= htmlspecialchars($property['size']) ?> sqm</li>
                    <li><strong>Price:</strong> ₱ <?= number_format($property['rentAmount']) ?></li>
                    <li><strong>Description:</strong> <?= htmlspecialchars($property['details']) ?></li>
                    <li><strong>Address:</strong> <?= htmlspecialchars($property['address']) ?></li>
                    <li><strong>City:</strong> <?= htmlspecialchars($property['city']) ?></li>
                    <li><strong>State:</strong> <?= htmlspecialchars($property['state']) ?></li>
                    <li><strong>Zip Code:</strong> <?= htmlspecialchars($property['zipCode']) ?></li>
                    <li><strong>Date Listed:</strong> <?= date('F j, Y', strtotime($property['dateListed'])) ?></li>
                </ul>
                <h1 class="mb-4">Property Owner Details</h1>
                <ul>
                    <li><strong>Full Name:</strong> <?= htmlspecialchars($owner['full_name']) ?></li>
                    <li><strong>Email:</strong> <?= htmlspecialchars($owner['email']) ?></li>
                    <li><strong>Address:</strong> <?= htmlspecialchars($owner['address']) ?></li>
                    <li><strong>Age:</strong> <?= htmlspecialchars($owner['age']) ?></li>
                    <li><strong>Mobile:</strong> <?= htmlspecialchars($owner['mobile']) ?></li>
                    <li><strong>Verification Tier:</strong> <?= htmlspecialchars($owner['verification_tier']) ?></li>
                </ul>
            </div>
            <script>
                window.print();
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "Property not found";
    }
} else {
    echo "Property ID not provided";
}
?>

<script>
    window.onload = function() {
        // Print the document
        window.print();

        // After printing is completed, redirect to landholder-table.php
        window.onafterprint = function() {
            window.location.href = 'landholder-table.php';
        };
    };
</script>
