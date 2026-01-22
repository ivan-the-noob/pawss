<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CART | DIGITAL PAWS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/cart.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">
    
    <style>
        /* Added for loading state */
        button.loading {
            position: relative;
        }
        
        button.loading .spinner-border {
            margin-right: 8px;
        }
        
        #checkoutButton:disabled {
            cursor: not-allowed !important;
            opacity: 0.5 !important;
        }
        
        #modalCheckoutButton:disabled {
            cursor: not-allowed !important;
            opacity: 0.5 !important;
        }
    </style>
  
</head>

<?php
session_start();
$email = isset($_SESSION['email']);
include '../../../../db.php';

$cart_items = [];

$sql = "SELECT * FROM product"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "No product found.";
}

// Check if the user is logged in (only store email in session)
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    header("Location: ../../web/api/login.php");
    exit();
}
if ($email) {
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $profile_picture = $row['profile_picture'] ?? 'default.png';
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

  

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $query = "SELECT * FROM cart WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
        }
    } else {
        $cart_items = null; 
    }

    $stmt->close();
    $conn->close();
} else {
    $cart_items = null;
}




?>

<div class="navbar-container">
<nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
            <a class="navbar-brand d-none d-lg-block" href="../../../../index.php">
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
                    
                    <div class="d-flex ml-auto">
                        <?php if ($email): ?>
                            <!-- Profile Dropdown -->
                           
                          <?php 
                            require '../../../../db.php';
                            include '../../function/php/count_cart.php';
                            
                          ?>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                       <a href="../../../../index.php" class="text-decoration-none">Home</a>
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
                                    <ul class="dropdown-menu dropdown-menu-end"  style="height: 400px; overflow-y: auto;">
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
                                 <div class="dropdown second-dropdown">
                                <button class="btn" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                 <img src="../../../../assets/img/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Image" class="profile">
                                </button>
                                <ul class="dropdown-menu custom-center-dropdown" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="dropdown-item" href="dashboard.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                            </div>
                            </div>


                        <?php else: ?>
                            <a href="login.php" class="btn-theme" type="button">Login</a>
                        <?php endif; ?>
                    </div>

        </nav>
    </div>

<style>
    input[type="checkbox"] {
            width: 20px;
            height: 20px;
            border: 2px solid #7A3015;
            background-color: #fff;
            cursor: pointer;
            appearance: none; 
            transition: background-color 0.3s ease, border-color 0.3s ease;
            display: flex;
            margin: auto;
        }

        input[type="checkbox"]:checked {
            background-color: #7A3015; 
            border-color: #7A3015;    
        }

        input[type="checkbox"]:focus {
            outline: 2px solid #7A3015;
        }

        
</style>
<body>
  <div class="container mt-4">
      <h5>My Cart</h5>
        
                    
      <?php if ($cart_items !== null && count($cart_items) > 0): ?>
    <div class="row">
        <!-- Cart items loop on the left side -->
        <div class="col-md-6" style="height: 90vh; overflow-y: auto;">
            <?php foreach ($cart_items as $item): ?>
                <div class="d-flex gap-2 cart-item">
                    <input type="checkbox" class="move-to-card-box" data-item="<?php echo htmlspecialchars(json_encode($item)); ?>">
                    <div class="card p-3 mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="../../../../assets/img/product/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         style="width: 50%; height: 10vh; display: flex; margin: auto; border-radius: 3px;" 
                                         alt="Product Image" name="product_image">
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1" style="font-size: 14px;">Product</p>
                                    <h5 class="card-title mb-1" name="product_name"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <p class="mb-1" name="quantity">Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                </div>

                                <hr>
                                <div class="d-flex justify-content-end">
                                    <p class="fw-bold" name="total_price">Price: <span class="price">₱<?php echo number_format($item['total_price'], 2); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Cart container on the right side -->
        <div class="col-md-6">
            <div class="cart-container">
                <div class="card-box" id="card-box">
                    <p class="p-3">Check Out Now</p>
                </div>
                <div class="d-flex gap-1">
                    <div class="check-out mt-2 w-100">
                        <button id="deleteSelectedButton" class="btn btn-danger" style="background-color: red;">Delete </button>
                    </div>
                    <div class="check-out mt-2 w-100">
                        <button id="checkoutButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No products in your cart.</p>
<?php endif; ?>
</div>

<!-- Add to Cart Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="../../function/php/checkout_post.php" enctype="multipart/form-data" id="checkoutForm">
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title">Customer Information</p>
                                    <div class="context">
                                        <p class="mb-0" name="contact-num"><?php echo $name; ?></p>
                                        <p class="mb-0" name="contact-num"><?php echo $contactNumber; ?></p>
                                        <p class="mb-0" name="address-search"><?php echo $addressSearch; ?></p>
                                    </div>
                                    <input type="hidden" name="name" value="<?php echo $name; ?>">
                                    <input type="hidden" name="contact-num" value="<?php echo $contactNumber; ?>">
                                    <input type="hidden" name="address-search" value="<?php echo $addressSearch; ?>">
                                  
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Card -->
                        <div class="col-md-6 mb-3">
                            <div class="card payment-method">
                                <div class="card-body">
                                <h5 class="card-title">Payment Method</h5>
                                    <div class="form-check d-flex justify-content-between align-items-center mb-3 form-payment">
                                        <div>
                                            <input type="radio" id="payment-cash" name="paymentMethod" value="cash" class="form-check-input" required checked>
                                            <label for="payment-cash" class="form-check-label">Cash on delivery</label>
                                        </div>
                                        <span class="cod">COD</span>
                                    </div>

                                    <div class="form-check d-flex justify-content-between align-items-center mb-3 form-payment">
                                        <div>
                                            <input type="radio" id="payment-gcash" name="paymentMethod" value="gcash" class="form-check-input" required>
                                            <label for="payment-gcash" class="form-check-label">Gcash</label>
                                        </div>
                                    </div>


                                    <div id="gcash-details" style="display: none;">
                                        <img src="../../../../assets/img/gcash.jfif" alt="Gcash" class="img-fluid mb-3 gcash">
                                        <input type="file" name="screenshot" id="gcash-image" class="form-control m">
                                        <label for="number" class="form-check-label mt-2">Reference # </label>
                                        <input type="number" name="reference" value="" class="form-control mt-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Card -->
                        <div class="col-md-6 mb-3">
                            <div class="card" id="product-card"></div>
                            <input type="hidden" name="product_name[]" id="product_name" value="">
                            <input type="hidden" name="quantity[]" id="quantity" value="">
                            <input type="hidden" name="sub_total[]" id="sub-total" value="">
                            <input type="hidden" name="product_img[]" id="product_img" value="">
                            <input type="hidden" name="cost[]" id="cost" value="">
                            
                        </div>

                        <!-- Order Summary Card -->
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Order Summary</h5>
                                    <!-- Subtotal Section -->
                                    <div class="d-flex justify-content-between">
                                        <p>Total:</p>
                                        <p class="mb-0 d-flex justify-content-center mt-2" id="total-cost-2" name="sub-total">
                                            ₱0.00
                                        </p>
                                        <input type="hidden" name="sub-total" value="0.00">
                                        <input type="hidden" name="from_cart" value="true">
                                    </div>

                                    <!-- Shipping Fee Section -->
                                    <div class="d-flex justify-content-between">
                                        <p>Shipping Fee:</p>
                                        <p><span id="shippingFee" name="shipping-fee">Via Lalamove</span></p>
                                    </div>

                                 
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php 
               
                
                if(!empty($addressSearch) && trim($addressSearch) !== ''): ?>
                    <button type="submit" class="btn btn-primary" id="modalCheckoutButton">Checkout</button>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-primary">Checkout</a>
                <?php endif; ?>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
     document.addEventListener("DOMContentLoaded", function () {
    // Initial values
    let subtotal = parseFloat(document.querySelector('input[name="sub-total"]').value);  // Subtotal from the hidden input
    const shippingFee = parseFloat(document.querySelector('input[name="shipping-fee"]').value);  // Shipping fee from the hidden input
    let totalAmount = subtotal + shippingFee;  // Total amount is subtotal + shipping fee

    // Display initial values
    document.getElementById('total-cost-2').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('shippingFee').textContent = shippingFee.toFixed(2);
    document.getElementById('totalAmount').textContent = `₱${totalAmount.toFixed(2)}`;

    // Function to update total amount
    function updateTotalAmount() {
        totalAmount = subtotal + shippingFee;  // Recalculate total
        document.getElementById('totalAmount').textContent = `₱${totalAmount.toFixed(2)}`;  // Update displayed total amount
        document.querySelector('input[name="total-amount"]').value = totalAmount.toFixed(2);  // Update hidden input for total amount
    }

    // Add event listener to update subtotal dynamically (example: when a checkbox is checked/unchecked)
    // Replace this with your actual event handling for dynamically adding/removing products.
    document.querySelectorAll('.move-to-card-box').forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const itemData = JSON.parse(this.dataset.item);  // Get item data from the checkbox

            if (this.checked) {
                subtotal += parseFloat(itemData.total_price);  // Add price of the selected item to subtotal
            } else {
                subtotal -= parseFloat(itemData.total_price);  // Subtract price of the removed item from subtotal
            }

            document.querySelector('input[name="sub-total"]').value = subtotal.toFixed(2);  // Update hidden input
            document.getElementById('total-cost-2').textContent = `₱${subtotal.toFixed(2)}`;  // Update displayed subtotal

            updateTotalAmount();  // Recalculate and update total amount
        });
    });
});

</script>
<script>
    const unitCost = <?php echo $product['cost']; ?>; // Get product cost from PHP

    const quantityInput = document.getElementById("quantity");
    const incrementBtn = document.getElementById("increment-btn"); // Increment button
    const decrementBtn = document.getElementById("decrement-btn"); // Decrement button

    // Get the shipping fee element and its value
    const shippingFeeElement = document.getElementById("shippingFee");
    const shippingFee = parseFloat(shippingFeeElement.textContent); // Assuming the shipping fee is in plain text, like "69.00"

    // Function to update the total cost (Subtotal)
    function updateTotalCost(quantity, totalCostElement) {
        const totalCost = (unitCost * quantity).toFixed(2); // Calculate total cost based on quantity
        totalCostElement.textContent = `₱${totalCost} PHP`; // Update total cost
        return totalCost; // Return the calculated total cost for further calculations
    }

    // Function to update the total amount (Subtotal + Shipping Fee)
    function updateTotalAmount() {
        // Get the updated subtotal from total-cost-2
        const subtotal = parseFloat(document.getElementById("total-cost-2").textContent.replace("₱", "").trim());

        // Calculate the total (Subtotal + Shipping Fee)
        const totalAmount = (subtotal + shippingFee).toFixed(2); // Sum of subtotal and shipping fee

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

        // Update the hidden input in the modal
        document.querySelector('input[name="quantity"]').value = value;
    }

    // Increment the quantity and update displays
    incrementBtn.addEventListener("click", () => {
        const newValue = parseInt(quantityInput.value) + 1; // Increment the value by 1
        syncQuantity(newValue);
    });

    // Decrement the quantity and update displays
    decrementBtn.addEventListener("click", () => {
        const newValue = parseInt(quantityInput.value) - 1; // Decrement the value by 1
        if (newValue > 0) { // Ensure the quantity doesn't go below 1
            syncQuantity(newValue);
        }
    });

    // Initialize default quantity
    syncQuantity(1);
</script>


<script>
  function showCartModal() {
    const productName = document.querySelector('#main-product-name').innerText;
    const productPrice = parseFloat(document.querySelector('#main-product-price').innerText.replace('₱', '').replace(' PHP', ''));
    const quantity = parseInt(document.querySelector('#quantity').value);
    const totalPrice = (productPrice * quantity).toFixed(2);
    
    const productId = document.querySelector('#main-product-id').value; 

    const productImageUrl = document.querySelector('#main-product-img').src;
    
    const productImageName = productImageUrl.substring(productImageUrl.lastIndexOf('/') + 1);

    // Populating the modal with the product details
    document.querySelector('#productImageModal').src = productImageUrl;
    document.querySelector('#modalNameProduct').innerText = productName;
    document.querySelector('#modalProductPrice').innerText = `₱${productPrice.toFixed(2)}`;
    document.querySelector('#modalProductQuantity').innerText = `Quantity: ${quantity}`;
    document.querySelector('#modalTotalPrice').innerText = totalPrice;

    // Setting hidden inputs to send data with the form
    document.querySelector('#productId').value = productId;
    document.querySelector('#productName').value = productName;
    document.querySelector('#productPrice').value = productPrice.toFixed(2);
    document.querySelector('#productQuantity').value = quantity;
    document.querySelector('#totalPrice').value = totalPrice;
    document.querySelector('#productImage').value = productImageName; 
  }
</script>


      
<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll(".move-to-card-box");
    const cardBox = document.getElementById("card-box");
    const productCard = document.getElementById("product-card");
    const subtotalDisplay = document.getElementById("total-cost-2");
    const subtotalInput = document.querySelector('input[name="sub-total"]');
    const deleteSelectedButton = document.getElementById("deleteSelectedButton");

    let subtotal = 0;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const itemData = JSON.parse(this.dataset.item);
            if (this.checked) {
                addItemToCard(itemData);
                subtotal += parseFloat(itemData.total_price);
                updateSubtotal();
                updateHiddenInputs(itemData, "add");
            } else {
                removeItemFromCard(itemData);
                subtotal -= parseFloat(itemData.total_price);
                updateSubtotal();
                updateHiddenInputs(itemData, "remove");
            }
        });
    });

    deleteSelectedButton.addEventListener("click", function () {
        let selectedItems = [];

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const itemData = JSON.parse(checkbox.dataset.item);
                selectedItems.push(itemData.id);
            }
        });

        if (selectedItems.length > 0) {
            // Call PHP to delete from DB
            fetch('../../function/php/delete_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: selectedItems })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect changes after deletion
                    location.reload();
                } else {
                    alert("Failed to delete item(s) from cart.");
                    console.error(data.message);
                }
            })
            .catch(error => {
                console.error("Delete error:", error);
            });
        } else {
            alert("Please select at least one item to delete.");
        }
    });

    function addItemToCard(itemData) {
        const card = document.createElement("div");
        card.classList.add("card", "p-3", "mt-2", "card-contents");
        card.dataset.itemId = itemData.id;
        card.innerHTML = `
            <div class="card-body" style="padding: 0;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/${itemData.product_image}" 
                                style="width: 30px; height: 30px; display: flex; margin: auto; border-radius: 3px;" 
                                alt="Product Image">
                        </div>
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-1">${itemData.product_name}</h5>
                            <p class="mb-1">${itemData.quantity}x</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <p class="fw-bold">Price: <span class="price">₱${parseFloat(itemData.total_price).toFixed(2)}</span></p>
                    </div>
                </div>
                <hr>
            </div>
        `;
        cardBox.appendChild(card);
        createProductCard(itemData);
    }

    function removeItemFromCard(itemData) {
        const cardToRemove = cardBox.querySelector(`div[data-item-id="${itemData.id}"]`);
        if (cardToRemove) cardBox.removeChild(cardToRemove);
        const productCardToRemove = productCard.querySelector(`div[data-item-id="${itemData.id}"]`);
        if (productCardToRemove) productCard.removeChild(productCardToRemove);
    }

    function createProductCard(itemData) {
        const card = document.createElement("div");
        card.classList.add("card", "p-3", "mt-2", "product-card-item");
        card.dataset.itemId = itemData.id;
        card.innerHTML = `
            <div class="card-body" style="padding: 0;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/${itemData.product_image}" 
                                style="width: 30px; height: 30px; display: flex; margin: auto; border-radius: 3px;" 
                                alt="Product Image">
                        </div>
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title mb-1">${itemData.product_name}</h5>
                            <p class="mb-1">${itemData.quantity}x</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <p class="fw-bold">Price: <span class="price">₱${parseFloat(itemData.total_price).toFixed(2)}</span></p>
                    </div>
                </div>
                <hr>
            </div>
        `;
        productCard.appendChild(card);
    }

    function updateSubtotal() {
        subtotalDisplay.textContent = `₱${subtotal.toFixed(2)}`;
        subtotalInput.value = subtotal.toFixed(2);
    }

    function updateHiddenInputs(itemData, action) {
        const productNames = document.querySelector('input[name="product_name[]"]');
        const productImages = document.querySelector('input[name="product_img[]"]');
        const quantities = document.querySelector('input[name="quantity[]"]');
        const costs = document.querySelector('input[name="cost[]"]');
        const updateField = (field, val) => {
            let arr = JSON.parse(field.value || "[]");
            if (action === "add") arr.push(val);
            else arr = arr.filter(v => v !== val);
            field.value = JSON.stringify(arr);
        };
        if (productNames) updateField(productNames, itemData.product_name);
        if (productImages) updateField(productImages, itemData.product_image);
        if (quantities) updateField(quantities, itemData.quantity);
        if (costs) updateField(costs, itemData.total_price);
    }
});
</script>



      

<!-- Displaying Orders -->
        

</body>
<!--Header End-->

<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>

<script>
// ============ 1. DISABLE CHECKOUT BUTTON WHEN NO ITEMS SELECTED ============
document.addEventListener("DOMContentLoaded", function () {
    // Disable checkout button initially
    const checkoutButton = document.getElementById('checkoutButton');
    if (checkoutButton) {
        checkoutButton.disabled = true;
        checkoutButton.style.opacity = '0.5';
        checkoutButton.style.cursor = 'not-allowed';
    }

    // Function to update checkout button state
    function updateCheckoutButton() {
        const checkboxes = document.querySelectorAll('.move-to-card-box:checked');
        const checkoutButton = document.getElementById('checkoutButton');
        
        if (!checkoutButton) return;
        
        if (checkboxes.length > 0) {
            checkoutButton.disabled = false;
            checkoutButton.style.opacity = '1';
            checkoutButton.style.cursor = 'pointer';
        } else {
            checkoutButton.disabled = true;
            checkoutButton.style.opacity = '0.5';
            checkoutButton.style.cursor = 'not-allowed';
        }
    }

    // Add event listener to all checkboxes
    document.querySelectorAll('.move-to-card-box').forEach(checkbox => {
        checkbox.addEventListener('change', updateCheckoutButton);
    });

    // Initialize on page load
    updateCheckoutButton();

    // ============ 2. GCASH TOGGLE AND REQUIRED FIELDS ============
    const gcashRadio = document.getElementById('payment-gcash');
    const cashRadio = document.getElementById('payment-cash');
    const gcashDetails = document.getElementById('gcash-details');
    const gcashImageInput = document.getElementById('gcash-image');
    const referenceInput = document.querySelector('input[name="reference"]');

    // Show/hide GCash details based on selection
    if (gcashRadio && cashRadio && gcashDetails) {
        gcashRadio.addEventListener('change', function() {
            if (this.checked) {
                gcashDetails.style.display = 'block';
                // Make fields required
                if (gcashImageInput) gcashImageInput.required = true;
                if (referenceInput) referenceInput.required = true;
            }
        });

        cashRadio.addEventListener('change', function() {
            if (this.checked) {
                gcashDetails.style.display = 'none';
                // Remove required attribute
                if (gcashImageInput) gcashImageInput.required = false;
                if (referenceInput) referenceInput.required = false;
            }
        });
    }

    // ============ 3. LOADING STATE FOR CHECKOUT ============
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const closeButton = this.querySelector('button[data-bs-dismiss="modal"]');
            
            if (submitButton) {
                // Check if already in loading state
                if (submitButton.classList.contains('loading')) {
                    e.preventDefault();
                    return;
                }
                
                // Validate GCash fields if GCash is selected
                if (gcashRadio && gcashRadio.checked) {
                    if (gcashImageInput && !gcashImageInput.files[0]) {
                        e.preventDefault();
                        alert('Please upload a GCash screenshot.');
                        return;
                    }
                    if (referenceInput && !referenceInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a reference number.');
                        return;
                    }
                }
                
                // Check if any items are selected
                const selectedCheckboxes = document.querySelectorAll('.move-to-card-box:checked');
                if (selectedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one item to checkout.');
                    return;
                }
                
                // Set loading state
                const originalText = submitButton.innerHTML;
                submitButton.classList.add('loading');
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processing...
                `;
                
                // Disable close button during processing
                if (closeButton) {
                    closeButton.disabled = true;
                }
                
                // Safety timeout to re-enable after 10 seconds
                setTimeout(() => {
                    if (submitButton && submitButton.classList.contains('loading')) {
                        submitButton.classList.remove('loading');
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                        
                        if (closeButton) {
                            closeButton.disabled = false;
                        }
                        
                        alert('Checkout is taking too long. Please try again.');
                    }
                }, 10000);
            }
        });
    }
});
</script>

</html>