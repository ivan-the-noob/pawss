<?php
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

include '../../../../db.php';

// Pagination logic
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 5; // Number of users per page
$offset = ($page - 1) * $limit;

// Get the total number of users with the role 'admin' or 'staff'
$query = "SELECT COUNT(*) FROM users WHERE role IN ('admin', 'staff')";
$result = mysqli_query($conn, $query);
$total_users = mysqli_fetch_row($result)[0];

// Get the users for the current page, with the role 'admin' or 'staff'
$query = "SELECT * FROM users WHERE role IN ('admin', 'staff') LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate total pages
$total_pages = ceil($total_users / $limit);

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User List | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-user.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
      <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">

</head>

<body>
    <!--Navigation Links-->
    <div class="navbar flex-column bg-white shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <a class="navbar-brand d-none d-md-block logo-container" href="admin.php">
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
                <a href="service-list.php">
                    <i class="fa-solid fa-list"></i>
                    <span>Service List</span>
                </a>
                <a href="product.php">
                    <i class="fa-solid fa-box"></i>
                    <span>Product</span>
                </a>
                <a href="admin-user.php" class="navbar-highlight">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Admin User List</span>
                </a>
                 <a href="review-list.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Review List</span>
                </a>
                 <a href="contact-section.php">
                    <i class="fa-solid fa-phone"></i>
                    <span>Contact List</span>
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
        <!--Noticiation and Profile Admin End-->
        <div class="app-req">
            <h3>Admin User List</h3>
            <div class="walk-in px-lg-5">
                <div class="mb-3 x d-flex">
                    <div class="">
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
            <!--Category Modal-->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header justify-content-between">
                            <h5 class="modal-title" id="addCategoryModalLabel">Add New User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="../../function/php/add_user.php" method="POST">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control mt-2" id="name" name="name"
                                        placeholder="Enter name" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control mt-2" id="email" name="email"
                                        placeholder="Enter email" required>
                                    <?php if (!empty($error)): ?>
                                        <small class="text-danger"><?php echo $error; ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control mt-2" id="password" name="password"
                                        placeholder="Enter password" required>
                                </div>

                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control mt-2" id="role" name="role" required>
                                        <option value="">Select User Type</option>
                                        <option value="staff">Staff</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>


                                <div class="modal-footer">
                                    <button type="submit" class="btn-new">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!--Category Modal End-->


            <!--Category Table-->
            <div class="table-wrapper px-lg-5">
                <table class="table table-hover table-remove-borders">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $index => $user): ?>
                                <tr class="test-hover">
                                    <td><?php echo $index + 1; ?></td>
                                    <td><img src="../../../../assets/img/customer.jfif" alt="Avatar"></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                                    <td>
                                        <!-- Button to open the modal -->
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal-<?php echo $user['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteUserModal-<?php echo $user['id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <div class="modal fade" id="editUserModal-<?php echo $user['id']; ?>" tabindex="-1"
                                            aria-labelledby="editUserModalLabel-<?php echo $user['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header d-flex justify-content-between">
                                                        <h5 class="modal-title"
                                                            id="editUserModalLabel-<?php echo $user['id']; ?>">Edit User</h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="../../function/php/edit_user.php" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                                                            <div class="mb-3">
                                                                <label for="name-<?php echo $user['id']; ?>"
                                                                    class="form-label">Name</label>
                                                                <input type="text" class="form-control edit_admin text-center"
                                                                    id="name-<?php echo $user['id']; ?>" name="name"
                                                                    value="<?php echo htmlspecialchars($user['name']); ?>"
                                                                    required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="email-<?php echo $user['id']; ?>"
                                                                    class="form-label">Email</label>
                                                                <input type="email" class="form-control edit_admin text-center"
                                                                    id="email-<?php echo $user['id']; ?>" name="email"
                                                                    value="<?php echo htmlspecialchars($user['email']); ?>"
                                                                    required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="role-<?php echo $user['id']; ?>"
                                                                    class="form-label">Role</label>
                                                                <select class="form-control edit_admin text-center"
                                                                    id="role-<?php echo $user['id']; ?>" name="role" required>
                                                                    <option value="staff"
                                                                        <?php if ($user['role'] == 'staff') echo 'selected'; ?>>
                                                                        Staff</option>
                                                                    <option value="admin"
                                                                        <?php if ($user['role'] == 'admin') echo 'selected'; ?>>
                                                                        Admin</option>
                                                                </select>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    Changes</button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="deleteUserModal-<?php echo $user['id']; ?>" tabindex="-1"
                                            aria-labelledby="deleteUserModalLabel-<?php echo $user['id']; ?>"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header d-flex justify-content-between">
                                                        <h5 class="modal-title"
                                                            id="deleteUserModalLabel-<?php echo $user['id']; ?>">Delete User
                                                        </h5>
                                                        <button type="button" class="close" data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this user?</p>
                                                        <p><strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                            (<?php echo htmlspecialchars($user['email']); ?>)</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form action="../../function/php/delete_user_admin.php" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>


                </table>
                <script>
window.onload = function () {
    document.getElementById('search-input').addEventListener('keyup', function () {
        const input = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tableBody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(input) ? '' : 'none';
        });
    });
};
</script>
                <?php if (!empty($error)): ?>
                    <small class="text-danger"><?php echo $error; ?></small>
                <?php endif; ?>

            </div>
            <?php
// Check if pagination is needed
$showPagination = $total_users > 5; // Only show pagination if more than 5 users

if ($showPagination): ?>
    <ul class="pagination justify-content-end mt-3 px-lg-5" id="paginationControls">
        <li class="page-item prev <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" data-page="prev">&lt;</a>
        </li>
        
        <ul class="pagination" id="pageNumbers">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                        <a class='page-link' href='?page=$i&search=" . urlencode($search) . "'>$i</a>
                    </li>";
            }
            ?>
        </ul>

        <li class="page-item next <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" data-page="next">&gt;</a>
        </li>
    </ul>
<?php endif; ?>



        </div>
        <!--Category Table End-->
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
