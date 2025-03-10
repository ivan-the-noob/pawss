let allProducts = []; 
let currentCategory = ''; 

function loadAllProducts() {
    const productElements = document.querySelectorAll('.product-item');
    allProducts = []; 
    productElements.forEach(product => {
        allProducts.push(product); 
    });

    displayLimitedProducts(allProducts, 6);
}

function filterProducts(type) {
    currentCategory = type; 


    const filteredProducts = allProducts.filter(product => {
        const productType = product.dataset.type;
        return productType === type || type === 'all';
    });


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
    filterProducts('all');
});
