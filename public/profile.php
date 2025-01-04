<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Moi\UserAppClaude\Models\User;
use Moi\UserAppClaude\Services\FileUploadService;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// Get current user data
$user = User::findById($_SESSION['user_id']);

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'upload_picture') {
        $uploadService = new FileUploadService();

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = $uploadService->uploadProfilePicture($_FILES['profile_picture']);

            if ($result['success']) {
                // Delete old profile picture if exists
                if ($user->getProfilePicture()) {
                    $uploadService->deleteProfilePicture($user->getProfilePicture());
                }

                // Update user profile with new picture
                $user->setProfilePicture($result['filename']);
                if ($user->save()) {
                    $message = 'Profile picture updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update profile picture in database.';
                    $messageType = 'danger';
                }
            } else {
                $message = $result['error'];
                $messageType = 'danger';
            }
        }
    } else if ($_POST['action'] === 'update_profile') {
        // Update profile information
        $user->setUsername($_POST['username'])
            ->setEmail($_POST['email'])
            ->setFirstName($_POST['first_name'])
            ->setLastName($_POST['last_name']);

        if ($user->save()) {
            $message = 'Profile updated successfully!';
            $messageType = 'success';
            $_SESSION['username'] = $user->getUsername(); // Update session username
        } else {
            $message = 'Failed to update profile.';
            $messageType = 'danger';
        }
    } else if ($_POST['action'] === 'update_password') {
        // Verify current password
        if (password_verify($_POST['current_password'], $user->getPasswordHash())) {
            // Check if new passwords match
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $user->setPasswordHash(password_hash($_POST['new_password'], PASSWORD_DEFAULT));

                if ($user->save()) {
                    $message = 'Password updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update password.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'New passwords do not match.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Current password is incorrect.';
            $messageType = 'danger';
        }
    } else if ($_POST['action'] === 'remove_picture') {
        $uploadService = new FileUploadService();

        // Delete the file
        if ($user->getProfilePicture() && $uploadService->deleteProfilePicture($user->getProfilePicture())) {
            // Update user profile
            $user->setProfilePicture(null);
            if ($user->save()) {
                $message = 'Profile picture removed successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to update profile.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Failed to remove profile picture.';
            $messageType = 'danger';
        }
    }
}

// Add this right after processing the file upload
if (isset($_FILES['profile_picture'])) {
    error_log("Upload attempt details:");
    error_log("Temp file: " . $_FILES['profile_picture']['tmp_name']);
    error_log("Upload error code: " . $_FILES['profile_picture']['error']);
    error_log("File size: " . $_FILES['profile_picture']['size']);
    error_log("File type: " . $_FILES['profile_picture']['type']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-picture-container {
            position: relative;
            display: inline-block;
        }

        .profile-picture-container:hover .edit-overlay {
            opacity: 1;
        }

        .edit-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .edit-overlay i {
            color: white;
            font-size: 24px;
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">User Management System</a>
            <div class="navbar-nav ms-auto">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="profile.php" class="nav-link active">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Picture Section -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Profile Picture</h4>
                    </div>
                    <div class="card-body text-center">
                        <?php if ($user->getProfilePicture()): ?>
                            <img src="uploads/profile_pictures/<?= htmlspecialchars($user->getProfilePicture()) ?>"
                                alt="Profile Picture"
                                class="rounded-circle mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary mb-3 mx-auto d-flex align-items-center justify-content-center"
                                style="width: 150px; height: 150px;">
                                <span class="text-white" style="font-size: 48px;">
                                    <?= strtoupper(substr($user->getUsername(), 0, 1)) ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload_picture">
                            <div class="mb-3">
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                    accept="image/jpeg,image/png,image/gif" required>
                                <div class="form-text">
                                    Maximum file size: 5MB. Allowed formats: JPG, PNG, GIF
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload New Picture</button>
                            <?php if ($user->getProfilePicture()): ?>
                                <button type="submit" name="action" value="remove_picture"
                                    class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    Remove Picture
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= htmlspecialchars($user->getUsername()) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    value="<?= htmlspecialchars($user->getFirstName() ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    value="<?= htmlspecialchars($user->getLastName() ?? '') ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_password">

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password"
                                    name="new_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Profile Picture Preview Script -->
    <script>
        document.getElementById('profile_picture').onchange = function(e) {
            if (this.files && this.files[0]) {
                if (this.files[0].size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.rounded-circle');
                    if (preview) {
                        if (preview.tagName === 'IMG') {
                            preview.src = e.target.result;
                        } else {
                            // Replace div with img
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'rounded-circle mb-3';
                            img.style = 'width: 150px; height: 150px; object-fit: cover;';
                            preview.parentNode.replaceChild(img, preview);
                        }
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        };
    </script>
</body>

</html>