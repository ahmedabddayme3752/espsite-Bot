jQuery(document).ready(function($) {
    // DOM Elements
    const chatMessages = $('#chat-messages');
    const userInput = $('#user-input');
    const sendButton = $('#send-message');
    const newChatButton = $('#new-chat-btn');

    // Variables
    let conversationHistory = [];
    let isProcessing = false;

    // Functions
    function scrollToBottom() {
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    function addMessage(message, isUser = false, isWelcome = false) {
        const messageHTML = `
            <div class="message ${isUser ? 'user' : 'bot'}${isWelcome ? ' welcome-message' : ''}">
                <div class="message-content">
                    <p>${message}</p>
                </div>
            </div>
        `;
        chatMessages.append(messageHTML);
        scrollToBottom();
    }

    function showTypingIndicator() {
        const typingHTML = `
            <div class="message bot typing-indicator">
                <div class="message-content">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        chatMessages.append(typingHTML);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        $('.typing-indicator').remove();
    }

    function initializeChat() {
        if (chatMessages.find('.welcome-message').length === 0) {
            const welcomeMessage = `
                <div class="message bot welcome-message">
                    <div class="message-content">
                        <p>👋 Bonjour! Je suis EspBot, votre assistant virtuel.</p>
                        <p class="subtitle">Comment puis-je vous aider aujourd'hui?</p>
                    </div>
                </div>
            `;
            chatMessages.prepend(welcomeMessage);
        }
    }

    function startNewChat() {
        console.log('Starting new chat');
        
        // Get the welcome message
        const welcomeMessage = chatMessages.find('.welcome-message');
        
        // Clear all messages
        chatMessages.empty();
        
        // Re-add the welcome message
        chatMessages.append(welcomeMessage);
        
        // Clear user input and conversation history
        userInput.val('');
        conversationHistory = [];
        
        // Focus on input
        userInput.focus();
        // Scroll to bottom
        scrollToBottom();
    }

    async function processUserMessage(message) {
        if (isProcessing || !message.trim()) return;
        
        isProcessing = true;
        userInput.val('');

        // Add user message to chat
        addMessage(message, true);
        conversationHistory.push({ role: 'user', content: message });

        // Show typing indicator
        showTypingIndicator();

        try {
            const response = await $.ajax({
                url: espbot_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'espbot_send_message',
                    nonce: espbot_ajax.nonce,
                    message: message,
                    history: JSON.stringify(conversationHistory)
                }
            });

            hideTypingIndicator();

            if (response.success) {
                addMessage(response.data);
                conversationHistory.push({ role: 'assistant', content: response.data });
            } else {
                addMessage('Désolé, une erreur s\'est produite. Veuillez réessayer.');
            }
        } catch (error) {
            console.error('Error:', error);
            hideTypingIndicator();
            addMessage('Désolé, une erreur s\'est produite. Veuillez réessayer.');
        }

        isProcessing = false;
        scrollToBottom();
    }

    // Event Listeners
    sendButton.on('click', () => {
        const message = userInput.val().trim();
        processUserMessage(message);
    });

    userInput.on('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const message = userInput.val().trim();
            processUserMessage(message);
        }
    });

    // Handle new chat button clicks
    $(document).on('click', '#new-chat-btn', function(e) {
        console.log('New chat button clicked');
        e.preventDefault();
        e.stopPropagation();
        startNewChat();
    });

    // Initialize chat with welcome message
    initializeChat();
});
