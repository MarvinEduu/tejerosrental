<?php
@include 'components/connection.php'; // Include your database connection script

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $propertyId = $_POST['propertyId'];
    $reason = htmlspecialchars($_POST['reason']);

    // Insert report into the database
    try {
        $stmt = $conn->prepare("INSERT INTO reports_tb (user_id, propertyId, reason) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $propertyId, $reason]);
        // Report submitted successfully
        echo '<script>
                alert("Property reported successfully!");
                window.location.href = "property-details.php";
              </script>';
    } catch (Exception $e) {
        die('Error submitting report: ' . $e->getMessage());
    }
}
?>

