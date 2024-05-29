<?php
include '../components/connection.php';
session_start();

// Function to generate a unique color based on property ID
function generateColor($id) {
    $colors = [
        "#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#e74c3c", "#e67e22",
        "#f1c40f", "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#c0392b"
    ];
    return $colors[$id % count($colors)];
}

// Check if user_id is set in session (assuming it represents landholder_id)
if (isset($_SESSION['landholder_id'])) {
    $landholderId = $_SESSION['landholder_id'];

    // Fetch propertyId(s) owned by the logged-in landholder
    $selectProperties = $conn->prepare("SELECT propertyId, name, image01 FROM properties_tb WHERE landholder_id = ?");
    $selectProperties->execute([$landholderId]);

    // Initialize an array to store propertyIds and property details
    $propertyDetails = [];

    // Fetch all propertyIds associated with the landholder
    while ($row = $selectProperties->fetch(PDO::FETCH_ASSOC)) {
        $propertyDetails[$row['propertyId']] = [
            'name' => $row['name'],
            'image' => $row['image01']
        ];
    }

    // Fetch bookings data for the properties owned by the logged-in landholder
    $events = [];

    foreach ($propertyDetails as $propertyId => $details) {
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
                'title' => htmlspecialchars($details['name']) . ': Booked by ' . htmlspecialchars($row['user_name']),
                'start' => $startDate,
                'end' => $endDate,
                'status' => $row['status'],
                'color' => generateColor($propertyId), // Assign a unique color to each property
                'extendedProps' => [
                    'propertyName' => $details['name'],
                    'userName' => $row['user_name'],
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'status' => $row['status'],
                    'image' => '../uploaded_image/' . $details['image']
                ]
            ];
        }
    }
} else {
    http_response_code(400); // Bad Request
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Admin Template</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

    <style>
        #propertyImage {
            max-height: 300px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 font-family-karla flex">

    <?php include 'landholder-header.php' ?>
    <div class="container mx-auto p-2 overflow-y-auto">
        <h1 class="text-3xl text-center my-2">Property Bookings Calendar</h1>
        <div id="calendar" class="mx-auto w-10/12 bg-white p-4 rounded shadow"></div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="propertyImage" src="" alt="Property Image" class="img-fluid mb-3">
                    <p><strong>Property:</strong> <span id="propertyName"></span></p>
                    <p><strong>Booked by:</strong> <span id="userName"></span></p>
                    <p><strong>Start Date:</strong> <span id="startDate"></span></p>
                    <p><strong>End Date:</strong> <span id="endDate"></span></p>
                    <p><strong>Status:</strong> <span id="status"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        if (calendarEl) {
            var events = <?php echo json_encode($events); ?>;
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                eventClick: function(info) {
                    // Get event details
                    var event = info.event.extendedProps;
                    var modal = new bootstrap.Modal(document.getElementById('bookingModal'));

                    // Set modal content
                    document.getElementById('propertyImage').src = event.image;
                    document.getElementById('propertyName').innerText = event.propertyName;
                    document.getElementById('userName').innerText = event.userName;
                    document.getElementById('startDate').innerText = new Date(event.startDate).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                    document.getElementById('endDate').innerText = new Date(event.endDate).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                    document.getElementById('status').innerText = event.status;

                    // Show modal
                    modal.show();
                }
            });

            calendar.render(); // Render the calendar
        } else {
            console.error('Calendar element not found.');
        }
    });
    </script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.0/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</body>
</html>
