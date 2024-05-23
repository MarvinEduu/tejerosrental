<?php
@include '../components/connection.php';
session_start();

// Retrieve user profile information from the database
$user_id = $_SESSION['user_id'];
$select_user = $conn->prepare("SELECT profile_picture, full_name, email, mobile, address, bio, facebook, linkedin, instagram FROM users_tb WHERE user_id = ?");
$select_user->execute([$user_id]);
$fetch_profile = $select_user->fetch(PDO::FETCH_ASSOC);

// Check if profile information exists
if (!$fetch_profile) {
    // Handle error when profile is not found
    echo "Profile not found!";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Include Tailwind CSS and DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <style>
        .profile-picture {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-100 py-5">
    
    <div class="container mx-auto">
        <div class="card bg-white p-5 rounded shadow">
            <h2 class="text-2xl font-bold text-center mb-4">Update Profile</h2>
            <form action="process-update-profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Left Column: User Information -->
                    <div>
                        <div class="text-center">
                        <input type="hidden" name="current_profile_picture" value="<?= $fetch_profile['profile_picture']; ?>">
                            <img src="../uploaded_image/<?= $fetch_profile['profile_picture']; ?>" alt="Profile Picture" class="profile-picture mx-auto">
                            <div class="form-file mt-2">
                                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="input input-bordered w-full">
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="full_name" class="label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="input input-bordered w-full" value="<?= $fetch_profile['full_name'] ?>" required>
                        </div>
                        <div class="form-control">
                            <label for="email" class="label">Email</label>
                            <input type="email" id="email" name="email" class="input input-bordered w-full" value="<?= $fetch_profile['email'] ?>" required>
                        </div>
                        <div class="form-control">
                            <label for="mobile" class="label">Mobile Number</label>
                            <input type="text" id="mobile" name="mobile" class="input input-bordered w-full" value="<?= $fetch_profile['mobile'] ?>" required>
                        </div>
                        <div class="form-control">
                            <label for="address" class="label">Address</label>
                            <input type="text" id="address" name="address" class="input input-bordered w-full" value="<?= $fetch_profile['address'] ?>" required>
                        </div>
                        <div class="form-control">
                            <label for="bio" class="label">Bio</label>
                            <textarea id="bio" name="bio" class="textarea textarea-bordered w-full" rows="4" required><?= $fetch_profile['bio'] ?></textarea>
                        </div>
                    </div>

                    <!-- Right Column: Social Media Links and Password Change -->
                    <div>
                        <h3 class="text-xl font-semibold mb-3">Social Media Links</h3>
                        <div class="form-control">
                            <label for="facebook" class="label">Facebook</label>
                            <input type="text" id="facebook" name="facebook" class="input input-bordered w-full" value="<?= $fetch_profile['facebook'] ?>">
                        </div>
                        <div class="form-control">
                            <label for="linkedin" class="label">LinkedIn</label>
                            <input type="text" id="linkedin" name="linkedin" class="input input-bordered w-full" value="<?= $fetch_profile['linkedin'] ?>">
                        </div>
                        <div class="form-control">
                            <label for="instagram" class="label">Instagram</label>
                            <input type="text" id="instagram" name="instagram" class="input input-bordered w-full" value="<?= $fetch_profile['instagram'] ?>">
                        </div>

                        <h3 class="text-xl font-semibold mt-5 mb-3">Change Password</h3>
                        <div class="form-control">
                            <label for="old_password" class="label">Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="input input-bordered w-full">
                        </div>
                        <div class="form-control">
                            <label for="new_password" class="label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="input input-bordered w-full">
                        </div>
                        <div class="form-control">
                            <label for="confirm_password" class="label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="input input-bordered w-full">
                        </div>
                    </div>
                </div>
                <!-- Hidden Input for user_id -->
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <!-- Submit Button -->
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="../loading-page-in.php" class="btn btn-secondary ml-3">Go Back</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

