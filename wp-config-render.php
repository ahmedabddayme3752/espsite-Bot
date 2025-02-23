<?php
/**
 * WordPress Configuration File for Render
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Helper function to get environment variables
function get_env_var($name) {
    $value = getenv($name);
    if ($value === false && isset($_ENV[$name])) {
        $value = $_ENV[$name];
    }
    error_log("[WordPress] $name: " . ($value ?: 'not set'));
    return $value;
}

// Log environment variables for debugging
error_log('[WordPress] Database environment variables:');
$db_name = get_env_var('MYSQL_DATABASE');
$db_user = get_env_var('MYSQL_USER');
$db_password = get_env_var('MYSQL_PASSWORD');
$db_host = get_env_var('MYSQL_HOST');
$db_port = get_env_var('MYSQL_PORT') ?: '3306';

// ** MySQL settings - You can get this info from your web host ** //
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_HOST', $db_host ? $db_host . ':' . $db_port : null);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Check database settings
if (!DB_NAME || !DB_USER || !DB_PASSWORD || !DB_HOST) {
    error_log('[WordPress] ERROR: Required database environment variables are not set');
    error_log('[WordPress] DB_NAME: ' . DB_NAME);
    error_log('[WordPress] DB_USER: ' . DB_USER);
    error_log('[WordPress] DB_HOST: ' . DB_HOST);
    die('Database configuration error: Required environment variables are not set.');
}

// Set up HTTPS if behind proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

/**
 * Authentication Unique Keys and Salts.
 */
define('AUTH_KEY',         get_env_var('AUTH_KEY'));
define('SECURE_AUTH_KEY',  get_env_var('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY',    get_env_var('LOGGED_IN_KEY'));
define('NONCE_KEY',        get_env_var('NONCE_KEY'));
define('AUTH_SALT',        get_env_var('AUTH_SALT'));
define('SECURE_AUTH_SALT', get_env_var('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT',   get_env_var('LOGGED_IN_SALT'));
define('NONCE_SALT',       get_env_var('NONCE_SALT'));

// WordPress Database Table prefix
$table_prefix = 'wp_';

// WordPress Localized Language
define('WPLANG', '');

// Debug settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Sets up WordPress vars and included files
require_once(ABSPATH . 'wp-settings.php');
