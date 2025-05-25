<?php
    session_start();
    if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../../../users/web/api/login.php");
        exit();
    }

    require '../../../../db.php';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; 
    $offset = ($page - 1) * $limit;
    
    // Query to fetch the finished appointments
    $sql = "SELECT * FROM appointment WHERE status = 'finish'";

    if ($search) {
        $sql .= " AND (owner_name LIKE '%" . $conn->real_escape_string($search) . "%' 
                    OR email LIKE '%" . $conn->real_escape_string($search) . "%')";
    }

    $sql .= " LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    // Query to get the total number of finished appointments
    $countSql = "SELECT COUNT(*) as total FROM appointment WHERE status = 'finish'";
    if ($search) {
        $countSql .= " AND (owner_name LIKE '%" . $conn->real_escape_string($search) . "%' 
                        OR email LIKE '%" . $conn->real_escape_string($search) . "%')";
    }
    $totalResult = $conn->query($countSql);
    $totalRow = $totalResult->fetch_assoc();
    $totalPages = ceil($totalRow['total'] / $limit);

    // Check if the total data count is greater than or equal to 10
    $showPagination = $totalRow['total'] > 10;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/app-req.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

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
                    <li><a class="dropdown-item " href="app-waiting.php"><i class="fa-solid fa-calendar-check"></i> <span>Waiting Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-ongoing.php"><i class="fa-solid fa-calendar-check"></i> <span>On Going Bookings</span></a></li>
                    <li><a class="dropdown-item navbar-highlight" href="app-finish.php"><i class="fa-solid fa-calendar-check"></i> <span>Finished Bookings</span></a></li>
                    <li><a class="dropdown-item" href="app-cancel.php"><i class="fa-solid fa-calendar-check"></i> <span>Cancelled Bookings</span></a></li>
                   
                </ul>
            </div> 
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center" id="checkoutDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
                <a href="admin-user.php">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Admin User List</span>
                </a>
                <a href="review-list.php">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Review List</span>
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
        <!--Notification and Profile Admin-->
        <div class="app-req">
            <h3>History</h3>
            <div class="walk-in px-lg-5">
                <div class="mb-3 x d-flex">
                <div class="search w-100 d-flex justify-content-end">
                    <form method="GET" action="">
                        <div class="search-bars d-flex justify-content-end">
                            <i class="fa fa-search"></i>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" placeholder="Search..." id="search-input" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>
                    </form>

                    </div>

                </div>
            </div>
            <div class="table-wrapper px-lg-5">
            <table class="table table-hover table-remove-borders">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Owner Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                           
                            <th>Payment</th>
                            <th>Payment Options</th>
                
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $index = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$index}</td>";
                                echo "<td>{$row['owner_name']}</td>";
                                echo "<td>{$row['contact_num']}</td>";
                                echo "<td>{$row['email']}</td>";
                               
                                echo "<td>{$row['payment']}</td>";
                                echo "<td>" . (!empty($row['gcash_image']) ? $row['payment_option'] : "On store") . "</td>";
                                echo "<td>
                                <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                    onclick='viewAdditionalInfo(
                                        {$row['id']}, 
                                        \"" . addslashes($row['barangay']) . "\", 
                                        \"" . addslashes($row['pet_type']) . "\", 
                                        \"" . addslashes($row['breed']) . "\", 
                                        \"" . addslashes($row['age']) . "\", 
                                        \"" . addslashes($row['service']) . "\", 
                                        \"" . date('F j, Y', strtotime($row['appointment_date'])) . "\", 
                                        \"" . addslashes($row['add_info']) . "\", 
                                        \"" . addslashes($row['contact_num']) . "\",
                                        \"" . date('F j, Y h:i A', strtotime($row['created_at'])) . "\"
                                    )'>
                                    <i class='fas fa-eye'></i>
                                </button>";
                        
                        if ($row['payment_option'] != 'On Store') {
                            echo "<button class='btn btn-primary' style='margin-right: 5px;' data-bs-toggle='modal' data-bs-target='#locationModal' 
                                    onclick='showMap({$row['latitude']}, {$row['longitude']})'>
                                    <i class='fas fa-map-marker-alt'></i>
                                </button>";
                        }
                        
                        if (!empty($row['gcash_image'])) {
                            echo "<button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#gcashModal' 
                                    onclick='showGcashImage(\"" . addslashes($row['gcash_image']) . "\")'>Proof</button>";
                        }
                        
                        echo "</td>";
                        
                                $index++;
                            }
                        } else {
                            echo "<tr><td colspan='14' class='text-center'>No pending appointments found</td></tr>";
                        }
                        ?>
                    </tbody>
                    <!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel">Additional Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                        <p class="d-none"><strong>Barangay:</strong> <span id="barangayDetail"></span></p>
                        <p><strong>Pet Type:</strong> <span id="petTypeDetail"></span></p>
                        <p><strong>Breed:</strong> <span id="breedDetail"></span></p>
                        <p><strong>Age:</strong> <span id="ageDetail"></span> Months</p>
                        <p><strong>Service:</strong> <span id="serviceDetail"></span></p>
                        <p><strong>Appointment Date:</strong> <span id="appointmentDateDetail"></span></p>
                         <p><strong>Date of Booked:</strong> <span id="timeOfBookedDetail"></span></p>
                        <p><strong>Address:</strong> <span id="additionalInfoDetail"></span></p>
                        <p><strong>Contact Number:</strong> <span id="contact_numberDetail"></span></p>
                    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
                    function viewAdditionalInfo(id, barangay, petType, breed, age, service, appointmentDate, additionalInfo, contact_number, created_at) {
                        document.getElementById('barangayDetail').innerText = barangay;
                        document.getElementById('petTypeDetail').innerText = petType;
                        document.getElementById('breedDetail').innerText = breed;
                        document.getElementById('ageDetail').innerText = age;
                        document.getElementById('serviceDetail').innerText = service;
                        document.getElementById('appointmentDateDetail').innerText = appointmentDate;
                        document.getElementById('additionalInfoDetail').innerText = additionalInfo;
                        document.getElementById('contact_numberDetail').innerText = contact_number;

                        document.getElementById('timeOfBookedDetail').innerText = created_at;
                    }
                </script>

                    <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="locationModalLabel">Appointment Location</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="map" style="height: 500px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 300px">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="gcashModalLabel">GCash Payment Image</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img id="gcashImage" src="" alt="GCash Receipt" class="img-fluid"/>
                        </div>
                    </div>
                </div>
            </div>
                </table>
            </div>
            <?php if ($showPagination): ?>
                <ul class="pagination justify-content-end mt-3 px-lg-5" id="paginationControls">
                    <li class="page-item prev <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" data-page="prev">&lt;</a>
                    </li>
                    <ul class="pagination" id="pageNumbers">
                        <?php
                        for ($i = 1; $i <= $totalPages; $i++) {
                            echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'>
                                    <a class='page-link' href='?page=$i&search=" . urlencode($search) . "'>$i</a>
                                </li>";
                        }
                        ?>
                    </ul>
                    <li class="page-item next <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" data-page="next">&gt;</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>



<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places">
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="../../function/script/toggle-menu.js"></script>
<script src="../../function/script/pagination.js"></script>
<script src="../../function/script/drop-down.js"></script>
<script src="../../function/script/booking_function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</html>
