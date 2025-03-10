function selectSize(button) {
    // Get all size buttons
    var buttons = document.querySelectorAll('.size-button');

    // Remove 'selected' class from all buttons
    buttons.forEach(function(btn) {
        btn.classList.remove('selected');
    });

    // Add 'selected' class to the clicked button
    button.classList.add('selected');
}