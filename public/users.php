<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Moi\UserAppClaude\Models\User;
use PDO;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get all users
function getAllUsers(): array
{
    $db = \Moi\UserAppClaude\Core\Database::getInstance();
    $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .user-card {
            transition: transform 0.2s;
        }

        .user-card:hover {
            transform: translateY(-5px);
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <?php include '../templates/nav.php'; ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2>User Directory</h2>
                <p class="text-muted">Discover and connect with other users</p>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($users as $userData):
                $userObj = new User($userData);
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card user-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <?php if ($userObj->getProfilePicture()): ?>
                                    <img src="uploads/profile_pictures/<?= htmlspecialchars($userObj->getProfilePicture()) ?>"
                                        alt="Profile"
                                        class="rounded-circle profile-picture">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary mx-auto profile-picture d-flex align-items-center justify-content-center">
                                        <span class="text-white" style="font-size: 48px;">
                                            <?= strtoupper(substr($userObj->getUsername(), 0, 1)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <h5 class="card-title mb-0">
                                <?= htmlspecialchars($userObj->getUsername()) ?>
                            </h5>

                            <?php if ($userObj->getFirstName() || $userObj->getLastName()): ?>
                                <p class="text-muted">
                                    <?= htmlspecialchars(trim($userObj->getFirstName() . ' ' . $userObj->getLastName())) ?>
                                </p>
                            <?php endif; ?>

                            <p class="card-text text-muted small">
                                <i class="bi bi-clock"></i>
                                Joined <?= (new DateTime($userData['created_at']))->format('M Y') ?>
                            </p>

                            <?php if ($userObj->getId() === $_SESSION['user_id']): ?>
                                <a href="profile.php" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit Profile
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>