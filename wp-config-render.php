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
$db_name = get_env_var('DB_NAME', 'wordpress');
$db_user = get_env_var('DB_USER', 'wordpress');
$db_password = get_env_var('DB_PASSWORD');
$db_host = get_env_var('DB_HOST', 'mysql');
$db_port = get_env_var('DB_PORT', '3306');

error_log('[WordPress] Attempting database connection with:');
error_log("[WordPress] Host: $db_host:$db_port");
error_log("[WordPress] Database: $db_name");
error_log("[WordPress] User: $db_user");
error_log("[WordPress] Password length: " . ($db_password ? strlen($db_password) : 0));

// Test database connection with retry logic
$max_retries = 5;
$retry_delay = 5; // seconds

for ($i = 0; $i < $max_retries; $i++) {
    try {
        error_log("[WordPress] Connection attempt " . ($i + 1) . " of $max_retries");
        $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name, intval($db_port));
        
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

/** MySQL settings */
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_HOST', $db_host);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

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
