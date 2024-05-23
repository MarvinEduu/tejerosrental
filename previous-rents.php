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
    // Fetch previous rents for the user that have ended and are rated
    $selectPreviousRents = $conn->prepare("
        SELECT b.booking_id, p.name AS propertyName, b.startDate, b.endDate, b.months, b.totalRent, l.full_name AS landholderName, ur.rating, ur.comment, l.landholder_id, l.profile_picture, l.mobile, l.address, l.email, l.bio,
        (SELECT AVG(ur1.rating) FROM user_ratings ur1 WHERE ur1.landholder_id = l.landholder_id) AS avgRating,
        (SELECT COUNT(ur2.rating) FROM user_ratings ur2 WHERE ur2.landholder_id = l.landholder_id) AS ratingCount
        FROM bookings_tb b
        JOIN properties_tb p ON b.propertyId = p.propertyId
        JOIN landholders_tb l ON p.landholder_id = l.landholder_id
        JOIN user_ratings ur ON b.booking_id = ur.booking_id
        WHERE b.user_id = ? AND b.status = 'Ended'
        ORDER BY b.endDate DESC
    ");
    $selectPreviousRents->execute([$userId]);
    $previousRents = $selectPreviousRents->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Rental Capstone - Previous Rents</title>
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
    <h1 class="text-3xl text-center my-8">Your Previous Rents</h1>

    <?php if (empty($previousRents)): ?>
        <div class="text-center text-gray-700 h-auto">
            <p>You have no previous rents.</p>
            <a href="properties.php" class="text-indigo-600 hover:underline">Browse properties</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($previousRents as $rent): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($rent['propertyName']); ?></h2>
                    <p class="text-gray-700 mb-4"><strong>Start Date:</strong> <?= htmlspecialchars($rent['startDate']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>End Date:</strong> <?= htmlspecialchars($rent['endDate']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Duration:</strong> <?= htmlspecialchars($rent['months']); ?> months</p>
                    <p class="text-gray-700 mb-4"><strong>Total Rent: </strong> ₱ <?= htmlspecialchars($rent['totalRent']); ?></p>
                    <p class="text-gray-700 mb-4"><strong>Your Rating:</strong> <?= htmlspecialchars($rent['rating']); ?> / 5</p>
                    <p class="text-gray-700 mb-4"><strong>Your Comment:</strong> <?= nl2br(htmlspecialchars($rent['comment'])); ?></p>

                    <!-- Landholder Information -->
                    <div class=" bg-white p-6 rounded-lg shadow-md w-full">
                        <h2 class="text-2xl font-bold mb-4">Landholder Details</h2>
                        <div class="flex items-center space-x-4">
                            <?php if (!empty($rent['profile_picture'])): ?>
                                <img src="uploaded_image/<?= htmlspecialchars($rent['profile_picture']); ?>" alt="<?= htmlspecialchars($rent['landholderName']); ?>" class="w-16 h-16 rounded-full">
                            <?php endif; ?>
                            <div>
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($rent['landholderName']); ?></p>
                                <!-- Display average rating and rating count -->
                                <p class="text-gray-600 flex items-center">
                                    <?php
                                    $avgRating = round($rent['avgRating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $avgRating) {
                                            echo '<i class="fas fa-star text-yellow-500"></i>';
                                        } else {
                                            echo '<i class="far fa-star text-yellow-500"></i>';
                                        }
                                    }
                                    ?>
                                    <span class="ml-2">(<?= htmlspecialchars($rent['ratingCount']); ?>)</span>
                                </p>
                            </div>
                        </div>
                        <form class="mt-4">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-600">Mobile:</label>
                                <p class="text-gray-800"><?= htmlspecialchars($rent['mobile']); ?></p>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-600">Address:</label>
                                <p class="text-gray-800"><?= htmlspecialchars($rent['address']); ?></p>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-600">Email:</label>
                                <p class="text-gray-800"><?= htmlspecialchars($rent['email']); ?></p>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-600">Bio: </label>
                                <p class="text-gray-800 text-justify"><?= htmlspecialchars($rent['bio']); ?></p>
                            </div>
                            <div class="flex justify-between items-center mt-4">
                                <a href="chat.php?receiver_id=<?= htmlspecialchars($rent['landholder_id']); ?>" class="text-blue-500 hover:text-blue-700 flex items-center">
                                    <i class="far fa-comment-dots mr-2"></i> Chat
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'user/user-footer.php' ?>

</body>
</html>
