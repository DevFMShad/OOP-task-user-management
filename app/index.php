<?php
require 'autoload.php';
session_start(); // Start PHP session

use App\Models\Admin;
use App\Models\RegularUser;
use App\Services\AuthService;

// Simple web interface
if (!isset($_SESSION['user'])) {
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $authService = new AuthService();

        // Test with Admin
        $admin = new Admin("Alice", "alice@example.com", "admin123");
        $result = $authService->authenticate($admin, $email, $password);
        echo $result . "<br>";

        if (strpos($result, "successfully") !== false) {
            $_SESSION['user'] = $admin->getName();
            $_SESSION['role'] = $admin->userRole();
        }
    }
} else {
    echo "Welcome, " . $_SESSION['user'] . " (" . $_SESSION['role'] . ")<br>";
    if (isset($_POST['logout'])) {
        $admin = new Admin("Alice", "alice@example.com", "admin123");
        echo $admin->logout() . "<br>";
        session_destroy();
        header("Location: index.php");
    }
}
?>

<!-- Simple HTML form -->
<?php if (!isset($_SESSION['user'])): ?>
<form method="POST">
    Email: <input type="email" name="email"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" name="login" value="Login">
</form>
<?php else: ?>
<form method="POST">
    <input type="submit" name="logout" value="Logout">
</form>
<?php endif; ?>