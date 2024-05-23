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
  <link rel="icon" type="image/x-icon" href="images/logoer.png">

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
    .swiper-slide {
      margin-right: 20px;
    }

    .swiper-slide:last-child {
      margin-right: 50px;
    }

    .swiper-slide img {
      width: 100%;
      height: 200px;
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
  </style>
</head>

<body class="overflow-x-hidden">

  <?php include 'user/user-header.php' ?>

  <div data-aos="zoom-out" data-aos-duration="700" data-aos-easing="ease-in-out-back">
    <div class="hero max-h-screen" style="background-image: url('images/houses.jpg'); background-size: cover; background-position: center;">
      <div class="hero-overlay rounded-lg bg-opacity-70"></div>
      <div class="hero-content flex-col lg:flex-row w-fit gap-10">
        <img src="images/home-family.png" class="w-3/6 rounded-lg" />
        <div style="width: 100%;">
          <h1 class="text-4xl font-semibold uppercase w-full text-left text-white">Skip the tedious process. Explore our hassle-free property rentals.</h1>
          <p class="mt-4 text-md text-white">Discover a place you'll love to live. Browse through our extensive list of properties tailored to fit your lifestyle and budget.</p>
          <a href="properties.php"><button class="btn btn-primary mt-6 mb-2">View Properties</button></a>
        </div>
      </div>
    </div>
  </div>

  <div data-aos="fade-up" data-aos-duration="1000" data-aos-easing="ease-in-out-back">
    <section class="home-products h-fit pb-14" style="background-image: url('images/wave3.svg'); background-size: cover; background-position: center;">
      <div class="swiper products-slider ml-10 mr-10">
        <div class="text-center mt-24">
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
              <form action="" method="post" class="swiper-slide border rounded-lg p-4 bg-base-200">
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
    <div class="hero min-h-fit bg-base-200" style="background-image: url('images/wave2.svg'); background-size: cover; background-position: center;">
      <div class="hero-content flex-col lg:flex-row-reverse mr-20 ml-20">
        <img src="images/house-bg.png" class="w-full lg:w-2/4 h-auto lg:h-auto rounded-lg object-cover" />
        <div class="text-justify">
          <h1 class="text-5xl text-white font-bold py-6">About Us</h1>
          <p class="font-normal text- text-white">We provide the best property for you.</p>
          <p class="py-4 text-white">Affordable houses comes with safety and security. Established to accommodate tenants and homeowners needs.</p>
          <p class="py-4">With careful and curated identification and analysis of properties authenticity.</p>
          <p class="py-4">Aims to help average normal families to move and settle without worrying from untrusted transaction deals.</p>
          <a href="guide.php"><button class="btn btn-primary">Learn More</button></a>
        </div>
      </div>
    </div>
  </div>

  <div data-aos="fade-up" data-aos-duration="1200" data-aos-easing="ease-in-out-back">
    <div class="hero min-h-screen" style="background-image: url(images/house-bg22.jpg); background-size: cover; background-position: center;">
      <div class="hero-overlay bg-opacity-50"></div>
      <div class="max-w-screen-lg mx-auto flex flex-col lg:flex-row justify-center px-4 py-4 lg:px-0">
        <div class="card md:card-side bg-base-300 shadow-xl mb-5 w-full lg:w-4/5 lg:mr-5">
          <div class="card-body">
            <img src="images/listinghouse.jpg" class="min-h-fit rounded-lg">
            <h2 class="card-title">Looking for a property?</h2>
            <p class="text-justify">Explore trusted listings from verified landlords and property managers, offering a range of homes to suit your preferences. Our platform ensures a secure and hassle-free rental experience, so you can feel confident in your choice of accommodation.</p>
            <div class="card-actions justify-end">
              <a href="properties.php"><button class="btn btn-primary">View Properties</button></a>
            </div>
          </div>
        </div>

        <div class="card lg:card-side bg-base-300 shadow-xl mb-5 w-full lg:w-4/5 lg:ml-5">
          <div class="card-body">
            <img src="images/rentinghouse.jpg" class="min-h-fit rounded-lg">
            <h2 class="card-title">Want to view trusted landholders?</h2>
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
            slidesPerView: 2,
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