<?php

include '../components/connection.php';

session_start();

// Retrieve landholder ID from session
$landholderId = $_SESSION['landholder_id']; // Adjust the session variable name as per your implementation

// Retrieve user data from database
$query = "SELECT mobile, email, facebook, business_permit, permit_status, verification_tier FROM landholders_tb WHERE landholder_id = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $landholderId, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if email and Facebook are present
$email = $result['email'];
$facebook = $result['facebook'];
$permitStatus = $result['permit_status'];

// Update verification_tier based on conditions
$verificationTier = 'Not Verified';
if (!empty($email) && !empty($facebook)) {
    $verificationTier = 'Semi-Verified';
}

if ($permitStatus === 'Verified') {
    $verificationTier = 'Fully Verified';
}

// Update the database with the correct verification tier
$updateQuery = "UPDATE landholders_tb SET verification_tier = ? WHERE landholder_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindParam(1, $verificationTier, PDO::PARAM_STR);
$updateStmt->bindParam(2, $landholderId, PDO::PARAM_INT);
$updateStmt->execute();

// Initialize other variables
$mobile = $result['mobile'];
$businessPermit = $result['business_permit'];
$progressWidth = '0'; // Default width for Not Verified

if ($verificationTier == 'Fully Verified') {
    $progressWidth = '100'; // Update progress bar width for Fully Verified
} elseif ($verificationTier == 'Semi-Verified') {
    $progressWidth = '50'; // Update progress bar width for Semi-Verified
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Verification Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php' ?>
    <div class="container mt-2 overflow-y-auto ">
        <div class="card">
            <div class="card-header">
                Seller Verification Status
            </div>
            <div class="card-body">
                <!-- Status Indicator -->
                <h5 class="card-title p-2">Account Age: <span id="accountAge">New Landholder</span></h5>
                <div class="progress" style="height: 50px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $progressWidth === '0' ? '25' : '0'; ?>%;" aria-valuenow="<?php echo $progressWidth === '0' ? '100' : '0'; ?>" aria-valuemin="0" aria-valuemax="100">Not Verified</div>
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $progressWidth === '50' ? '50' : '0'; ?>%;" aria-valuenow="<?php echo $progressWidth === '50' ? '50' : '0'; ?>" aria-valuemin="0" aria-valuemax="100">Semi-Verified</div>
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progressWidth === '100' ? '100' : '0'; ?>%;" aria-valuenow="<?php echo $progressWidth === '100' ? '100' : '0'; ?>" aria-valuemin="0" aria-valuemax="100">Fully Verified</div>
                </div>
                <!-- Verification Details -->
                <div class="mt-5">
                    <!-- <h4 class="font-bold mb-3">Why Verification Matters:</h4> -->
                    <ul>
                        <li class="mb-4">
                            <?php if (empty($mobile)) : ?>
                                <i class="fas fa-times text-danger text-lg"></i> <strong>Not Verified (Mobile Number)</strong><br>
                                <span class="text-gray-600">Remember: Your account is new or lacks essential verifications. Consider completing all verifications for enhanced trustworthiness.</span>
                            <?php else : ?>
                                <i class="fas fa-check text-success text-lg"></i> <strong> Verified (Mobile Number)</strong>
                            <?php endif; ?>
                        </li>
                        <li class="mb-4">
                            <?php if (empty($email) || empty($facebook)) : ?>
                                <i class="fas fa-times text-danger text-lg"></i> <strong>Not Semi-Verified (Email & Facebook)</strong><br>
                                <span class="text-gray-600">Remember: You've completed basic verifications, but more is needed for full trust. Aim for full verification to increase buyer confidence.</span>
                            <?php else : ?>
                                <i class="fas fa-check text-success text-lg"></i> <strong>Semi-Verified (Email & Facebook)</strong>
                            <?php endif; ?>
                        </li>
                        <li class="mb-4">
                            <?php if (empty($businessPermit)) : ?>
                                <i class="fas fa-times text-danger text-lg"></i> <strong>Not Fully Verified (Business Permit)</strong><br>
                                <?php if ($permitStatus === 'Validating') : ?>
                                    <i class="fas fa-hourglass-half text-warning text-lg"></i> <strong>Business permit is being reviewed, wait for admin's approval.</strong><br>
                                <?php else : ?>
                                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadPermitModal">Upload Business Permit</button><br>
                                    <span class="text-gray-600">Remember: Complete all verifications, including submitting a valid business permit. This builds maximum trust and professionalism with buyers.</span>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if ($permitStatus === 'Verified') : ?>
                                    <i class="fas fa-check text-success text-lg"></i> <strong>Fully Verified (Business Permit)</strong>
                                <?php else : ?>
                                    <i class="fas fa-hourglass-half text-warning text-lg"></i> <strong>Business permit is being reviewed, wait for admin's approval.</strong><br>
                                <?php endif; ?>
                                <img src="../images/verify.png" alt="Empty Illustration" class="mx-auto mt-4" style="max-width: 400px;">
                            <?php endif; ?> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Business Permit Modal -->
    <div class="modal fade" id="uploadPermitModal" tabindex="-1" aria-labelledby="uploadPermitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPermitModalLabel">Upload Business Permit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="upload_permit.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="permitFile" class="form-label">Select File:</label>
                            <input type="file" class="form-control" id="permitFile" name="permitFile" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</body>

</html>
