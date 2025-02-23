<?php
// Define ABSPATH first
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Define database type for pg4wp
define('DB_DRIVER', 'pgsql');
define('DB_TYPE', 'pgsql');
define('PG4WP_DEBUG', true);
define('WP_USE_EXT_MYSQL', false);

// Load pg4wp adapter BEFORE any WordPress files
$pg4wp_path = ABSPATH . 'wp-content/plugins/pg4wp/db.php';
$db_php_path = ABSPATH . 'wp-content/db.php';

error_log('[WordPress] Looking for pg4wp at: ' . $pg4wp_path);
error_log('[WordPress] Looking for db.php at: ' . $db_php_path);

if (!file_exists($pg4wp_path)) {
    error_log('[WordPress] ERROR: pg4wp adapter not found at: ' . $pg4wp_path);
    die('PostgreSQL adapter error: pg4wp adapter not found. Please check your installation.');
}

if (!file_exists($db_php_path)) {
    error_log('[WordPress] WARNING: db.php not found in wp-content. Attempting to copy from pg4wp...');
    if (!copy($pg4wp_path, $db_php_path)) {
        error_log('[WordPress] ERROR: Failed to copy pg4wp adapter to wp-content/db.php');
        die('PostgreSQL adapter error: Failed to copy adapter file. Please check file permissions.');
    }
    error_log('[WordPress] SUCCESS: Successfully copied pg4wp adapter to wp-content/db.php');
}

// Load the adapter before any other database operations
require_once($db_php_path);
error_log('[WordPress] INFO: pg4wp adapter loaded successfully');

// Parse database URL for Render
$database_url = getenv('DATABASE_URL');
if (!$database_url) {
    error_log('[WordPress] ERROR: DATABASE_URL environment variable is not set');
    die('Database configuration error: DATABASE_URL is not set. Please configure your environment variables.');
}

$url = parse_url($database_url);
if (!$url || !isset($url['path']) || !isset($url['user']) || !isset($url['pass']) || !isset($url['host'])) {
    error_log('[WordPress] ERROR: Invalid DATABASE_URL format');
    die('Database configuration error: Invalid DATABASE_URL format. Please check your environment variables.');
}

// Database connection settings
$host = $url['host'];
$port = $url['port'] ?? '5432';
$dbname = ltrim($url['path'], '/');

define('DB_NAME', $dbname);
define('DB_USER', $url['user']);
define('DB_PASSWORD', $url['pass']);
define('DB_HOST', $host);
define('DB_PORT', $port);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Test database connection
try {
    $dbh = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require",
        DB_USER,
        DB_PASSWORD,
        array(
            PDO::ATTR_PERSISTENT => true
        )
    );
    error_log('[WordPress] SUCCESS: Database connection established successfully');
    error_log('[WordPress] INFO: Connected to PostgreSQL database at ' . DB_HOST);
    $dbh = null; // Close the test connection
} catch (PDOException $e) {
    error_log('[WordPress] ERROR: Database connection failed - ' . $e->getMessage());
    error_log('[WordPress] DEBUG: Connection details - Host: ' . DB_HOST . ', Database: ' . DB_NAME . ', User: ' . DB_USER);
    die('Database connection error: ' . $e->getMessage() . '. Please check your database credentials and ensure the database server is accessible.');
}

// Authentication unique keys and salts
define('AUTH_KEY',         getenv('AUTH_KEY') ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY') ?: 'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY') ?: 'put your unique phrase here');
define('NONCE_KEY',        getenv('NONCE_KEY') ?: 'put your unique phrase here');
define('AUTH_SALT',        getenv('AUTH_SALT') ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT') ?: 'put your unique phrase here');
define('NONCE_SALT',       getenv('NONCE_SALT') ?: 'put your unique phrase here');

// WordPress database table prefix
$table_prefix = 'wp_';

// HTTPS settings
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Debug settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

// Disable automatic updates
define('AUTOMATIC_UPDATER_DISABLED', true);

// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Set WP_HOME and WP_SITEURL
define('WP_HOME', 'https://espbot.onrender.com');
define('WP_SITEURL', WP_HOME);

// Include wp-settings.php
require_once(ABSPATH . 'wp-settings.php');
