<?php

include '../components/connection.php';

session_start();

// Fetch pending bookings for the current landholder
$landholderId = $_SESSION['landholder_id']; // Assuming you have a session variable for the landholder ID
try {
    $selectBookings = $conn->prepare("
        SELECT b.*, p.name AS propertyName, p.image01, u.full_name AS userName, u.email AS userEmail, u.address AS userAddress, u.age AS userAge, u.mobile AS userMobile,
        (SELECT COUNT(*) FROM bookings_tb WHERE user_id = b.user_id) AS userBookingCount
        FROM bookings_tb b
        JOIN properties_tb p ON b.propertyId = p.propertyId
        JOIN users_tb u ON b.user_id = u.user_id
        WHERE p.landholder_id = ? AND b.status = 'Pending'
    ");
    $selectBookings->execute([$landholderId]);
    $pendingBookings = $selectBookings->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Pending Bookings</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

</head>

<body class="bg-gray-100 font-family-karla flex">

    <?php include 'landholder-header.php' ?>

    <div class="container mx-auto p-6 overflow-y-auto">
        <div class="bg-white overflow-hidden sm:rounded-lg p-6">
            <h1 class="text-3xl font-bold mb-6">Pending Bookings</h1>

            <!-- Display messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-4">
                <?php if (empty($pendingBookings)) : ?>
                    <p class="text-center text-gray-500">No pending bookings yet.</p>
                    <img src="../images/empty1.png" alt="Empty Illustration" class="mx-auto mt-4" style="max-width: 500px;">
                <?php else : ?>
                    <?php foreach ($pendingBookings as $booking) : ?>
                        <div class="bg-white p-4 rounded-lg shadow-md flex flex-col md:flex-row relative border border-gray-400">
                            <img src="../uploaded_image/<?= htmlspecialchars($booking['image01']); ?>" alt="Property Image" class="w-full md:w-1/3 h-48 object-cover rounded-t-lg md:rounded-t-none md:rounded-l-lg">
                            <div class="flex-1 p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="flex items-center"><i class="fas fa-home mr-2"></i> <?= htmlspecialchars($booking['propertyName']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i> <?= date('F j, Y', strtotime($booking['startDate'])); ?></p>
                                        <p class="flex items-center"><i class="fas fa-calendar-alt mr-2"></i> <?= date('F j, Y', strtotime($booking['endDate'])); ?></p>

                                        <p class="flex items-center"><i class="fas fa-coins mr-2"></i> ₱ <?= number_format($booking['totalRent']); ?></p>

                                        <p class="flex items-center"><i class="fas fa-info-circle mr-2"></i> <?= htmlspecialchars($booking['status']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-clock mr-2"></i> <?= date('F j, Y', strtotime($booking['created_at'])); ?></p>
                                    </div>
                                    <div>
                                        <p class="flex items-center"><i class="fas fa-user mr-2"></i> <?= htmlspecialchars($booking['userName']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-envelope mr-2"></i> <?= htmlspecialchars($booking['userEmail']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($booking['userAddress']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-birthday-cake mr-2"></i> <?= htmlspecialchars($booking['userAge']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-phone mr-2"></i> <?= htmlspecialchars($booking['userMobile']); ?></p>
                                        <p class="flex items-center"><i class="fas fa-book mr-2"></i> <?= htmlspecialchars($booking['userBookingCount']); ?> bookings</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute bottom-4 right-4 flex space-x-2">
                                <form method="post" action="handle-booking.php" class="flex space-x-2">
                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                    <button type="submit" name="action" value="accept" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">Accept</button>
                                    <button type="submit" name="action" value="cancel" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Cancel</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</body>

</html>
