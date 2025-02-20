<?php
/**
 * Plugin Name: EspBot
 * Plugin URI: https://esp.mr
 * Description: A multilingual chatbot for ESP Mauritanie
 * Version: 1.0.0
 * Author: ESP Team
 * Author URI: https://esp.mr
 * Text Domain: espbot
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('ESPBOT_VERSION', '1.0.0');
define('ESPBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ESPBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Plugin activation hook
 */
function espbot_activate() {
    // Create necessary database tables
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}espbot_conversations (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        session_id varchar(50) NOT NULL,
        user_message text NOT NULL,
        bot_response text NOT NULL,
        language varchar(10) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add plugin version to options
    add_option('espbot_version', ESPBOT_VERSION);
}
register_activation_hook(__FILE__, 'espbot_activate');

/**
 * Plugin deactivation hook
 */
function espbot_deactivate() {
    // Cleanup tasks if needed
}
register_deactivation_hook(__FILE__, 'espbot_deactivate');

/**
 * Add admin menu
 */
function espbot_admin_menu() {
    add_menu_page(
        __('EspBot Settings', 'espbot'),
        __('EspBot', 'espbot'),
        'manage_options',
        'espbot-settings',
        'espbot_settings_page',
        'dashicons-format-chat'
    );
}
add_action('admin_menu', 'espbot_admin_menu');

/**
 * Render settings page
 */
function espbot_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('espbot_options');
            do_settings_sections('espbot_settings');
            submit_button(__('Save Settings', 'espbot'));
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register plugin settings
 */
function espbot_register_settings() {
    register_setting('espbot_options', 'espbot_settings');
    
    add_settings_section(
        'espbot_settings_section',
        __('General Settings', 'espbot'),
        'espbot_settings_section_callback',
        'espbot_settings'
    );
    
    add_settings_field(
        'espbot_default_language',
        __('Default Language', 'espbot'),
        'espbot_language_field_callback',
        'espbot_settings',
        'espbot_settings_section'
    );
}
add_action('admin_init', 'espbot_register_settings');

/**
 * Settings section callback
 */
function espbot_settings_section_callback() {
    echo '<p>' . __('Configure your EspBot settings below:', 'espbot') . '</p>';
}

/**
 * Language field callback
 */
function espbot_language_field_callback() {
    $options = get_option('espbot_settings');
    $default_language = isset($options['default_language']) ? $options['default_language'] : 'fr';
    ?>
    <select name="espbot_settings[default_language]">
        <option value="fr" <?php selected($default_language, 'fr'); ?>><?php _e('French', 'espbot'); ?></option>
        <option value="ar" <?php selected($default_language, 'ar'); ?>><?php _e('Arabic', 'espbot'); ?></option>
        <option value="en" <?php selected($default_language, 'en'); ?>><?php _e('English', 'espbot'); ?></option>
    </select>
    <?php
}

/**
 * Add shortcode for embedding chatbot
 */
function espbot_shortcode() {
    ob_start();
    ?>
    <div id="espbot-chat">
        <button class="chat-toggle">
            <i class="fas fa-robot"></i>
        </button>
        <div class="chat-container">
            <!-- Chat content will be dynamically added by JavaScript -->
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('espbot', 'espbot_shortcode');

/**
 * Load plugin textdomain
 */
function espbot_load_textdomain() {
    load_plugin_textdomain('espbot', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'espbot_load_textdomain');
