<?php
/**
 * WordPress Configuration File for Render
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Helper function to get environment variables with more robust checking
function get_env_var($name, $default = null) {
    $value = getenv($name);
    if ($value !== false) {
        error_log("[WordPress] Found in getenv: $name = " . $value);
        return $value;
    }
    
    if (isset($_ENV[$name])) {
        error_log("[WordPress] Found in _ENV: $name = " . $_ENV[$name]);
        return $_ENV[$name];
    }
    
    if (isset($_SERVER[$name])) {
        error_log("[WordPress] Found in _SERVER: $name = " . $_SERVER[$name]);
        return $_SERVER[$name];
    }
    
    error_log("[WordPress] Using default for $name: " . $default);
    return $default;
}

// Database settings
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') );
define( 'DB_USER', getenv('WORDPRESS_DB_USER') );
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') );
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Test database connection with retry logic
$max_retries = 5;
$retry_delay = 5; // seconds

for ($i = 0; $i < $max_retries; $i++) {
    try {
        error_log("[WordPress] Connection attempt " . ($i + 1) . " of $max_retries");
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, intval(get_env_var('DB_PORT', '3306')));
        
        if ($mysqli->connect_error) {
            error_log('[WordPress] MySQL Connection Error: ' . $mysqli->connect_error);
            error_log('[WordPress] MySQL Error Number: ' . $mysqli->connect_errno);
            
            if ($i < $max_retries - 1) {
                error_log("[WordPress] Retrying in $retry_delay seconds...");
                sleep($retry_delay);
                continue;
            }
            
            throw new Exception('Database connection error: ' . $mysqli->connect_error);
        }
        
        error_log('[WordPress] Successfully connected to MySQL');
        $mysqli->close();
        break;
        
    } catch (Exception $e) {
        if ($i < $max_retries - 1) {
            error_log('[WordPress] Exception: ' . $e->getMessage());
            error_log("[WordPress] Retrying in $retry_delay seconds...");
            sleep($retry_delay);
            continue;
        }
        
        error_log('[WordPress] All connection attempts failed');
        error_log('[WordPress] Final Exception: ' . $e->getMessage());
        error_log('[WordPress] Stack trace: ' . $e->getTraceAsString());
        die('Database connection error after ' . $max_retries . ' attempts: ' . $e->getMessage());
    }
}

/**
 * Authentication Unique Keys and Salts.
 */
define('AUTH_KEY',         getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY'));
define('NONCE_KEY',        getenv('NONCE_KEY'));
define('AUTH_SALT',        getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT'));
define('NONCE_SALT',       getenv('NONCE_SALT'));

// WordPress Database Table prefix
$table_prefix = 'wp_';

// Debug settings
define( 'WP_DEBUG', getenv('WP_DEBUG') === 'true' );
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

// Absolute path to the WordPress directory
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Sets up WordPress vars and included files
require_once(ABSPATH . 'wp-settings.php');
