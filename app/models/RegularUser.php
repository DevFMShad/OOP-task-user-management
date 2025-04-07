<?php
namespace App\Models;

use App\Core\AbstractUser;
use App\Core\AuthInterface;

class RegularUser extends AbstractUser implements AuthInterface {

    public function userRole(): string {
        return "Regular User";
    }

    public function login($email, $password): string {
        // Debug output (remove in production)
        echo "<pre style='background: #eee; padding: 10px; border: 1px solid red;'>";
        echo "DEBUG inside RegularUser::login\n";
        echo "Comparing submitted password: "; var_dump($password);
        echo "Comparing password from DB (this->password): "; var_dump($this->getPassword());
        echo "Result of comparison (\$password === \$this->getPassword()): "; var_dump($password === $this->getPassword());
        echo "</pre>";

        if ($email === $this->getEmail() && $password === $this->getPassword()) {
            return "User logged in successfully.";
        }
        return "Invalid credentials.";
    }

    public function logout(): string {
        return "User logged out.";
    }
}