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

// Add CORS headers for AJAX requests
function espbot_add_cors_headers() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        status_header(200);
        exit();
    }
}
add_action('init', 'espbot_add_cors_headers');

// Add CORS headers for font files
function espbot_add_font_cors_headers() {
    if (strpos($_SERVER['REQUEST_URI'], '.woff2') !== false || 
        strpos($_SERVER['REQUEST_URI'], '.woff') !== false || 
        strpos($_SERVER['REQUEST_URI'], '.ttf') !== false) {
        header('Access-Control-Allow-Origin: *');
    }
}
add_action('init', 'espbot_add_font_cors_headers');

// Handle AJAX message sending
function espbot_handle_message() {
    check_ajax_referer('espbot_nonce', 'nonce');
    
    $message = sanitize_text_field($_POST['message']);
    
    // Get relevant documents based on the query
    $relevant_docs = espbot_search_documents($message);
    
    // Generate response using the context
    $response_data = espbot_generate_response($message, $relevant_docs);
    
    wp_send_json_success($response_data);
}
add_action('wp_ajax_espbot_send_message', 'espbot_handle_message');
add_action('wp_ajax_nopriv_espbot_send_message', 'espbot_handle_message');

// Function to search documents
function espbot_search_documents($query) {
    global $wpdb;
    $relevant_docs = [];
    
    // Search in your existing database tables
    // Adjust the table name and columns according to your database structure
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM your_content_table 
            WHERE content LIKE %s 
            ORDER BY relevance DESC 
            LIMIT 3",
            '%' . $wpdb->esc_like($query) . '%'
        )
    );
    
    if ($results) {
        foreach ($results as $row) {
            $relevant_docs[] = [
                'title' => $row->title,
                'content' => $row->content,
                'relevance' => $row->relevance
            ];
        }
    }
    
    return $relevant_docs;
}

// Function to generate response
function espbot_generate_response($query, $docs) {
    // Extract relevant context from documents
    $context = array_map(function($doc) {
        return [
            'title' => $doc['title'],
            'content' => espbot_extract_relevant_section($doc['content'])
        ];
    }, $docs);
    
    // Generate main response based on the context
    $main_response = espbot_generate_main_response($query, $context);
    
    // Generate variations with different perspectives
    $variations = [
        [
            'title' => 'Perspective Académique',
            'content' => espbot_format_academic_response($main_response)
        ],
        [
            'title' => 'Explication Simplifiée',
            'content' => espbot_format_simple_response($main_response)
        ],
        [
            'title' => 'Vue Administrative',
            'content' => espbot_format_administrative_response($main_response)
        ]
    ];
    
    // Generate FAQ if needed
    $faq = [];
    if (espbot_should_generate_faq($query)) {
        $faq = espbot_generate_faq_from_context($query, $context);
    }
    
    return [
        'context' => $context,
        'response' => $main_response,
        'variations' => $variations,
        'faq' => $faq,
        'sources' => array_column($docs, 'title')
    ];
}

// Function to extract relevant section
function espbot_extract_relevant_section($content) {
    // Extract the most relevant part of the content
    // You can implement more sophisticated relevance extraction here
    return wp_trim_words($content, 50, '...');
}

// Function to generate main response
function espbot_generate_main_response($query, $context) {
    // Combine context information to generate a response
    $response = "Basé sur les documents disponibles:\n\n";
    foreach ($context as $doc) {
        $response .= "- " . strip_tags($doc['content']) . "\n";
    }
    return $response;
}

// Function to format academic response
function espbot_format_academic_response($response) {
    return "D'un point de vue académique: " . $response;
}

// Function to format simple response
function espbot_format_simple_response($response) {
    return "En termes simples: " . $response;
}

// Function to format administrative response
function espbot_format_administrative_response($response) {
    return "Du point de vue administratif: " . $response;
}

// Function to determine if FAQ should be generated
function espbot_should_generate_faq($query) {
    // Determine if FAQ would be helpful for this query
    $faq_triggers = ['comment', 'quoi', 'pourquoi', 'qui', 'quand', 'où'];
    foreach ($faq_triggers as $trigger) {
        if (stripos($query, $trigger) !== false) {
            return true;
        }
    }
    return false;
}

// Function to generate FAQ from context
function espbot_generate_faq_from_context($query, $context) {
    // Generate FAQ based on the context
    $faq = [];
    
    // Extract key points from context to create FAQ
    foreach ($context as $doc) {
        $content = $doc['content'];
        // Add relevant questions based on content
        // You can implement more sophisticated FAQ generation here
        $faq[] = [
            'question' => "Que dit le document '{$doc['title']}' à ce sujet ?",
            'answer' => wp_trim_words($content, 30, '...')
        ];
    }
    
    return $faq;
}