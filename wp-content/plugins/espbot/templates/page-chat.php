<?php
/**
 * Template Name: Chat Page
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="chat-container">
            <div class="chat-header">
                <div class="bot-profile">
                    <div class="bot-avatar">
                        <img src="<?php echo ESPBOT_PLUGIN_URL; ?>assets/images/esplogo.png" alt="EspBot Logo">
                    </div>
                    <div class="bot-info">
                        <h2>EspBot</h2>
                        <span class="bot-status">Online</span>
                    </div>
                </div>
                <div class="header-actions">
                    <button id="new-chat-btn" style="
                        width: 70px !important;
                        height: 70px !important;
                        border-radius: 50% !important;
                        background-color: #FFD700 !important;
                        color: #000 !important;
                        border: none !important;
                        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3) !important;
                        cursor: pointer !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    ">
                        <i class="fas fa-plus" style="font-size: 32px !important;"></i>
                    </button>
                </div>
            </div>

            <div id="chat-messages" class="chat-messages">
                <!-- Messages will be dynamically added here -->
                <div class="message bot welcome-message">
                    <div class="message-content">
                        <p>👋 Bonjour! Je suis EspBot, votre assistant virtuel.</p>
                        <p class="subtitle">Comment puis-je vous aider aujourd'hui?</p>
                    </div>
                </div>
            </div>

            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <textarea id="user-input" 
                        placeholder="Écrivez votre message ici..." 
                        rows="1"></textarea>
                    <div class="input-actions">
                        <button id="send-message" class="send-btn" title="Envoyer">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
get_footer();
?>
