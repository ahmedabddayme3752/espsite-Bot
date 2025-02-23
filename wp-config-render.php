<?php
/**
 * WordPress Configuration File for Render
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Helper function to get environment variables with more robust checking
function get_env_var($name, $default = null) {
    // Try getenv first
    $value = getenv($name);
    if ($value !== false) {
        return $value;
    }
    
    // Then try $_ENV
    if (isset($_ENV[$name])) {
        return $_ENV[$name];
    }
    
    // Then try $_SERVER
    if (isset($_SERVER[$name])) {
        return $_SERVER[$name];
    }
    
    // Finally return default
    return $default;
}

// Log all environment variables for debugging
error_log('[WordPress] Environment variables dump:');
foreach ($_ENV as $key => $value) {
    error_log("[WordPress] $_ENV[$key]: $value");
}
foreach ($_SERVER as $key => $value) {
    if (is_string($value)) {
        error_log("[WordPress] $_SERVER[$key]: $value");
    }
}

// Database settings
$db_name = get_env_var('MYSQL_DATABASE', 'wordpress');
$db_user = get_env_var('MYSQL_USER', 'wordpress');
$db_password = get_env_var('MYSQL_PASSWORD');
$db_host = get_env_var('MYSQL_HOST', 'mysql');
$db_port = get_env_var('MYSQL_PORT', '3306');

error_log('[WordPress] Database configuration:');
error_log("[WordPress] DB_NAME: $db_name");
error_log("[WordPress] DB_USER: $db_user");
error_log("[WordPress] DB_HOST: $db_host:$db_port");
error_log('[WordPress] DB_PASSWORD: ' . ($db_password ? 'set' : 'not set'));

// Check required database settings
if (!$db_password) {
    error_log('[WordPress] ERROR: Required database password is not set');
    die('Database configuration error: Required password is not set.');
}

/** MySQL settings */
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_HOST', "$db_host:$db_port");
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Set up HTTPS if behind proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

/**
 * Authentication Unique Keys and Salts.
 */
define('AUTH_KEY',         get_env_var('AUTH_KEY', 'unique-key-1'));
define('SECURE_AUTH_KEY',  get_env_var('SECURE_AUTH_KEY', 'unique-key-2'));
define('LOGGED_IN_KEY',    get_env_var('LOGGED_IN_KEY', 'unique-key-3'));
define('NONCE_KEY',        get_env_var('NONCE_KEY', 'unique-key-4'));
define('AUTH_SALT',        get_env_var('AUTH_SALT', 'unique-salt-1'));
define('SECURE_AUTH_SALT', get_env_var('SECURE_AUTH_SALT', 'unique-salt-2'));
define('LOGGED_IN_SALT',   get_env_var('LOGGED_IN_SALT', 'unique-salt-3'));
define('NONCE_SALT',       get_env_var('NONCE_SALT', 'unique-salt-4'));

// WordPress Database Table prefix
$table_prefix = 'wp_';

// WordPress Localized Language
define('WPLANG', '');

// Debug settings
define('WP_DEBUG', filter_var(get_env_var('WP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Sets up WordPress vars and included files
require_once(ABSPATH . 'wp-settings.php');
