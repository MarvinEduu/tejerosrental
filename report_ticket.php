<?php
include 'components/connection.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Handle case when user is not logged in or user_id is not available
    die('User not logged in or user_id not available');
}

// Fetch property details from the database
$property = null;
$landholder = null;
if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $select_property = $conn->prepare("SELECT * FROM `properties_tb` WHERE propertyId = ? AND status != 'Pending' AND status != 'Rejected' AND is_deleted = 0");
    $select_property->execute([$pid]);
    $property = $select_property->fetch(PDO::FETCH_ASSOC);

    // Fetch landholder details
    if ($property && isset($property['landholder_id'])) {
        $landholderId = $property['landholder_id'];
        $select_landholder = $conn->prepare("SELECT * FROM `landholders_tb` WHERE landholder_id = ?");
        $select_landholder->execute([$landholderId]);
        $landholder = $select_landholder->fetch(PDO::FETCH_ASSOC);
    }
}

// Retrieve form data using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['reportType'];
    $reportDescription = $_POST['reportDescription'];

    // Assuming you have user_id and landholder_id from the session or another source
    $user_id = 1; // Example user ID
    $landholder_id = 1; // Example landholder ID

    try {
        // Prepare SQL statement to insert report into the database
        $stmt = $conn->prepare("INSERT INTO reports (user_id, landholder_id, report_type, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $landholder_id, $reportType, $reportDescription]);

        echo "Report has been sent. Admins will investigate. Thank you.";
    } catch(PDOException $e) {
        echo "Error submitting report: " . $e->getMessage();
    }
}

// Assuming $user_id is obtained from session or another source
$user_id = 1; // Example user ID

// Check if the user_id exists in the users table
$stmt = $conn->prepare('SELECT COUNT(*) FROM users_tb WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$userExists = $stmt->fetchColumn();

if (!$userExists) {
    die('Invalid user ID'); // Handle invalid user ID scenario
}

// Prepare and execute SQL insert statement
$stmt = $conn->prepare('INSERT INTO reports (user_id, landholder_id, report_type, description) VALUES (:user_id, :landholder_id, :report_type, :description)');
$stmt->execute([
    'user_id' => $user_id,
    'landholder_id' => $landholder_id, // Replace with actual landholder ID if applicable
    'report_type' => $reportType,
    'description' => $description
]);

// If execution reaches here, report has been successfully inserted
echo 'Report has been sent. Admins will investigate. Thank you.';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Ticket</title>
    <style>
        /* Style for the modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2>Report Ticket</h2>

<!-- Button to open the modal -->
<button onclick="openModal()">Report</button>

<!-- The modal -->
<div id="reportModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Issue a report:</h3>
    <form id="reportForm" action="submit_report.php" method="post">
      <label for="reportType">Type of Report:</label>
      <select id="reportType" name="reportType">
        <option value="Non-Responsive Landholder">Non-Responsive Landholder</option>
        <option value="Customer Service Complaint">Customer Service Complaint</option>
        <option value="Safety or Trust Concern Report">Safety or Trust Concern Report</option>
        <option value="User Experience Feedback">User Experience Feedback</option>
      </select>
      <br>
      <label for="reportDescription">Description:</label><br>
      <textarea id="reportDescription" name="reportDescription" rows="4" cols="50" placeholder="Describe what happened..."></textarea>
      <br>
      <button type="submit">Submit Report</button>
    </form>
  </div>
</div>

<script>
// Function to open modal
function openModal() {
  document.getElementById('reportModal').style.display = 'block';
}

// Function to close modal
function closeModal() {
  document.getElementById('reportModal').style.display = 'none';
}
</script>

</body>
</html>
