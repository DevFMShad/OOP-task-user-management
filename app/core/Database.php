<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;
    private $config;

    // Private constructor to prevent direct instantiation
    private function __construct() {
        // Load configuration - Ensure this path is correct relative to Database.php
        // It goes up two directories (from Core to App, then App to root) then into config.
        $configPath = __DIR__ . '/../../config/database.php';
        if (!file_exists($configPath)) {
            // Throw a more specific error if the config file is missing
            throw new \Exception("Database configuration file not found at: " . realpath(__DIR__ . '/../..') . "/config/database.php");
        }
        $this->config = require $configPath;

        // Data Source Name (DSN) for PDO connection
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";

        // PDO options for error handling and fetch mode
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements (safer)
        ];

        try {
            // Attempt to create the PDO instance
            $this->pdo = new PDO($dsn, $this->config['user'], $this->config['password'], $options);
        } catch (PDOException $e) {
            // In a real app, log this error securely instead of potentially exposing details
            error_log("Database Connection Error: " . $e->getMessage());
            // Throw a generic exception to the calling code
            throw new PDOException("Database connection failed. Please try again later.", (int)$e->getCode());
        }
    }

    // Static method to get the single instance of the PDO connection
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self(); // Create instance if it doesn't exist
        }
        return self::$instance->pdo; // Return the PDO connection object
    }

    // Prevent cloning and unserialization to enforce Singleton pattern
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}