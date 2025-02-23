<?php
/**
 * WordPress Configuration File for Render
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Log environment variables for debugging
error_log('[WordPress] Database environment variables:');
error_log('[WordPress] MYSQL_HOST: ' . getenv('MYSQL_HOST'));
error_log('[WordPress] MYSQL_DATABASE: ' . getenv('MYSQL_DATABASE'));
error_log('[WordPress] MYSQL_USER: ' . getenv('MYSQL_USER'));
error_log('[WordPress] MYSQL_PORT: ' . getenv('MYSQL_PORT'));

// ** MySQL settings - You can get this info from your web host ** //
define('DB_NAME', getenv('MYSQL_DATABASE'));
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASSWORD', getenv('MYSQL_PASSWORD'));
define('DB_HOST', getenv('MYSQL_HOST') . ':' . (getenv('MYSQL_PORT') ?: '3306'));
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
define('AUTH_KEY',         getenv('AUTH_KEY') ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY') ?: 'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY') ?: 'put your unique phrase here');
define('NONCE_KEY',        getenv('NONCE_KEY') ?: 'put your unique phrase here');
define('AUTH_SALT',        getenv('AUTH_SALT') ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT') ?: 'put your unique phrase here');
define('NONCE_SALT',       getenv('NONCE_SALT') ?: 'put your unique phrase here');

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
