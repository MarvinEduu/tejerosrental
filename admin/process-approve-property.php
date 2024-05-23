<?php

include '../components/connection.php';

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["approve"]) || isset($_POST["reject"])) {
        $property_id = $_POST["propertyId"];
        $action = isset($_POST["approve"]) ? "Approved" : "Rejected";

        // Perform database operation to update property status
        $updateQuery = $conn->prepare("UPDATE properties_tb SET status = ? WHERE propertyId = ?");
        $updateQuery->execute([$action, $property_id]);

        if ($updateQuery) {
            // Successful update, redirect back to admin-approval.php
            header("Location: admin-approval.php");
            exit();
        } else {
            // Error handling
            echo "Failed to update property status.";
        }
    }
}

?>
