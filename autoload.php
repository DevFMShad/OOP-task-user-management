<?php
// autoload.php (PSR-4 Autoloader Implementation)

/**
 * Autoloader function compatible with PSR-4 standard.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    // Project-specific namespace prefix
    $prefix = 'App\\';

    // Base directory for the namespace prefix.
    // Assumes autoload.php is in the project root, and 'App' folder is alongside it.
    $base_dir = __DIR__ . '/App/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader (if any exists)
        return;
    }

    // Get the relative class name (e.g., Core\Database from App\Core\Database)
    $relative_class = substr($class, $len);

   
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it once
    if (file_exists($file)) {
        require_once $file; // Use require_once to prevent duplicate loading
    } else {
       
    }
});

