<?php
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use Moi\UserAppClaude\Services\AuthService;

$message = '';
$authService = new AuthService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $authService->login($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        header('Location: dashboard.php');
        exit;
    } else {
        $message = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="register.php">Don't have an account? Register here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>