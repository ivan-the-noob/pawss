function toggleChat() {
    const chatInterface = document.getElementById('chat-interface');
    const chatButton = document.getElementById('chat-bot-button');
    const isHidden = chatInterface.classList.contains('hidden');
    
    if (isHidden) {
        chatInterface.classList.remove('hidden');
        chatButton.classList.add('hidden');
    } else {
        chatInterface.classList.add('hidden');
        chatButton.classList.remove('hidden');
    }
}
