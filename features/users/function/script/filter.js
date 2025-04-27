
let allProducts = []; 
let currentCategory = ''; 

function loadAllProducts() {
    const productElements = document.querySelectorAll('.product-item');
    allProducts = []; 
    productElements.forEach(product => {
        allProducts.push(product); 
    });

    // Debug: Check all products loaded
    console.log('All products loaded:', allProducts);

    displayLimitedProducts(allProducts, 6);
}

function filterProducts(type) {
    currentCategory = type;

    // Debug: Logging the category being filtered
    console.log(`Filtering for: ${type}`);

    const filteredProducts = allProducts.filter(product => {
        const productType = product.dataset.type;
        console.log(`Product type: ${productType}, Looking for: ${type}`);  // Debug log
        return productType === type || type === 'all';
    });

    // Debug: Logging filtered products
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