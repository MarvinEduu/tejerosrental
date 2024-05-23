<?php
include 'components/connection.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Handle case when user is not logged in or user_id is not available
    die('User not logged in or user_id not available');
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve report data
    $reportType = $_POST['reportType'];
    $description = $_POST['reportDescription'];

    // Example: Retrieve user_id from session (replace with your actual method of obtaining user ID)
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
    } else {
        // Handle case when user is not logged in or user_id is not available
        die('User not logged in or user_id not available');
    }

    // Example: Assuming landholder_id is a static value (replace with your actual logic)
    $landholder_id = 1; // Replace with actual landholder ID if applicable

    try {
        // Prepare and execute SQL insert statement
        $stmt = $conn->prepare('INSERT INTO reports (user_id, landholder_id, report_type, description) VALUES (:user_id, :landholder_id, :report_type, :description)');
        $stmt->execute([
            'user_id' => $user_id,
            'landholder_id' => $landholder_id,
            'report_type' => $reportType,
            'description' => $description
        ]);

        // Prepare response
        $response = ['message' => 'Report has been sent. Admins will investigate. Thank you.'];

        // Send JSON response back to client
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        // Handle database errors
        die('Database error: ' . $e->getMessage());
    }
}
?>
