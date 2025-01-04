<?php

use Moi\UserAppClaude\Models\User;

if (isset($_SESSION['user_id'])) {
    $navUser = User::findById($_SESSION['user_id']);
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">User Management System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                        href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>"
                        href="users.php">Users</a>
                </li>
            </ul>
            <?php if (isset($navUser)): ?>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <?php if ($navUser->getProfilePicture()): ?>
                                <img src="../uploads/profile_pictures/<?= htmlspecialchars($navUser->getProfilePicture()) ?>"
                                    alt="Profile"
                                    class="rounded-circle me-2"
                                    style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    <span class="text-white" style="font-size: 16px;">
                                        <?= strtoupper(substr($navUser->getUsername(), 0, 1)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <span class="text-white"><?= htmlspecialchars($navUser->getUsername()) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../profile.php">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>