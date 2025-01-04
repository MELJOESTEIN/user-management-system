<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><?= $_ENV['APP_NAME'] ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'login.php' ? 'active' : '' ?>" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'register.php' ? 'active' : '' ?>" href="register.php">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>