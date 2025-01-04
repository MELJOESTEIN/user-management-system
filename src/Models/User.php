<?php

namespace Moi\UserAppClaude\Models;

use PDO;
use Moi\UserAppClaude\Core\Database;

class User
{
    private ?int $id = null;
    private string $username;
    private string $email;
    private string $password_hash;
    private ?string $first_name = null;
    private ?string $last_name = null;
    private ?string $profile_picture = null;  // Add this property
    private bool $is_active = true;
    private ?string $last_login = null;
    private string $role = 'user';

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // Existing Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }
    public function getLastName(): ?string
    {
        return $this->last_name;
    }
    public function isActive(): bool
    {
        return $this->is_active;
    }
    public function getLastLogin(): ?string
    {
        return $this->last_login;
    }
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    // Add new profile picture getter
    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    // Existing Setters
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setPasswordHash(string $password_hash): self
    {
        $this->password_hash = $password_hash;
        return $this;
    }
    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }
    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }
    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }
    public function setLastLogin(?string $last_login): self
    {
        $this->last_login = $last_login;
        return $this;
    }

    // Add new profile picture setter
    public function setProfilePicture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;
        return $this;
    }


    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        if (!in_array($role, ['user', 'admin'])) {
            throw new \InvalidArgumentException('Invalid role specified');
        }
        $this->role = $role;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Replace the existing save() method with this updated version
    public function save(): bool
    {
        $db = Database::getInstance();

        if ($this->id === null) {
            $sql = "INSERT INTO users (username, email, role, password_hash, first_name, last_name, profile_picture, is_active) 
                VALUES (:username, :email, :role, :password_hash, :first_name, :last_name, :profile_picture, :is_active)";
        } else {
            $sql = "UPDATE users SET 
                username = :username, 
                email = :email,
                role = :role,
                password_hash = :password_hash, 
                first_name = :first_name, 
                last_name = :last_name,
                profile_picture = :profile_picture,
                is_active = :is_active 
                WHERE id = :id";
        }

        $stmt = $db->prepare($sql);

        $params = [
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'password_hash' => $this->password_hash,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_picture' => $this->profile_picture,
            'is_active' => $this->is_active
        ];

        if ($this->id !== null) {
            $params['id'] = $this->id;
        }

        return $stmt->execute($params);
    }


    // Keep existing static methods
    public static function findById(int $id): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userData) {
            return null;
        }

        return new self($userData);
    }

    public static function findByEmail(string $email): ?self
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$userData) {
            return null;
        }

        return new self($userData);
    }
}
