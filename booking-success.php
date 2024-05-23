<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Include Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include necessary CSS/JS libraries -->
</head>
<body>
        
</body>
</html>

<?php

echo '<script>
        Swal.fire({
          icon: "success",
          title: "Property booked successfully!",
          showConfirmButton: false,
          timer: 2000
        }).then(function() {
            window.location.href = "own-booking.php";
        });
        exit;
      </script>';
?>
