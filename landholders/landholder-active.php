<?php

include '../components/connection.php';

session_start();

// Check if the user is logged in as a landholder
if (!isset($_SESSION['landholder_id'])) {
    header('Location: login.php');
    exit;
}

$landholderId = $_SESSION['landholder_id'];

// Define the default status to show accepted bookings
$status = isset($_GET['status']) && $_GET['status'] === 'cancelled' ? 'Cancelled' : 'Accepted';

try {
    // Fetch bookings for the landholder's properties based on the selected status
    $query = "
        SELECT b.*, p.name AS property_name, p.image01, u.full_name AS user_name, u.email AS userEmail, u.address AS userAddress, u.age AS userAge, u.mobile AS userMobile
        FROM bookings_tb b
        JOIN properties_tb p ON b.propertyId = p.propertyId
        JOIN users_tb u ON b.user_id = u.user_id
        WHERE p.landholder_id = ? AND b.status = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$landholderId, $status]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php' ?>

    <div class="container mx-auto p-6 overflow-y-auto">
    <div class="bg-white overflow-hidden sm:rounded-lg p-6">
        <!-- Dropdown to toggle between Accepted and Cancelled Bookings -->
        <h1 class="text-2xl font-bold mb-6">
            <a href="?status=accepted" class="<?= $status === 'Accepted' ? 'text-blue-500' : 'text-gray-500' ?>">Accepted Bookings</a>
            /
            <a href="?status=cancelled" class="<?= $status === 'Cancelled' ? 'text-blue-500' : 'text-gray-500' ?>">Cancelled Bookings</a>
        </h1>
        <div class="grid grid-cols-1 gap-4">
            <?php if (empty($bookings)): ?>
                <p class="text-center text-gray-500">No <?= strtolower($status) ?> bookings yet.</p>
                <img src="../images/empty1.png" alt="Empty Illustration" class="mx-auto mt-4" style="max-width: 500px;">
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="bg-white p-4 rounded-lg shadow-md flex flex-col md:flex-row relative border border-gray-400">
                        <img src="../uploaded_image/<?= htmlspecialchars($booking['image01']); ?>" alt="Property Image" class="w-full md:w-1/3 h-48 object-cover rounded-t-lg md:rounded-t-none md:rounded-l-lg">
                        <div class="flex-1 p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="flex items-center"><i class="fas fa-home mr-2"></i> <?= htmlspecialchars($booking['property_name']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i> <?= htmlspecialchars($booking['startDate']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i> <?= htmlspecialchars($booking['endDate']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-coins mr-2"></i> <?= htmlspecialchars($booking['totalRent']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-info-circle mr-2"></i> <?= htmlspecialchars($booking['status']); ?></p> <!-- Display dynamic status -->
                                    <p class="flex items-center"><i class="fas fa-clock mr-2"></i> <?= date('F j, Y', strtotime($booking['created_at'])); ?></p>
                                </div>
                                <div>
                                    <p class="flex items-center"><i class="fas fa-user mr-2"></i> <?= htmlspecialchars($booking['user_name']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-envelope mr-2"></i> <?= htmlspecialchars($booking['userEmail']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($booking['userAddress']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i> <?= htmlspecialchars($booking['userAge']); ?></p>
                                    <p class="flex items-center"><i class="fas fa-phone mr-2"></i> <?= htmlspecialchars($booking['userMobile']); ?></p>
                                </div>
                            </div>
                            <?php if ($booking['status'] === 'Accepted'): ?> <!-- Check if status is Accepted -->
                                <button class="bg-red-500 text-white font-bold py-2 px-4 rounded mt-4 absolute bottom-4 right-4 end-rent-btn" data-booking-id="<?= $booking['booking_id']; ?>">End User Rent</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="endRentModal" tabindex="-1" aria-labelledby="endRentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="endRentForm" method="POST action="end-rent.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="endRentModalLabel">End User Rent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="booking_id">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="conditionCheck" name="conditionCheck" required>
                            <label class="form-check-label" for="conditionCheck">Property is in good condition after user stay</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="paymentsCheck" name="paymentsCheck" required>
                            <label class="form-check-label" for="paymentsCheck">Payments are all settled</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="keysCheck" name="keysCheck" required>
                            <label class="form-check-label" for="keysCheck">Keys, Access Cards Retrieved</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="noticeCheck" name="noticeCheck" required>
                            <label class="form-check-label" for="noticeCheck">User has received a formal notice of termination</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">End User Rent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endRentButtons = document.querySelectorAll('.end-rent-btn');
            endRentButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const bookingId = this.dataset.bookingId;
                    document.getElementById('booking_id').value = bookingId;
                    const endRentModal = new bootstrap.Modal(document.getElementById('endRentModal'));
                    endRentModal.show();
                });
            });

            const endRentForm = document.getElementById('endRentForm');
            endRentForm.addEventListener('submit', function (event) {
                event.preventDefault();
                
                const formData = new FormData(endRentForm);
                fetch('end-rent.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const bookingId = data.booking_id;
                        document.querySelector(`.end-rent-btn[data-booking-id='${bookingId}']`).closest('.bg-white').remove();
                        const endRentModal = bootstrap.Modal.getInstance(document.getElementById('endRentModal'));
                        endRentModal.hide();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });
    </script>
</body>
</html>

