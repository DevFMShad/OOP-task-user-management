<?php
// index.php (Main Application File)

// Ensure errors are displayed during development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to manage user login state
session_start();

// Include the autoloader
require_once 'autoload.php'; // Make sure this path is correct

// Use necessary classes from namespaces
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Core\AuthInterface;

// Initialize variables for messages
$error = '';
$success = '';

// --- Logic ---

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // --- Handle Logout Request ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        // Optional: If you need user-specific logout logic (like logging from Admin)
        // you might fetch the user object here based on session data, but it's often not needed.
        // $userName = $_SESSION['user']; // Get username from session

        // Clear all session variables
        session_unset();
        // Destroy the session
        session_destroy();

        // Redirect to the login page with a success message
        header("Location: index.php?logged_out=1");
        exit(); // Important to prevent further script execution
    }
} else {
    // --- Handle Login Request ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $email = trim($_POST['email'] ?? ''); // Trim whitespace
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($email) || empty($password)) {
            $error = "Email and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            try {
                // Instantiate necessary services/repositories
                $userRepository = new UserRepository();
                $authService = new AuthService();

                // Attempt to find the user by email in the database
                $user = $userRepository->findByEmail($email);

                // Check if a user was found and if they implement the AuthInterface
                if ($user instanceof AuthInterface) {
                    // Attempt to authenticate the user using the AuthService
                    $result = $authService->authenticate($user, $email, $password);

                    // Check if authentication was successful (based on the return message)
                    if (strpos($result, "successfully") !== false) {
                        // Regenerate session ID for security upon successful login
                        session_regenerate_id(true);

                        // Store essential user info in the session
                        $_SESSION['user'] = $user->getName();
                        $_SESSION['role'] = $user->userRole();
                        // You might also store the user ID if needed later:
                        // $userId = $user->getId(); // Would require adding getId() method
                        // $_SESSION['user_id'] = $userId;

                        // Redirect to the same page (now showing the logged-in state)
                        // This prevents form resubmission on refresh
                        header("Location: index.php");
                        exit();
                    } else {
                        // Authentication failed (invalid credentials)
                        $error = $result; // Show the "Invalid credentials." message
                    }
                } else {
                    // No user found with the provided email address
                    $error = "Invalid credentials."; // Keep the error message generic for security
                }
            } catch (\PDOException $e) {
                // Handle database connection or query errors
                error_log("Database Error during login: " . $e->getMessage()); // Log detailed error
                $error = "An error occurred during login. Please try again later."; // Show generic error
            } catch (\Exception $e) {
                // Handle other potential errors (e.g., config file not found)
                error_log("General Error during login: " . $e->getMessage()); // Log detailed error
                $error = "An unexpected error occurred. Please try again later."; // Show generic error
            }
        }
    }

    // Check for logout success message from redirect
    if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
        $success = "You have been logged out successfully.";
    }
}

// --- Presentation (HTML) ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        /* Basic Styling - feel free to customize */
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background-color: #f4f4f4; line-height: 1.6; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 25px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { background-color: #5cb85c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; width: 100%; }
        input[type="submit"]:hover { background-color: #4cae4c; }
        .logout-btn { background-color: #d9534f; }
        .logout-btn:hover { background-color: #c9302c; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .error { color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; }
        .success { color: #3c763d; background-color: #dff0d8; border: 1px solid #d6e9c6; }
        .welcome-message { text-align: center; margin-bottom: 20px; font-size: 1.1em; color: #333; }
        .welcome-message strong { color: #0056b3; }
        .credentials-info { margin-top: 30px; padding: 15px; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 4px; font-size: 0.9em; color: #555; }
        .credentials-info p { margin: 5px 0; }
        .credentials-info strong { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management System</h2>

        <?php // Display error or success messages ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php // Show Login form OR Welcome message ?>
        <?php if (!isset($_SESSION['user'])): ?>
            <?php // --- Login Form --- ?>
            <form method="POST" action="index.php">
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div>
                    <input type="submit" name="login" value="Login">
                </div>
            </form>
            <div class="credentials-info">
                <p><strong>Test Credentials (from Database):</strong></p>
                <p>Admin Email: alice@example.com / Password: admin123</p>
                <p>Regular User Email: bob@example.com / Password: user123</p>
            </div>
        <?php else: ?>
            <?php // --- Welcome Message & Logout --- ?>
            <div class="welcome-message">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>!
                (Role: <?php echo htmlspecialchars($_SESSION['role']); ?>)
            </div>
            <form method="POST" action="index.php">
                <input type="submit" name="logout" value="Logout" class="logout-btn">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>