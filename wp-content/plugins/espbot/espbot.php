<?php
/*
Plugin Name: ESPBot
Description: A chatbot plugin wordpress for Esp Maurtanie
Version: 1.0.0
Author: Ahmed Abd Dayme (AhmedBouha)
Author URI: https://github.com/ahmedabddayme3752
Text Domain: espbot
Domain Path: /languages
License: Proprietary
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ESPBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ESPBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// API Configuration - These should be defined in wp-config.php
if (!defined('ESPBOT_API_KEY')) {
    define('ESPBOT_API_KEY', 'app-IpWkVHUIINQVrU4fOBmJuE0b'); // Set your API key in wp-config.php
}
if (!defined('ESPBOT_API_URL')) {
    define('ESPBOT_API_URL', 'http://45.147.251.181/v1');
}
if (!defined('ESPBOT_API_CHAT_URL')) {
    define('ESPBOT_API_CHAT_URL', ESPBOT_API_URL . '/chat-messages');
}

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
        'nonce' => wp_create_nonce('espbot_send_message'),
        'api_url' => ESPBOT_API_URL,
        'api_chat_url' => ESPBOT_API_CHAT_URL,
        'api_key' => defined('ESPBOT_API_KEY') ? ESPBOT_API_KEY : 'app-IpWkVHUIINQVrU4fOBmJuE0b',
        'debug' => WP_DEBUG
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
    try {
        error_log('ESPBot: Starting message handler');
        
        if (!isset($_POST['nonce'])) {
            error_log('ESPBot Error: No nonce provided');
            throw new Exception('Security check failed - no nonce provided');
        }
        
        check_ajax_referer('espbot_send_message', 'nonce');
        error_log('ESPBot: Nonce verification passed');
        
        if (!isset($_POST['message'])) {
            error_log('ESPBot Error: No message provided');
            throw new Exception('No message provided');
        }
        
        $message = sanitize_text_field($_POST['message']);
        $conversation_id = isset($_POST['conversation_id']) ? sanitize_text_field($_POST['conversation_id']) : '';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : 'guest-' . substr(md5($_SERVER['REMOTE_ADDR']), 0, 8);
        
        error_log('ESPBot: Processing message from user: ' . $user_id);
        
        $api_key = defined('ESPBOT_API_KEY') ? ESPBOT_API_KEY : '';
        if (empty($api_key)) {
            error_log('ESPBot Error: API key not configured');
            throw new Exception('API key not configured');
        }

        $request_data = [
            'query' => $message,
            'response_mode' => 'blocking',
            'conversation_id' => $conversation_id,
            'user' => $user_id,
            'inputs' => [],
            'files' => []
        ];

        $args = [
            'body' => json_encode($request_data),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ],
            'timeout' => 120
        ];

        $api_url = defined('ESPBOT_API_CHAT_URL') ? ESPBOT_API_CHAT_URL : 'http://45.147.251.181/v1/chat-messages';
        error_log('ESPBot: Sending request to API: ' . $api_url);
        error_log('ESPBot: Request data: ' . json_encode($request_data));
        
        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('ESPBot Error: WordPress HTTP Error: ' . $error_message);
            throw new Exception('WordPress HTTP Error: ' . $error_message);
        }

        $response_code = wp_remote_retrieve_response_code($response);
        error_log('ESPBot: API response code: ' . $response_code);
        
        if ($response_code !== 200) {
            $error_message = 'API Error: Received response code ' . $response_code;
            error_log('ESPBot Error: ' . $error_message);
            error_log('ESPBot Error Response: ' . wp_remote_retrieve_body($response));
            throw new Exception($error_message);
        }

        $body = wp_remote_retrieve_body($response);
        error_log('ESPBot: API Response body: ' . $body);
        
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message = 'Invalid JSON response: ' . json_last_error_msg();
            error_log('ESPBot Error: ' . $error_message);
            throw new Exception($error_message);
        }

        if (!isset($data['answer'])) {
            $error_message = 'Invalid response format: Missing answer field';
            error_log('ESPBot Error: ' . $error_message);
            error_log('ESPBot Response data: ' . json_encode($data));
            throw new Exception($error_message);
        }

        error_log('ESPBot: Successfully processed message');
        wp_send_json_success([
            'message' => $data['answer'],
            'conversation_id' => $data['conversation_id'] ?? '',
            'message_id' => $data['id'] ?? '', // Add message_id to the response
            'metadata' => $data['metadata'] ?? null
        ]);

    } catch (Exception $e) {
        error_log('ESPBot Error: ' . $e->getMessage());
        wp_send_json_error([
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
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