jQuery(document).ready(function($) {
    // Handle chat button click
    $('.chat-btn').on('click', function(e) {
        e.preventDefault();
        const chatUrl = $(this).attr('href');
        
        // Navigate to chat page
        window.location.href = chatUrl;
    });
});
