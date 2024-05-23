<?php
// Include database connection file
include '../components/connection.php';

session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $bio = $_POST['bio'];
    $facebook = $_POST['facebook'];
    $linkedin = $_POST['linkedin'];
    $instagram = $_POST['instagram'];
    $new_password = $_POST['new_password']; // New password from form input

    // Initialize profile_picture variable with existing value (assuming it's passed as a hidden input)
    $profile_picture = $_POST['current_profile_picture'];

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_path = "../uploaded_image/" . $file_name;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the database with the new file path
            $profile_picture = $file_name;
        } else {
            // Handle error during file upload
            header('Location: update-profile.php?status=upload_error');
            exit;
        }
    }

    // Prepare SQL query based on whether a new password is provided
    $sql = "UPDATE `users_tb` SET profile_picture = ?, full_name = ?, email = ?, mobile = ?, address = ?, bio = ?, facebook = ?, linkedin = ?, instagram = ?" . (!empty($new_password) ? ", password = ?" : "") . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $params = [$profile_picture, $full_name, $email, $mobile, $address, $bio, $facebook, $linkedin, $instagram];
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $params[] = $hashed_password;
    }
    $params[] = $user_id;

    // Execute query
    $update_success = $stmt->execute($params);

    if ($update_success) {
        // Redirect back to update-profile.php with success message
        header('Location: update-profile.php?status=success');
        exit;
    } else {
        // Redirect back to update-profile.php with error message
        header('Location: update-profile.php?status=error');
        exit;
    }
} else {
    // Redirect back to update-profile.php with error message (if accessed directly)
    header('Location: update-profile.php?status=error');
    exit;
}
?>
