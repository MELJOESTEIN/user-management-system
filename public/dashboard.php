<?php
// public/dashboard.php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Moi\UserAppClaude\Models\User;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = User::findById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>

<body>
    <?php include '../templates/nav.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Welcome, <?= htmlspecialchars($user->getFirstName() ?? $user->getUsername()) ?>!</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
                                        <h5 class="card-title">Update Profile</h5>
                                        <p class="card-text">Modify your personal information and settings</p>
                                        <a href="profile.php" class="btn btn-primary">
                                            <i class="bi bi-pencil"></i> Edit Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
                                        <h5 class="card-title">User Directory</h5>
                                        <p class="card-text">Browse and connect with other users</p>
                                        <a href="users.php" class="btn btn-primary">
                                            <i class="bi bi-search"></i> View Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>