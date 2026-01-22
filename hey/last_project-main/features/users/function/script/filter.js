
let allProducts = []; 
let currentCategory = ''; 

function loadAllProducts() {
    const productElements = document.querySelectorAll('.product-item');
    allProducts = Array.from(productElements); // Make sure all products are stored as an array

    console.log('All products loaded:', allProducts);

    displayLimitedProducts(allProducts, 6);
}

function filterProducts(type) {
    currentCategory = type;

    console.log(`Filtering for: ${type}`);

    const filteredProducts = allProducts.filter(product => {
        const productType = product.dataset.type;
        console.log(`Product type: ${productType}, Looking for: ${type}`);

        // If type is 'all', show all products
        if (type === 'all') {
            return true;
        }
        
        // Ensure case-insensitive comparison
        return productType.toLowerCase() === type.toLowerCase();
    });

    console.log(`Filtered Products for ${type}:`, filteredProducts);

    displayLimitedProducts(filteredProducts, 6);
}


function displayLimitedProducts(products, limit) {
    allProducts.forEach(product => {
        product.style.display = 'none';
    });

    let displayedCount = 0;
    products.forEach(product => {
        if (displayedCount < limit) {
            product.style.display = 'block'; 
            displayedCount++;
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    loadAllProducts();
    filterProducts('all');  // Initially show all products
});