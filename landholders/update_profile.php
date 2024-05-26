<?php
session_start();
@include '../components/connection.php';

if (!isset($_SESSION['landholder_id'])) {
    header("Location: ../login.php");
    exit;
}

$landholder_id = $_SESSION['landholder_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $bio = $_POST['bio'];
    $facebook = $_POST['facebook'];
    $linkedin = $_POST['linkedin'];
    $instagram = $_POST['instagram'];
    
    // Handle the profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_path = "../uploaded_image/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $profile_picture = $file_name;
    } else {
        $profile_picture = $_SESSION['profile_picture'];
    }

    // Update the database
    $query = "UPDATE landholders_tb SET full_name = :full_name, email = :email, mobile = :mobile, address = :address, profile_picture = :profile_picture, bio = :bio, facebook = :facebook, linkedin = :linkedin, instagram = :instagram WHERE landholder_id = :landholder_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':profile_picture', $profile_picture);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':facebook', $facebook);
    $stmt->bindParam(':linkedin', $linkedin);
    $stmt->bindParam(':instagram', $instagram);
    $stmt->bindParam(':landholder_id', $landholder_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['mobile'] = $mobile;
        $_SESSION['address'] = $address;
        $_SESSION['profile_picture'] = $profile_picture;
        $_SESSION['bio'] = $bio;
        $_SESSION['facebook'] = $facebook;
        $_SESSION['linkedin'] = $linkedin;
        $_SESSION['instagram'] = $instagram;

        // Redirect or show success message
        header("Location: landholder-home.php");
        exit;
    } else {
        echo "Failed to update profile.";
    }
}
?>
