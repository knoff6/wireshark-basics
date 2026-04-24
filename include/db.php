<?php
/**
 * Database Connection Configuration
 * Fill in your cPanel MySQL details here.
 */

// Only attempt to connect if auth_mode is enabled
$settings_file = __DIR__ . '/settings.json';
$settings = ['auth_mode' => false];
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
}

$pdo = null;

if ($settings['auth_mode']) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'your_database_name'); // CHANGE THIS
    define('DB_USER', 'your_database_user'); // CHANGE THIS
    define('DB_PASS', 'your_database_password'); // CHANGE THIS

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Log error or display message
        die("ERROR: Could not connect to database. " . $e->getMessage());
    }
}
?>
