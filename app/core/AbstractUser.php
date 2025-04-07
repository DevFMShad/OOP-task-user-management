<?php
namespace App\Core;

abstract class AbstractUser {
    protected $name;
    protected $email;
    protected $password;

    public function __construct($name, $email, $password, $isHashed = false) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password; // Store as-is (plain text in this case)
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    abstract public function userRole(): string;
}