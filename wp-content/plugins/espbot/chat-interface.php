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

    .espbot-chat-input-area {
        padding: 15px;
        background: #f5f5f5;
        border-top: 1px solid #fff;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 0 0 10px 10px;
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
                        <p>👋 Bonjour! Je suis EspBot, votre assistant virtuel.</p>
                        <p class="subtitle">Comment puis-je vous aider aujourd'hui?</p>
                        <button class="copy-message">
                            <i class="fas fa-copy"></i>
                        </button>
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
    // Check nonce for security
    if (!check_ajax_referer('espbot_chat_nonce', 'nonce', false)) {
        wp_send_json_error('Invalid security token');
    }

    $message = sanitize_text_field($_POST['message']);
    
    // First, try to connect to the service
    $connect_response = wp_remote_post('https://main-bvxea6i-jvnjg77fkbzzi.eu-5.platformsh.site/api/connect', array(
        'headers'     => array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ),
        'body'        => json_encode(array(
            'credentials' => array(
                'deployment' => 'Weaviate',
                'url' => 'https://fuoh2jhszcblccxawdgg.c0.us-west3.gcp.weaviate.cloud',
                'key' => ''
            ),
            'port' => ''
        )),
        'timeout'     => 45,
        'sslverify'   => false
    ));

    if (is_wp_error($connect_response)) {
        error_log('ESPBot Connect Error: ' . $connect_response->get_error_message());
    } else {
        error_log('ESPBot Connect Response: ' . wp_remote_retrieve_body($connect_response));
    }

    // Make request to FastAPI
    $request_body = json_encode(array(
        'query' => $message,
        'RAG' => array(
            'Reader' => array(
                'selected' => 'Default',
                'components' => array(
                    'Default' => array(
                        'name' => 'Default',
                        'variables' => array(),
                        'library' => array('pypdf', 'docx', 'spacy'),
                        'description' => 'Ingests text, code, PDF, and DOCX files',
                        'config' => (object)array(),
                        'type' => 'FILE',
                        'available' => true
                    )
                )
            ),
            'Chunker' => array(
                'selected' => 'Token',
                'components' => array(
                    'Token' => array(
                        'name' => 'Token',
                        'variables' => array(),
                        'library' => array(),
                        'description' => 'Splits documents based on word tokens',
                        'config' => (object)array(
                            'Tokens' => array(
                                'type' => 'number',
                                'value' => 250,
                                'description' => 'Choose how many Token per chunks',
                                'values' => array()
                            ),
                            'Overlap' => array(
                                'type' => 'number',
                                'value' => 50,
                                'description' => 'Choose how many Tokens should overlap between chunks',
                                'values' => array()
                            )
                        ),
                        'type' => '',
                        'available' => true
                    )
                )
            ),
            'Embedder' => array(
                'selected' => 'VoyageAI',
                'components' => array(
                    'VoyageAI' => array(
                        'name' => 'VoyageAI',
                        'variables' => array(),
                        'library' => array(),
                        'description' => 'Vectorizes documents and queries using VoyageAI',
                        'config' => (object)array(
                            'Model' => array(
                                'type' => 'dropdown',
                                'value' => 'voyage-multilingual-2',
                                'description' => 'Select a VoyageAI Embedding Model',
                                'values' => array('voyage-multilingual-2')
                            ),
                            'URL' => array(
                                'type' => 'text',
                                'value' => 'https://api.voyageai.com/v1',
                                'description' => 'OpenAI API Base URL (if different from default)',
                                'values' => array()
                            )
                        ),
                        'type' => '',
                        'available' => true
                    )
                )
            ),
            'Retriever' => array(
                'selected' => 'Advanced',
                'components' => array(
                    'Advanced' => array(
                        'name' => 'Advanced',
                        'variables' => array(),
                        'library' => array(),
                        'description' => 'Retrieve relevant chunks from Weaviate',
                        'config' => (object)array(
                            'Search Mode' => array(
                                'type' => 'dropdown',
                                'value' => 'Hybrid Search',
                                'description' => 'Switch between search types.',
                                'values' => array('Hybrid Search')
                            ),
                            'Limit Mode' => array(
                                'type' => 'dropdown',
                                'value' => 'Autocut',
                                'description' => 'Method for limiting the results',
                                'values' => array('Autocut')
                            ),
                            'Limit/Sensitivity' => array(
                                'type' => 'number',
                                'value' => 1,
                                'description' => 'Value for limiting the results',
                                'values' => array()
                            ),
                            'Chunk Window' => array(
                                'type' => 'number',
                                'value' => 1,
                                'description' => 'Number of surrounding chunks',
                                'values' => array()
                            ),
                            'Threshold' => array(
                                'type' => 'number',
                                'value' => 80,
                                'description' => 'Threshold score',
                                'values' => array()
                            ),
                            'Suggestion' => array(
                                'type' => 'bool',
                                'value' => 1,
                                'description' => 'Enable Autocomplete Suggestions',
                                'values' => array()
                            )
                        ),
                        'type' => '',
                        'available' => true
                    )
                )
            ),
            'Generator' => array(
                'selected' => 'Groq',
                'components' => array(
                    'Groq' => array(
                        'name' => 'Groq',
                        'variables' => array(),
                        'library' => array(),
                        'description' => "Generator using Groq's LPU inference engine",
                        'config' => (object)array(
                            'Model' => array(
                                'type' => 'dropdown',
                                'value' => 'llama-3.1-8b-instant',
                                'description' => 'Select a Groq model',
                                'values' => array('llama-3.1-8b-instant')
                            ),
                            'System Message' => array(
                                'type' => 'textarea',
                                'value' => "Vous êtes ESPbot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP). Votre mission est d'aider les étudiants, les professeurs et le personnel de l'ESP en fournissant des informations précises, pertinentes et utiles. Vous utilisez la technologie RAG (Retrieval-Augmented Generation) pour vous appuyer sur des sources fiables et vérifiées, telles que les documents, bases de données et ressources officielles de l'ESP. Vous devez toujours répondre de manière claire, concise et professionnelle en français.",
                                'description' => 'System Message',
                                'values' => array()
                            )
                        ),
                        'type' => '',
                        'available' => true
                    )
                )
            )
        ),
        'labels' => array(),
        'documentFilter' => array(),
        'credentials' => array(
            'deployment' => 'Weaviate',
            'url' => 'https://fuoh2jhszcblccxawdgg.c0.us-west3.gcp.weaviate.cloud',
            'key' => ''
        )
    ));

    // Log the request and query
    error_log('ESPBot Query: ' . $message);
    error_log('ESPBot Request to FastAPI: ' . $request_body);

    // FastAPI endpoint
    $api_url = 'https://main-bvxea6i-jvnjg77fkbzzi.eu-5.platformsh.site/api/query';

    error_log('ESPBot Attempting to connect to: ' . $api_url);

    $response = wp_remote_post($api_url, array(
        'headers'     => array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ),
        'body'        => $request_body,
        'timeout'     => 45,
        'sslverify'   => false
    ));

    if (is_wp_error($response)) {
        error_log('ESPBot Error: ' . $response->get_error_message());
        wp_send_json_error(array(
            'response' => 'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.',
            'error' => $response->get_error_message()
        ));
        return;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    error_log('ESPBot Response Code: ' . $response_code);
    error_log('ESPBot Response Body: ' . $response_body);

    $data = json_decode($response_body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('ESPBot JSON Error: ' . json_last_error_msg());
        wp_send_json_error(array(
            'response' => 'Désolé, j\'ai reçu une réponse invalide. Veuillez réessayer.',
            'error' => 'JSON decode error: ' . json_last_error_msg()
        ));
        return;
    }

    wp_send_json_success($data);
}