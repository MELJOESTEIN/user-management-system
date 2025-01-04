<?php
// public/admin/users.php
require_once __DIR__ . '/../../vendor/autoload.php';

use Moi\UserAppClaude\Middleware\AdminMiddleware;
use Moi\UserAppClaude\Models\User;

AdminMiddleware::handleRequest();

// Get all users
$db = \Moi\UserAppClaude\Core\Database::getInstance();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>

<body>
    <?php include '../../templates/admin-nav.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">User Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_user.php" class="btn btn-sm btn-primary">
                            <i class="bi bi-person-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $userData):
                                $userObj = new User($userData);
                            ?>
                                <tr>
                                    <td><?= $userObj->getId() ?></td>
                                    <td>
                                        <?php if ($userObj->getProfilePicture()): ?>
                                            <img src="../uploads/profile_pictures/<?= htmlspecialchars($userObj->getProfilePicture()) ?>"
                                                class="rounded-circle" width="32" height="32">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <span class="text-white"><?= strtoupper(substr($userObj->getUsername(), 0, 1)) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($userObj->getUsername()) ?></td>
                                    <td><?= htmlspecialchars($userObj->getEmail()) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $userObj->getRole() === 'admin' ? 'primary' : 'secondary' ?>">
                                            <?= ucfirst($userObj->getRole()) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $userObj->isActive() ? 'success' : 'danger' ?>">
                                            <?= $userObj->isActive() ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= (new DateTime($userData['created_at']))->format('Y-m-d') ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form method="POST" action="dashboard.php">
                                                        <input type="hidden" name="user_id" value="<?= $userObj->getId() ?>">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <button type="submit" class="dropdown-item">
                                                            <?= $userObj->isActive() ? 'Deactivate' : 'Activate' ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="dashboard.php">
                                                        <input type="hidden" name="user_id" value="<?= $userObj->getId() ?>">
                                                        <input type="hidden" name="action" value="toggle_role">
                                                        <button type="submit" class="dropdown-item">
                                                            <?= $userObj->getRole() === 'admin' ? 'Remove Admin' : 'Make Admin' ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form method="POST" action="dashboard.php"
                                                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        <input type="hidden" name="user_id" value="<?= $userObj->getId() ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>