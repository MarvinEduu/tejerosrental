<?php
include 'components/connection.php';

session_start();

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

try {
    // Fetch current bookings for the user excluding those that are already rated
    $selectBookings = $conn->prepare("
        SELECT b.booking_id, p.name AS propertyName, p.image01, p.details, b.startDate, b.endDate, b.months, b.totalRent, b.status, p.landholder_id
        FROM bookings_tb b
        JOIN properties_tb p ON b.propertyId = p.propertyId
        LEFT JOIN user_ratings ur ON b.booking_id = ur.booking_id
        WHERE b.user_id = ? AND ur.booking_id IS NULL
        ORDER BY b.startDate DESC
    ");
    $selectBookings->execute([$userId]);
    $bookings = $selectBookings->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html data-theme="winter">
<!-- Include other head elements here -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rental Capstone</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/logoer.png">

    <!-- Include necessary CSS and JavaScript libraries -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>
<body class="overflow-x-hidden">

<?php include 'user/user-header.php' ?>

<div class="container mx-auto p-6">
    <h1 class="text-3xl text-center my-8">Your Current Bookings</h1>

    <?php if (empty($bookings)): ?>
        <div class="text-center text-gray-700 h-auto">
            <p>You are not booked in any properties yet.</p>
            <a href="properties.php" class="text-indigo-600 hover:underline">Browse properties</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($bookings as $booking): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <img src="uploaded_image/<?= htmlspecialchars($booking['image01']); ?>" alt="<?= htmlspecialchars($booking['propertyName']); ?>" class="w-full h-64 object-cover mb-6 rounded-lg">
                    <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($booking['propertyName']); ?></h2>
                    <p class="text-gray-700 mb-4 text-justify"><?= htmlspecialchars($booking['details']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Start Date:</strong> <?= htmlspecialchars($booking['startDate']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>End Date:</strong> <?= htmlspecialchars($booking['endDate']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Duration:</strong> <?= htmlspecialchars($booking['months']); ?> months</p>
                    <p class="text-gray-700 mb-4"><strong>Total Rent: </strong> ₱ <?= number_format($booking['totalRent']); ?></p>

                    <?php if ($booking['status'] == 'Pending'): ?>
                        <p class="text-yellow-500 font-bold mb-4">Your booking is pending. Wait for the landholder to confirm.</p>
                        <!-- Display cancel booking button for pending bookings -->
                        <form method="post" action="cancel-booking.php">
                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Cancel Booking</button>
                        </form>
                    <?php elseif ($booking['status'] == 'Cancelled'): ?>
                        <p class="text-red-500 font-bold mb-4">Booking Cancelled</p>
                    <?php elseif ($booking['status'] == 'Ended'): ?>
                        <p class="text-green-500 font-bold mb-4">Rent has ended</p>
                        <!-- Display rate landholder button for ended bookings -->
                        <button onclick="showRateModal(<?= $booking['landholder_id']; ?>, <?= $booking['booking_id']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Rate Landholder</button>
                    <?php elseif ($booking['status'] == 'Accepted'): ?>
                        <p class="text-green-500 font-bold mb-4">Property Booked</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for rating landholder -->
<div id="rateModal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg relative">
        <h2 class="text-xl font-bold mb-4">Rate Landholder</h2>
        <!-- Rating form -->
        <form action="rate-landholder.php" method="post">
            <!-- Rating stars -->
            <div class="flex items-center mb-4">
                <label for="rating" class="mr-2">Rating:</label>
                <div class="star-rating">
                    <input type="radio" name="rating" id="rating5" value="5"><label for="rating5">☆</label>
                    <input type="radio" name="rating" id="rating4" value="4"><label for="rating4">☆</label>
                    <input type="radio" name="rating" id="rating3" value="3"><label for="rating3">☆</label>
                    <input type="radio" name="rating" id="rating2" value="2"><label for="rating2">☆</label>
                    <input type="radio" name="rating" id="rating1" value="1"><label for="rating1">☆</label>
                </div>
            </div>
            <!-- Comment -->
            <div class="mb-4">
                <label for="comment">Comment:</label><br>
                <textarea name="comment" id="comment" cols="30" rows="5" required></textarea>
            </div>
            <input type="hidden" name="landholderId" id="landholderId">
            <input type="hidden" name="bookingId" id="bookingId">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Submit Rating</button>
        </form>
        <!-- Close button -->
        <button onclick="hideRateModal()" class="absolute top-0 right-0 mt-2 mr-2 text-gray-600 hover:text-gray-800">&times;</button>
    </div>
</div>

<script>
    // Function to show the rating modal
    function showRateModal(landholderId, bookingId) {
        document.getElementById('landholderId').value = landholderId;
        document.getElementById('bookingId').value = bookingId;
        document.getElementById('rateModal').classList.remove('hidden');
    }

    // Function to hide the rating modal
    function hideRateModal() {
        document.getElementById('rateModal').classList.add('hidden');
    }
</script>

<?php include 'user/user-footer.php' ?>

</body>
</html>

<style>
.star-rating {
  direction: rtl;
  display: inline-block;
  padding: 0;
}

.star-rating input[type="radio"] {
  display: none;
}

.star-rating label {
  font-size: 2rem;
  color: #ccc;
  cursor: pointer;
}

.star-rating input[type="radio"]:checked ~ label {
  color: #f0c040;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
  color: #f0c040;
}
</style>
