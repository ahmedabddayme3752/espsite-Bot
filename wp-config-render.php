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

// Database settings
$db_name = get_env_var('DB_NAME', 'wordpress');
$db_user = get_env_var('DB_USER', 'wordpress');
$db_password = get_env_var('DB_PASSWORD');
$db_host = get_env_var('DB_HOST', 'mysql');
$db_port = get_env_var('DB_PORT', '3306');

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
define('AUTH_KEY',         'K8:}3Ne*4XP&UWm9v#2$dQ5^jH@xYc');
define('SECURE_AUTH_KEY',  'T6#kM2!nR9$vL4@pX7*wJ5^yB');
define('LOGGED_IN_KEY',    'F3%mP8#qN5@vK2$xL7*wY9^tH');
define('NONCE_KEY',        'B7#nK4!mX9$vP2@tL5*wY8^yQ');
define('AUTH_SALT',        'M4#kP7!nX2$vL9@tH5*wY8^qB');
define('SECURE_AUTH_SALT', 'R6#mN9!kX4$vP2@tL7*wY3^qH');
define('LOGGED_IN_SALT',   'T8#kP5!nX2$vL4@tH7*wY9^qM');
define('NONCE_SALT',       'W3#mK7!nX9$vP4@tL5*wY2^qB');

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
