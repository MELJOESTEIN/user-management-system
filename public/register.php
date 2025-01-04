<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Moi\UserAppClaude\Services\AuthService;

$message = '';
$success = false;
$authService = new AuthService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'username' => $_POST['username'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'first_name' => $_POST['first_name'] ?? null,
        'last_name' => $_POST['last_name'] ?? null
    ];

    if ($authService->register($userData)) {
        $message = 'Registration successful! Redirecting to login page...';
        $success = true;
    } else {
        $message = 'Registration failed. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if ($success): ?>
        <meta http-equiv="refresh" content="3;url=login.php">
    <?php endif; ?>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Register</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" <?= $success ? 'style="display: none;"' : '' ?>>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="login.php" class="btn btn-link">Already have an account? Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Add a loading spinner for successful registration -->
    <?php if ($success): ?>
        <style>
            .loading-spinner {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }

            .spinner-border {
                width: 3rem;
                height: 3rem;
            }
        </style>
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>