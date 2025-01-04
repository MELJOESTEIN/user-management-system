<?php

namespace Moi\UserAppClaude\Middleware;

use Moi\UserAppClaude\Models\User;

class AdminMiddleware
{
    public static function handleRequest(): void
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $user = User::findById($_SESSION['user_id']);
        if (!$user || !$user->isAdmin()) {
            $_SESSION['error'] = 'Access denied. Administrator privileges required.';
            header('Location: dashboard.php');
            exit;
        }
    }
}
