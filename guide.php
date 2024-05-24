<?php
@include 'components/connection.php';

session_start();


?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rental Capstone</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/x-icon" href="images/logoer.png">

  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="bg-gray-100 flex flex-col">

  <?php include 'user/user-header.php' ?>

  <div class="container mx-auto p-6 flex flex-col lg:flex-row">
    <div class="lg:w-1/2 lg:pr-8">
      <h1 class="text-3xl font-bold mb-6">Welcome to Your Tejeros Rental</h1>
      <p class="text-lg mb-4 text-justify">Looking for the perfect property to rent? Look no further! Our property guide is your one-stop destination for finding your ideal home effortlessly. Whether you're searching for a cozy apartment in the heart of the city or a spacious house in a tranquil neighborhood, we've got you covered. Our comprehensive listings feature a diverse range of properties, each meticulously curated to suit your preferences and budget.</p>
      <h2 class="text-2xl font-bold mb-4">Why Choose Us?</h2>
      <ul class="list-disc pl-6 mb-6">
        <li class="text-lg mb-2 text-justify">Personalized Assistance: Our team of experienced professionals is dedicated to providing personalized assistance every step of the way. From helping you narrow down your search to negotiating favorable terms with landlords, we're here to make the rental process smooth and stress-free.</li>
        <li class="text-lg mb-2 text-justify">Extensive Network: With our extensive network and insider knowledge of the market, we can connect you with hidden gems and exclusive deals that you won't find elsewhere.</li>
        <li class="text-lg mb-2 text-justify">Transparent and Reliable: Enjoy peace of mind knowing that all properties listed in our guide undergo rigorous screening to ensure they meet our high standards of quality and safety. Say goodbye to hidden fees and unpleasant surprises with our transparent pricing and clear terms.</li>
        <li class="text-lg mb-2 text-justify">Dedicated Support: Our dedicated support team is always available to address any questions or concerns you may have, providing ongoing assistance even after you've moved into your new home.</li>
      </ul>
      <p class="text-lg text-justify">Start Your Search Today! With our property guide and trusted services by your side, finding and renting your dream property has never been easier. Explore our listings now and take the first step towards your new home!</p>
    </div>
    <!-- Add an image or additional content in the second column if needed -->
     <div class="lg:w-1/2 lg:pl-8">
     <img src="images/cavite.jpg" alt="Tejeros Rental Logo" class="mr-2 w-full h-full rounded-md ">
    </div>
  </div>

</body>
<?php include 'user/user-footer.php' ?>
</html>
