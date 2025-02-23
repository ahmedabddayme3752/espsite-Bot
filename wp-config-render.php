<?php
/**
 * WordPress Configuration File for Render
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// ** MySQL settings - You can get this info from your web host ** //
define('DB_NAME', getenv('MYSQL_DATABASE'));
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASSWORD', getenv('MYSQL_PASSWORD'));
define('DB_HOST', getenv('MYSQL_HOST') . ':' . getenv('DB_PORT'));
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Check database settings
if (!DB_NAME || !DB_USER || !DB_PASSWORD || !DB_HOST) {
    error_log('[WordPress] ERROR: Required database environment variables are not set');
    die('Database configuration error: Required environment variables are not set.');
}

// Set up HTTPS if behind proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    define('FORCE_SSL_ADMIN', true);
}

/**
 * Authentication Unique Keys and Salts.
 * Change these to different unique phrases!
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

// WordPress Auto-updates
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);

// Memory settings
define('WP_MEMORY_LIMIT', '256M');

// Debug settings
define('WP_DEBUG', filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
if (WP_DEBUG) {
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', true);
} else {
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_DISPLAY', false);
}

// Cache settings
define('WP_CACHE', filter_var(getenv('WP_CACHE'), FILTER_VALIDATE_BOOLEAN));

// Additional WordPress configuration
if (getenv('WORDPRESS_CONFIG_EXTRA')) {
    eval(getenv('WORDPRESS_CONFIG_EXTRA'));
}

// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Sets up WordPress vars and included files
require_once(ABSPATH . 'wp-settings.php');
