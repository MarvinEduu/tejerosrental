<?php
include 'components/connection.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
};
?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Category</title>
  <link rel="icon" type="image/x-icon" href="images/logoer.png">

  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- font awesome cdn link  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- custom css file link  -->
  <link rel="stylesheet" href="css/style.css">
  <style>
    html,
    body {
      overflow-x: hidden;
    }
  </style>
</head>

<body>
  <?php include 'user/user-header.php' ?>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mx-10 justify-between mt-8">
    <?php
    // Define the house types and their respective URLs and image filenames
    $houseTypes = array(
      "House" => array("url" => "house", "image" => "icon-1.png"),
      "Apartment" => array("url" => "apartment", "image" => "icon-2.png"),
      "Dorm" => array("url" => "dorm", "image" => "icon-3.png"),
      "Bedspace" => array("url" => "bedspace", "image" => "icon-4.png")
    );

    // Output the house type links with specific images
    foreach ($houseTypes as $typeName => $typeDetails) {
      // Add additional class to adjust width and margins for the "House" category
      $additionalClass = $typeName === "House" ? "md:w-full" : ""; // Adjust width for the "House" category
      echo '<a href="category.php?category=' . $typeDetails["url"] . '" class="bg-blue-200 hover:bg-blue-300 rounded-lg p-4 flex flex-col items-center justify-center" >';
      echo '<img src="images/' . $typeDetails["image"] . '" alt="' . $typeName . '" class="w-16 h-16 mb-2">';
      echo '<h3 class="text-lg font-semibold">' . $typeName . '</h3>';
      echo '</a>';
    }
    ?>
  </div>

  <section class="products">
    <div class="container mt-6 w-11/12 ml-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
        <?php
        // Number of items per page
        $itemsPerPage = 6;

        // Get the current page number
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        // Calculate the offset
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Query to fetch products with pagination based on house type
        // Include is_deleted check in the query and join with bookings_tb to exclude accepted bookings
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
          WHERE p.housetype = :category 
            AND p.status != 'Pending' 
            AND p.status != 'Rejected' 
            AND p.is_deleted = 0 
            AND b.booking_id IS NULL
          LIMIT :offset, :itemsPerPage
        ");
        $select_products->bindParam(':category', $_GET['category'], PDO::PARAM_STR);
        $select_products->bindParam(':offset', $offset, PDO::PARAM_INT);
        $select_products->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
        $select_products->execute();

        if ($select_products->rowCount() > 0) {
          while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <form action="" method="post" class="box bg-white rounded-lg shadow-md p-6">
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
          echo '<p class="empty">No products found!</p>';
        }
        ?>
      </div>
    </div>
    <div class="mt-8 mb-8 flex justify-center">
      <!-- Pagination links -->
      <?php
      // Calculate total number of pages
      $select_count = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM properties_tb p
        LEFT JOIN bookings_tb b ON p.propertyId = b.propertyId
            AND b.status = 'Accepted'
            AND b.endDate > NOW()
        WHERE p.housetype = :category 
          AND p.status != 'Pending' 
          AND p.status != 'Rejected' 
          AND p.is_deleted = 0 
          AND b.booking_id IS NULL
      ");
      $select_count->bindParam(':category', $_GET['category'], PDO::PARAM_STR);
      $select_count->execute();
      $totalRows = $select_count->fetch(PDO::FETCH_ASSOC)['total'];
      $totalPages = ceil($totalRows / $itemsPerPage);

      // Previous page link
      $prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
      echo '<a href="?page=' . $prevPage . '&category=' . $_GET['category'] . '" class="mx-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>';

      // Page numbers
      for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = $i == $currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
        echo '<a href="?page=' . $i . '&category=' . $_GET['category'] . '" class="mx-2 px-4 py-2 rounded ' . $activeClass . '">' . $i . '</a>';
      }

      // Next page link
      $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
      echo '<a href="?page=' . $nextPage . '&category=' . $_GET['category'] . '" class="mx-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>';
      ?>
    </div>
  </section>

  <script src="js/script.js"></script>

  <?php include 'user/user-footer.php' ?>
</body>

</html>
