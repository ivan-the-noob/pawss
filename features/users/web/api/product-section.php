<?php

session_start();
if (isset($_SESSION['email']) && isset($_SESSION['profile_picture'])) {
    $email = $_SESSION['email'];
    $profile_picture = $_SESSION['profile_picture'];
} else {
    header("Location: features/users/web/api/login.php");
    exit();
}

require '../../../../db.php';

// Pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 1; // Define how many items you want to show per page

// Query to get the total number of products
$sql = "SELECT COUNT(*) AS total FROM product";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalItems = $row['total'];

// Calculate total number of pages
$totalPages = ceil($totalItems / $itemsPerPage);

// Make sure the page number is within the valid range
$page = max(1, min($page, $totalPages));

// Calculate the starting product for the current page
$start = ($page - 1) * $itemsPerPage;

// Get the products for the current page
$sql = "SELECT * FROM product LIMIT $start, $itemsPerPage";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>
  <link rel="stylesheet" href="../../css/products.css">

</head>

<body>
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">Products</a>
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
                                    <li><a class="dropdown-item" href="features/users/function/authentication/logout.php">Logout</a></li>
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
                                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                        <?php
                                        include '../../../../db.php'; 

                                        $query = "SELECT message FROM notification ORDER BY id DESC";
                                        $result = $conn->query($query);

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $message = $row['message'];

                                                $classes = 'dropdown-item bg-white shadow-sm px-3 py-2 rounded';
                                                $style = 'box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);';

                                                if (trim($message) == "Your appointment has been approved!") {
                                                    $classes .= ' text-success mx-auto';
                                                } else if (trim($message) == "Your checkout has been approved") {
                                                    $classes .= ' text-success mx-auto';
                                                } else if (trim($message) == "Your item has been picked up by courier. Please ready payment for COD.") {
                                                    $classes .= ' text-info mx-auto';
                                                } else if (trim($message) == "Your profile info has been updated.") {
                                                    $classes .= ' text-info mx-auto';
                                                } else if (trim($message) == "New services offered! Check it now!") {
                                                    $classes .= ' text-success mx-auto';
                                                } else if (trim($message) == "New product has been arrived! Check it now!") {
                                                    $classes .= ' text-success mx-auto';
                                                }

                                                echo "<li><a class=\"$classes\" href=\"#\" style=\"$style\">$message</a></li>";
                                                echo "<li><hr class=\"dropdown-divider\"></li>";
                                            }
                                        } else {
                                            echo "<li><a class=\"dropdown-item bg-white shadow-sm\" href=\"#\">No notifications</a></li>";
                                        }

                                        $conn->close();
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

    <section class="essentials py-5">
    <div class="d-flex">
        <div class="how-headings col-8 text-center mt-4">
            <p class="mb-0">Explore pet care</p>
            <h2 class="mb-4">Essentials</h2>
        </div>
        <div class="col-md-4 d-flex align-items-center">
        <form method="GET" class="w-100">
                <input type="search" name="search" id="search-product" class="search-product" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="product-button">Search</button>
            </form>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-start justify-content-center">
            <div class="col-lg-3 col-md-4 col-12 mb-3">
                <div class="essentials-button d-flex flex-column align-items-start">
                    <button onclick="filterProducts('petfood')">Pet Food</button>
                    <button onclick="filterProducts('pettoys')">Pet Toys</button>
                    <button onclick="filterProducts('supplements')">Supplements</button>
                </div>
            </div>

            <div class="col-lg-9 col-md-8 col-12">
                <div class="row" id="product-list">
                <?php
                    require '../../../../db.php';

                    // Pagination and search parameters
                    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $itemsPerPage = 6;

                    // Search condition
                    $searchCondition = $searchQuery ? "WHERE product_name LIKE ?" : "";

                    // Get total items for pagination
                    $sqlTotal = "SELECT COUNT(*) AS total FROM product $searchCondition";
                    $stmtTotal = $conn->prepare($sqlTotal);
                    if ($searchQuery) {
                        $stmtTotal->bind_param("s", $searchLike);
                        $searchLike = "%$searchQuery%";
                    }
                    $stmtTotal->execute();
                    $resultTotal = $stmtTotal->get_result();
                    $totalItems = $resultTotal->fetch_assoc()['total'];

                    $totalPages = ceil($totalItems / $itemsPerPage);
                    $page = max(1, min($page, $totalPages));
                    $start = ($page - 1) * $itemsPerPage;

                    // Get paginated and filtered products
                    $sql = "SELECT * FROM product $searchCondition LIMIT ?, ?";
                    $stmt = $conn->prepare($sql);
                    if ($searchQuery) {
                        $stmt->bind_param("sii", $searchLike, $start, $itemsPerPage);
                    } else {
                        $stmt->bind_param("ii", $start, $itemsPerPage);
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while ($product = $result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-4 product-item" data-type="<?= strtolower($product['type']) ?>">
                                <div class="product">
                                    <div class="product-itemss" style="height: 36vh;">
                                        <img src="../../../../assets/img/product/<?= $product['product_img'] ?>" alt="Product Image">
                                        <h5 class="mt-4 mb-0 product_name"><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <p class="mt-0 mb-0 product_name"><?= htmlspecialchars($product['quantity']) ?>x</p>
                                    </div>
                                    <div class="d-flex prices">
                                        <p class="tag align-items-center mb-0 d-flex">PHP</p>
                                        <p class="price mb-0"><?= htmlspecialchars(number_format($product['cost'], 2)) ?></p>
                                    </div>
                                    <?php if ($product['quantity'] > 0): ?>
                                    <div class="d-flex justify-content-between item-btn">
                                        <a href="../../../../features/users/web/api/buy-now.php?id=<?= $product['id'] ?>&type=<?= htmlspecialchars($product['type']) ?>" class="btn buy-now">BUY NOW!</a>
                                        <a href="../../../../features/users/web/api/buy-now.php?id=<?= $product['id'] ?>&type=<?= htmlspecialchars($product['type']) ?>&triggerModal=true" class="btn add-to-cart">
                                            <span class="material-symbols-outlined">shopping_cart</span>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <button class="buy-now">Out Of Stock!</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; 
                    else: ?>
                        <p>No products found matching your search criteria.</p>
                    <?php endif; ?>
                </div>

                <!-- Pagination Links -->
                <div class="pagination d-flex justify-content-end mt-4">
                    <nav>
                        <ul class="pagination d-flex gap-2">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link prev" href="?page=<?= $page - 1 ?>"><</a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 1);
                            $endPage = min($totalPages, $startPage + 2);
                            $startPage = max(1, $endPage - 2);

                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link next" href="?page=<?= $page + 1 ?>">></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
   

      <!--Chat Bot-->
      <button id="chat-bot-button" onclick="toggleChat()">
        <i class="fa-solid fa-headset"></i>
    </button>

    <div id="chat-interface" class="hidden">
    <div id="chat-header">
        <p>Amazing Day! How may I help you?</p>
        <button onclick="toggleChat()">X</button>
    </div>
    <div id="chat-body">
    <div class="button-bot">
            <button onclick="sendResponse('How to log in?')">How to log in?</button>
            <button onclick="sendResponse('How to book?')">How to book?</button>
            <button onclick="sendResponse('What are the services?')">What are the services?</button>
            <button onclick="sendResponse('Contact information?')">Contact information?</button>
        </div>
        
        <div class="admin mt-3">
            <div class="admin-chat">
                <img src="../../../../assets/img/logo.png" alt="Admin">
                <p>Admin</p>
            </div>
            <p class="text" id="typing-text">Hello, I am Chat Bot. Please Ask me a question just by pressing the question buttons.</p>
        </div>
      
    </div>
    <div class="line"></div>
</div>
    <!--Chat Bot End-->



</body>
<script src="../../function/script/chat-bot_product.js"></script>
<script src="../../function/script/chatbot_questionslide.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="../../function/script/filter.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</html>