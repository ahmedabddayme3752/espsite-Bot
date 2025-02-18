<?php
function espbot_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Add support for title tag
    add_theme_support('title-tag');

    // Add support for HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'espbot'),
    ));
}
add_action('after_setup_theme', 'espbot_setup');

// Enqueue scripts and styles
function espbot_scripts() {
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    
    // Enqueue theme stylesheet
    wp_enqueue_style('espbot-style', get_stylesheet_uri());
    
    // Enqueue navigation script
    wp_enqueue_script('espbot-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'espbot_scripts');

function espbot_enqueue_chat_scripts() {
    if (is_page_template('page-chat.php')) {
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');
        wp_enqueue_style('espbot-chat-style', get_template_directory_uri() . '/css/chat-style.css', array(), '1.0.0');
        wp_enqueue_script('espbot-chat', get_template_directory_uri() . '/js/chat.js', array('jquery'), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'espbot_enqueue_chat_scripts');

?>
