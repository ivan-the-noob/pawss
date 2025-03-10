function toggleChat() {
    const chatInterface = document.getElementById('chat-interface');
    chatInterface.classList.toggle('hidden');
}

function typeMessage(message, element, typingSpeed = 100) {
    element.textContent = ""; // Clear any existing text
    let charIndex = 0;

    function type() {
        if (charIndex < message.length) {
            element.textContent += message.charAt(charIndex);
            charIndex++;
            setTimeout(type, typingSpeed); // Adjust typing speed here
        }
    }

    type();
}

document.addEventListener("DOMContentLoaded", function() {
    const initialMessage = "Hello, I am Chat Bot. Please ask me a question just by pressing the question buttons.";
    const typingTextElement = document.getElementById("typing-text");
    typeMessage(initialMessage, typingTextElement, 20); // Typing speed in ms
});

function sendResponse(question) {
    const chatBody = document.getElementById('chat-body');
    const responseContainer = document.createElement('div');
    responseContainer.classList.add('admin', 'mt-3');

    const adminChatBubble = document.createElement('div');
    adminChatBubble.classList.add('admin-chat');

    const adminImage = document.createElement('img');
    adminImage.src = "assets/img/logo.png";
    adminImage.alt = "Admin";

    const adminName = document.createElement('p');
    adminName.textContent = "Admin";

    adminChatBubble.appendChild(adminImage);
    adminChatBubble.appendChild(adminName);

    const responseText = document.createElement('p');
    responseText.classList.add('text');

    let responseMessage;
    switch (question) {
        case 'How to log in?':
            responseMessage = 'To log in, go to the login page, enter your credentials, and click "Login".';
            break;
        case 'How to book?':
            responseMessage = 'To book an appointment, select a service, choose a date, and fill in your details.';
            break;
        case 'What are the services?':
            responseMessage = 'We offer various services including veterinary consultations, vaccinations, and grooming.';
            break;
        case 'Contact information?':
            responseMessage = 'You can contact us at contact@yourclinic.com or call us at (123) 456-7890.';
            break;
        default:
            responseMessage = 'I am sorry, I cannot answer that question.';
            break;
    }

    typeMessage(responseMessage, responseText, 20); 

    responseContainer.appendChild(adminChatBubble);
    responseContainer.appendChild(responseText);

    chatBody.appendChild(responseContainer);

    chatBody.scrollTop = chatBody.scrollHeight;
}
