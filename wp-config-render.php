<?php
// Set up Platform.sh database configuration.
if (isset($_ENV['PLATFORM_RELATIONSHIPS'])) {
    $relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), true);

    // We are using the first relationship called "database" found in your
    // relationships. Note that you can call this relationship as you want
    // in config.yaml, but it must have at least one endpoint of type "mysql".
    $database = $relationships['database'][0];

    // Configure WordPress database settings
    define('DB_NAME', $database['path']);
    define('DB_USER', $database['username']);
    define('DB_PASSWORD', $database['password']);
    define('DB_HOST', sprintf('%s:%s', $database['host'], $database['port']));
    define('DB_CHARSET', 'utf8');
    define('DB_COLLATE', '');

    // You can have multiple relationships.
    if (isset($relationships['redis'])) {
        define('WP_REDIS_HOST', $relationships['redis'][0]['host']);
        define('WP_REDIS_PORT', $relationships['redis'][0]['port']);
    }
}

// Set up Platform.sh reverse proxy configuration.
if (isset($_ENV['PLATFORM_ROUTES'])) {
    $routes = json_decode(base64_decode($_ENV['PLATFORM_ROUTES']), true);
    $main_route = FALSE;
    foreach ($routes as $url => $route) {
        if ($route['type'] === 'upstream' && $route['upstream'] === 'app') {
            $main_route = $url;
            break;
        }
    }
    if ($main_route !== FALSE) {
        define('WP_HOME', $main_route);
        define('WP_SITEURL', $main_route);
    }
}

/**
 * Authentication Unique Keys and Salts.
 *
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY',         isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'AUTH_KEY');
define('SECURE_AUTH_KEY',  isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'SECURE_AUTH_KEY');
define('LOGGED_IN_KEY',    isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'LOGGED_IN_KEY');
define('NONCE_KEY',        isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'NONCE_KEY');
define('AUTH_SALT',        isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'AUTH_SALT');
define('SECURE_AUTH_SALT', isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'SECURE_AUTH_SALT');
define('LOGGED_IN_SALT',   isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'LOGGED_IN_SALT');
define('NONCE_SALT',       isset($_ENV['PLATFORM_PROJECT_ENTROPY']) ? $_ENV['PLATFORM_PROJECT_ENTROPY'] : 'NONCE_SALT');

// If on Platform.sh, this will be set to the Git branch name.
if (isset($_ENV['PLATFORM_BRANCH'])) {
    define('WP_DEBUG', TRUE);
    define('WP_DEBUG_LOG', TRUE);
    define('WP_DEBUG_DISPLAY', FALSE);
} else {
    define('WP_DEBUG', FALSE);
}

// Determine HTTP or HTTPS, then set WP_SITEURL and WP_HOME
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Disable WordPress auto-updates, as they won't work in this environment.
define('AUTOMATIC_UPDATER_DISABLED', TRUE);

// Disable WP cron, as we'll use an external cron task.
define('DISABLE_WP_CRON', TRUE);

// Set PHP memory limit to a high value
define('WP_MEMORY_LIMIT', '256M');

$table_prefix = 'wp_';

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
