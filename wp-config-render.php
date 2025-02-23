<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');

// Print environment variables for debugging
error_log("Environment variables:");
error_log("WORDPRESS_DB_HOST: " . getenv('WORDPRESS_DB_HOST'));
error_log("WORDPRESS_DB_USER: " . getenv('WORDPRESS_DB_USER'));
error_log("WORDPRESS_DB_NAME: " . getenv('WORDPRESS_DB_NAME'));

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

// Try to connect to the database with retry logic
$max_retries = 30;
$retry_interval = 2;
$connected = false;

// Parse host and port from DB_HOST
$host_parts = explode(':', DB_HOST);
$host = $host_parts[0];
$port = isset($host_parts[1]) ? $host_parts[1] : 3306;

error_log("Attempting to connect to MySQL at host: $host, port: $port");

for ($i = 0; $i < $max_retries; $i++) {
    try {
        error_log("Attempt " . ($i + 1) . " of $max_retries to connect to MySQL");
        
        $mysqli = mysqli_init();
        if (!$mysqli) {
            error_log("mysqli_init failed");
            continue;
        }

        // Set connection timeout
        mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        
        if (!mysqli_real_connect($mysqli, $host, DB_USER, DB_PASSWORD, null, $port)) {
            error_log("Failed to connect to MySQL: " . mysqli_connect_error());
            if ($i < $max_retries - 1) {
                error_log("Waiting $retry_interval seconds before retry...");
                sleep($retry_interval);
                continue;
            }
        } else {
            error_log("Successfully connected to MySQL server");
            
            // Try to select or create the database
            if (!mysqli_select_db($mysqli, DB_NAME)) {
                error_log("Database " . DB_NAME . " does not exist, attempting to create it");
                if (mysqli_query($mysqli, "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`")) {
                    error_log("Successfully created database " . DB_NAME);
                    mysqli_select_db($mysqli, DB_NAME);
                } else {
                    error_log("Failed to create database: " . mysqli_error($mysqli));
                }
            }
            
            $connected = true;
            mysqli_close($mysqli);
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
    die("Failed to connect to MySQL. Check the error logs for details.");
}

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
