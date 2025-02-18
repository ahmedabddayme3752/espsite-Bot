<?php
/*
Plugin Name: EspBot Chat
Plugin URI: https://espbot.com
Description: A chatbot plugin for WordPress
Version: 1.0.0
Author: ESP Mauritanie
Author URI: https://espmauritanie.com
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ESPBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ESPBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once ESPBOT_PLUGIN_PATH . 'chat-interface.php';

// Load plugin template
function espbot_load_template($template) {
    if (is_page('chat')) {
        $new_template = ESPBOT_PLUGIN_PATH . 'templates/page-chat.php';
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'espbot_load_template', 999);

// Add chat interface to footer
function espbot_add_chat_interface() {
    if (!is_page('chat')) {
        echo espbot_chat_interface();
    }
}
add_action('wp_footer', 'espbot_add_chat_interface');

// Enqueue scripts and styles
function espbot_enqueue_scripts() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    wp_enqueue_style('espbot-chat-style', ESPBOT_PLUGIN_URL . 'assets/css/chat-style.css');
    wp_enqueue_script('espbot-chat-js', ESPBOT_PLUGIN_URL . 'assets/js/chat.js', array('jquery'), '1.0', true);
    
    // Add AJAX URL and nonce to our script
    wp_localize_script('espbot-chat-js', 'espbotAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('espbot_chat_nonce'),
        'fastapi_url' => defined('FASTAPI_URL') ? FASTAPI_URL : 'https://main-bvxea6i-jvnjg77fkbzzi.eu-5.platformsh.site'
    ));
}
add_action('wp_enqueue_scripts', 'espbot_enqueue_scripts');

// Handle AJAX message sending
function espbot_handle_message() {
    check_ajax_referer('espbot_chat_nonce', 'nonce');
    
    $message = sanitize_text_field($_POST['message']);
    $response = "Je suis désolé, je suis en cours de développement. Je ne peux pas encore répondre à vos questions.";
    
    wp_send_json_success($response);
}
add_action('wp_ajax_espbot_send_message', 'espbot_handle_message');
add_action('wp_ajax_nopriv_espbot_send_message', 'espbot_handle_message');