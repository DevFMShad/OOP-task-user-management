<?php
namespace App\Repositories;

use App\Core\Database;
use App\Models\Admin;
use App\Models\RegularUser;
use App\Core\AbstractUser;
use PDO;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find a user by their email address (stored in the 'user' column).
     * **MODIFIED FOR PLAIN TEXT PASSWORDS AND DIFFERENT COLUMN NAMES**
     *
     * @param string $email The email address to search for (in the 'user' column).
     * @return AbstractUser|null Returns the User object or null if not found.
     */
    public function findByEmail(string $email): ?AbstractUser {
        // MODIFIED QUERY: Select 'user' and 'Password' columns from 'users' table
        $stmt = $this->db->prepare("SELECT id, user, Password FROM users WHERE user = :email LIMIT 1"); // Changed email to user, Password
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            return null; // User not found
        }

        // --- Determine User Type (Workaround for missing 'role' column) ---
        // This is fragile - assumes specific emails correspond to specific roles.
        // Adding a 'role' column to the DB is the proper way.
        $userEmail = $userData['user']; // Get email from 'user' column
        $plainPassword = $userData['Password']; // Get plain text password from 'Password' column

        // Pass FALSE as the last argument to the constructor because the password is NOT hashed.
        // We also need to provide a 'name' - let's just use the email part before '@' for now,
        // or you could add a 'name' column to your DB.
        $name = explode('@', $userEmail)[0]; // Simple way to get a name

        if ($userEmail === 'alice@example.com') {
             // Assume alice is Admin
            return new Admin($name, $userEmail, $plainPassword, false); // Pass plain password, $isHashed = false
        } elseif ($userEmail === 'bob@example.com') {
            // Assume bob is RegularUser
             return new RegularUser($name, $userEmail, $plainPassword, false); // Pass plain password, $isHashed = false
        } else {
            // Default to RegularUser or handle unknown users if necessary
            // You might want to add a 'name' column to your database table
            // For now, just returning null if not explicitly matched. Add error log?
             error_log("User type cannot be determined for email: " . $userEmail);
            return null;
            // Or default: return new RegularUser($name, $userEmail, $plainPassword, false);
        }
    }
}