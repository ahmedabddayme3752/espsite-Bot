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
        const messageDiv = $('<div>').addClass('espbot-message espbot-message-bot');
        const contentDiv = $('<div>').addClass('espbot-message-content');
        
        try {
            // Parse the message if it's JSON
            const parsedMessage = typeof message === 'string' ? JSON.parse(message) : message;
            
            // Add context section if available
            if (parsedMessage.context && parsedMessage.context.length > 0) {
                const contextDiv = $('<div>').addClass('espbot-context-section');
                contextDiv.append($('<h4>').text('Documents Pertinents:'));
                parsedMessage.context.forEach(ctx => {
                    const contextItem = $('<div>').addClass('espbot-context-item');
                    contextItem.html(md.render(ctx)); // Use markdown rendering
                    contextDiv.append(contextItem);
                });
                contentDiv.append(contextDiv);
            }
            
            // Add main response
            if (parsedMessage.response) {
                const responseDiv = $('<div>').addClass('espbot-response-section');
                responseDiv.html(md.render(parsedMessage.response)); // Use markdown rendering
                contentDiv.append(responseDiv);
            }
            
            // Add variations if available
            if (parsedMessage.variations && parsedMessage.variations.length > 0) {
                const variationsDiv = $('<div>').addClass('espbot-variations-section');
                variationsDiv.append($('<h4>').text('Perspectives:'));
                
                parsedMessage.variations.forEach(variation => {
                    const variationDiv = $('<div>').addClass('espbot-variation');
                    variationDiv.append($('<h5>').text(variation.title));
                    variationDiv.append($('<div>').html(md.render(variation.content))); // Use markdown rendering
                    variationsDiv.append(variationDiv);
                });
                
                contentDiv.append(variationsDiv);
            }
            
            // Add FAQ if available
            if (parsedMessage.faq && parsedMessage.faq.length > 0) {
                const faqDiv = $('<div>').addClass('espbot-faq-section');
                faqDiv.append($('<h4>').text('Questions Fréquentes:'));
                
                parsedMessage.faq.forEach(item => {
                    const questionDiv = $('<div>').addClass('espbot-faq-item');
                    questionDiv.append($('<h5>').text(item.question));
                    questionDiv.append($('<div>').html(md.render(item.answer))); // Use markdown rendering
                    faqDiv.append(questionDiv);
                });
                
                contentDiv.append(faqDiv);
            }
            
            // Add sources if available
            if (parsedMessage.sources && parsedMessage.sources.length > 0) {
                const sourcesDiv = $('<div>').addClass('espbot-sources-section');
                sourcesDiv.append($('<h4>').text('Sources:'));
                const sourcesList = $('<ul>').addClass('espbot-sources-list');
                
                parsedMessage.sources.forEach(source => {
                    if (source) {
                        const sourceItem = $('<li>').addClass('espbot-source-item');
                        sourceItem.html(md.render(source)); // Use markdown rendering
                        sourcesList.append(sourceItem);
                    }
                });
                
                sourcesDiv.append(sourcesList);
                contentDiv.append(sourcesDiv);
            }
            
        } catch (e) {
            // Fallback for plain text messages
            contentDiv.html(md.render(message)); // Use markdown rendering for plain text
        }
        
        // Add copy button
        const copyButton = $('<button>')
            .addClass('copy-message')
            .html('<i class="fas fa-copy"></i>')
            .on('click', function(e) {
                e.preventDefault();
                const content = contentDiv.clone();
                content.find('.copy-message').remove();
                const textToCopy = content.text().trim();
                
                copyToClipboard(textToCopy);
                
                // Show feedback
                const $button = $(this);
                $button.html('<i class="fas fa-check"></i>');
                setTimeout(() => {
                    $button.html('<i class="fas fa-copy"></i>');
                }, 2000);
            });
        
        contentDiv.append(copyButton);
        messageDiv.append(contentDiv);
        chatMessages.append(messageDiv);
        scrollToBottom();
    }

    let currentSessionId = '';
    let conversationId = '';

    // Function to send message
    async function sendMessage() {
        const message = userInput.val().trim();
        if (message === '') return;

        userInput.val('');
        addUserMessage(message);

        try {
            const response = await sendMessageToBackend(message);
            addBotMessage(response);
        } catch (error) {
            console.error('Error sending message:', error);
            addBotMessage('Désolé, j\'ai rencontré une erreur. Veuillez réessayer plus tard.');
        }
    }

    // Function to send message to backend
    async function sendMessageToBackend(userMessage) {
        showTypingIndicator();
        
        try {
            const response = await $.ajax({
                url: espbotAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'espbot_send_message',
                    nonce: espbotAjax.nonce,
                    message: userMessage,
                    conversation_id: conversationId
                }
            });

            hideTypingIndicator();

            if (!response || !response.success) {
                const errorMsg = response?.data?.message || 'Unknown error occurred';
                console.error('Server error:', errorMsg);
                throw new Error(errorMsg);
            }

            const data = response.data;
            if (!data || typeof data.message === 'undefined') {
                throw new Error('Invalid response format from server');
            }

            // Update conversation ID if provided
            if (data.conversation_id) {
                conversationId = data.conversation_id;
            }

            return data.message;

        } catch (error) {
            hideTypingIndicator();
            console.error('Network error:', error);
            if (error.status === 403) {
                throw new Error('Authentication error. Please refresh the page and try again.');
            }
            throw new Error(error.message || 'Error communicating with the server');
        }
    }

    // Function to start new chat
    function startNewChat() {
        currentSessionId = '';
        conversationId = '';
        chatMessages.empty();
        addBotMessage("Bonjour ! Je suis ESPbot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP). Je suis là pour vous aider avec vos questions et vous fournir des informations précises et pertinentes sur l'ESP. Comment puis-je vous aider aujourd'hui ?");
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

    console.log('Chat initialized');
});

// Function to display message
function displayMessage(message, isUser = false) {
    const messageContainer = document.createElement('div');
    messageContainer.className = `espbot-message ${isUser ? 'espbot-message-user' : 'espbot-message-bot'}`;

    const messageContent = document.createElement('div');
    messageContent.className = 'espbot-message-content';
    
    if (!isUser) {
        try {
            // Pre-process the message
            let processedMessage = message
                // Fix line endings
                .replace(/\r\n/g, '\n')
                // Ensure proper spacing after colons in bold text
                .replace(/\*\*([^*]+):\*\*/g, '**$1 :**')
                // Handle list items
                .replace(/^\*/gm, '*')
                // Clean up extra spaces (but preserve indentation)
                .replace(/([^\n])\s{2,}([^\s])/g, '$1 $2')
                .trim();

            // Convert markdown to HTML using markdown-it
            const html = md.render(processedMessage);
            
            // Create temporary div for processing
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Process lists and spacing
            const lists = tempDiv.querySelectorAll('ul');
            lists.forEach(list => {
                list.classList.add('md-list');
                const items = list.querySelectorAll('li');
                items.forEach(item => {
                    item.classList.add('md-list-item');
                });
            });

            // Fix spacing after bold text with colons
            const boldElements = tempDiv.querySelectorAll('.md-bold');
            boldElements.forEach(bold => {
                if (bold.textContent.endsWith(':')) {
                    const space = document.createTextNode(' ');
                    bold.parentNode.insertBefore(space, bold.nextSibling);
                }
            });

            messageContent.innerHTML = tempDiv.innerHTML;

        } catch (error) {
            console.error('Markdown parsing error:', error);
            messageContent.textContent = message;
        }
    } else {
        messageContent.textContent = message;
    }

    const timeElement = document.createElement('div');
    timeElement.className = 'espbot-message-time';
    timeElement.textContent = new Date().toLocaleTimeString();

    messageContainer.appendChild(messageContent);
    messageContainer.appendChild(timeElement);

    const messagesContainer = document.querySelector('.espbot-chat-messages');
    messagesContainer.appendChild(messageContainer);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
