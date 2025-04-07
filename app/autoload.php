<?php
spl_autoload_register(function ($class) {
    // Replace namespace separator with directory separator
    $classPath = str_replace('\\', '/', $class);
    // Remove the 'App' prefix from the namespace to match the folder structure
    $classPath = str_replace('App/', '', $classPath);
    // Construct the file path relative to the App/ directory
    $file = __DIR__ . '/' . $classPath . '.php';
    
    if (file_exists($file)) {
        require $file;
    } else {
        echo "Autoload failed: File not found - $file<br>";
    }
});