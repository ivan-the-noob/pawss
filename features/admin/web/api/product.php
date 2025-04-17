<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/category-list.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <!--Navigation Links-->
    <div class="navbar flex-column bg-white shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <a class="navbar-brand d-none d-md-block logo-container" href="#">
                <img src="../../../../assets/img/logo.png" alt="Logo">
            </a>
            <a href="admin.php">
                <i class="fa-solid fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php">
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center" id="checkoutDropdowns" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="ms-2">Booking</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="checkoutDropdowns">
                    <li><a class="dropdown-item" href="app-req.php"><i class="fa-solid fa-calendar-check"></i> <span>Pending Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-waiting.php"><i class="fa-solid fa-calendar-check"></i> <span>Waiting Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-ongoing.php"><i class="fa-solid fa-calendar-check"></i> <span>On Going Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-finish.php"><i class="fa-solid fa-calendar-check"></i> <span>Finished Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-cancel.php"><i class="fa-solid fa-calendar-check"></i> <span>Cancelled Bookings</span></a></li>
                   
                </ul>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center dropdown-toggle" id="checkoutDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="ms-2">Checkout</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="checkoutDropdown">
                    <li><a class="dropdown-item" href="pending_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>Pending CheckOut</span></a></li>
                    <li><a class="dropdown-item" href="to-ship_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Ship CheckOut</span></a></li>
                    <li><a class="dropdown-item" href="to-receive.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Receive CheckOut</span></a></li>
                    <li><a class="dropdown-item" href="delivered_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>Delivered</span></a></li>
                    <li><a class="dropdown-item" href="decline.php"><i class="fa-solid fa-calendar-check"></i> <span>Declined</span></a></li>
                </ul>
            </div> 


            <div class="maintenance">
                <p class="maintenance-text">Maintenance</p>
                <a href="service-list.php">
                    <i class="fa-solid fa-list"></i>
                    <span>Service List</span>
                </a>
                <a href="product.php" class="navbar-highlight">
                    <i class="fa-solid fa-box"></i>
                    <span>Product</span>
                </a>
                <a href="admin-user.php">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Admin User List</span>
                </a>
            </div>

        </div>
    </div>
    <!--Navigation Links-->
    <div class="content flex-grow-1">
        <div class="header">
            <button class="navbar-toggler d-block d-md-none" type="button" onclick="toggleMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    style="stroke: black; fill: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
            <!--Notification and Profile Admin-->
            <div class="profile-admin">
                <div class="dropdown">
                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../../../../assets/img/vet logo.png"
                            style="width: 40px; height: 40px; object-fit: cover;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../../users/web/api/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!--Notification and Profile Admin End-->
        <div class="app-req">
            <h3>Products</h3>
            <div class="container mt-4 mb-4 " style="background-color: #fff;">
                <h3>Low Quantity</h3>
    <div class="row">
        <?php
        
        require '../../../../db.php';

        $products = $conn->query("SELECT * FROM product WHERE quantity < 4");

        if ($products->num_rows > 0):
            while ($product = $products->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                     
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <p class="card-text">
                                <strong>Type:</strong> <?= htmlspecialchars($product['type']) ?>
                            </p>
                            <p class="card-text">
                                <strong>Price:</strong> PHP <?= htmlspecialchars(number_format($product['cost'], 2)) ?>
                            </p>
                            <p class="card-text fw-bold" style="color: red;">
                                <strong>Quantity:</strong> <?= htmlspecialchars($product['quantity']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-primary" title="Update" data-bs-toggle="modal" 
                                        data-bs-target="#editProductModal<?= $product['id'] ?>">
                                    <i class="fas fa-edit"></i> Restock 
                                </button>
                                <button class="btn btn-danger" title="Delete" data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal<?= $product['id'] ?>">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-start">
                <p class="text-muted" style="padding-left: 30px">No low stocks.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
           
            <div class="walk-in px-lg-5">
                <div class="mb-3 x d-flex">
                    <div class="search">
                        <div class="search-bars">
                            <i class="fa fa-magnifying-glass"></i>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                    <button type="button" class="btn-new mb-3" data-toggle="modal" data-target="#addProductModal">
                        Add New Product
                    </button>
                </div>
            </div>
            <!--Notification and Profile Admin End-->

            <?php

                require '../../../../db.php';

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["product_img"])) {
                    $productName = $_POST['product_name'];
                    $description = $_POST['description'];
                    $cost = $_POST['cost'];
                    $type = $_POST['type'];
                    $quantity = $_POST['quantity'];

                    $targetDir = "../../../../assets/img/product/";
                    $imageName = basename($_FILES["product_img"]["name"]);
                    $targetFilePath = $targetDir . $imageName;

                    if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $targetFilePath)) {
                        $sql = "INSERT INTO product (product_img, product_name, description, cost, type, quantity) 
                                VALUES ('$imageName', '$productName', '$description', '$cost', '$type', '$quantity')";

                        if ($conn->query($sql) === TRUE) {

                            // Add notification
                            $notifMessage = "New product has been arrived! Check it now!";
                            $notifSql = "INSERT INTO notification (message) VALUES ('$notifMessage')";
                            $conn->query($notifSql);

                            echo "New product added successfully!";
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }

                $products = $conn->query("SELECT * FROM product");
            ?>


            <!--Category List Modal (add new)-->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-header d-flex justify-content-between">
                                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="product_img">Image</label>
                                    <input type="file" class="form-control-file" id="product_img" name="product_img"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="product_name">Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="2"
                                        required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="cost">Price</label>
                                    <input type="number" class="form-control" id="cost" name="cost" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="petfood">Pet Food</option>
                                        <option value="pettoys">Pet Toys</option>
                                        <option value="supplements">Supplements</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Product Table -->
            <div class="px-lg-5" style="overflow-x: auto;">
                <table class="table table-hover table-remove-borders">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Quantity</th> <!-- New Quantity Column -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($products->num_rows > 0): ?>
                                            <?php while ($product = $products->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $product['id'] ?></td>
                                                    <td>
                                                        <img src="../../../../assets/img/product/<?= htmlspecialchars($product['product_img']) ?>"
                                                            alt="Product Image" style="width: 50px; height: 50px;">
                                                    </td>
                                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                    <td><?= htmlspecialchars($product['description']) ?></td>
                                                    <td><?= htmlspecialchars($product['type']) ?></td>
                                                    <td>PHP <?= htmlspecialchars(number_format($product['cost'], 2)) ?></td>
                                                    <td><?= htmlspecialchars($product['quantity']) ?></td> <!-- Display Quantity -->
                                                    <td>
                                                        <!-- Edit Button -->
                                                        <button class="btn btn-primary" title="Update" data-bs-toggle="modal"
                                                            data-bs-target="#editProductModal<?= $product['id'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#confirmDeleteModal<?= $product['id'] ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!--Delete Modal -->
                                                <div class="modal fade" id="confirmDeleteModal<?= $product['id'] ?>" tabindex="-1"
                                                    aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete this product?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <form action="../../function/php/delete_product.php" method="POST"
                                                                    class="d-inline">
                                                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1"
                                                    aria-labelledby="editProductModalLabel<?= $product['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <form action="../../function/php/update_product.php" method="POST"
                                                                enctype="multipart/form-data">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editProductModalLabel<?= $product['id'] ?>">Edit
                                                                        Product
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                                    <div class="form-group">
                                                                        <label for="product_img">Image</label>
                                                                        <input type="file" class="form-control-file" id="product_img"
                                                                            name="product_img">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="product_name">Name</label>
                                                                        <input type="text" class="form-control" id="product_name"
                                                                            name="product_name"
                                                                            value="<?= htmlspecialchars($product['product_name']) ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="description">Description</label>
                                                                        <textarea class="form-control" id="description" name="description"
                                                                            rows="2"
                                                                            required><?= htmlspecialchars($product['description']) ?></textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="cost">Price</label>
                                                                        <input type="number" class="form-control" id="cost" name="cost"
                                                                            value="<?= htmlspecialchars($product['cost']) ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="quantity">Quantity</label>
                                                                        <!-- Add Quantity Field in Edit Modal -->
                                                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                                                            value="<?= htmlspecialchars($product['quantity']) ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="type">Type</label>
                                                                        <select class="form-control" id="type" name="type" required>
                                                                            <option value="petfood"
                                                                                <?= ($product['type'] == 'petfood') ? 'selected' : '' ?>>Pet
                                                                                Food
                                                                            </option>
                                                                            <option value="pettoys"
                                                                                <?= ($product['type'] == 'pettoys') ? 'selected' : '' ?>>Pet
                                                                                Toys
                                                                            </option>
                                                                            <option value="supplements"
                                                                                <?= ($product['type'] == 'supplements') ? 'selected' : '' ?>>
                                                                                Supplements</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No products found</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php $conn->close(); ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <ul class="pagination justify-content-end mt-3 px-lg-5" id="paginationControls">
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="prev">
                                    < </a>
                            </li>
                            <li class="page-item" id="pageNumbers"></li>
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="next">></a>
                            </li>
                        </ul>

                    </div>

                    <!-- Edit Product Modal -->
                <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <div class="modal fade" id="editProductModal<?= $product['id'] ?>" tabindex="-1"
                                aria-labelledby="editProductModalLabel<?= $product['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="../../function/update_product.php" method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editProductModalLabel<?= $product['id'] ?>">Edit Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="product_img">Image</label>
                                                    <input type="file" class="form-control" id="product_img" name="product_img">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="product_name">Name</label>
                                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                                        value="<?= htmlspecialchars($product['product_name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control" id="description" name="description" rows="2"
                                                        required><?= htmlspecialchars($product['description']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="cost">Price</label>
                                                    <input type="number" class="form-control" id="cost" name="cost"
                                                        value="<?= htmlspecialchars($product['cost']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="type">Type</label>
                                                    <select class="form-select" id="type" name="type" required>
                                                        <option value="petfood" <?= ($product['type'] == 'petfood') ? 'selected' : '' ?>>Pet
                                                            Food</option>
                                                        <option value="pettoys" <?= ($product['type'] == 'pettoys') ? 'selected' : '' ?>>Pet
                                                            Toys</option>
                                                        <option value="supplements"
                                                            <?= ($product['type'] == 'supplements') ? 'selected' : '' ?>>Supplements</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                        </table>
</body>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous">
</script>
<script src="../../function/script/toggle-menu.js"></script>
<script src="../../function/script/pagination.js"></script>
<script src="../../function/script/drop-down.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</html>
