<?php
/**
 * Generate the chat interface HTML
 *
 * @return string The chat interface HTML
 */
function espbot_chat_interface() {
    ob_start();
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/markdown-it/13.0.1/markdown-it.min.js"></script>
    <script>
    // Initialize markdown-it with better options for lists
    const md = window.markdownit({
        html: true,
        linkify: true,
        typographer: true,
        breaks: true
    });

    // Add custom renderer rules
    const defaultRender = md.renderer.rules.text;
    md.renderer.rules.text = function (tokens, idx, options, env, self) {
        let text = tokens[idx].content;
        
        // Handle bold text with double asterisks
        text = text.replace(/\*\*([^*]+)\*\*/g, '<strong class="md-bold">$1</strong>');
        
        // Clean up any remaining asterisks that aren't part of bold text
        text = text.replace(/(?<!\*)\*(?!\*)/g, '•');
        
        tokens[idx].content = text;
        return defaultRender(tokens, idx, options, env, self);
    };

    // Custom list rendering
    md.renderer.rules.list_item_open = function () {
        return '<li class="md-list-item">';
    };

    md.renderer.rules.bullet_list_open = function () {
        return '<ul class="md-list">';
    };
    </script>

    <style>
    .espbot-chat-window {
        display: none;
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: min(450px, 95%);
        height: min(600px, 85vh);
        background: #f5f5f5;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y pinch-zoom;
        overscroll-behavior: contain;
    }

    @supports not (width: min(450px, 95%)) {
        .espbot-chat-window {
            width: 95%;
            max-width: 450px;
        }
    }

    .espbot-chat-header {
        background: #006400;
        color: white;
        padding: 15px;
        height: 70px;
        flex-shrink: 0;
        border-radius: 10px 10px 0 0;
        display: flex;
        align-items: center;
    }

    .espbot-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        margin-bottom: 60px;
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
        background: #f5f5f5;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        touch-action: pan-y;
    }

    .espbot-message {
        margin-bottom: 15px;
        max-width: 85%;
        clear: both;
        word-wrap: break-word;
        display: flex;
        flex-direction: column;
        user-select: text;
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
    }

    .espbot-message-bot {
        float: left;
        align-items: flex-start;
        padding-left: 45px;
        position: relative;
        margin-bottom: 20px;
    }

    .espbot-message-bot::before {
        content: "\f544"; /* Font Awesome robot icon unicode */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 0;
        width: 35px;
        height: 35px;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #006400;
        color: white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .espbot-message-user {
        float: right;
        align-items: flex-end;
        margin-bottom: 20px;
    }

    .espbot-message-content {
        padding: 15px 20px;
        border-radius: 15px;
        display: inline-block;
        max-width: 100%;
        word-wrap: break-word;
        line-height: 1.6;
        font-size: 15px;
        position: relative;
        user-select: text;
        -webkit-user-select: text;
    }

    .espbot-message-content p {
        margin: 0;
        padding: 0;
    }

    .espbot-message-content strong {
        font-weight: 600;
    }

    .espbot-message-content ul {
        list-style-type: disc !important;
        padding-left: 20px !important;
        margin: 8px 0 !important;
    }

    .espbot-message-content ul ul {
        margin: 4px 0 4px 0 !important;
        list-style-type: circle !important;
    }

    .espbot-message-content ul ul li {
        list-style-type: circle !important;
    }

    .espbot-message-content ul li {
        position: relative;
        padding-left: 20px;
        margin: 8px 0;
    }

    .espbot-message-content ul li:first-child {
        margin-top: 0;
    }

    .espbot-message-content ul li:last-child {
        margin-bottom: 0;
    }

    .espbot-message-content ul li::before {
        content: "•";
        position: absolute;
        left: 4px;
        top: 0;
        font-size: 15px;
        line-height: 1.6;
    }

    .espbot-message-bot .espbot-message-content {
        background: white;
        color: #333;
        margin-left: 0;
    }

    .espbot-message-user .espbot-message-content {
        background: #006400;
        color: white;
    }

    /* Headers and sections */
    .espbot-message-content p strong {
        display: block;
        margin: 16px 0 8px 0;
    }

    .espbot-message-content p:first-child strong {
        margin-top: 0;
    }

    /* Fix spacing between sections */
    .espbot-message-content > p + ul,
    .espbot-message-content > ul + p {
        margin-top: 16px;
    }

    /* Ensure proper text wrapping */
    .espbot-message-content ul li {
        white-space: normal;
        word-break: break-word;
    }

    /* Chat bubble arrows */
    .espbot-message-bot .espbot-message-content::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 15px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid white;
    }

    .espbot-message-user .espbot-message-content::before {
        content: '';
        position: absolute;
        right: -8px;
        top: 15px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-left: 8px solid #006400;
    }

    .espbot-message-time {
        font-size: 12px;
        color: #888;
        margin-top: 4px;
    }

    .espbot-context-section {
        margin-bottom: 10px;
    }

    .espbot-context-section h4 {
        color: #2c3e50;
        margin: 0 0 5px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .espbot-context-list {
        margin: 0;
        padding-left: 20px;
    }

    .espbot-context-item {
        color: #555;
        margin-bottom: 5px;
    }

    .espbot-response-section {
        margin-bottom: 10px;
    }

    .espbot-sources-section {
        margin-top: 10px;
        padding-top: 5px;
        border-top: 1px solid #eee;
    }

    .espbot-sources-section h4 {
        color: #2c3e50;
        margin: 0 0 5px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .espbot-sources-list {
        margin: 0;
        padding-left: 20px;
        font-size: 12px;
        color: #666;
    }

    .espbot-chat-input-area {
        padding: 15px;
        background: #f5f5f5;
        border-top: 1px solid #fff;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        border-radius: 0 0 10px 10px;
    }

    .espbot-chat-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #fff;
        border-radius: 20px;
        resize: none;
        max-height: 40px;
        min-height: 40px;
        font-size: 14px;
        background: #f8f9fa;
    }

    .espbot-chat-send {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #006400;
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .espbot-chat-send:hover {
        background: #005000;
    }

    .typing-indicator {
        display: none;
        padding: 10px 15px;
        background: white;
        border: 1px solid #fff;
        border-radius: 15px;
        margin-bottom: 15px;
        color: #666;
        max-width: 85%;
        float: left;
        clear: both;
    }

    .copy-message {
        position: absolute;
        right: 8px;
        bottom: 8px;
        background: transparent;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
        opacity: 0.7;
        transition: opacity 0.2s;
        z-index: 1;
    }

    .copy-message:hover {
        opacity: 1;
    }

    .copy-message i {
        font-size: 14px;
    }

    .copy-message .fa-check {
        color: #28a745;
    }

    /* Markdown Styles */
    .espbot-message-content code {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 2px 4px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.9em;
    }

    .espbot-message-content pre {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 10px;
        border-radius: 8px;
        overflow-x: auto;
        margin: 10px 0;
    }

    .espbot-message-content pre code {
        background-color: transparent;
        padding: 0;
    }

    .espbot-message-content p {
        margin: 0 0 10px 0;
    }

    .espbot-message-content p:last-child {
        margin-bottom: 0;
    }

    .espbot-message-content ul, 
    .espbot-message-content ol {
        margin: 10px 0;
        padding-left: 20px;
    }

    .espbot-message-content blockquote {
        border-left: 3px solid #ddd;
        margin: 10px 0;
        padding-left: 10px;
        color: #666;
    }

    .espbot-message-content a {
        color: #0066cc;
        text-decoration: underline;
    }

    .espbot-message-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 10px 0;
    }

    .espbot-message-content table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
    }

    .espbot-message-content th,
    .espbot-message-content td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .espbot-message-content th {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Enhanced markdown styles */
    .espbot-message-content {
        font-size: 15px;
        line-height: 1.6;
        color: #333;
    }

    .espbot-message-content .md-list {
        margin: 8px 0 !important;
        padding-left: 24px !important;
        list-style-type: disc !important;
    }

    .espbot-message-content .md-list .md-list {
        margin: 4px 0 !important;
        list-style-type: circle !important;
    }

    .espbot-message-content .md-list-item {
        margin: 6px 0 !important;
        padding-left: 4px !important;
        line-height: 1.5;
        display: list-item !important;
    }

    .espbot-message-content .md-bold {
        font-weight: 600;
        color: inherit;
        display: inline;
    }

    /* Force proper list styling */
    .espbot-message-content ul {
        list-style-type: disc !important;
        padding-left: 24px !important;
        margin: 8px 0 !important;
    }

    .espbot-message-content li {
        display: list-item !important;
        margin: 6px 0 !important;
    }

    .espbot-message-content li::marker {
        color: #666 !important;
    }

    /* Remove any custom bullets */
    .espbot-message-content li::before {
        display: none !important;
    }

    /* Better spacing for text after bold */
    .espbot-message-content .md-bold + span {
        margin-left: 4px;
    }

    /* Theme colors */
    .espbot-message-bot .espbot-message-content {
        color: #333;
    }

    .espbot-message-user .espbot-message-content {
        color: #fff;
    }

    /* Better code block styling */
    .espbot-message-content pre {
        background: #f6f8fa;
        border-radius: 6px;
        padding: 16px;
        overflow: auto;
    }

    .espbot-message-content code {
        font-family: SFMono-Regular, Consolas, Liberation Mono, Menlo, monospace;
        font-size: 85%;
        padding: 0.2em 0.4em;
        background: rgba(27,31,35,0.05);
        border-radius: 3px;
    }

    .espbot-message-content pre code {
        padding: 0;
        background: transparent;
    }
    </style>

    <div id="espbot-widget" class="espbot-widget">
        <!-- Chat Button -->
        <button id="espbot-toggle" class="espbot-toggle">
            <span class="espbot-open-icon">
                <i class="fas fa-robot"></i>
            </span>
            <span class="espbot-close-icon">✕</span>
        </button>
        <div class="espbot-bubble">
            👋 Je suis là pour vous aider.
        </div>

        <!-- Chat Window -->
        <div id="espbot-chat-window" class="espbot-chat-window">
            <!-- Chat Header -->
            <div class="espbot-chat-header">
                <div class="header-actions" style="position: absolute; right: 10px; top: 10px;">
                    <button id="widget-new-chat-btn" class="tooltip-container" style="
                        width: 40px !important;
                        height: 40px !important;
                        border-radius: 50% !important;
                        background-color: #FFD700 !important;
                        color: #000 !important;
                        border: none !important;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
                        cursor: pointer !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        z-index: 1000 !important;
                    ">
                        <i class="fas fa-plus" style="font-size: 20px !important;"></i>
                        <span class="tooltip-text">Nouvelle conversation</span>
                    </button>
                </div>
                <div class="espbot-header-info">
                    <img src="<?php echo ESPBOT_PLUGIN_URL; ?>assets/img/esplogo.png" alt="EspBot" class="espbot-avatar">
                    <div class="espbot-header-text">
                        <h3>Assistant EspBot</h3>
                        <span class="espbot-status">
                            <span class="status-dot"></span>
                            En ligne et prêt à vous aider
                        </span>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="espbot-chat-messages" class="espbot-chat-messages">
                <!-- Welcome message -->
                <div class="espbot-message espbot-message-bot welcome-message">
                    <div class="espbot-message-content">
                        <p>👋 Bonjour! Je suis EspBot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP).</p>
                        <p class="subtitle">Comment puis-je vous aider aujourd'hui?</p>
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="espbot-chat-input-area">
                <textarea id="espbot-chat-input" 
                          class="espbot-chat-input" 
                          placeholder="Tapez votre message ici..."
                          rows="1"></textarea>
                <button id="espbot-chat-send" class="espbot-chat-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatWindow = document.getElementById('espbot-chat-window');
        const toggleButton = document.getElementById('espbot-toggle');
        const openIcon = toggleButton.querySelector('.espbot-open-icon');
        const closeIcon = toggleButton.querySelector('.espbot-close-icon');

        // Initially hide chat window and X icon
        chatWindow.style.display = 'none';
        closeIcon.style.display = 'none';
        openIcon.style.display = 'block';

        // Toggle chat when clicking button
        toggleButton.addEventListener('click', function() {
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
                closeIcon.style.display = 'block';
                openIcon.style.display = 'none';
            } else {
                chatWindow.style.display = 'none';
                closeIcon.style.display = 'none';
                openIcon.style.display = 'block';
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

// Add AJAX actions
add_action('wp_ajax_espbot_chat', 'espbot_handle_chat_ajax');
add_action('wp_ajax_nopriv_espbot_chat', 'espbot_handle_chat_ajax');

// Handle AJAX requests
function espbot_handle_chat_ajax() {
    // RAGFlow API configuration
    $ragflow_endpoint = 'http://45.147.251.181';
    $ragflow_api_key = 'ragflow-kzNWZlNWE0ZWZlMjExZWZiN2MxMjI2YT';
    $agent_id = '418d0384efe111efac53226a81084050';

    // Enable error logging
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__FILE__) . '/debug.log');

    $message = sanitize_text_field($_POST['message']);
    $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : null;
    
    $request_url = "{$ragflow_endpoint}/api/v1/agents/{$agent_id}/completions";
    
    $request_body = array(
        'question' => $message,
        'stream' => false,
        'session_id' => $session_id
    );

    // Make request to RAGFlow API
    $response = wp_remote_post($request_url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $ragflow_api_key
        ),
        'body' => json_encode($request_body),
        'timeout' => 45,
        'sslverify' => false
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array(
            'error' => $response->get_error_message(),
            'response' => 'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.'
        ));
        return;
    }

    $response_data = json_decode(wp_remote_retrieve_body($response), true);
    
    if (json_last_error() !== JSON_ERROR_NONE || !isset($response_data['data']['answer'])) {
        wp_send_json_error(array(
            'error' => 'Invalid response from RAGFlow',
            'response' => 'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.'
        ));
        return;
    }

    // Get the answer and translate common English responses to French
    $answer = $response_data['data']['answer'];
    
    // Translate common English responses to French
    $english_to_french = array(
        "Hi! I'm your smart assistant" => "Bonjour ! Je suis ESPbot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP).",
        "What can I do for you?" => "Comment puis-je vous aider aujourd'hui ?",
        "I'm here to help" => "Je suis là pour vous aider.",
        "I don't understand" => "Je ne comprends pas votre question. Pourriez-vous la reformuler ?",
        "Could you please rephrase" => "Pourriez-vous reformuler votre question ?"
    );

    foreach ($english_to_french as $english => $french) {
        if (stripos($answer, $english) !== false) {
            $answer = str_replace($english, $french, $answer);
        }
    }

    wp_send_json_success(array(
        'message' => $answer,
        'session_id' => $response_data['data']['session_id'] ?? null,
        'timestamp' => current_time('mysql')
    ));
}