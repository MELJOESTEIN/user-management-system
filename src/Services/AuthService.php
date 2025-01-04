<?php

namespace Moi\UserAppClaude\Services;

use Moi\UserAppClaude\Models\User;

class AuthService
{
    public function register(array $userData): bool
    {
        if (!$this->validateRegistrationData($userData)) {
            return false;
        }

        // Check if user already exists
        if (User::findByEmail($userData['email'])) {
            return false;
        }

        // Hash password
        $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        unset($userData['password']);

        $user = new User($userData);
        return $user->save();
    }

    public function login(string $email, string $password): ?User
    {
        $user = User::findByEmail($email);
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return null;
        }

        // Update last login time
        $user->setLastLogin(date('Y-m-d H:i:s'));
        $user->save();

        return $user;
    }

    private function validateRegistrationData(array $data): bool
    {
        return !empty($data['email']) &&
            !empty($data['password']) &&
            !empty($data['username']) &&
            filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }
}
