<!-- process_create_announcement.php -->
<?php
include '../components/connection.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $details = $_POST['details'];

    $insertQuery = "INSERT INTO announcements_tb (type, title, details) VALUES (:type, :title, :details)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':details', $details);

    if ($stmt->execute()) {
        header("Location: admin-announcements.php");
        exit();
    } else {
        echo "Failed to create announcement.";
    }
}
?>
