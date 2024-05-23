<?php
// Include database connection
require_once 'components/connection.php';

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    try {
        // Prepare SQL to insert into contacts_tb using PDO
        $sql = "INSERT INTO contacts_tb (name, email, message, created_at) VALUES (:name, :email, :message, NOW())";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        // Execute the query
        if ($stmt->execute()) {
            echo "Message sent successfully!";
        } else {
            echo "Error executing query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close prepared statement and database connection
    $stmt = null;
    $conn = null;
}
?>
