document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const decrementButton = document.getElementById('decrement');
    const incrementButton = document.getElementById('increment');

    decrementButton.addEventListener('click', function() {
        let value = parseInt(quantityInput.value, 10);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });

    incrementButton.addEventListener('click', function() {
        let value = parseInt(quantityInput.value, 10);
        quantityInput.value = value + 1;
    });
});