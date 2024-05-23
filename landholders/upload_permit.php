<?php
include '../components/connection.php'; // Include your database connection file

session_start();
$landholderId = $_SESSION['landholder_id']; // Adjust the session variable name as per your implementation

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["permitFile"]) && $_FILES["permitFile"]["error"] == 0) {
        $permitFile = $_FILES["permitFile"];

        // Check file size (maximum 5MB)
        if ($permitFile["size"] > 5 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
            exit;
        }

        // Allow only certain file formats
        $allowedFormats = array("pdf", "doc", "docx");
        $fileExtension = pathinfo($permitFile["name"], PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $allowedFormats)) {
            echo "Sorry, only PDF, DOC, and DOCX files are allowed.";
            exit;
        }

        // Generate a unique filename
        $newFileName = uniqid() . '_' . basename($permitFile["name"]);

        // Move the file to the specified directory
        if (move_uploaded_file($permitFile["tmp_name"], "../uploaded_image/business_permit/" . $newFileName)) {
            // Insert the filename into the database and set permit_status to 'Validating'
            $query = "UPDATE landholders_tb SET business_permit = :business_permit, permit_status = 'Validating' WHERE landholder_id = :landholder_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":business_permit", $newFileName);
            $stmt->bindParam(":landholder_id", $landholderId);
            $stmt->execute();

            echo "The file " . htmlspecialchars(basename($permitFile["name"])) . " has been uploaded and is being reviewed.";
            header("Location: landholder-status.php");
            exit;
        } else {
            echo "Sorry, there was an error uploading your file.";
            header("Location: landholder-status.php");
            exit;
        }
    } else {
        echo "No file was uploaded.";
    }
} else {
    // Redirect back to the form page if accessed directly
    header("Location: landholder-status.php");
    exit;
}
?>
