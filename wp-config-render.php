<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ** PostgreSQL for WordPress configuration ** //
if (!defined('DB_TYPE')) {
    define('DB_TYPE', 'pgsql');
}
define('DB_DRIVER', 'pgsql');

// Set up pg4wp
define('PG4WP_ROOT', __DIR__ . '/wp-content/plugins/pg4wp');
if (!file_exists(PG4WP_ROOT . '/db.php')) {
    error_log('pg4wp not found, attempting to download...');
    
    // Create directory if it doesn't exist
    if (!file_exists(PG4WP_ROOT)) {
        if (!mkdir(PG4WP_ROOT, 0755, true)) {
            error_log('Failed to create pg4wp directory');
            die('Failed to create required PostgreSQL adapter directory');
        }
        error_log('Created pg4wp directory successfully');
    }
    
    // Download pg4wp
    $pg4wp_url = 'https://raw.githubusercontent.com/PostgreSQL-For-Wordpress/postgresql-for-wordpress/master/pg4wp/db.php';
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]);
    $pg4wp_content = file_get_contents($pg4wp_url, false, $context);
    if ($pg4wp_content === false) {
        error_log('Failed to download pg4wp from: ' . $pg4wp_url);
        die('Failed to download required PostgreSQL adapter. Please try again.');
    }
    
    if (!file_put_contents(PG4WP_ROOT . '/db.php', $pg4wp_content)) {
        error_log('Failed to write pg4wp file');
        die('Failed to save PostgreSQL adapter');
    }
    error_log('Successfully downloaded and saved pg4wp');
}

// Verify pg4wp file exists and is readable
if (!is_readable(PG4WP_ROOT . '/db.php')) {
    error_log('pg4wp file exists but is not readable');
    die('PostgreSQL adapter exists but is not readable');
}

// Load pg4wp
require_once(PG4WP_ROOT . '/db.php');

// ** Database settings - You can get this info from your web host ** //

// Parse database URL for Render
$database_url = getenv('DATABASE_URL');
if (!$database_url) {
    die('DATABASE_URL environment variable is not set');
}

$url = parse_url($database_url);
if ($url === false) {
    die('Failed to parse DATABASE_URL');
}

define('DB_NAME', ltrim($url['path'], '/'));
define('DB_USER', $url['user']);
define('DB_PASSWORD', $url['pass']);
$host = $url['host'] . '.oregon-postgres.render.com';
$port = $url['port'] ?? 5432;
define('DB_HOST', $host);
define('DB_PORT', $port);

if (!filter_var(gethostbyname($host), FILTER_VALIDATE_IP)) {
    die("DNS resolution failed for: $host");
}

$ip = gethostbyname($host);
$socket = @fsockopen($ip, 5432, $errno, $errstr, 5);
if (!$socket) {
    die("Connection failed to $ip:5432 - $errstr");
}
fclose($socket);

// PostgreSQL connection settings
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('DB_SSL', true);
define('DB_SSLMODE', 'require');
define('DB_SSLROOTCERT', '/etc/ssl/certs/ca-certificates.crt');

// Temporary connection test
error_log("Connecting to PostgreSQL at: pgsql:host=$host;port=$port;dbname=".DB_NAME);
$conn = new PDO(
    "pgsql:host=$host;port=$port;dbname=".DB_NAME.";sslmode=require",
    DB_USER,
    DB_PASSWORD,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);
echo "<!-- Connection successful! -->";

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
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);

// Performance settings
define('WP_CACHE', false);
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// Security settings
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);

// Enable error logging
error_log("Database connection details:");
error_log("DB_HOST: " . DB_HOST);
error_log("DB_PORT: " . DB_PORT);
error_log("DB_USER: " . DB_USER);
error_log("DB_NAME: " . DB_NAME);

// SSL/HTTPS settings
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    define('FORCE_SSL_ADMIN', true);
    define('FORCE_SSL_LOGIN', true);
}

define('WP_HOME', 'https://espbot.onrender.com');
define('WP_SITEURL', WP_HOME);

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
