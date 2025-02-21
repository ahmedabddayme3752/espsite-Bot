<?php
/**
 * Generate the chat interface HTML
 *
 * @return string The chat interface HTML
 */
function espbot_chat_interface() {
    ob_start();
    ?>
    <style>
    .espbot-chat-window {
        display: none;
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 450px;
        height: 600px;
        background: #f5f5f5;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        flex-direction: column;
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
    }

    .espbot-message {
        margin-bottom: 15px;
        max-width: 85%;
        clear: both;
        word-wrap: break-word;
        display: flex;
        flex-direction: column;
    }

    .espbot-message-bot {
        float: left;
        align-items: flex-start;
    }

    .espbot-message-user {
        float: right;
        align-items: flex-end;
    }

    .espbot-message-content {
        padding: 12px 15px;
        border-radius: 15px;
        display: inline-block;
        max-width: 100%;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .espbot-message-bot .espbot-message-content {
        position: relative;
        padding: 10px 15px;
        padding-bottom: 30px;
        border-radius: 15px;
        background: white;
        border: 1px solid #fff;
        display: inline-block;
        max-width: 100%;
        word-wrap: break-word;
        line-height: 1.5;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .espbot-message-user .espbot-message-content {
        background: #006400;
        color: white;
        border: none;
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
        transition: background-color 0.2s;
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

    /* Scrollbar Styles */
    .espbot-chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .espbot-chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .espbot-chat-messages::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .espbot-chat-messages::-webkit-scrollbar-thumb:hover {
        background: #666;
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