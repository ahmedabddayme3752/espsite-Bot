document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const chatSidebar = document.querySelector('.chat-sidebar');
    const chatMain = document.querySelector('.chat-main');

    // Toggle sidebar
    if (sidebarToggle && chatSidebar) {
        sidebarToggle.addEventListener('click', function() {
            chatSidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!chatSidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) && 
                chatSidebar.classList.contains('active')) {
                chatSidebar.classList.remove('active');
            }
        });
    }

    // Adjust textarea height
    const chatInput = document.querySelector('#user-input');
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Scroll to bottom on new messages
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
        const scrollToBottom = () => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };
        
        // Create observer for chat messages
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(chatMessages, {
            childList: true,
            subtree: true
        });
    }
});
