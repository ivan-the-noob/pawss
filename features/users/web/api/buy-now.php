<?php
session_start(); // Start the session
require '../../../../db.php';

$product = null; 
$products = [];

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $stmt = $conn->prepare("SELECT * FROM product WHERE id != ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }

  $stmt->close();
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
  }
  $stmt->close();


}

if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];
  $stmt = $conn->prepare("SELECT name, contact_number, home_street, address_search FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if (!$user) {
    echo "User not found.";
    exit;
  }

  $name = htmlspecialchars($user['name']);
  $contactNumber = htmlspecialchars($user['contact_number']);
  $homeStreet = htmlspecialchars($user['home_street']);
  $addressSearch = htmlspecialchars($user['address_search']);
} else {
  echo "User not logged in.";
  exit;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Now</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="../../css/buy-now.css">
</head>

<body>
  <!-- Navbar -->
  <div class="navbar-container">
<nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
            <a class="navbar-brand d-none d-lg-block" href="#">
                    <img src="../../../../assets/img/logo.png" alt="Logo" width="30" height="30">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        style="stroke: black; fill: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../../../../index.php">Home</a>
                        </li>
                    </ul>
                    <div class="d-flex ml-auto">
                        <?php if ($email): ?>
                            <!-- Profile Dropdown -->
                            <div class="dropdown second-dropdown">
                                <button class="btn" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="../../../../assets/img/<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Image" class="profile">
                                </button>
                                <ul class="dropdown-menu custom-center-dropdown" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="dropdown-item" href="features/users/web/api/dashboard.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                          <?php
                            include '../../function/php/count_cart.php';
                          ?>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="../../function/php/update_cart_status.php" class="header-cart">
                            <span class="material-symbols-outlined">
                                shopping_cart
                            </span>

                            <?php if ($newCartData > 0): ?>
                                <span class="badge"><?= $newCartData ?></span>
                            <?php endif; ?>
                        </a>
                                <a href="my-orders.php" class="header-cart">
                                    <span class="material-symbols-outlined">
                                        local_shipping
                                    </span>
                                </a>
                                <div class="dropdown">
                                    <a href="#" class="header-cart " data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-symbols-outlined">
                                        notifications
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end"  style="width: 300px; height: 400px; overflow-y: auto;">
                                    <?php
                                        include '../../../../db.php';
                                       

                                        $email = $_SESSION['email'] ?? '';

                                        if ($email) {
                                            $query = "SELECT message, created_at FROM notification WHERE email = ? ORDER BY id DESC";
                                            $stmt = $conn->prepare($query);
                                            $stmt->bind_param("s", $email);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $message = $row['message'];
                                                    $created_at = $row['created_at'];

                                                    // Format the created_at date as "April 4, 5:00 PM"
                                                    $formatted_date = date('F j, g:i a', strtotime($created_at));

                                                    // Apply styles for the message
                                                    $classes = 'dropdown-item bg-white shadow-sm px-3 py-2 rounded';
                                                    $style = 'box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);';

                                                    if (trim($message) == "Your appointment has been approved!") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "Your checkout has been approved") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "Your item has been picked up by courier. Please ready payment for COD.") {
                                                        $classes .= ' text-info';
                                                    } else if (trim($message) == "Your profile info has been updated.") {
                                                        $classes .= ' text-info';
                                                    } else if (trim($message) == "New services offered! Check it now!") {
                                                        $classes .= ' text-success';
                                                    } else if (trim($message) == "New product has been arrived! Check it now!") {
                                                        $classes .= ' text-success';
                                                    }

                                                    // Display the message with the date below
                                                    echo "<li><a class=\"$classes d-flex flex-column mx-auto\" href=\"#\" style=\"$style\">";
                                                    echo "<span>$message</span>";
                                                    echo "<div style=\"font-size: 0.9em; color: black; margin-top: 5px;\">$formatted_date</div></a></li>";
                                                    echo "<li><hr class=\"dropdown-divider\"></li>";
                                                }
                                            } else {
                                                echo "<li><a class=\"dropdown-item bg-white shadow-sm\" href=\"#\">No notifications</a></li>";
                                            }

                                            $stmt->close();
                                        } else {
                                            echo "<li><a class=\"dropdown-item bg-white shadow-sm\" href=\"#\">Please log in to see notifications</a></li>";
                                        }

                                 
                                        ?>
                                    </ul>

                                </div>
                            </div>
                            </div>


                        <?php else: ?>
                            <a href="features/users/web/api/login.php" class="btn-theme" type="button">Login</a>
                        <?php endif; ?>
                    </div>

        </nav>
    </div>

  <!-- Product Section -->
  <div class="container">
    <section class="product row">
    <input type="hidden" id="main-product-id" value="<?= htmlspecialchars($product['id']) ?>">
    <div class="col-md-6">
    <img src="../../../../assets/img/product/<?= htmlspecialchars($product['product_img']) ?>" alt="Product Image" class="img-fluid" id="main-product-img">
  </div>
  <div class="col-md-5">
        <div class="product-text">
            <p>Digital Paws</p>
            <h1 id="main-product-name"><?= htmlspecialchars($product['product_name']) ?></h1>
            <p class="stock" id="main-product-stock">Stock: <?= htmlspecialchars($product['quantity']) ?></p>
            <p class="price" id="main-product-price">₱<?= htmlspecialchars(number_format($product['cost'], 2)) ?> PHP</p>

            <p class="mb-0 mt-3">Quantity</p>
            <?php if ($product['quantity'] < 0): ?>
                <p class="text-danger mt-2">Out of Stock</p>
            <?php else: ?>
                <div class="quantity-wrapper">
                    <button class="quantity-btn" id="decrement-btn">-</button> <!-- Decrement button -->
                    <input type="number" class="form-control" id="quantity" min="1" value="1">
                    <!-- Quantity input -->
                    <button class="quantity-btn" id="increment-btn">+</button> <!-- Increment button -->
                </div>
                <button class="add-to-cart mt-2" data-bs-toggle="modal" data-bs-target="#addToCartModal" onclick="showCartModal()">Add to cart</button>
                <button class="buy-it-now mt-2" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" onclick="openOrderDetailsModal()">Buy it now</button>
            <?php endif; ?>
        </div>
    </div>




<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-bottom-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addToCartModalLabel">Add to cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="cartForm" method="POST" action="../../function/php/add_to_cart.php">
          <div class="row">
            <div class="col-md-4">
              <img id="productImageModal" src="" alt="Product Image" class="img-fluid">
            </div>
            <div class="col-md-8">
              <h5 id="modalNameProduct">Product Name</h5>
              <p id="modalProductPrice">₱0.00 PHP</p>
              <p id="modalProductQuantity">Quantity: 1</p>
              <p><strong>Total:</strong> ₱<span id="modalTotalPrice">0.00</span></p>

              <input type="hidden" id="productId" name="product_id">
              <input type="hidden" id="productName" name="product_name">
              <input type="hidden" id="productPrice" name="product_price">
              <input type="hidden" id="productQuantity" name="quantity">
              <input type="hidden" id="totalPrice" name="total_price">
              <input type="hidden" id="productImage" name="product_image"> 

              <button type="submit" class="btn d-flex add-to-carts">Add to cart</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<script>
 function showCartModal() {
  const productName = document.querySelector('#main-product-name').innerText;
  const productPrice = parseFloat(document.querySelector('#main-product-price').innerText.replace('₱', '').replace(' PHP', ''));
  const quantity = parseInt(document.querySelector('#quantity').value);
  const totalPrice = (productPrice * quantity).toFixed(2);
  
  const productId = document.querySelector('#main-product-id').value; 

  const productImageUrl = document.querySelector('#main-product-img').src;
  
  const productImageName = productImageUrl.substring(productImageUrl.lastIndexOf('/') + 1);

  document.querySelector('#productImageModal').src = productImageUrl;
  document.querySelector('#modalNameProduct').innerText = productName;
  document.querySelector('#modalProductPrice').innerText = `₱${productPrice.toFixed(2)}`;
  document.querySelector('#modalProductQuantity').innerText = `Quantity: ${quantity}`;
  document.querySelector('#modalTotalPrice').innerText = totalPrice;

  document.querySelector('#productId').value = productId;
  document.querySelector('#productName').value = productName;
  document.querySelector('#productPrice').value = productPrice.toFixed(2);
  document.querySelector('#productQuantity').value = quantity;
  document.querySelector('#totalPrice').value = totalPrice;
  document.querySelector('#productImage').value = productImageName; 
}

</script>
      <script>
let previousProduct = null;

function fetchProductDetails(id) {
  fetch(`../../function/php/detail_product.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      const currentMainProduct = {
        id: document.querySelector('#main-product-id').value, // Get the current main product id
        product_img: document.querySelector('#main-product-img').src.split('/').pop(), // Extract current image filename
        product_name: document.querySelector('#main-product-name').innerText,
        cost: parseFloat(document.querySelector('#main-product-price').innerText.replace('₱', '').replace(' PHP', '')),
        quantity: document.querySelector('#main-product-stock').innerText
      };

      // Step 3: Update the main product details with the clicked product's details
      document.querySelector('#main-product-img').src = `../../../../assets/img/product/${data.product_img}`;
      document.querySelector('#main-product-name').innerText = data.product_name;
      document.querySelector('#main-product-stock').innerText = `Stock: ${data.quantity}`;
      document.querySelector('#main-product-price').innerText = `₱${parseFloat(data.cost).toFixed(2)} PHP`;
      document.querySelector('#quantity').value = 1;

      // Store the clicked product id in a hidden field for reference
      document.querySelector('#main-product-id').value = data.id;

      // Step 4: Move the current main product back to the "You may also like" section (if it's not already there)
      const prevProductItem = document.createElement('div');
      prevProductItem.classList.add('col-md-3', 'col-sm-6', 'col-12', 'mb-4', 'product-item');
      prevProductItem.setAttribute('data-id', currentMainProduct.id);
      prevProductItem.innerHTML = `
        <div class="product-item">
          <div class="img-product">
            <img src="../../../../assets/img/product/${currentMainProduct.product_img}" alt="Product Image" class="img-fluid mb-2">
          </div>
          <h5 class="product-title">${currentMainProduct.product_name}</h5>
          <div class="product-price">₱${currentMainProduct.cost.toFixed(2)} PHP</div>
        </div>
      `;

      // Append previous product to "You may also like"
      const productRow = document.querySelector('.row.px-5');
      productRow.appendChild(prevProductItem); 

      // Attach the click event handler to the newly appended product in "You may also like"
      prevProductItem.addEventListener('click', function() {
        fetchProductDetails(currentMainProduct.id); // Trigger fetch on click to swap again
      });

      // Step 5: Remove the clicked product from "You may also like"
      const productItems = document.querySelectorAll('.row.px-5 .product-item');
      productItems.forEach(item => {
        if (item.getAttribute('data-id') == id) {
          item.remove(); // Remove the clicked product item from the list
        }
      });
    })
    .catch(error => console.error('Error fetching product details:', error));
}

// Ensure that all existing products in the "You may also like" section are clickable
document.querySelectorAll('.row.px-5 .product-item').forEach(item => {
  item.addEventListener('click', function() {
    const productId = item.getAttribute('data-id');
    fetchProductDetails(productId); // Trigger fetch when clicking an existing product
  });
});


      </script>
      <script>
      <?php if ($triggerModal): ?>
        document.addEventListener("DOMContentLoaded", function () {
          document.querySelector('.add-to-cart').click();
        });
      <?php endif; ?>
    </script>
  



      <!-- Bootstrap Modal for Order Details -->
      <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered justify-content-center">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="../../function/php/checkout_process.php"
                enctype="multipart/form-data">
                <div class="row">

                  <div class="col-md-6 mb-3">
                    <div class="card">
                      <div class="card-body">
                        <p class="card-title">Customer Information</p>
                        <div class="context">
                          <p class="mb-0" name="contact-num"><?php echo $name; ?></p>
                          <p class="mb-0" name="contact-num"><?php echo $contactNumber; ?></p>
                          <p class="mb-0" name="address-search"><?php echo $addressSearch; ?></p>
                        </div>
                        <!-- Hidden inputs for Customer Information -->
                        <input type="hidden" name="name" value="<?php echo $name; ?>">
                        <input type="hidden" name="contact-num"
                          value="<?php echo $contactNumber; ?>">
                        <input type="hidden" name="address-search"
                          value="<?php echo $addressSearch; ?>">
                      </div>
                    </div>
                  </div>

                  <!-- Payment Method Card -->
                  <div class="col-md-6 mb-3">
                    <div class="card payment-method">
                      <div class="card-body">
                        <h5 class="card-title">Payment Method</h5>
                        <div
                          class="form-check d-flex justify-content-between align-items-center mb-3 form-payment">
                          <div>
                            <input type="radio" id="payment-cash" name="paymentMethod"
                              value="cash" class="form-check-input">
                            <label for="payment-cash" class="form-check-label">Cash on
                              delivery</label>
                          </div>
                          <span class="cod">COD</span>
                        </div>
                        <div
                          class="form-check d-flex justify-content-between align-items-center mb-3 form-payment">
                          <div>
                            <input type="radio" id="payment-gcash" name="paymentMethod"
                              value="gcash" class="form-check-input">
                            <label for="payment-gcash"
                              class="form-check-label">Gcash</label>
                          </div>
                        </div>

                        <!-- Gcash specific inputs and image -->
                        <div id="gcash-details" style="display: none;">
                            <img src="../../../../assets/img/gcash.jfif" alt="Gcash" class="img-fluid mb-3 gcash">
                            <input type="file" name="screenshot" id="gcash-image" class="form-control m">
                            <label for="number" class="form-check-label mt-2">Reference # </label>
                            <input type="number" name="reference" value="" class="form-control mt-1">
                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card">
                      <div class="card-body">
                      <h5 class="card-title">Product</h5>
                        <div class="row">
                          <div class="col-12 col-md-5 mb-3 mb-md-0 text-center">
                          <img id="modalProductImage" src="" alt="Product Image" class="img-fluid">
                          <input type="hidden" name="product_img" id="product_img">
                          </div>

                          <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <h6 id="modalProductName" name="product-name">Product Name</h6>
                          </div>

                          <div class="col-12 col-md-4 d-flex justify-content-end mt-auto">
                            <div class="mb-2">
                              <div class="d-flex justify-content-end">
                                <div id="quantity-display" name="quantity">1x</div>
                              </div>
                              <p class="mb-0 d-flex justify-content-center mt-2"
                                id="total-cost-1" name="cost">
                                ₱<?= htmlspecialchars(number_format($product['cost'], 2)) ?>
                              </p>
                              <!-- Hidden Inputs for Product Details -->
                              <input type="hidden" name="product-name"
                                value="<?= htmlspecialchars($product['product_name']) ?>">
                              <input type="hidden" name="size" value="25kg">
                              <input type="hidden" name="quantity" id="quantity-hidden" value="1">
                              <input type="hidden" name="cost"
                                value="<?= htmlspecialchars(number_format($product['cost'], 2)) ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>



                  <!-- Order Summary Card -->
                  <div class="col-md-6 mb-3">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                          <div class="d-flex justify-content-between">
                            <p>Subtotal:</p>
                            <p class="mb-0 d-flex justify-content-center mt-2" id="total-cost-2" name="sub-total">
                              ₱<?= htmlspecialchars(number_format($product['cost'], 2)) ?>
                            </p>
                            <input type="hidden" name="sub-total" 
                                  value="<?= htmlspecialchars(number_format($product['cost'], 2)) ?>">
                          </div>
                        <div class="d-flex justify-content-between">
                          <p>Shipping Fee:</p>
                          <p><span id="shippingFee" name="shipping-fee">Via Lalamove</span></p>
                          <input type="hidden" name="shipping-fee" value="0.00">
                        </div>
                        <div class="d-flex justify-content-between">
                          <h6>Total:</h6>
                          <h6><span id="totalAmount" name="total-amount">192.00</span></h6>
                          <input type="hidden" name="total-amount" value="<?htmlspecialchars(number_format($product['cost'], 2)) ?>">
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Checkout</button>
            </div>
          </div>
        </div>
      </div>


      <script>
        const unitCost = <?php echo $product['cost']; ?>; // Get product cost from PHP

        const quantityInput = document.getElementById("quantity");
        const incrementBtn = document.getElementById("increment-btn"); // Increment button
        const decrementBtn = document.getElementById("decrement-btn"); // Decrement button

        // Get the shipping fee element and its value
        const shippingFeeElement = document.getElementById("shippingFee");
        const shippingFee = parseFloat(shippingFeeElement
          .textContent); // Assuming the shipping fee is in plain text, like "69.00"

        // Function to update the total cost (Subtotal)
        function updateTotalCost(quantity, totalCostElement) {
              const totalCost = (unitCost * quantity).toFixed(2); // Calculate total cost based on quantity
              totalCostElement.textContent = `₱${totalCost} PHP`; // Update total cost display
              
              // Update the hidden input for subtotal
              document.querySelector('input[name="sub-total"]').value = totalCost; 

              return totalCost; // Return the calculated total cost for further calculations
          }

        // Function to update the total amount (Subtotal + Shipping Fee)
        function updateTotalAmount() {
          // Get the updated subtotal from total-cost-2
          const subtotal = parseFloat(document.getElementById("total-cost-2").textContent.replace("₱", "")
            .trim());

          // Calculate the total (Subtotal + Shipping Fee)
          const totalAmount = (subtotal).toFixed(2); // Sum of subtotal and shipping fee

          // Update the total amount display
          document.getElementById("totalAmount").textContent = `₱${totalAmount}`;
        }

        // Sync the input value with quantity and update the display
        function syncQuantity(value) {
    quantityInput.value = value;

    // Update the quantity display
    const quantityDisplay = document.getElementById("quantity-display");
    quantityDisplay.textContent = `${value}x`;

    // Update both total cost elements
    const totalCostDisplay1 = document.getElementById("total-cost-1");
    const totalCostDisplay2 = document.getElementById("total-cost-2");

    const subtotal = updateTotalCost(value, totalCostDisplay1); // Update total cost for the first div
    updateTotalCost(value, totalCostDisplay2); // Update total cost for the second div

    // Update the total amount (Subtotal + Shipping Fee)
    updateTotalAmount();

    // Update the hidden input for quantity
    document.getElementById('quantity-hidden').value = value;
}



        // Increment the quantity and update displays
        incrementBtn.addEventListener("click", () => {
    const newValue = parseInt(quantityInput.value) + 1; // Increment the value by 1
    syncQuantity(newValue); // Update quantity input and display
});

        // Decrement the quantity and update displays
        decrementBtn.addEventListener("click", () => {
          if (quantityInput.value > 1) {
            const newValue = parseInt(quantityInput.value) - 1; // Decrement the value by 1
            syncQuantity(newValue); // Update quantity input and display
          }
        });

        // Update display when the quantity input is changed manually
        quantityInput.addEventListener("input", () => {
          const newValue = Math.max(1, parseInt(quantityInput
            .value)); // Ensure the value doesn't go below 1
          syncQuantity(newValue); // Update quantity input and display
        });

        // Initialize the display when the page loads
        document.addEventListener('DOMContentLoaded', () => {
          syncQuantity(quantityInput.value); // Ensure the initial display reflects the input value
        });
      </script>


      <script>
      function openOrderDetailsModal() {
    const productImage = document.querySelector('.product img').src;
    const productName = document.querySelector('.product h1').innerText;
    const productPrice = document.querySelector('.price').innerText;
    const productQuantity = document.querySelector('#quantity').value; // get the input value
    const selectedSize = document.querySelector('.size-button.selected'); // get selected size button

    // Update modal content
    document.getElementById('modalProductImage').src = productImage;
    document.getElementById('modalProductName').innerText = productName;
    document.getElementById('modalProductPrice').innerText = productPrice;
    document.getElementById('modalProductQuantity').innerText = `Quantity: ${productQuantity}`;
    document.getElementById('modalProductSize').innerText = selectedSize ? selectedSize.innerText : 'Size not selected';

    // Update hidden inputs
    document.querySelector('input[name="quantity"]').value = productQuantity;

    // Update subtotal and total
    document.getElementById('subtotalAmount').innerText = (parseFloat(productPrice.replace('₱', '').replace(',', '')) * productQuantity).toFixed(2);
    document.getElementById('totalAmount').innerText = (parseFloat(document.getElementById('subtotalAmount').innerText) + 0.00).toFixed(2); // Adding static shipping fee

    document.getElementById('product_img').value = productImage;
}

      </script>

      <script>
        // Get the radio buttons and the Gcash specific details container
        const paymentCash = document.getElementById("payment-cash");
        const paymentGcash = document.getElementById("payment-gcash");
        const gcashDetails = document.getElementById("gcash-details");

        // Show/hide Gcash details based on the selected payment method
        paymentGcash.addEventListener("change", () => {
          if (paymentGcash.checked) {
            gcashDetails.style.display = "block"; // Show Gcash details
          }
        });

        paymentCash.addEventListener("change", () => {
          if (paymentCash.checked) {
            gcashDetails.style.display = "none"; // Hide Gcash details
          }
        });
      </script>




      <!-- Recommended Products Section -->


      <?php if ($product['quantity'] > 0): ?>
          <h3 class="mt-5">You may also like</h3>
          <div class="row px-5">
              <?php if (empty($products)): ?>
                  <div class="col-12">
                      <p>No products available.</p>
                  </div>
              <?php else: ?>
                  <?php foreach (array_slice($products, 0, 4) as $item): ?>
                      <div class="col-md-3 col-sm-6 col-12 mb-4 product-item" data-id="<?= $item['id'] ?>" onclick="fetchProductDetails(<?= $item['id'] ?>)">
                          <div class="product-item">
                              <div class="img-product">
                                  <img src="../../../../assets/img/product/<?= htmlspecialchars($item['product_img']) ?>" alt="Product Image" class="img-fluid mb-2">
                              </div>
                              <h5 class="product-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                              <div class="product-price">₱<?= number_format($item['cost'], 2) ?> PHP</div>
                          </div>
                      </div>
                  <?php endforeach; ?>
              <?php endif; ?>
          </div>
      <?php endif; ?>



    </section>




  </div>

  

</body>
<script src="../../function/script/select-size.js"></script>

<script src="../../function/script/chatbot_questionslide.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>