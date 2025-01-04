<?php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $db = Moi\UserAppClaude\Core\Database::getInstance();  // Changed namespace
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
