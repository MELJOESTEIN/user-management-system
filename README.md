# Modern PHP User Management System

A comprehensive user management system built with PHP 8.1+, featuring secure authentication, role-based access control, and modern architecture.

## Features

- User Authentication (Login/Register)
- Profile Management with Picture Upload
- Admin Dashboard
- Role-based Access Control
- Secure Password Handling
- Environment Configuration
- PSR-4 Autoloading
- Modern MVC Architecture

## Technical Requirements

- PHP 8.1+
- MySQL/MariaDB
- Apache/Nginx
- Composer
- PDO PHP Extension
- GD PHP Extension

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd user-management
```

2. Install dependencies:
```bash
composer install
```

3. Create database and import schema:
```sql
CREATE DATABASE user_management_system;
```
Import the SQL file from `database/schema.sql`

4. Configure environment:
```bash
cp .env.example .env
```
Update `.env` with your database credentials and app settings

5. Set up directories:
```bash
mkdir -p public/uploads/profile_pictures
chmod 777 public/uploads/profile_pictures
```

## Project Structure

```
user-management/
├── config/
│   └── database.php
├── public/
│   ├── index.php
│   ├── admin/
│   └── uploads/
├── src/
│   ├── Core/
│   │   └── Database.php
│   ├── Controllers/
│   ├── Models/
│   │   └── User.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── FileUploadService.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Views/
├── templates/
│   ├── nav.php
│   └── admin-nav.php
├── vendor/
├── .env
├── .gitignore
└── composer.json
```

## Key Components

### Models
- `User.php`: User entity with profile management
- Database interactions using PDO

### Services
- `AuthService.php`: Authentication logic
- `FileUploadService.php`: Profile picture handling

### Middleware
- `AdminMiddleware.php`: Admin access control

### Features Implementation

#### User Authentication
- Secure password hashing
- Session management
- Remember me functionality
- Password reset capability

#### Profile Management
- Profile picture upload
- Personal information updates
- Account settings

#### Admin Dashboard
- User management CRUD operations
- Role management
- User activity monitoring

## Security Features

- Password Hashing (PHP's password_hash)
- SQL Injection Prevention (Prepared Statements)
- XSS Protection
- CSRF Protection
- Secure File Upload Handling
- Role-based Access Control

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is released under the [MIT License](LICENSE).

Copyright (c) 2025 pront-Ix

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.


## Acknowledgments

- [pront-Ix](https://pront-ix.com)
- [PHP 8.1+](https://www.php.net/)
- [MySQL/MariaDB](https://www.mysql.com/)
- [Apache/Nginx](https://httpd.apache.org/)
- [Composer](https://getcomposer.org/)
- [GD PHP Extension](https://php.net/manual/en/image.installation.php)
- [PDO PHP Extension](https://php.net/manual/en/pdo.installation.php)


## Support

For support, email contact@pront-ix.com or create an issue in the repository.

## Credits

Created by pront-Ix