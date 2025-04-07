<?php
require 'autoload.php';
session_start();

use App\Models\Admin;
use App\Models\RegularUser;
use App\Services\AuthService;

// Initialize variables for error messages
$error = '';
$success = '';

// Predefined users (in a real app, these would come from a database)
$users = [
    'admin' => new Admin("Alice", "alice@example.com", "admin123"),
    'regular' => new RegularUser("Bob", "bob@example.com", "user123")
];

// Handle login
if (!isset($_SESSION['user'])) {
    if (isset($_POST['login'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $authService = new AuthService();

        // Try to authenticate with each user type
        $result = null;
        foreach ($users as $user) {
            if ($user->getEmail() === $email) {
                $result = $authService->authenticate($user, $email, $password);
                if (strpos($result, "successfully") !== false) {
                    $_SESSION['user'] = $user->getName();
                    $_SESSION['role'] = $user->userRole();
                    $success = $result;
                    header("Location: index.php");
                    exit();
                }
            }
        }
        $error = $result ?? "No user found with that email.";
    }
} else {
    // Handle logout
    if (isset($_POST['logout'])) {
        foreach ($users as $user) {
            if ($user->getName() === $_SESSION['user']) {
                $success = $user->logout();
                break;
            }
        }
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .logout-btn {
            background-color: #f44336;
        }
        .logout-btn:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management System</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user'])): ?>
            <form method="POST">
                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" required><br>
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" required><br><br>
                <input type="submit" name="login" value="Login">
            </form>
            <p><strong>Test Credentials:</strong></p>
            <p>Admin: alice@example.com / admin123</p>
            <p>Regular User: bob@example.com / user123</p>
        <?php else: ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</p>
            <form method="POST">
                <input type="submit" name="logout" value="Logout" class="logout-btn">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>