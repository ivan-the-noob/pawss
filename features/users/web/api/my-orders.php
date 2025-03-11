<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/my-order.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places">
        
    </script>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

</head>

<?php
session_start();
$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
include '../../../../db.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];  // Get the email from session
    
    // Prepare and execute the query to fetch orders where from_cart = 1 and match the email
    $sql = "SELECT * FROM checkout WHERE from_cart = 1 AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);  // Bind the email parameter
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all orders as an associative array
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // No email in session, so set orders as empty
    $orders = [];
}


?>

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
                                    <li><a class="dropdown-item" href="dashboard.php">Profile</a></li>
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
                            </div>
                            </div>


                        <?php else: ?>
                            <a href="features/users/web/api/login.php" class="btn-theme" type="button">Login</a>
                        <?php endif; ?>
                    </div>

        </nav>


<body>
  <div class="container mt-4">
    <div class="row">
      <h5>My Orders</h5>

      <div class="order-button d-flex gap-1 mt-4">
        <button class="button-highlight" onclick="showSection('orders')">Orders</button>
        <button onclick="showSection('to-ship')">To Ship</button>
        <button onclick="showSection('to-receive')">To Receive</button>
        <button onclick="showSection('received-orders')">Received Orders</button>
        <button onclick="showSection('cancelled-orders')">Cancelled Orders</button>
      </div>
    </div>
    <?php
    // Get the user's email from the session
    $email = $_SESSION['email'];

    // Orders query
    $sql = "SELECT * FROM checkout WHERE status = 'orders' AND email = '$email'";
    $result = $conn->query($sql);

    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    // To Ship query
    $sql_to_ship = "SELECT * FROM checkout WHERE status = 'to-ship' AND email = '$email'";
    $result_to_ship = $conn->query($sql_to_ship);

    $to_ship_orders = [];
    if ($result_to_ship->num_rows > 0) {
        while ($row = $result_to_ship->fetch_assoc()) {
            $to_ship_orders[] = $row;
        }
    }

    // To Receive query
    $sql_to_receive = "SELECT * FROM checkout WHERE status = 'to-receive' AND email = '$email'";
    $result_to_receive = $conn->query($sql_to_receive);

    $to_receive_orders = [];
    if ($result_to_receive->num_rows > 0) {
        while ($row = $result_to_receive->fetch_assoc()) {
            $to_receive_orders[] = $row;
        }
    }

    // Completed query
    $sql_completed = "SELECT * FROM checkout WHERE status = 'received-order' AND email = '$email'";
    $result_completed = $conn->query($sql_completed);

    $completed_orders = [];
    if ($result_completed->num_rows > 0) {
        while ($row = $result_completed->fetch_assoc()) {
            $completed_orders[] = $row;
        }
    }

    // Cancelled query
    $sql_cancelled = "SELECT * FROM checkout WHERE status = 'cancel' AND email = '$email'";
    $result_cancelled = $conn->query($sql_cancelled);

    $cancelled_orders = [];
    if ($result_cancelled->num_rows > 0) {
        while ($row = $result_cancelled->fetch_assoc()) {
            $cancelled_orders[] = $row;
        }
    }
?>


<div class="orders">
    <?php if (!empty($orders)): ?> 
        <div class="card p-3 mt-4">
            <div class="d-flex gap-1 mb-3 justify-content-end">
                <p class="p-2 pending">Pending</p>
                <button type="button" class="update" data-bs-toggle="modal" data-bs-target="#updateModal">
                Update
                </button>
                <button class="cancel" data-id="<?php echo $orders[0]['id']; ?>">Cancel</button>
            </div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <?php
                    $totalSubTotal = 0; 
                    $totalShippingFee = 0;
                    foreach ($orders as $order): 
                        $totalSubTotal += $order['cost'] * $order['quantity'];
                        $totalShippingFee = $order['shipping_fee']; 
                    ?>
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <img src="../../../../assets/img/product/<?php echo htmlspecialchars($order['product_img']); ?>" alt="Product Image" class="img-fluid" style="border-radius: 10px;" />
                            </div>
                            <div class="col-md-7">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                                <p class="mb-0">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                            </div>
                            <div class="col-md-3 text-end">
                            <p class="mb-1">Subtotal: <span class="price">₱<?php echo number_format($order['cost'] * $order['quantity'], 2); ?></span></p>

                            </div>
                        </div>
                        <hr>
                        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel">Update Orders</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Display Orders as Cards -->
                                    <div class="row">
                                    <?php foreach ($orders as $order): ?>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Order ID: <?= $order['id'] ?></h5>
                                                    <p class="card-text">Product: <?= $order['product_name'] ?></p>
                                                    <p class="card-text">Current Quantity: <?= $order['quantity'] ?></p>
                                                    <!-- Quantity Input -->
                                                    <form action="../../function/php/update_checkout.php" method="post">
                                                        <input type="hidden" name="id" value="<?= $order['id'] ?>"> <!-- Use 'id' as the hidden field -->
                                                        <div class="form-group">
                                                            <label for="quantity_<?= $order['id'] ?>">Update Quantity</label>
                                                            <input type="number" class="form-control" id="quantity_<?= $order['id'] ?>" name="quantity" value="<?= $order['quantity'] ?>" min="1" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                                </div>
                            </div>
                            </div>
                    <?php endforeach; ?>
                    <p class="total-row mb-1 d-flex justify-content-end">Shipping Fee: <span class="price">₱<?php echo number_format($totalShippingFee, 2); ?></span></p>
                    <p class="total-row d-flex justify-content-end">Total: <span class="price">₱<?php echo number_format($totalSubTotal + $totalShippingFee, 2); ?></span></p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>No items in the cart for this email.</p>
    <?php endif; ?>   
</div>







<script>
 document.querySelectorAll('.cancel').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.getAttribute('data-id'); 

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../../function/php/update_order_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
    if (xhr.status == 200) {
        console.log(xhr.responseText); 
        showSection('cancelled-orders'); 
    } else {
        console.log('Error:', xhr.statusText);
    }
};

        xhr.send('id=' + orderId + '&status=Cancel'); 
    });
});

</script>


<div class="to-ship">
<?php if (!empty($to_ship_orders)): ?>
    <div class="card p-3 mt-4">
        <div class="d-flex gap-1 mb-3 justify-content-end">
            <p class="p-2 to-ship-w">To Ship</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-12">
                <?php
                $totalToShipSubTotal = 0;
                $totalToShipShippingFee = 0;
                foreach ($to_ship_orders as $order):
                    $totalToShipSubTotal += $order['cost']; 
                    $totalToShipShippingFee = $order['shipping_fee']; 
                ?>
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/<?php echo htmlspecialchars($order['product_img']); ?>" alt="Product Image" class="img-fluid" style="border-radius: 10px;" />
                        </div>
                        <div class="col-md-7">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="mb-0">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <p class="mb-1">Subtotal: <span class="price">₱<?php echo number_format($order['cost'], 2); ?></span></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <p class="total-row mb-1 d-flex justify-content-end">Shipping Fee: <span class="price">₱<?php echo number_format($totalToShipShippingFee, 2); ?></span></p>
                <p class="total-row d-flex justify-content-end">Total: <span class="price">₱<?php echo number_format($totalToShipSubTotal + $totalToShipShippingFee, 2); ?></span></p>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No to-ship orders available.</p>
<?php endif; ?>
</div>

<div class="to-receive">
<?php if (!empty($to_receive_orders)): ?>
    <div class="card p-3 mt-4">
        <div class="d-flex gap-1 mb-3 justify-content-end">
            <p class="p-2 to-receive-w">To Receive</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-12">
                <?php
                $totalToReceiveSubTotal = 0;
                $totalToReceiveShippingFee = 0;
                foreach ($to_receive_orders as $order):
                    $totalToReceiveSubTotal += $order['cost'];
                    $totalToReceiveShippingFee = $order['shipping_fee']; 
                ?>
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/<?php echo htmlspecialchars($order['product_img']); ?>" alt="Product Image" class="img-fluid" style="border-radius: 10px;" />
                        </div>
                        <div class="col-md-7">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="mb-0">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <p class="mb-1">Subtotal: <span class="price">₱<?php echo number_format($order['cost'], 2); ?></span></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <p class="total-row mb-1 d-flex justify-content-end">Shipping Fee: <span class="price">₱<?php echo number_format($totalToReceiveShippingFee, 2); ?></span></p>
                <p class="total-row d-flex justify-content-end">Total: <span class="price">₱<?php echo number_format($totalToReceiveSubTotal + $totalToReceiveShippingFee, 2); ?></span></p>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No to-receive orders available.</p>
<?php endif; ?>
</div>

<div class="received-orders">
<?php if (!empty($completed_orders)): ?>
    <div class="card p-3 mt-4">
        <div class="d-flex gap-1 mb-3 justify-content-end">
            <p class="p-2 completed-orders">Completed Orders</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-12">
                <?php
                $totalCompletedSubTotal = 0;
                $totalCompletedShippingFee = 0;
                foreach ($completed_orders as $order):
                    $totalCompletedSubTotal += $order['cost']; 
                    $totalCompletedShippingFee = $order['shipping_fee']; 
                ?>
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/<?php echo htmlspecialchars($order['product_img']); ?>" alt="Product Image" class="img-fluid" style="border-radius: 10px;" />
                        </div>
                        <div class="col-md-7">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="mb-0">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <p class="mb-1">Subtotal: <span class="price">₱<?php echo number_format($order['cost'], 2); ?></span></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <p class="total-row mb-1 d-flex justify-content-end">Shipping Fee: <span class="price">₱<?php echo number_format($totalCompletedShippingFee, 2); ?></span></p>
                <p class="total-row d-flex justify-content-end">Total: <span class="price">₱<?php echo number_format($totalCompletedSubTotal + $totalCompletedShippingFee, 2); ?></span></p>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No completed orders available.</p>
<?php endif; ?>
</div>


<div class="cancelled-orders">
<?php if (!empty($cancelled_orders)): ?>
    <div class="card p-3 mt-4">
        <div class="d-flex gap-1 mb-3 justify-content-end">
            <p class="p-2 cancelled-order">Cancelled Orders</p>
        </div>
        <div class="row align-items-center">
            <div class="col-md-12">
                <?php
                $totalCancelledSubTotal = 0;
                $totalCancelledShippingFee = 0;
                foreach ($cancelled_orders as $order):
                    $totalCancelledSubTotal += $order['cost']; 
                    $totalCancelledShippingFee = $order['shipping_fee'];
                ?>
                    <div class="row mb-2">
                        <div class="col-md-2">
                            <img src="../../../../assets/img/product/<?php echo htmlspecialchars($order['product_img']); ?>" alt="Product Image" class="img-fluid" style="border-radius: 10px;" />
                        </div>
                        <div class="col-md-7">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                            <p class="mb-0">Quantity: <?php echo htmlspecialchars($order['quantity']); ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <p class="mb-1">Subtotal: <span class="price">₱<?php echo number_format($order['cost'], 2); ?></span></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <p class="total-row mb-1 d-flex justify-content-end">Shipping Fee: <span class="price">₱<?php echo number_format($totalCancelledShippingFee, 2); ?></span></p>
                <p class="total-row d-flex justify-content-end">Total: <span class="price">₱<?php echo number_format($totalCancelledSubTotal + $totalCancelledShippingFee, 2); ?></span></p>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No cancelled orders available.</p>
<?php endif; ?>
</div>


  </div>

  <script>
 
 document.addEventListener("DOMContentLoaded", function () {
        showSection('orders');

        document.querySelectorAll('.order-button button').forEach(button => {
            button.addEventListener('click', function (event) {
                const section = this.textContent.trim().toLowerCase().replace(' ', '-');
                showSection(section, event);
            });
        });
    });

    function showSection(section, event = null) {
    document.querySelectorAll('.order-button button').forEach(button => {
        button.classList.remove('button-highlight');
        if (button.textContent.trim().toLowerCase().replace(' ', '-') === section) {
            button.classList.add('button-highlight');
        }
    });

    document.querySelectorAll('.orders, .to-ship, .to-receive, .received-orders, .cancelled-orders').forEach(div => {
        div.style.display = 'none';
    });

    document.querySelector(`.${section}`).style.display = 'block';
}
  </script>
</body>
<!--Header End-->


<script src=" https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js">
</script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>

</html>