# User Management System

A simple PHP-based user authentication system with role-based access for admins and regular users. This project demonstrates user login, logout, and session management using a MySQL database.

## Features
- User authentication with email and password.
- Role-based access: Admin and Regular User roles.
- Session management for secure logins.
- Logging of admin activities.
- Basic UI with HTML and CSS for login/logout functionality.

## Tech Stack
- **PHP**: Backend logic and user authentication.
- **MySQL**: Database for storing user data.
- **HTML/CSS**: Frontend for the login interface.
- **PDO**: Secure database connection and queries.
- **PSR-4 Autoloading**: For organized class loading.

## Prerequisites
Before running the project, ensure you have:
- PHP 7.4 or higher.
- MySQL 5.7 or higher.
- A web server (e.g., Apache) with PHP support (e.g., via XAMPP, WAMP, or MAMP).
- Git (to clone the repository).
- Composer (optional, if you add dependencies later).

## Installation
Follow these steps to set up the project locally:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/DevFMShad/OOP-task-user-management
   cd user-management-system


-----------------------------------------------------Database Setup--------------------------------------

Set Up the Database:
Create a MySQL database named user_auth_system.

Import the following SQL to create the users table and add test users:
sql

CREATE DATABASE user_auth_system;
USE user_auth_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (user, Password) VALUES
    ('alice@example.com', 'admin123'),
    ('bob@example.com', 'user123');

----------------------------Configure the Connection---------------------------------------

Configure Database Connection:
The database configuration is located in config/database.php.

Update the file with your MySQL credentials if necessary (default uses root with no password):
php

return [
    'host' => 'localhost',
    'dbname' => 'user_auth_system',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

------------------------------------------------------------------------------------------------

Usage

Test Credentials:
Admin: Email: alice@example.com, Password: admin123

Regular User: Email: bob@example.com, Password: user123

Log in with the above credentials to test the system.

Admins have activity logging (visible in the UI), while regular users do not.

Use the "Logout" button to end the session.

--------------------------------------------------------------------------------------------------


Project Structure

user-management-system/
├── App/                    # Application code
│   ├── Core/               # Core classes and interfaces
│   ├── Models/             # User models (Admin, RegularUser)
│   ├── Repositories/       # Data access layer
│   └── Services/           # Business logic
├── config/                 # Configuration files
│   └── database.php        # Database configuration
├── autoload.php            # PSR-4 autoloader
├── index.php               # Main entry point (login page)
├── README.md               # Project documentation
├── .gitignore              # Git ignore file
└── LICENSE                 # License file





Security Note
 This project stores passwords in plain text, which is insecure. For production use:
Hash passwords using password_hash() and verify them with password_verify().

Add a role column to the users table instead of hardcoding roles in UserRepository.

Use HTTPS to secure data in transit.

Sanitize and validate all user inputs to prevent SQL injection (already mitigated by PDO in this project).

Contributing
Contributions are welcome! Please follow these steps:
Fork the repository.

Create a new branch (git checkout -b feature/your-feature).

Make your changes and commit them (git commit -m "Add your feature").

Push to your branch (git push origin feature/your-feature).

Open a pull request.

License
This project is licensed under the MIT License. See the LICENSE file for details.
Acknowledgments
Built as a learning project for PHP and MySQL.

Uses PDO for secure database interactions.

Inspired by basic MVC patterns and OOP principles.





