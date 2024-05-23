<?php
include 'components/connection.php';

session_start();

?>



<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Property</title>
    <link rel="icon" type="image/x-icon" href="images/logoer.png">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://unpkg.com/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://unpkg.com/tippy.js@6.3.2/dist/tippy-bundle.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    

    <?php
    

// Ensure property ID is passed via URL
if (!isset($_GET['pid'])) {
    // Redirect or handle error if property ID is missing
    header('Location: booking-calendar.php');
    exit;
}

// Fetch property details using the provided property ID (pid)
$pid = $_GET['pid'];
try {
    $select_property = $conn->prepare("
        SELECT p.*, l.full_name AS propertyOwner 
        FROM properties_tb p 
        JOIN landholders_tb l ON p.landholder_id = l.landholder_id 
        WHERE p.propertyId = ? AND p.status != 'Pending' AND p.status != 'Rejected' AND p.is_deleted = 0
    ");
    $select_property->execute([$pid]);
    $property = $select_property->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Check if property exists and is valid
if (!$property) {
    // Redirect or handle error if property is not found
    header('Location: booking-calendar.php');
    exit;
}

// Handle form submission for booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    // Process booking form data here
    if (!isset($_SESSION['user_id'])) {
        // Redirect or handle error if user is not logged in
        header('Location: login.php');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $startDate = $_POST['start_date'];
    $months = $_POST['months'];

    // Calculate end date based on start date and months
    $endDate = date('Y-m-d', strtotime($startDate . ' + ' . $months . ' months'));

    // Fetch rent amount from the property details
    $rentAmount = $property['rentAmount'];

    // Calculate total rent based on number of months and rent amount
    $totalRent = $months * $rentAmount;

    try {
        // Check if the user has already booked this property
        $checkExistingBooking = $conn->prepare("SELECT COUNT(*) FROM bookings_tb WHERE user_id = ? AND propertyId = ?");
        $checkExistingBooking->execute([$userId, $pid]);
        $existingBookingCount = $checkExistingBooking->fetchColumn();

        if ($existingBookingCount > 0) {
            // User has already booked this property, handle accordingly (e.g., show error message)
            echo '<script>
            Swal.fire({
              icon: "error",
              title: "You have already booked this property.",
              showConfirmButton: false,
              timer: 2000
            }).then(function() {
                window.location.href = "own-booking.php";
            });
          </script>';
        exit;
        }

        // Insert booking into database
        $insertBooking = $conn->prepare("INSERT INTO bookings_tb (user_id, propertyId, startDate, endDate, months, totalRent) VALUES (?, ?, ?, ?, ?, ?)");
        $insertBooking->execute([$userId, $pid, $startDate, $endDate, $months, $totalRent]);
        
        // Redirect user after booking is successfully processed
        header('Location: booking-success.php');
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

?>
<?php include 'user/user-header.php'; ?>

    <div class="container mx-auto p-6 flex flex-col lg:flex-row">
        <div class="lg:w-1/2 lg:pr-8">
            <form method="post" action="">
                <h1 class="text-3xl font-bold mb-6">Book Property - <?= htmlspecialchars($property['name']); ?></h1>
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required
                        class="mt-1 px-4 py-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label for="months" class="block text-sm font-medium text-gray-700">Duration (in months):</label>
                    <input type="number" id="months" name="months" min="1" required
                        class="mt-1 px-4 py-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
                    <input type="text" id="end_date" name="end_date" readonly
                        class="mt-1 px-4 py-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label for="total_rent" class="block text-sm font-medium text-gray-700">Total Rent:</label>
                    <input type="text" id="total_rent" name="total_rent" readonly
                        class="mt-1 px-4 py-2 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <input type="checkbox" id="agree" name="agree" class="mr-2" required>
                    <label for="agree" class="text-sm font-medium text-gray-700">
                        I hereby follow and understand the policies and rules of the property. I acknowledge that breaking these rules has consequences.
                    </label>
                </div>

                <!-- Submit button -->
                <button type="submit" name="confirm_booking" id="confirm_booking_button"
                    class="inline-block px-6 py-3 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" disabled>
                    Confirm Booking
                </button>
            </form>
        </div>
        <div class="lg:w-1/2 lg:pl-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <img src="uploaded_image/<?= htmlspecialchars($property['image01']); ?>" alt="<?= htmlspecialchars($property['name']); ?>" class="w-full h-64 object-cover mb-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($property['name']); ?></h2>
                <p class="text-gray-700 mb-4"><strong>Address:</strong> <?= htmlspecialchars($property['address']); ?></p>
                <p class="text-gray-700 mb-4"><strong>Type:</strong> <?= htmlspecialchars($property['houseType']); ?></p>
                <p class="text-gray-700 mb-4"><strong>Size:</strong> <?= htmlspecialchars($property['size']); ?> sq ft</p>
                <p class="text-gray-700 mb-4"><strong>Bedrooms:</strong> <?= htmlspecialchars($property['bedroomNum']); ?></p>
                <p class="text-gray-700 mb-4"><strong>Bathrooms:</strong> <?= htmlspecialchars($property['bathroomNum']); ?></p>
                <p class="text-gray-700 mb-4"><strong>Property Owner:</strong> <?= htmlspecialchars($property['propertyOwner']); ?></p>
            </div>
        </div>
    </div>

    

    <script>
        // Function to calculate end date and total rent
        function calculateEndDateAndRent() {
            const startDateInput = document.getElementById('start_date');
            const monthsInput = document.getElementById('months');
            const endDateInput = document.getElementById('end_date');
            const totalRentInput = document.getElementById('total_rent');

            if (startDateInput.value && monthsInput.value) {
                const startDate = new Date(startDateInput.value);
                const months = parseInt(monthsInput.value);

                // Calculate end date
                const endDate = new Date(startDate);
                endDate.setMonth(startDate.getMonth() + months);
                endDateInput.value = endDate.toISOString().split('T')[0];

                // Calculate total rent (assuming a fixed rent per month, adjust as needed)
                const totalRent = months * <?= $property['rentAmount']; ?>;
                totalRentInput.value = totalRent.toFixed(2);
            } else {
                endDateInput.value = '';
                totalRentInput.value = '';
            }
        }

        // Event listeners to update on input change
        const startDateInput = document.getElementById('start_date');
        const monthsInput = document.getElementById('months');
        const agreeCheckbox = document.getElementById('agree');
        const confirmBookingButton = document.getElementById('confirm_booking_button');

        startDateInput.addEventListener('input', calculateEndDateAndRent);
        monthsInput.addEventListener('input', calculateEndDateAndRent);

        // Enable or disable the confirm booking button based on the checkbox state
        agreeCheckbox.addEventListener('change', function() {
            confirmBookingButton.disabled = !this.checked;
        });

        // Initial calculation on page load (if values are pre-filled)
        calculateEndDateAndRent();
    </script>

    <?php include 'user/user-footer.php'; ?>

</body>

</html>

