jQuery(document).ready(function($) {
    // Initialize variables
    const widget = $('#espbot-widget');
    const toggleBtn = $('#espbot-toggle');
    const chatMessages = $('#espbot-chat-messages');
    const userInput = $('#espbot-chat-input');
    const sendButton = $('#espbot-chat-send');
    const typingIndicator = $('#typing-indicator');

    console.log('Chat elements initialized:', {
        widget: widget.length,
        toggleBtn: toggleBtn.length,
        chatMessages: chatMessages.length,
        userInput: userInput.length,
        sendButton: sendButton.length,
        typingIndicator: typingIndicator.length
    });

    // Toggle chat window
    toggleBtn.on('click', function() {
        widget.toggleClass('active');
    });

    // Function to create typing indicator element
    function createTypingIndicator() {
        return `
            <div id="typing-indicator" class="espbot-message espbot-message-bot">
                <div class="espbot-message-content">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
    }

    // Function to show typing indicator
    function showTypingIndicator() {
        console.log('Attempting to show typing indicator');
        let indicator = $('#typing-indicator');
        
        // If indicator doesn't exist, create it
        if (!indicator.length) {
            chatMessages.append(createTypingIndicator());
            indicator = $('#typing-indicator');
            console.log('Created new typing indicator');
        }
        
        indicator.fadeIn(200);
        console.log('Typing indicator shown successfully');
        scrollToBottom();
    }

    // Function to hide typing indicator
    function hideTypingIndicator() {
        console.log('Attempting to hide typing indicator');
        const indicator = $('#typing-indicator');
        if (indicator.length) {
            indicator.fadeOut(200, function() {
                $(this).remove(); // Remove the indicator after fade out
            });
            console.log('Typing indicator hidden successfully');
        }
    }

    // Function to scroll to bottom
    function scrollToBottom() {
        const messagesDiv = chatMessages[0];
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Function to add user message
    function addUserMessage(message) {
        const userMessageHtml = `
            <div class="espbot-message espbot-message-user">
                <div class="espbot-message-content">
                    <p>${escapeHtml(message)}</p>
                </div>
            </div>
        `;
        chatMessages.append(userMessageHtml);
        scrollToBottom();
    }

    // Function to copy text to clipboard
    function copyToClipboard(text) {
        // Create temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        
        // Select and copy
        textarea.select();
        document.execCommand('copy');
        
        // Cleanup
        document.body.removeChild(textarea);
    }

    // Function to add bot message
    function addBotMessage(message) {
        const botMessageHtml = `
            <div class="espbot-message espbot-message-bot">
                <div class="espbot-message-content">
                    ${message}
                    <button class="copy-message" title="Copier le message">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        `;
        chatMessages.append(botMessageHtml);
        scrollToBottom();

        // Add click handler for the copy button
        chatMessages.find('.copy-message').last().on('click', function(e) {
            e.preventDefault();
            const content = $(this).parent().clone();
            content.find('.copy-message').remove(); // Remove copy button from clone
            const textToCopy = content.text().trim();
            
            copyToClipboard(textToCopy);

            // Show feedback
            const $button = $(this);
            $button.html('<i class="fas fa-check"></i>');
            setTimeout(() => {
                $button.html('<i class="fas fa-copy"></i>');
            }, 2000);
        });
    }

    // Function to send message to backend
    async function sendMessageToBackend(message) {
        showTypingIndicator();
        
        try {
            // Special handling for "Bonjour"
            if (message.toLowerCase().trim() === 'bonjour') {
                hideTypingIndicator();
                return "Bonjour ! Je suis ESPbot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP). Je suis là pour vous aider avec vos questions et vous fournir des informations précises et pertinentes sur l'ESP. Comment puis-je vous aider aujourd'hui ?";
            }

            const response = await $.ajax({
                url: espbotAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'espbot_chat',
                    nonce: espbotAjax.nonce,
                    message: message
                }
            });

            hideTypingIndicator();

            if (!response.success) {
                console.error('Server error:', response.data?.error || 'Unknown error');
                return response.data?.response || 'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.';
            }

            const data = response.data;
            if (data.error) {
                console.error('API error:', data.error);
                return 'Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.';
            }

            // Process the response like Platform.sh site
            if (data.context) {
                // Extract the main content section
                const mainContent = data.context.split('Variation 1:')[0].trim();
                
                // Clean up the content
                let cleanContent = mainContent
                    .replace(/Document Title:.*?\n/g, '')
                    .replace(/Chunk: \d+\n/g, '')
                    .replace(/High Relevancy:.*?\n/g, '')
                    .replace(/SUIVEZ-NOUS SUR.*$/gms, '')
                    .replace(/CONTACTEZ-NOUS.*$/gms, '')
                    .trim();

                // Split into lines and process
                const lines = cleanContent.split('\n');
                let formattedContent = '';
                
                // Process each line
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i].trim();
                    if (!line) continue;

                    // Skip bullet points that are just formatting
                    if (line === '*') continue;

                    // Handle bullet points
                    if (line.startsWith('*')) {
                        formattedContent += line.substring(1).trim() + '\n';
                    } else {
                        formattedContent += line + '\n';
                    }
                }

                // Clean up extra newlines and spaces
                formattedContent = formattedContent
                    .replace(/\n{3,}/g, '\n\n')
                    .replace(/\s+\n/g, '\n')
                    .trim();

                // Make dates bold
                formattedContent = formattedContent.replace(/(\d{1,2} [A-Za-zéû]+ \d{4})/g, '<strong>$1</strong>');

                return formattedContent || 'Je suis désolé, je n\'ai pas trouvé d\'informations pertinentes pour votre question. Pouvez-vous reformuler votre question?';
            }

            return 'Je suis désolé, je n\'ai pas trouvé d\'informations pertinentes pour votre question. Pouvez-vous reformuler votre question?';

        } catch (error) {
            hideTypingIndicator();
            console.error('Network error:', error);
            return 'Désolé, j\'ai rencontré une erreur de connexion. Veuillez vérifier votre connexion et réessayer.';
        }
    }

    // Function to handle message sending
    async function sendMessage() {
        const message = userInput.val().trim();
        
        if (message === '') {
            return;
        }

        // Disable input and button while processing
        userInput.prop('disabled', true);
        sendButton.prop('disabled', true);

        // Add user message to chat
        addUserMessage(message);
        userInput.val('');
        scrollToBottom();

        try {
            // Get response from backend
            const botResponse = await sendMessageToBackend(message);
            
            // Add bot response
            addBotMessage(botResponse);
        } catch (error) {
            // Show error message
            addBotMessage('Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.');
        } finally {
            // Re-enable input and button
            userInput.prop('disabled', false);
            sendButton.prop('disabled', false);
            userInput.focus();
            scrollToBottom();
        }
    }

    // Event listeners
    sendButton.on('click', sendMessage);
    userInput.on('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Auto-resize textarea
    userInput.on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Handle new chat button clicks
    $(document).on('click', '#widget-new-chat-btn', function(e) {
        e.preventDefault();
        startNewChat();
    });

    // Function to start new chat
    function startNewChat() {
        // Get the welcome message
        const welcomeMessage = chatMessages.find('.welcome-message');
        
        // Clear all messages
        chatMessages.empty();
        
        // Re-add the welcome message
        chatMessages.append(welcomeMessage);
        
        // Clear user input
        userInput.val('');
        // Focus on input
        userInput.focus();
        // Scroll to bottom
        scrollToBottom();
    }

    console.log('Chat initialized');
});
