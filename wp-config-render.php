<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Force PostgreSQL adapter
define('DB_DRIVER', 'pgsql');
define('DB_TYPE', 'pgsql');

// Set up pg4wp before anything else
define('PG4WP_ROOT', __DIR__ . '/wp-content/plugins/pg4wp');
define('PG4WP_INSTALLING', true);

// Create and load the pg4wp adapter
if (!file_exists(PG4WP_ROOT . '/db.php')) {
    error_log('pg4wp not found, downloading...');
    
    if (!file_exists(PG4WP_ROOT)) {
        if (!mkdir(PG4WP_ROOT, 0755, true)) {
            error_log('Failed to create pg4wp directory');
            die('Failed to create PostgreSQL adapter directory');
        }
    }
    
    $pg4wp_url = 'https://raw.githubusercontent.com/PostgreSQL-For-Wordpress/postgresql-for-wordpress/master/pg4wp/db.php';
    $pg4wp_content = file_get_contents($pg4wp_url);
    
    if ($pg4wp_content === false) {
        error_log('Failed to download pg4wp');
        die('Failed to download PostgreSQL adapter');
    }
    
    if (!file_put_contents(PG4WP_ROOT . '/db.php', $pg4wp_content)) {
        error_log('Failed to write pg4wp file');
        die('Failed to save PostgreSQL adapter');
    }
    
    error_log('Successfully installed pg4wp');
}

// Load pg4wp before any database operations
if (file_exists(PG4WP_ROOT . '/db.php')) {
    error_log('Loading pg4wp adapter...');
    require_once(PG4WP_ROOT . '/db.php');
    error_log('pg4wp adapter loaded successfully');
} else {
    error_log('pg4wp adapter not found');
    die('PostgreSQL adapter not found');
}

// Parse database URL for Render
$database_url = getenv('DATABASE_URL');
if (!$database_url) {
    error_log('DATABASE_URL environment variable is not set');
    die('DATABASE_URL environment variable is not set');
}

$url = parse_url($database_url);
if ($url === false) {
    error_log('Failed to parse DATABASE_URL');
    die('Failed to parse DATABASE_URL');
}

// Define database constants
define('DB_NAME', ltrim($url['path'], '/'));
define('DB_USER', $url['user']);
define('DB_PASSWORD', $url['pass']);
define('DB_HOST', $url['host'] . '.oregon-postgres.render.com');
define('DB_PORT', $url['port'] ?? 5432);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// PostgreSQL specific settings
define('DB_SSL', true);
define('DB_SSLMODE', 'require');
define('DB_SSLROOTCERT', '/etc/ssl/certs/ca-certificates.crt');

// Test database connection
try {
    error_log("Testing PostgreSQL connection...");
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Database connection successful!");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

// Set table prefix
$table_prefix = 'wp_';

// Authentication Unique Keys and Salts
define('AUTH_KEY',         getenv('WORDPRESS_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_KEY',  getenv('WORDPRESS_SECURE_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_KEY',    getenv('WORDPRESS_LOGGED_IN_KEY') ?: bin2hex(random_bytes(32)));
define('NONCE_KEY',        getenv('WORDPRESS_NONCE_KEY') ?: bin2hex(random_bytes(32)));
define('AUTH_SALT',        getenv('WORDPRESS_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_SALT', getenv('WORDPRESS_SECURE_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_SALT',   getenv('WORDPRESS_LOGGED_IN_SALT') ?: bin2hex(random_bytes(32)));
define('NONCE_SALT',       getenv('WORDPRESS_NONCE_SALT') ?: bin2hex(random_bytes(32)));

// Debug settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);

// Performance settings
define('WP_CACHE', false);
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// Security settings
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);

// Enable error logging
error_log("Database connection details:");
error_log("DB_HOST: " . DB_HOST);
error_log("DB_PORT: " . DB_PORT);
error_log("DB_USER: " . DB_USER);
error_log("DB_NAME: " . DB_NAME);

// SSL/HTTPS settings
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);
}

define('WP_HOME', 'https://espbot.onrender.com');
define('WP_SITEURL', WP_HOME);

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
