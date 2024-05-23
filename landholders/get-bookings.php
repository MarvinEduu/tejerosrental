<?php
include '../components/connection.php';
session_start();

// Check if user_id is set in session (assuming it represents landholder_id)
if (isset($_SESSION['landholder_id'])) {
    $landholderId = $_SESSION['landholder_id'];

    // Fetch propertyId(s) owned by the logged-in landholder
    $selectProperties = $conn->prepare("SELECT propertyId, name FROM properties_tb WHERE landholder_id = ?");
    $selectProperties->execute([$landholderId]);

    // Initialize an array to store propertyIds and property details
    $propertyDetails = [];

    // Fetch all propertyIds associated with the landholder
    while ($row = $selectProperties->fetch(PDO::FETCH_ASSOC)) {
        $propertyDetails[$row['propertyId']] = $row['name'];
    }

    // Fetch bookings data for the properties owned by the logged-in landholder
    $events = [];

    foreach ($propertyDetails as $propertyId => $propertyName) {
        $selectBookings = $conn->prepare("
            SELECT b.startDate, b.endDate, b.status, u.full_name AS user_name
            FROM bookings_tb b
            JOIN users_tb u ON b.user_id = u.user_id
            WHERE b.propertyId = ? AND b.status NOT IN ('Pending', 'Cancelled')
        ");
        $selectBookings->execute([$propertyId]);

        // Prepare events array for FullCalendar
        while ($row = $selectBookings->fetch(PDO::FETCH_ASSOC)) {
            $startDate = $row['startDate'];
            $endDate = date('Y-m-d', strtotime($row['endDate'] . ' +1 day')); // Add 1 day to include the end date fully in FullCalendar

            // Add event object to events array
            $events[] = [
                'title' => 'Booked by ' . htmlspecialchars($row['user_name']) . ' for ' . htmlspecialchars($propertyName),
                'start' => $startDate,
                'end' => $endDate,
                'status' => $row['status'],
                'color' => '#ff9f89' // Optional: Add custom color for booked events
            ];
        }
    }

    // Output events data as JSON
    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
} else {
    http_response_code(400); // Bad Request
    exit;
}
?>
