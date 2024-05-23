<?php
// Include database connection
include '../components/connection.php';
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_path = "../uploaded_image/" . $file_name;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            $profile_picture = $file_name;

            // Retrieve form data
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $mobile = $_POST['mobile'];
            $address = $_POST['address'];

            // Retrieve landholder_id from session
            if (isset($_SESSION['landholder_id'])) {
                $landholder_id = $_SESSION['landholder_id'];

                try {
                    // Prepare update query
                    $query = "UPDATE landholders_tb SET profile_picture = :profile_picture, full_name = :full_name, email = :email, mobile = :mobile, address = :address WHERE landholder_id = :landholder_id";
                    $stmt = $conn->prepare($query);

                    // Bind parameters
                    $stmt->bindParam(':profile_picture', $profile_picture);
                    $stmt->bindParam(':full_name', $full_name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':mobile', $mobile);
                    $stmt->bindParam(':address', $address);
                    $stmt->bindParam(':landholder_id', $landholder_id);

                    // Execute query
                    if ($stmt->execute()) {
                        // Update session variables
                        $_SESSION['profile_picture'] = $profile_picture;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['email'] = $email;
                        $_SESSION['mobile'] = $mobile;
                        $_SESSION['address'] = $address;

                        // Redirect to landholder-home.php with success message
                        header("Location: landholder-home.php");
                        exit();
                    } else {
                        throw new Exception("Failed to execute database update query.");
                    }
                } catch (Exception $e) {
                    error_log('Database Update Error: ' . $e->getMessage());
                    header('Location: landholder-home.php?status=database_error');
                    exit();
                }
            } else {
                // Redirect with unauthorized access status
                header('Location: landholder-home.php?status=unauthorized');
                exit();
            }
        } else {
            // Handle file upload error
            header('Location: landholder-home.php?status=file_upload_error');
            exit();
        }
    } else {
        // Handle file upload error
        header('Location: landholder-home.php?status=file_upload_error');
        exit();
    }
} else {
    // Redirect for invalid request method (e.g., GET request)
    header('Location: landholder-home.php?status=invalid_request');
    exit();
}
?>
