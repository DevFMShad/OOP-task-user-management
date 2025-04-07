<?php
namespace App\Models;

use App\Core\AbstractUser;
use App\Core\AuthInterface;
use App\Core\LoggerTrait;

class Admin extends AbstractUser implements AuthInterface {
    use LoggerTrait;

    public function userRole(): string {
        return "Admin";
    }

    public function login($email, $password): string {
        if ($email === $this->getEmail() && $password === $this->getPassword()) {
            $this->logActivity("Admin {$this->getName()} logged in.");
            return "Admin logged in successfully.";
        }
        return "Invalid credentials.";
    }

    public function logout(): string {
        $this->logActivity("Admin {$this->getName()} logged out.");
        return "Admin logged out.";
    }
}