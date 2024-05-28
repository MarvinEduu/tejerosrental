<?php
include 'components/connection.php';

session_start();

?>

<!DOCTYPE html>
<html data-theme="winter">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rental Capstone</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/x-icon" href="images/logo1.svg">

  <link href="vendor/daisyui/full.min.css" rel="stylesheet" type="text/css" />
  <script src="vendor/tailwind/tailwind.js"></script>
  <link href="vendor/swiperjs/node_modules/swiper/swiper-bundle.js" rel="stylesheet">
  <link href="vendor/swiperjs/node_modules/swiper/swiper-bundle.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <style>
    /* Add fixed dimensions for the images */
    .swiper-pagination-bullet {
  background-color: white;
  width: 10px;
  height: 10px;
}

    .swiper-slide {
      margin-right: 20px;
    }

    .swiper-slide:last-child {
      margin-right: 50px;
    }

    .swiper-slide img {
      width: 100%;
      height: 250px;
      object-fit: cover;
    }

    @media (max-width: 640px) {
      .hero-content {
        flex-direction: column;
        text-align: center;
      }

      .hero-content img {
        width: 100%;
      }

      .hero-content div {
        width: 100%;
      }

      .home-products .swiper {
        padding-left: 0;
        padding-right: 0;
      }

      .home-products .text-center {
        margin-top: 12px;
      }

      .home-products .swiper-slide {
        margin-right: 10px;
      }

      .hero-content {
        padding: 1rem;
      }

      .card-body img {
        width: 100%;
      }

      .hero-content.flex-col img {
        margin-bottom: 1rem;
      }

      .hero-content.flex-col-reverse img {
        margin-top: 1rem;
      }
    }

    .hero-content h1 {
      animation: fadeIn 2s ease-in-out forwards;
    }

    .hero-content p {
      animation: fadeIn 2s ease-in-out forwards 0.5s;
    }

    .hero-content button {
      animation: fadeIn 2s ease-in-out forwards 1s;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

.headline {
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.headline.active {
  opacity: 1;
}

.hidden {
  display: none;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

  </style>
</head>

<body class="overflow-x-hidden">

  <?php include 'user/user-header.php' ?>

  <div data-aos="zoom-out" data-aos-duration="700" data-aos-easing="ease-in-out-back">
  <div class="hero h-screen max-h-screen" style="background-image: url('images/houses.jpg'); background-size: cover; background-position: center;">
    <div class="hero-overlay rounded-lg bg-opacity-70"></div>
    <div class="hero-content flex flex-col items-center justify-center text-center w-full px-4 sm:px-8 lg:px-16">
      <div class="headline-container text-6xl font-bold uppercase text-white shadow-md">
        <h1 class="headline">Skip the tedious process. Explore hassle-free property rentals.</h1>
        <h1 class="headline hidden">Discover your dream home with ease and comfort.</h1>
        <h1 class="headline hidden">Find the perfect property that fits your lifestyle.</h1>
      </div>
      <p class="mt-4 text-lg font-semibold text-white max-w-lg shadow-md">Discover a place you'll love to live. Browse through our extensive list of properties tailored to fit your lifestyle and budget.</p>
      <a href="properties.php"><button class="btn btn-primary mt-6">View Properties</button></a>
    </div>
  </div>
</div>

  <div data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
    <section class="home-products h-fit pb-14" style="background-image: url('images/wave3.svg'); background-size: cover; background-position: center;">
      <div class="swiper products-slider ml-10 mr-10">
        <div class="text-center mt-16">
          <h2 class="text-3xl font-bold">RECENT ADDED PROPERTIES</h2>
        </div>
        <div class="swiper-wrapper mt-12 mb-24">
          <?php
          $select_products = $conn->prepare("
                    SELECT 
                        p.propertyId AS p_propertyId, 
                        p.name AS p_name, 
                        p.rentAmount AS p_rentAmount, 
                        p.bathroomNum AS p_bathroomNum, 
                        p.bedroomNum AS p_bedroomNum, 
                        p.image01 AS p_image01, 
                        p.city AS p_city, 
                        p.houseType AS p_houseType, 
                        p.status AS p_status,
                        p.size AS p_size
                    FROM properties_tb p
                    LEFT JOIN bookings_tb b ON p.propertyId = b.propertyId
                        AND b.status = 'Accepted'
                        AND b.endDate > NOW()
                    WHERE p.status != 'Pending'
                      AND p.status != 'Rejected'
                      AND p.is_deleted = 0
                      AND b.booking_id IS NULL
                    ORDER BY p.propertyId DESC
                    LIMIT 6
                ");

          $select_products->execute();
          if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
          ?>
              <form action="" method="post" class="swiper-slide border rounded-lg p-4 bg-white">
                <input type="hidden" name="pid" value="<?= $fetch_product['p_propertyId']; ?>">
                <input type="hidden" name="name" value="<?= $fetch_product['p_name']; ?>">
                <input type="hidden" name="price" value="<?= $fetch_product['p_rentAmount']; ?>">
                <input type="hidden" name="bathroomNum" value="<?= $fetch_product['p_bathroomNum']; ?>">
                <input type="hidden" name="image" value="<?= $fetch_product['p_image01']; ?>">
                <input type="hidden" name="size" value="<?= $fetch_product['p_size']; ?>">
                <a href="property-details.php?pid=<?= $fetch_product['p_propertyId']; ?>" class="block">
                  <img src="uploaded_image/<?= $fetch_product['p_image01']; ?>" alt="<?= $fetch_product['p_name']; ?>" class="w-full h-64 object-cover rounded-t-lg">
                  <div class="px-2 py-2">
                    <div class="flex justify-between items-center">
                      <div class="price text-lg font-semibold text-gray-800"><?= $fetch_product['p_name']; ?></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                      <div class="name text-gray-600 flex items-center">
                        <i class='bx bx-map-alt bx-sm'></i>&nbsp; <?= $fetch_product['p_city']; ?>, <?= $fetch_product['p_houseType']; ?>
                      </div>
                      <div class="flex items-center">
                        <i class='bx bx-bath mr-1 bx-sm'></i>&nbsp;
                        <span><?= $fetch_product['p_bathroomNum']; ?></span>
                        <i class='bx bx-bed ml-2 mr-1 bx-sm'></i>&nbsp;
                        <span><?= $fetch_product['p_bedroomNum']; ?></span>
                      </div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                      <div class="status text-gray-600 flex items-center">
                        <i class='bx bx-coin-stack mr-1 bx-sm'></i>&nbsp;₱ <?= number_format($fetch_product['p_rentAmount'], 0, '.', ','); ?>
                      </div>
                      <div class="size text-gray-600 flex items-center">
                        <i class='bx bx-ruler mr-1 bx-sm'></i>&nbsp;<?= $fetch_product['p_size']; ?> sqm
                      </div>
                    </div>
                  </div>
                </a>
              </form>
          <?php
            }
          } else {
            echo '<p>No products added yet!</p>';
          }
          ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </section>
  </div>


  <div data-aos="zoom-out" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
  <div class="hero min-h-fit bg-base-200 py-10 flex flex-col lg:flex-row items-center justify-center" style="background-image: url('images/wave2.svg'); background-size: cover; background-position: center;">
    <!-- Left Container -->
    <div class="w-full lg:w-2/3 mx-8 lg:mx-20 lg:order-2">
      <h1 class="text-3xl text-white font-bold py-3 text-center lg:text-left">ABOUT US</h1>
      <div class="hero-content space-y-8 text-center lg:text-left">
  <div class="w-full bg-primary p-8 rounded-lg flex flex-col space-y-6 bg-[#5098b3]">
    <div class="flex flex-col lg:flex-row items-center">
      <div class="w-12 h-12 flex-shrink-0 bg-primary text-white flex items-center justify-center rounded-full mr-4">
        <i class="fas fa-home fa-2x"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-white mb-2">Quality Properties</h2>
        <p class="text-white">We offer a diverse selection of quality properties, ranging from cozy apartments to spacious family homes.</p>
      </div>
    </div>
    <div class="flex flex-col lg:flex-row items-center">
      <div class="w-12 h-12 flex-shrink-0 bg-secondary text-white flex items-center justify-center rounded-full mr-4">
        <i class="fas fa-shield-alt fa-2x"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-white mb-2">Safety & Security</h2>
        <p class="text-white">Your safety and security are our top priorities. We ensure that all properties meet stringent safety standards.</p>
      </div>
    </div>
    <div class="flex flex-col lg:flex-row items-center">
      <div class="w-12 h-12 flex-shrink-0 bg-accent text-white flex items-center justify-center rounded-full mr-4">
        <i class="fas fa-check-circle fa-2x"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-white mb-2">Verified Listings</h2>
        <p class="text-white">Each property listing undergoes a thorough verification process to ensure authenticity and accuracy.</p>
      </div>
    </div>
    <div class="flex flex-col lg:flex-row items-center">
      <div class="w-12 h-12 flex-shrink-0 bg-success text-white flex items-center justify-center rounded-full mr-4">
        <i class="fas fa-users fa-2x"></i>
      </div>
      <div>
        <h2 class="text-xl font-semibold text-white mb-2">Community Engagement</h2>
        <p class="text-white">We foster a sense of community among our tenants, providing social events and support networks.</p>
      </div>
    </div>
    <!-- Additional bullet points -->
    
    <!-- End of Additional bullet points -->
    <div class="flex justify-center lg:justify-end ">
      <a href="guide.php"><button class="btn btn-primary mt-4 bg-white text-black">Learn More</button></a>
    </div>
  </div>
</div>

    </div>
    
    <!-- Right Container with Image -->
    <div class="w-full lg:w-1/3 mx-8 lg:mx-20 mt-8 lg:mt-0 lg:order-1">
  <img src="images/orange.png" alt="Orange Image" class="w-full lg:w-auto rounded-lg">
</div>

  </div>
</div>


<!-- Add the required Font Awesome script -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>



  <div data-aos="fade-up" data-aos-duration="1200" data-aos-easing="ease-in-out-back">
    <div class="hero min-h-screen" style="background-image: url(images/house-bg22.jpg); background-size: cover; background-position: center;">
      <div class="hero-overlay bg-opacity-50"></div>
      <div class="max-w-screen-lg mx-auto flex flex-col lg:flex-row justify-center px-4 py-4 lg:px-0">
        <div class="card md:card-side bg-white shadow-xl mb-5 w-full lg:w-4/5 lg:mr-5">
          <div class="card-body">
            <img src="images/listinghouse.jpg" class="min-h-fit rounded-lg">
            <h2 class="card-title text-xl font-bold">Looking for a property?</h2>
            <p class="text-justify">Explore trusted listings from verified landlords and property managers, offering a range of homes to suit your preferences. Our platform ensures a secure and hassle-free rental experience, so you can feel confident in your choice of accommodation.</p>
            <div class="card-actions justify-end">
              <a href="properties.php"><button class="btn btn-primary">View Properties</button></a>
            </div>
          </div>
        </div>

        <div class="card lg:card-side bg-white shadow-xl mb-5 w-full lg:w-4/5 lg:ml-5">
          <div class="card-body">
            <img src="images/rentinghouse.jpg" class="min-h-fit rounded-lg">
            <h2 class="card-title text-xl font-bold">Want to view trusted landholders?</h2>
            <p class="text-justify">Connect with reliable landholders and explore properties from verified owners. Ensure a trustworthy and transparent experience as you search for your next rental property, backed by genuine and dependable landholder listings.</p>
            <div class="card-actions justify-end">
              <a href="seller-list.php"><button class="btn btn-primary w-full">View Landholders</button></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/javascript.js"></script>
    <script>
      var swiperProducts = new Swiper(".products-slider", {
        loop: true,
        spaceBetween: 30,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        autoplay: {
          delay: 3000,
        },
        effect: "slide",
        breakpoints: {
          550: {
            slidesPerView: 1,
          },
          768: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 3,
          },
        },
      });
    </script>

    <script>
      AOS.init({
        duration: 1000,
        easing: 'ease-in-out-quart',
      });
    </script>

</body>
<?php include 'user/user-footer.php' ?>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let headlines = document.querySelectorAll('.headline');
  let currentHeadlineIndex = 0;

  function showNextHeadline() {
    headlines[currentHeadlineIndex].classList.remove('active');
    headlines[currentHeadlineIndex].classList.add('hidden');
    currentHeadlineIndex = (currentHeadlineIndex + 1) % headlines.length;
    headlines[currentHeadlineIndex].classList.remove('hidden');
    headlines[currentHeadlineIndex].classList.add('active');
  }

  headlines[currentHeadlineIndex].classList.add('active');
  setInterval(showNextHeadline, 3000);
});
</script>
