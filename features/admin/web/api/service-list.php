<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

require '../../../../db.php';

$sqlCount = "SELECT COUNT(*) FROM service_list WHERE created_at > NOW() - INTERVAL 1 DAY"; // Adjust the interval as needed
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute();
$stmtCount->bind_result($newCount);
$stmtCount->fetch();

$stmtCount->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service List | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/service-list.css">
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
                    <li><a class="dropdown-item" href="to-ship_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Ship</span></a></li>
                    <li><a class="dropdown-item" href="to-receive.php"><i class="fa-solid fa-calendar-check"></i> <span>To-Receive</span></a></li>
                    <li><a class="dropdown-item" href="delivered_checkout.php"><i class="fa-solid fa-calendar-check"></i> <span>Delivered</span></a></li>
                    <li><a class="dropdown-item" href="decline.php"><i class="fa-solid fa-calendar-check"></i> <span>Declined</span></a></li>
                </ul>
            </div> 

           

            <div class="maintenance">
                <p class="maintenance-text">Maintenance</p>
                <a href="service-list.php" class="navbar-highlight">
                    <i class="fa-solid fa-list"></i>
                    <span>Service List</span>
                </a>
                <a href="product.php">
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
    <!--Navigation Links End-->
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
            <h3>Service List</h3>
            <div class="walk-in px-lg-5">
                <div class="mb-3 x d-flex">
                    <div class="search">
                        <div class="search-bars">
                            <i class="fa fa-magnifying-glass"></i>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                  
                    <button type="button" class=" btn-new" data-toggle="modal" data-target="#addCategoryModal">
                        Add new
                    </button>
                </div>
            </div>
            <!--Service Modal (add new)-->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header justify-content-between">
                            <h5 class="modal-title" id="addCategoryModalLabel">Add New Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="../../function/php/save_service.php" method="POST">
                                <div class="form-group">
                                    <label for="categoryType">Type</label>
                                    <select class="form-control mt-2" id="categoryType" name="service_type">
                                        <option value="">Select Type</option>
                                        <option value="home">Home</option>
                                        <option value="clinic">Clinic</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="categoryName">Service</label>
                                    <input type="text" class="form-control mt-2" id="categoryName" name="service_name"
                                        placeholder="Enter Name">
                                </div>

                                <div class="form-group">
                                    <label for="serviceCost">Cost</label>
                                    <input type="text" class="form-control mt-2" id="serviceCost" name="cost"
                                        placeholder="Enter Cost">
                                </div>

                                <div class="form-group">
                                    <label for="serviceDiscount">Discount</label>
                                    <input type="number" class="form-control mt-2" id="serviceDiscount" name="discount"
                                        placeholder="Enter Discount">
                                </div>
                                <div class="form-group">
                                    <label for="serviceInfo">Info</label>
                                    <input type="text" class="form-control mt-2" id="serviceInfo" name="info"
                                        placeholder="Enter additional information">
                                </div>



                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn-new">Save changes</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--Service Modal (add new) End-->

            <!--Service List Table-->
            <div class="table-wrapper px-lg-5">
                <table class="table table-hover table-remove-borders">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Service</th>
                            <th>Cost</th>
                            <th>Discount</th>
                            <th>Info</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php
                        require '../../../../db.php';
                        include '../../function/php/table_service_list.php';
                        include '../../function/php/edit_service.php';
                        include '../../function/php/delete_service.php';

                        ?>
                    </tbody>
                </table>
                <script>
                document.getElementById('search-input').addEventListener('keyup', function () {
                    const input = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#tableBody tr');

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        row.style.display = rowText.includes(input) ? '' : 'none';
                    });
                });
                </script>

                <!--Service List Table End-->

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

        <?php foreach ($services as $service): ?>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $service['id']; ?>" tabindex="-1" role="dialog"
                aria-labelledby="editModalLabel<?php echo $service['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <h5 class="modal-title" id="editModalLabel<?php echo $service['id']; ?>">Edit Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="../../function/php/edit_service.php" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                <div class="form-group">
                                    <label for="serviceType<?php echo $service['id']; ?>">Type</label>
                                    <input type="text" class="form-control" id="serviceType<?php echo $service['id']; ?>"
                                        name="service_type"
                                        value="<?php echo htmlspecialchars($service['service_type']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="serviceName<?php echo $service['id']; ?>">Service</label>
                                    <input type="text" class="form-control" id="serviceName<?php echo $service['id']; ?>"
                                        name="service_name"
                                        value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="serviceCost<?php echo $service['id']; ?>">Cost</label>
                                    <input type="text" class="form-control" id="serviceCost<?php echo $service['id']; ?>"
                                        name="cost" value="<?php echo htmlspecialchars($service['cost']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="serviceDiscount<?php echo $service['id']; ?>">Discount</label>
                                    <input type="number" class="form-control"
                                        id="serviceDiscount<?php echo $service['id']; ?>" name="discount"
                                        value="<?php echo htmlspecialchars($service['discount']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="serviceInfo<?php echo $service['id']; ?>">Info</label>
                                    <input type="text" class="form-control" id="serviceInfo<?php echo $service['id']; ?>"
                                        name="info" value="<?php echo htmlspecialchars($service['info']); ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal<?php echo $service['id']; ?>" tabindex="-1" role="dialog"
                aria-labelledby="deleteModalLabel<?php echo $service['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <h5 class="modal-title" id="deleteModalLabel<?php echo $service['id']; ?>">Delete Service</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the service
                            "<?php echo htmlspecialchars($service['service_name']); ?>"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <a href="../../function/php/delete_service.php?id=<?php echo $service['id']; ?>"
                                class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

</body>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous">
</script>
<script src="../../function/script/toggle-menu.js"></script>
<script src="../../function/script/pagination.js"></script>
<script src="../../function/script/drop-down.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</html>
