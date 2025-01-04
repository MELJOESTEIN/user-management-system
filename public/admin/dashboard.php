<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Moi\UserAppClaude\Middleware\AdminMiddleware;
use Moi\UserAppClaude\Models\User;
use PDO;

AdminMiddleware::handleRequest();

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$db = \Moi\UserAppClaude\Core\Database::getInstance();

// Get total users count
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = ceil($totalUsers / $perPage);

// Get users for current page
$stmt = $db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle user actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = $_POST['user_id'] ?? null;

    if ($userId) {
        $targetUser = User::findById($userId);

        if ($targetUser) {
            switch ($_POST['action']) {
                case 'toggle_status':
                    $targetUser->setIsActive(!$targetUser->isActive());
                    if ($targetUser->save()) {
                        $message = 'User status updated successfully!';
                        $messageType = 'success';
                    }
                    break;

                case 'toggle_role':
                    $newRole = $targetUser->getRole() === 'admin' ? 'user' : 'admin';
                    $targetUser->setRole($newRole);
                    if ($targetUser->save()) {
                        $message = 'User role updated successfully!';
                        $messageType = 'success';
                    }
                    break;

                case 'delete':
                    // Add user deletion logic here
                    if ($targetUser->getId() != $_SESSION['user_id']) {
                        // Delete user's profile picture if exists
                        if ($targetUser->getProfilePicture()) {
                            $picturePath = __DIR__ . '/../uploads/profile_pictures/' . $targetUser->getProfilePicture();
                            if (file_exists($picturePath)) {
                                unlink($picturePath);
                            }
                        }
                        // Delete user from database
                        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
                        if ($stmt->execute(['id' => $userId])) {
                            $message = 'User deleted successfully!';
                            $messageType = 'success';
                        }
                    } else {
                        $message = 'You cannot delete your own account!';
                        $messageType = 'danger';
                    }
                    break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
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
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- User Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
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
                                                alt="Profile"
                                                class="rounded-circle"
                                                style="width: 32px; height: 32px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <span class="text-white" style="font-size: 14px;">
                                                    <?= strtoupper(substr($userObj->getUsername(), 0, 1)) ?>
                                                </span>
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
                                                    <form method="POST">
                                                        <input type="hidden" name="user_id" value="<?= $userObj->getId() ?>">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <button type="submit" class="dropdown-item">
                                                            <?= $userObj->isActive() ? 'Deactivate' : 'Activate' ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST">
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
                                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>