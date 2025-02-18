<?php
// ** Database settings - You can get this info from your web host ** //

// Try to get database URL from environment
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $url = parse_url($database_url);
    
    // Remove leading slash from database name
    define('DB_NAME', ltrim($url['path'], '/'));
    define('DB_USER', $url['user']);
    define('DB_PASSWORD', $url['pass']);
    define('DB_HOST', $url['host']);
} else {
    // Fallback to individual environment variables
    define('DB_NAME', getenv('WORDPRESS_DB_NAME'));
    define('DB_USER', getenv('WORDPRESS_DB_USER'));
    define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD'));
    define('DB_HOST', getenv('WORDPRESS_DB_HOST'));
}

// Enable error logging
@ini_set('log_errors', 'On');
@ini_set('error_log', '/var/www/html/php-errors.log');
error_log("Database connection details:");
error_log("DB_HOST: " . DB_HOST);
error_log("DB_USER: " . DB_USER);
error_log("DB_NAME: " . DB_NAME);

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Authentication Unique Keys and Salts
define('AUTH_KEY',         getenv('WORDPRESS_AUTH_KEY') ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('WORDPRESS_SECURE_AUTH_KEY') ?: 'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('WORDPRESS_LOGGED_IN_KEY') ?: 'put your unique phrase here');
define('NONCE_KEY',        getenv('WORDPRESS_NONCE_KEY') ?: 'put your unique phrase here');
define('AUTH_SALT',        getenv('WORDPRESS_AUTH_SALT') ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('WORDPRESS_SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('WORDPRESS_LOGGED_IN_SALT') ?: 'put your unique phrase here');
define('NONCE_SALT',       getenv('WORDPRESS_NONCE_SALT') ?: 'put your unique phrase here');

$table_prefix = 'wp_';

// Debug settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// SSL settings
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    define('FORCE_SSL_ADMIN', true);
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
