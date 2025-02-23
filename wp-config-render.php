<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('WORDPRESS_DB_NAME'));

/** MySQL database username */
define('DB_USER', getenv('WORDPRESS_DB_USER'));

/** MySQL database password */
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD'));

/** MySQL hostname */
define('DB_HOST', getenv('WORDPRESS_DB_HOST'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
 * WordPress Database Table prefix.
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */

// Try to connect to the database with retry logic
$max_retries = 30;
$retry_interval = 2;
$connected = false;

for ($i = 0; $i < $max_retries; $i++) {
    try {
        error_log("Attempt " . ($i + 1) . " of $max_retries to connect to MySQL at " . DB_HOST);
        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
        
        if ($mysqli->connect_error) {
            error_log("Failed to connect to MySQL: " . $mysqli->connect_error);
            if ($i < $max_retries - 1) {
                error_log("Waiting $retry_interval seconds before retry...");
                sleep($retry_interval);
                continue;
            }
        } else {
            error_log("Successfully connected to MySQL server");
            
            // Try to select the database
            if (!$mysqli->select_db(DB_NAME)) {
                error_log("Database " . DB_NAME . " does not exist, attempting to create it");
                if ($mysqli->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`")) {
                    error_log("Successfully created database " . DB_NAME);
                } else {
                    error_log("Failed to create database: " . $mysqli->error);
                }
            }
            
            $connected = true;
            $mysqli->close();
            break;
        }
    } catch (Exception $e) {
        error_log("Exception while connecting to MySQL: " . $e->getMessage());
        if ($i < $max_retries - 1) {
            error_log("Waiting $retry_interval seconds before retry...");
            sleep($retry_interval);
        }
    }
}

if (!$connected) {
    error_log("Failed to connect to MySQL after $max_retries attempts");
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
