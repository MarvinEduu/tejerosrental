<!DOCTYPE html>
<html lang="en" data-theme="winter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Page</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Centering the loading animation */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6; /* Optional: Set background color */
        }

        /* Styling the loading animation */
        .loading-page {
            font-size: 50px; /* Adjust size as needed */
        }
    </style>
</head>
<body>
    <div class="loading-page">
        <div class="loading loading-bars loading-lg text-primary"></div>
    </div>

    <script>
        // Simulating loading process
        setTimeout(() => {
            window.location.href = "user-home.php"; // Redirect to login page after 3 seconds (adjust time as needed)
        }, 2000); // 3000 milliseconds = 3 seconds
    </script>
</body>
</html>