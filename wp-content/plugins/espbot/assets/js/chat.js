jQuery(document).ready(function($) {
    // Initialize variables
    const widget = $('#espbot-widget');
    const toggleBtn = $('#espbot-toggle');
    const chatWindow = $('.espbot-chat-window');
    const chatMessages = $('#espbot-chat-messages');
    const userInput = $('#espbot-chat-input');
    const sendButton = $('#espbot-chat-send');
    const typingIndicator = $('#typing-indicator');

    console.log('Chat elements initialized:', {
        widget: widget.length,
        toggleBtn: toggleBtn.length,
        chatWindow: chatWindow.length,
        chatMessages: chatMessages.length,
        userInput: userInput.length,
        sendButton: sendButton.length,
        typingIndicator: typingIndicator.length
    });

    // Toggle chat window
    toggleBtn.on('click', function() {
        widget.toggleClass('active');
        if (widget.hasClass('active')) {
            chatWindow.fadeIn(300);
            scrollToBottom();
        } else {
            chatWindow.fadeOut(300);
        }
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

    // Function to generate a random user ID
    function generateUserId() {
        return 'user-' + Math.random().toString(36).substring(2, 15);
    }

    let currentSessionId = '';
    let conversationId = '';

    // Initialize session ID if not exists
    if (!currentSessionId) {
        currentSessionId = localStorage.getItem('espbot_user_id') || generateUserId();
        localStorage.setItem('espbot_user_id', currentSessionId);
    }

    // Function to fetch suggestions
    async function fetchSuggestions(messageId) {
        try {
            // Ensure we have a valid user ID
            if (!currentSessionId) {
                currentSessionId = localStorage.getItem('espbot_user_id') || generateUserId();
                localStorage.setItem('espbot_user_id', currentSessionId);
            }

            const suggestionsUrl = `${espbotAjax.api_url}/messages/${messageId}/suggested?user=${encodeURIComponent(currentSessionId)}`;

            const response = await fetch(suggestionsUrl, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${espbotAjax.api_key}`,
                    'Content-Type': 'application/json'
                }
            });
            
            console.log('Suggestions API response status:', response.status);
            const data = await response.json();
            console.log('Suggestions API response data:', data);
            
            if (data.result === 'success' && Array.isArray(data.data)) {
                return data.data;
            }
            return [];
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            return [];
        }
    }

    // Function to send message
    async function sendMessage() {
        const message = userInput.val().trim();
        if (!message) return;

        // Clear input
        userInput.val('');

        // Add user message
        addUserMessage(message);

        try {
            await sendMessageToBackend(message);
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
                    conversation_id: conversationId,
                    user_id: currentSessionId
                }
            });

            hideTypingIndicator();

            if (!response || !response.success) {
                throw new Error('Invalid response from server');
            }

            if (response.data) {
                if (response.data.conversation_id) {
                    conversationId = response.data.conversation_id;
                }

                const messageId = response.data.message_id;
                const botResponse = response.data.message || response.data;

                const messageDiv = $('<div>').addClass('espbot-message espbot-message-bot');
                const contentDiv = $('<div>').addClass('espbot-message-content');
                contentDiv.html(md.render(botResponse));

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

                // Then fetch and show suggestions immediately
                if (messageId) {
                    console.log('Fetching suggestions for message:', messageId);
                    const suggestions = await fetchSuggestions(messageId);
                    
                    if (suggestions && suggestions.length > 0) {
                        const $suggestionsContainer = $(`
                            <div class="espbot-suggestions-container">
                                <div class="espbot-try-ask">
                                    <i class="fa fa-star"></i>
                                    Essayez de demander
                                </div>
                                <div class="espbot-suggestions">
                                    ${suggestions.map(suggestion => `
                                        <button class="espbot-suggestion-btn" onclick="jQuery(this).closest('.espbot-suggestions').find('button').prop('disabled', true); jQuery('#espbot-chat-input').val('${escapeHtml(suggestion)}'); jQuery('#espbot-chat-send').click();">
                                            ${escapeHtml(suggestion)}
                                        </button>
                                    `).join('')}
                                </div>
                            </div>
                        `);
                        
                        messageDiv.append($suggestionsContainer);
                        scrollToBottom();
                    }
                }

                return botResponse;
            }

            return response.message || 'An error occurred';

        } catch (error) {
            console.error('Error in sendMessageToBackend:', error);
            hideTypingIndicator();
            checkServerStatus(); // Check server status on error
            throw error;
        }
    }

    // Function to create suggestions HTML
    function createSuggestionsHtml(suggestions) {
        return `
            <div class="espbot-suggestions">
                ${suggestions.map(suggestion => `
                    <button class="espbot-suggestion-btn" onclick="jQuery(this).closest('.espbot-suggestions').find('button').prop('disabled', true); jQuery('#espbot-chat-input').val('${escapeHtml(suggestion)}'); jQuery('#espbot-chat-send').click();">
                        ${escapeHtml(suggestion)}
                    </button>
                `).join('')}
            </div>
        `;
    }

    // Function to display suggestions (keeping for compatibility)
    function displaySuggestions(suggestions) {
        if (!suggestions || !suggestions.length) {
            console.log('No suggestions to display');
            return;
        }
        
        console.log('Displaying suggestions:', suggestions);
        const $suggestions = $(createSuggestionsHtml(suggestions));
        chatMessages.append($suggestions);
        
        setTimeout(() => {
            $suggestions.css({
                'transition': 'opacity 0.3s ease',
                'opacity': '1'
            });
        }, 100);
        
        scrollToBottom();
    }

    // Function to start new chat
    async function startNewChat() {
        currentSessionId = generateUserId();
        conversationId = '';
        chatMessages.empty();
        
        // Show welcome message immediately
        addBotMessage("Bonjour ! Je suis ESPbot, l'assistant virtuel officiel de l'École Supérieure Polytechnique (ESP). Je suis là pour vous aider avec vos questions et vous fournir des informations précises et pertinentes sur l'ESP. Comment puis-je vous aider aujourd'hui ?");

        // Initialize session in background
        try {
            const response = await $.ajax({
                url: espbotAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'espbot_send_message',
                    nonce: espbotAjax.nonce,
                    message: 'start_new_chat',
                    conversation_id: '',
                    user_id: currentSessionId
                }
            });

            if (response && response.success && response.data.conversation_id) {
                conversationId = response.data.conversation_id;
            }
        } catch (error) {
            console.error('Error initializing chat session:', error);
            // Don't show error message since welcome message is already shown
        }
    }

    // Function to update server status
    async function checkServerStatus() {
        try {
            await $.ajax({
                url: espbotAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'espbot_send_message',
                    nonce: espbotAjax.nonce,
                    message: 'ping',
                    conversation_id: conversationId,
                    user_id: currentSessionId
                }
            });
            
            // Server is online
            $('.espbot-status').removeClass('offline');
            $('.espbot-status span:not(.status-dot)').text('En ligne et prêt à vous aider');
        } catch (error) {
            // Server is offline
            $('.espbot-status').addClass('offline');
            $('.espbot-status span:not(.status-dot)').text('Serveur hors ligne');
        }
    }

    // Check server status initially and every 30 seconds
    checkServerStatus();
    setInterval(checkServerStatus, 30000);

    // Event listeners
    sendButton.on('click', sendMessage);
    userInput.on('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Add click handler for Nouvelle chat button
    $('#espbot-nouvelle-chat').on('click', function(e) {
        e.preventDefault();
        startNewChat();
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
