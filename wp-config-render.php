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
    define('DB_HOST', $url['host'] . ':' . ($url['port'] ?? '5432'));
} else {
    // Fallback to individual environment variables
    define('DB_NAME', getenv('WORDPRESS_DB_NAME'));
    define('DB_USER', getenv('WORDPRESS_DB_USER'));
    define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD'));
    define('DB_HOST', getenv('WORDPRESS_DB_HOST') . ':' . (getenv('WORDPRESS_DB_PORT') ?: '5432'));
}

// PostgreSQL SSL configuration
define('DB_SSL', true);
define('DB_SSLMODE', 'require');
define('DB_SSL_CA', '/etc/ssl/certs/ca-certificates.crt');

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Authentication Unique Keys and Salts
define('AUTH_KEY',         getenv('WORDPRESS_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_KEY',  getenv('WORDPRESS_SECURE_AUTH_KEY') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_KEY',    getenv('WORDPRESS_LOGGED_IN_KEY') ?: bin2hex(random_bytes(32)));
define('NONCE_KEY',        getenv('WORDPRESS_NONCE_KEY') ?: bin2hex(random_bytes(32)));
define('AUTH_SALT',        getenv('WORDPRESS_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('SECURE_AUTH_SALT', getenv('WORDPRESS_SECURE_AUTH_SALT') ?: bin2hex(random_bytes(32)));
define('LOGGED_IN_SALT',   getenv('WORDPRESS_LOGGED_IN_SALT') ?: bin2hex(random_bytes(32)));
define('NONCE_SALT',       getenv('WORDPRESS_NONCE_SALT') ?: bin2hex(random_bytes(32)));

$table_prefix = 'wp_';

// Debug settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// Enable error logging
error_log("Database connection details:");
error_log("DB_HOST: " . DB_HOST);
error_log("DB_USER: " . DB_USER);
error_log("DB_NAME: " . DB_NAME);

// SSL/HTTPS settings
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

define('WP_HOME', 'https://' . $_SERVER['HTTP_HOST']);
define('WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST']);

// ** PostgreSQL for WordPress configuration ** //

// Enable pg4wp
define('DB_DRIVER', 'pgsql');
define('DB_TYPE', 'pgsql');

// Include pg4wp
define('PG4WP_ROOT', dirname(__FILE__) . '/wp-content/plugins/pg4wp');
if (!defined('PG4WP_ROOT')) {
    define('PG4WP_ROOT', dirname(__FILE__) . '/wp-content/plugins/pg4wp');
}

if (defined('PG4WP_ROOT') && file_exists(PG4WP_ROOT . '/db.php')) {
    require_once(PG4WP_ROOT . '/db.php');
} else {
    // Download pg4wp if not present
    if (!file_exists(dirname(__FILE__) . '/wp-content/plugins/pg4wp')) {
        mkdir(dirname(__FILE__) . '/wp-content/plugins/pg4wp', 0755, true);
        $pg4wp_url = 'https://raw.githubusercontent.com/PostgreSQL-For-Wordpress/postgresql-for-wordpress/master/pg4wp/db.php';
        $pg4wp_content = file_get_contents($pg4wp_url);
        file_put_contents(dirname(__FILE__) . '/wp-content/plugins/pg4wp/db.php', $pg4wp_content);
    }
    require_once(dirname(__FILE__) . '/wp-content/plugins/pg4wp/db.php');
}

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
