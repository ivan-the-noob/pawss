<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>APPOINTMENT | DIGITAL PAWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/appointment.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">
</head>
<style>
  .dropdown-menu .dropdown-item {
    white-space: normal;  
    overflow: hidden;   
    word-wrap: break-word; 
    max-width: 280px;   
    }

    .is-invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>
<?php
session_start();

include '../../../../db.php';
// Set the timezone to Philippine Time (Asia/Manila)
date_default_timezone_set('Asia/Manila');

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in (only store email in session)
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    header("Location: ../../web/api/login.php");
    exit();
}

// Always fetch profile_picture fresh from DB
$profile_picture = 'default.png'; // fallback

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

$today = date('Y-m-d');

$sql = "SELECT COUNT(*) as total FROM appointment WHERE email = ? AND DATE(created_at) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (($row['total'] ?? 0) >= 3) {
    header("Location: ../../../../index.php");
    exit();
}

// Error handling
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorMessages = {
                'gcash_image_required': 'GCash payment screenshot is required.',
                'gcash_reference_required': 'GCash reference number is required.',
                'invalid_gcash_reference': 'Invalid GCash reference number format.',
                'invalid_phone': 'Invalid phone number. Must be 10 digits starting with 9.'
            };
            
            if (errorMessages[`$error`]) {
                alert(errorMessages[`$error`]);
            }
        });
    </script>";
}

// Handle POST request for booking an appointment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ========== ADD THIS WHITESPACE VALIDATION SECTION ==========
    // Trim and check all required fields for whitespace
    $requiredFields = [
        'firstName', 'lastName', 'contactNum', 'ownerEmail', 'petType', 
        'breed', 'age', 'service', 'payment', 'paymentOption', 'add-info'
    ];

    foreach ($requiredFields as $field) {
        if (isset($_POST[$field])) {
            $value = trim($_POST[$field]);
            if ($value === '') {
                // Redirect back with error
                header("Location: appointment.php?error=empty_field");
                exit();
            }
        } else {
            // Field is not set at all
            header("Location: appointment.php?error=empty_field");
            exit();
        }
    }

    // Additional validation for specific fields
    if (empty(trim($_POST['firstName'])) || empty(trim($_POST['lastName']))) {
        header("Location: appointment.php?error=empty_name");
        exit();
    }

    if (empty(trim($_POST['add-info']))) {
        header("Location: appointment.php?error=empty_address");
        exit();
    }
    
    // Validate breed specifically (not just empty, but no whitespace only)
    if (trim($_POST['breed']) === '') {
        header("Location: appointment.php?error=empty_breed");
        exit();
    }
    
    // Validate age specifically
    if (trim($_POST['age']) === '') {
        header("Location: appointment.php?error=empty_age");
        exit();
    }
    
    // Check if service_type is set when needed
    if (empty($_POST['service_type'])) {
        header("Location: appointment.php?error=empty_service_type");
        exit();
    }
    
    // For home services, check address fields
    if ($_POST['service_type'] === 'home') {
        if (empty($_POST['latitude']) || empty($_POST['longitude'])) {
            header("Location: appointment.php?error=empty_location");
            exit();
        }
        if (empty(trim($_POST['barangayDropdown'] ?? ''))) {
            header("Location: appointment.php?error=empty_barangay");
            exit();
        }
    }
    // ========== END OF WHITESPACE VALIDATION ==========
    
    // Continue with your existing code...
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $ownerName = trim($firstName . ' ' . $lastName);
    $rawContact = $_POST['contactNum'] ?? '';
    $rawContact = preg_replace('/\D/', '', $rawContact); // Remove all non-digit characters

    // Validate phone number - must start with 9 and be exactly 10 digits
    if (strlen($rawContact) !== 10 || !ctype_digit($rawContact) || $rawContact[0] !== '9') {
        // Handle the error (redirect back with error message or show error)
        header("Location: appointment.php?error=invalid_phone");
        exit();
    }

    $contactNum = '0' . $rawContact;
    $email = htmlspecialchars($_POST['ownerEmail']);
    $barangay = isset($_POST['barangayDropdown']) ? htmlspecialchars($_POST['barangayDropdown']) : null;
    $petType = htmlspecialchars($_POST['petType']);
    $breed = htmlspecialchars($_POST['breed']);
    $age = htmlspecialchars($_POST['age']);
    $service = htmlspecialchars($_POST['service']);
    $payment = htmlspecialchars($_POST['payment']);
    $paymentOption = htmlspecialchars($_POST['paymentOption']);
    $appointmentDateRaw = htmlspecialchars($_POST['appointment_date']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $addInfo = htmlspecialchars($_POST['add-info']);
    
    $gcashImage = '';
    $gcashReference = '';

    // Validate GCash fields if payment option is GCash
    if ($paymentOption === 'gcash') {
        // Check if image is uploaded
        if (!isset($_FILES['gcash_image']) || $_FILES['gcash_image']['error'] == UPLOAD_ERR_NO_FILE) {
            header("Location: appointment.php?error=gcash_image_required");
            exit();
        }
        
        // Check if reference number is provided
        if (empty($_POST['gcash_reference'])) {
            header("Location: appointment.php?error=gcash_reference_required");
            exit();
        }
        
        // Validate reference number format
        $gcashReference = $_POST['gcash_reference'];
        if (!preg_match('/^\d{10,13}$/', $gcashReference)) {
            header("Location: appointment.php?error=invalid_gcash_reference");
            exit();
        }
    }

    // Handle GCash specific fields if payment method is GCash
    if (isset($_FILES['gcash_image']) && $_FILES['gcash_image']['error'] == 0) {
        $fileType = strtolower(pathinfo($_FILES['gcash_image']['name'], PATHINFO_EXTENSION));
        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $uploadDir = '../../../../assets/img/gcash/';
            $gcashImageFileName = basename($_FILES['gcash_image']['name']);
            $gcashImage = $gcashImageFileName;
            if (!move_uploaded_file($_FILES['gcash_image']['tmp_name'], $uploadDir . $gcashImageFileName)) {
                echo "Failed to upload image.<br>";
            }
        } else {
            echo "Invalid image type. Please upload jpg, jpeg, png, or gif.<br>";
        }
    }

    $gcashReference = isset($_POST['gcash_reference']) ? htmlspecialchars($_POST['gcash_reference']) : '';

    // Get current PH time
    date_default_timezone_set('Asia/Manila');
    $createdAt = date('Y-m-d H:i:s');

    // Adjust appointment date (plus one day)
    $date = new DateTime($appointmentDateRaw);
    $date->modify('+1 day');
    $appointmentDate = $date->format('Y-m-d');

    // Get the service type from POST
    $serviceType = htmlspecialchars($_POST['service_type']);

    // Modify your INSERT statement to include service_category
    $stmt = $conn->prepare("INSERT INTO appointment 
        (owner_name, contact_num, email, barangay, pet_type, breed, age, service, service_category, payment, payment_option, appointment_date, latitude, longitude, add_info, gcash_image, gcash_reference, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssisssssssssss", $ownerName, $contactNum, $email, $barangay, $petType, $breed, $age, $service, $serviceType, $payment, $paymentOption, $appointmentDate, $latitude, $longitude, $addInfo, $gcashImage, $gcashReference, $createdAt);

    if ($stmt->execute()) {
        // Log to global_reports table
        $appointmentTime = date("h:i A | m/d/Y");
        $message = "$email booked an appointment at $appointmentTime";
        $logSql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param("s", $message);
        $logStmt->execute();
        $logStmt->close();

        // Insert notification with created_at
        $notificationMessage = "Successfully Booked! Please wait for admin to accept your appointment";
        $notificationCreatedAt = date('Y-m-d H:i:s');
        $notificationSql = "INSERT INTO notification (email, message, created_at) VALUES (?, ?, ?)";
        $notificationStmt = $conn->prepare($notificationSql);
        $notificationStmt->bind_param("sss", $email, $notificationMessage, $notificationCreatedAt);
        $notificationStmt->execute();
        $notificationStmt->close();

        // Redirect to success page
        header("Location: my-app.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }

    $stmt->close();
}
?>


<body onload="initAutocomplete()">
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
    <!--Header End-->

  <!--Appointment Section-->
  <section class="appointment">
    <div class="content py-5 date">
      <div class="col-md-8 col-11 app">
        <div class="appoints">
          <button>Appointment Availability</button>
          <a href="my-app.php" class="appoint text-decoration-underline">My Appointment</a>
        </div>
        <div class="card card-outline card-primary rounded-0 shadow" id="appointmentSection">
          <div class="card-body">
            <div class="calendar-container">
              <div id="appointmentCalendar"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="dayModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" id="info" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <h5 class="modal-title" id="modalLabel">Book Your Desired Schedule</h5>
        <button type="button"  data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="appointmentForm" method="POST" action="appointment.php" enctype="multipart/form-data">
        <div class="sched row">
          <div class="col-md-6">
            <p>Appointment Schedule</p>
            <div id="modalContent" class="col-6"></div>
            <input type="hidden" id="appointment_date" name="appointment_date" value="">
            <script>
                // When a date is clicked in the calendar
                document.querySelectorAll('.calendar-day').forEach(day => {
                    day.addEventListener('click', function () {
                    const clickedDate = this.getAttribute('data-date'); // format: "YYYY-MM-DD"

                    const date = new Date(clickedDate);
                    date.setDate(date.getDate() + 1); // Add 1 day

                    const adjustedDate = date.toISOString().split('T')[0]; // Get "YYYY-MM-DD"
                    document.getElementById('appointment_date').value = adjustedDate;

                    console.log('Adjusted Appointment Date:', adjustedDate);
                    });
                });
                </script>

          </div>
         
        </div>
        <div class="line w-100 my-3"></div>
        <div class="row">
          <!-- Left Side: Autocomplete and Map -->
          <div class="col-md-6">
          <h6>Owner Information</h6>
          <?php
include '../../../../db.php';


$name = "";
$home_street = "";

if (isset($_SESSION['email'])) {
    $session_email = $_SESSION['email'];

    $sql = "SELECT home_street, name FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_email);
    $stmt->execute();
    $stmt->bind_result($fetched_home_street, $fetched_name);

    if ($stmt->fetch()) {
        $home_street = $fetched_home_street;
        $name = $fetched_name;
    }

    $stmt->close();
}

$conn->close();
?>

            <div class="form-group">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars(explode(' ', $name)[0]); ?>" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars(explode(' ', $name)[1] ?? ''); ?>" id="lastName" name="lastName" required>
            </div>



              <div class="form-group">
                <label for="contactNum" class="form-label">Contact #</label>
                <div class="input-group">
                    <span class="input-group-text">+63</span>
                    <input type="tel" 
                        class="form-control" 
                        id="contactNum" 
                        name="contactNum" 
                        placeholder="9123456789" 
                        pattern="9[0-9]{9}" 
                        maxlength="10" 
                        required
                        oninput="validatePhoneNumber(this)">
                </div>
                <script>
                    function isWhitespaceOnly(str) {
                        return !str || str.trim().length === 0;
                    }

                    // Enhanced phone validation
                    function validatePhoneNumber(input) {
                        // Remove all non-digit characters
                        input.value = input.value.replace(/\D/g, '');
                        
                        // Check if empty or whitespace only
                        if (isWhitespaceOnly(input.value)) {
                            input.setCustomValidity("Phone number is required");
                        } else if (input.value.length > 0 && input.value[0] !== '9') {
                            input.setCustomValidity("Phone number must start with 9");
                        } else {
                            input.setCustomValidity("");
                        }
                    }

                    function validateFormFields() {
                        const requiredFields = [
                            'firstName',
                            'lastName',
                            'contactNum',
                            'ownerEmail',
                            'add-info',
                            'petType',
                            'breed',
                            'age',
                            'service'
                        ];
                        
                        let isValid = true;
                        let firstInvalidField = null;
                        
                        // Check all required fields
                        for (const fieldName of requiredFields) {
                            const field = document.getElementById(fieldName);
                            if (field && (field.type !== 'file' && field.type !== 'radio' && field.type !== 'checkbox')) {
                                if (isWhitespaceOnly(field.value)) {
                                    field.classList.add('is-invalid');
                                    isValid = false;
                                    if (!firstInvalidField) {
                                        firstInvalidField = field;
                                    }
                                } else {
                                    field.classList.remove('is-invalid');
                                }
                            }
                        }
                        
                        // Special handling for select elements
                        const petTypeSelect = document.getElementById('petType');
                        const serviceSelect = document.getElementById('service');
                        
                        if (petTypeSelect && isWhitespaceOnly(petTypeSelect.value)) {
                            petTypeSelect.classList.add('is-invalid');
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = petTypeSelect;
                        } else if (petTypeSelect) {
                            petTypeSelect.classList.remove('is-invalid');
                        }
                        
                        if (serviceSelect && isWhitespaceOnly(serviceSelect.value)) {
                            serviceSelect.classList.add('is-invalid');
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = serviceSelect;
                        } else if (serviceSelect) {
                            serviceSelect.classList.remove('is-invalid');
                        }
                        
                        // Check for GCash reference if GCash is selected
                        if (document.getElementById('gcash').checked) {
                            const gcashReference = document.getElementById('gcashReference');
                            if (gcashReference && isWhitespaceOnly(gcashReference.value)) {
                                gcashReference.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) firstInvalidField = gcashReference;
                            } else if (gcashReference) {
                                gcashReference.classList.remove('is-invalid');
                            }
                        }
                        
                        // Check for home service address fields
                        if (document.getElementById('service_type').value === 'home') {
                            const addressInput = document.getElementById('autocomplete');
                            const barangaySelect = document.getElementById('barangayDropdown');
                            
                            if (addressInput && isWhitespaceOnly(addressInput.value)) {
                                addressInput.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) firstInvalidField = addressInput;
                            } else if (addressInput) {
                                addressInput.classList.remove('is-invalid');
                            }
                            
                            if (barangaySelect && isWhitespaceOnly(barangaySelect.value)) {
                                barangaySelect.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) firstInvalidField = barangaySelect;
                            } else if (barangaySelect) {
                                barangaySelect.classList.remove('is-invalid');
                            }
                        }
                        
                        return { isValid, firstInvalidField };
                    }

                    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
                        const phoneInput = document.getElementById('contactNum');
                        if (phoneInput.value.length !== 10 || phoneInput.value[0] !== '9') {
                            e.preventDefault();
                            alert("Phone number must be 10 digits starting with 9");
                            phoneInput.focus();
                        }
                    });
                    </script>
                <small class="form-text text-muted">Enter 10-digit number (e.g., 9123456789)</small>
                </div>

              <div class="form-group">
                  <label for="ownerEmail" class="form-label">Email</label>
                  <input type="email" class="form-control" id="ownerEmail" name="ownerEmail" 
                        value="<?php echo htmlspecialchars($email); ?>" readonly required>
              </div>
              <div class="form-group">
              <label for="ownerEmail" class="form-label">Complete Address</label>
              <input type="text" value="<?php echo htmlspecialchars($home_street); ?>" class="form-control mb-1" id="addInfo" name="add-info"
                placeholder="Street Name, Building, House No." required
                >
              </div>
              <h6 class="pet-divide">Pet Information</h6>
              <div class="form-group">
                <label for="petType" class="form-label">Pet Type</label>
                <select class="form-control" id="petType" name="petType" required>
                  <option>Cat</option>
                  <option>Dog</option>
                  <option>Rabbit</option>
                  <option>Reptile</option>
                  <option>Others</option>
                </select>
              </div>
              <div class="form-group">
                <label for="breed" class="form-label">Breed</label>
                <input type="text" class="form-control" id="breed" name="breed" placeholder="Husky" required>
              </div>
              <div class="form-group">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" placeholder="Months" required>
              </div>
              
            </div>
            
          <div class="col-md-6">
          <h6>Services</h6>
              <?php
require '../../../../db.php';

try {
    $sql = "SELECT service_name, cost, discount, service_type FROM service_list";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $clinic_services = [];
    $home_services = [];

    while ($row = $result->fetch_assoc()) {
      $cost_str = $row['cost']; 
      $discount = (float) $row['discount'];
      $discounted_cost = $cost_str; 
      $service_data = [
          'service_name' => $row['service_name'],
          'cost' => htmlspecialchars($cost_str),
          'discount' => $row['discount'],
          'discounted_cost' => $discounted_cost,
          'service_type' => $row['service_type']
      ];
  
      if (strtolower($row['service_type']) === 'clinic') {
          $clinic_services[] = $service_data;
      } elseif (strtolower($row['service_type']) === 'home') {
          $home_services[] = $service_data;
      }
  }
  

    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>



<!-- HTML Select Element -->
<div class="form-group">
   <select class="form-control" id="service" name="service" required onchange="updatePayment()" required>
    <!-- Clinic Services -->
    <option value="">Select a service</option>
    <optgroup label="Clinic Services">
        <?php foreach ($clinic_services as $service): ?>
            <option 
                value="<?php echo htmlspecialchars($service['service_name']); ?>" 
                data-type="clinic"
                data-payment="<?php echo htmlspecialchars($service['discounted_cost']); ?>" 
                data-discount="<?php echo htmlspecialchars($service['discount']); ?>">
                <?php echo htmlspecialchars($service['service_name']); ?> - 
                ₱<?php echo htmlspecialchars($service['cost']); ?>  
            </option>
        <?php endforeach; ?>
    </optgroup>

    <!-- Home Services -->
    <optgroup label="Home Services">
        <?php foreach ($home_services as $service): ?>
            <option 
                value="<?php echo htmlspecialchars($service['service_name']); ?>" 
                data-type="home"
                data-payment="<?php echo htmlspecialchars($service['discounted_cost']); ?>" 
                data-discount="<?php echo htmlspecialchars($service['discount']); ?>">
                <?php echo htmlspecialchars($service['service_name']); ?> - 
                ₱<?php echo htmlspecialchars($service['cost']); ?>  
            </option>
        <?php endforeach; ?>
    </optgroup>
</select>

<!-- Add this hidden input field to capture the service type -->
<input type="hidden" id="service_type" name="service_type">

<script>
document.getElementById('service').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var serviceType = selectedOption.getAttribute('data-type');
    document.getElementById('service_type').value = serviceType;
    
    // Your existing updatePayment() function can remain the same
    updatePayment();
});
</script>

    
</div>


    <div class="form-group mt-2">
        <label for="totalPayment" class="form-label mb-0">Total Payment</label>
        <p id="totalPayment">₱ 0.00</p>
    </div>

    <!-- Hidden input for payment -->
    <input type="hidden" id="payment" name="payment"/>

    <div class="form-group">
        <label for="paymentOption" class="form-label">Payment Option *</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="gcash" name="paymentOption" value="gcash" onchange="togglePaymentFields()" required>
            <label class="form-check-label" for="gcash">GCash</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="onStore" name="paymentOption" value="onStore" onchange="togglePaymentFields()" required>
            <label class="form-check-label" for="onStore">Cash (On Store)</label>
        </div>
    </div>

        <!-- GCash Payment Fields (Initially hidden) -->
        <div id="gcashFields" style="display:none;">
            <div class="form-group">
                <img src="../../../../assets/img/gcash/gcash.jpg" alt="" style="width: 100%;">
                <label for="gcashImage" class="form-label">GCash Image *</label>
                <input type="file" class="form-control" id="gcashImage" name="gcash_image" accept="image/*" required>
                <small class="form-text text-muted">Upload screenshot of your GCash payment</small>
            </div>
            <div class="form-group">
                <label for="gcashReference" class="form-label">GCash Reference Number *</label>
                <input type="text" class="form-control" id="gcashReference" name="gcash_reference" 
                       placeholder="Enter GCash Reference Number (10-13 digits)" 
                       pattern="\d{10,13}" required>
                <small class="form-text text-muted">Enter the reference number from your GCash transaction</small>
            </div>
        </div>
        <div class="add-info" style="display: none;">
            <h6 style="margin-top: 76px;">Address Information</h6>
              <div class="form-group">
              <?php
                require '../../../../db.php';
                    $email = $_SESSION['email']; // make sure session email is set
                    $name = $_SESSION['name'];
                    $addressValue = '';
                    $query = $conn->prepare("SELECT latitude, longitude, address_search, home_street FROM users WHERE email = ?");
                    $query->bind_param("s", $email);
                    $query->execute();
                    $result = $query->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $addressValue = !empty($row['address_search']) ? $row['address_search'] : $row['home_street'];
                    }
                ?>
             
              <div class="form-group mt-3" id= "autocomplete-wrapper">
                <input id="autocomplete" placeholder="Can't find your location? Search here." type="text" class="form-control" value="<?php echo htmlspecialchars($addressValue); ?>">

              </div>
                <select id="barangayDropdown" class="form-control w-50 mt-2 mb-2" name="barangayDropdown">
                  <option value="">Select Barangay</option>
                </select>
              </div>
              <div id="modalMap" style="height: 400px;"></div>
             
              <input type="hidden" id="latitude" name="latitude">
              <input type="hidden" id="longitude" name="longitude">
        </div>
   



        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary book-save" id="submitBtn">
                <span id="btnText">Book Appointment</span>
                <span id="btnLoader" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processing...
                </span>
            </button>
        </div>

            </div>
                        
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    
    .spinner-border {
        vertical-align: middle;
        margin-right: 5px;
    }
    
    #btnLoader {
        display: inline-flex;
        align-items: center;
    }
</style>

<script>
// Global flag to prevent duplicate submissions
let isSubmitting = false;

// Helper function to check if a string is empty or contains only whitespace
function isWhitespaceOnly(str) {
    return !str || str.trim().length === 0;
}

// Function to validate all form fields for whitespace
function validateFormFields() {
    const requiredFields = [
        'firstName',
        'lastName',
        'contactNum',
        'ownerEmail',
        'add-info',
        'petType',
        'breed',
        'age',
        'service'
    ];
    
    let isValid = true;
    let firstInvalidField = null;
    
    // Check all required fields
    for (const fieldName of requiredFields) {
        const field = document.getElementById(fieldName);
        if (field && (field.type !== 'file' && field.type !== 'radio' && field.type !== 'checkbox')) {
            if (isWhitespaceOnly(field.value)) {
                field.classList.add('is-invalid');
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            } else {
                field.classList.remove('is-invalid');
            }
        }
    }
    
    // Special handling for select elements
    const petTypeSelect = document.getElementById('petType');
    const serviceSelect = document.getElementById('service');
    
    if (petTypeSelect && isWhitespaceOnly(petTypeSelect.value)) {
        petTypeSelect.classList.add('is-invalid');
        isValid = false;
        if (!firstInvalidField) firstInvalidField = petTypeSelect;
    } else if (petTypeSelect) {
        petTypeSelect.classList.remove('is-invalid');
    }
    
    if (serviceSelect && isWhitespaceOnly(serviceSelect.value)) {
        serviceSelect.classList.add('is-invalid');
        isValid = false;
        if (!firstInvalidField) firstInvalidField = serviceSelect;
    } else if (serviceSelect) {
        serviceSelect.classList.remove('is-invalid');
    }
    
    // Check for GCash reference if GCash is selected
    if (document.getElementById('gcash').checked) {
        const gcashReference = document.getElementById('gcashReference');
        if (gcashReference && isWhitespaceOnly(gcashReference.value)) {
            gcashReference.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = gcashReference;
        } else if (gcashReference) {
            gcashReference.classList.remove('is-invalid');
        }
    }
    
    // Check for home service address fields
    if (document.getElementById('service_type').value === 'home') {
        const addressInput = document.getElementById('autocomplete');
        const barangaySelect = document.getElementById('barangayDropdown');
        
        if (addressInput && isWhitespaceOnly(addressInput.value)) {
            addressInput.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = addressInput;
        } else if (addressInput) {
            addressInput.classList.remove('is-invalid');
        }
        
        if (barangaySelect && isWhitespaceOnly(barangaySelect.value)) {
            barangaySelect.classList.add('is-invalid');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = barangaySelect;
        } else if (barangaySelect) {
            barangaySelect.classList.remove('is-invalid');
        }
    }
    
    return { isValid, firstInvalidField };
}

// Function to validate GCash fields
function validateGCashFields() {
    var gcashChecked = document.getElementById('gcash').checked;
    
    if (gcashChecked) {
        var gcashImage = document.getElementById('gcashImage');
        var gcashReference = document.getElementById('gcashReference');
        
        // Check if file is selected
        if (!gcashImage.value) {
            alert('Please upload a GCash payment screenshot/image.');
            gcashImage.focus();
            return false;
        }
        
        // Check file type
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if (!allowedExtensions.exec(gcashImage.value)) {
            alert('Please upload an image file (JPG, JPEG, PNG, GIF).');
            gcashImage.focus();
            return false;
        }
        
        // Check reference number
        if (!gcashReference.value.trim()) {
            alert('Please enter the GCash reference number.');
            gcashReference.focus();
            return false;
        }
        
        // Validate reference number format (GCash reference numbers are usually 10-13 digits)
        var refPattern = /^\d{10,13}$/;
        if (!refPattern.test(gcashReference.value.trim())) {
            alert('Please enter a valid GCash reference number (10-13 digits).');
            gcashReference.focus();
            return false;
        }
    }
    return true;
}

// Function to reset the submit button state
function resetSubmitButton() {
    isSubmitting = false;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    
    if (submitBtn) {
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    }
}

// Function to toggle payment fields
function togglePaymentFields() {
    var gcashChecked = document.getElementById('gcash').checked;
    var gcashFields = document.getElementById('gcashFields');
    var gcashImage = document.getElementById('gcashImage');
    var gcashReference = document.getElementById('gcashReference');
    
    if (gcashChecked) {
        gcashFields.style.display = 'block';
        // Make fields required
        gcashImage.setAttribute('required', 'required');
        gcashReference.setAttribute('required', 'required');
    } else {
        gcashFields.style.display = 'none';
        // Remove required attribute
        gcashImage.removeAttribute('required');
        gcashReference.removeAttribute('required');
    }
    
    // Reset button state when payment option changes
    resetSubmitButton();
}

// Function to update payment display
function updatePayment() {
    const serviceSelect = document.getElementById('service');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

    const addInfoDiv = document.querySelector('.add-info');
    const type = selectedOption.getAttribute('data-type');
    const payment = selectedOption.getAttribute('data-payment');

    // Show/hide address info
    if (type === 'home') {
        addInfoDiv.style.display = 'block';
    } else {
        addInfoDiv.style.display = 'none';
    }

    // Update total payment
    if (payment) {
        document.getElementById('totalPayment').textContent = '₱' + parseFloat(payment).toFixed(2);
        document.getElementById('payment').value = payment;
    } else {
        document.getElementById('totalPayment').textContent = '₱0.00';
        document.getElementById('payment').value = '';
    }
    
    // Reset button state when service changes
    resetSubmitButton();
}

// MAIN FORM SUBMISSION EVENT HANDLER
document.getElementById('appointmentForm').addEventListener('submit', function(event) {
    // Prevent multiple submissions
    if (isSubmitting) {
        event.preventDefault();
        return;
    }
    
    // Validate all fields for whitespace
    const validationResult = validateFormFields();
    if (!validationResult.isValid) {
        event.preventDefault();
        alert("Please fill in all required fields. Fields cannot contain only spaces.");
        if (validationResult.firstInvalidField) {
            validationResult.firstInvalidField.focus();
        }
        return;
    }
    
    // Get selected service type
    var serviceSelect = document.getElementById('service');
    var selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    var serviceType = selectedOption.getAttribute('data-type');
    
    // If home service, validate location fields
    if (serviceType === 'home') {
        var latitude = document.getElementById('latitude').value;
        var longitude = document.getElementById('longitude').value;
        var barangay = document.getElementById('barangayDropdown').value;
        
        // Validate location is selected
        if (!latitude || !longitude) {
            alert("Please make sure the location is selected on the map for home service.");
            event.preventDefault();
            return;
        }
    }
    
    // Validate phone number format
    const phoneInput = document.getElementById('contactNum');
    if (phoneInput.value.length !== 10 || phoneInput.value[0] !== '9') {
        event.preventDefault();
        alert("Phone number must be 10 digits starting with 9");
        phoneInput.focus();
        return;
    }
    
    // Validate GCash fields if GCash is selected
    if (document.getElementById('gcash').checked) {
        if (!validateGCashFields()) {
            event.preventDefault();
            return;
        }
    }
    
    // Validate payment option is selected
    var paymentSelected = document.querySelector('input[name="paymentOption"]:checked');
    if (!paymentSelected) {
        alert("Please select a payment option.");
        event.preventDefault();
        return;
    }
    
    // If all validations pass, show loading state
    isSubmitting = true;
    
    // Disable the submit button and show loading spinner
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-flex';
});

// Add event listeners to validate on blur for all required fields
document.addEventListener('DOMContentLoaded', function() {
    const requiredFields = [
        'firstName',
        'lastName',
        'contactNum',
        'add-info',
        'breed',
        'age'
    ];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('blur', function() {
                if (isWhitespaceOnly(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
            
            // Also validate on input
            field.addEventListener('input', function() {
                if (!isWhitespaceOnly(this.value)) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
    
    // For select elements
    const petTypeSelect = document.getElementById('petType');
    const serviceSelect = document.getElementById('service');
    
    if (petTypeSelect) {
        petTypeSelect.addEventListener('change', function() {
            if (!isWhitespaceOnly(this.value)) {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            if (!isWhitespaceOnly(this.value)) {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // For the autocomplete field
    const autocompleteField = document.getElementById('autocomplete');
    if (autocompleteField) {
        autocompleteField.addEventListener('change', function() {
            if (!isWhitespaceOnly(this.value)) {
                this.classList.remove('is-invalid');
            }
        });
    }
});

// Optional: Reset loading state if form validation fails and user goes back
window.addEventListener('pageshow', function(event) {
    // Reset the submission flag when page is shown (including back/forward navigation)
    if (event.persisted) {
        resetSubmitButton();
    }
});

// Also reset on page unload (when user leaves the page)
window.addEventListener('beforeunload', function() {
    resetSubmitButton();
});
</script>

<script>
function initAutocomplete() {
    var mapCenter = { lat: 14.283634481584178, lng: 120.86458688732908 }; 

    var map = new google.maps.Map(document.getElementById('modalMap'), {
        center: mapCenter,
        zoom: 20,
        mapTypeId: 'roadmap'
    });

    var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(14.2680, 120.8400), 
        new google.maps.LatLng(14.2940, 120.8695)
    );

    var input = document.getElementById('autocomplete');

    var autocomplete = new google.maps.places.Autocomplete(input, {
        bounds: defaultBounds,
        strictBounds: false,
        componentRestrictions: {}
    });
    autocomplete.bindTo('bounds', map);

    autocomplete.setFields(['address_component', 'geometry', 'icon', 'name']);

    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29),
        draggable: true,
        visible: true
    });

    $('#autocomplete').on('focus', function () {
        var pacContainer = $('.pac-container');
        pacContainer.appendTo('#autocomplete-wrapper');
    });

    autocomplete.addListener('place_changed', function () {
        infowindow.close();
        var place = autocomplete.getPlace();

        if (!place.geometry) {
            console.log("No details available for input: '" + place.name + "'");
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        marker.setPosition(place.geometry.location);

        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);

        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();

        var city = place.address_components.find(component => component.types.includes('locality'))?.short_name;
        if (city) {
            populateBarangayDropdown(city);
        }
    });

    map.addListener('click', function(event) {
        marker.setPosition(event.latLng);
        marker.setVisible(true);
        infowindow.setContent('<div>New location: <br>' + event.latLng.lat() + ', ' + event.latLng.lng() + '</div>');
        infowindow.open(map, marker);

        document.getElementById('latitude').value = event.latLng.lat();
        document.getElementById('longitude').value = event.latLng.lng();
    });

    marker.addListener('dragend', function(event) {
        infowindow.setContent('<div>Your Exact Location</div>');
        infowindow.open(map, marker);

        document.getElementById('latitude').value = event.latLng.lat();
        document.getElementById('longitude').value = event.latLng.lng();
    });

    function populateBarangayDropdown(city) {
        var barangays = {
            'Trece Martires': {
                'Aguado': { lat: 14.25542581655494, lng: 120.8656522150248},
                'Cabezas': { lat: 14.263709144217062, lng: 120.89555461026328},
                'Cabuco': { lat: 14.279359898149433, lng: 120.84468022563351 },
                'Conchu': { lat: 14.260485726947172, lng: 120.88286485988135 },
                'De Ocampo': { lat: 14.300501942835817, lng: 120.86460081581872 },
                'Gregorio': { lat: 14.288636521925628, lng: 120.87205210047465 },
                'Inocencio': { lat: 14.253491166057374, lng: 120.8777464139661 }, 
                'Lallana': { lat: 14.252491559765417, lng: 120.89643030232541 },
                'Lapidario': { lat: 14.273823626659963, lng: 120.86629069154668 },
                'Luciano': { lat: 14.274976771584905, lng: 120.86903695259147},
                'Osorio': { lat: 14.297669620091915, lng: 120.87694138698265 },
                'Perez': { lat: 14.28327618887629, lng: 120.88951665599205 },
                'San Agustin': { lat: 14.278496301453135, lng: 120.86424085850058 }
            }
        };

        var barangayDropdown = $('#barangayDropdown');
        barangayDropdown.empty();
        barangayDropdown.append('<option value="">Select Barangay</option>');

        if (barangays[city]) {
            for (var barangay in barangays[city]) {
                barangayDropdown.append('<option value="' + barangay + '">' + barangay + '</option>');
            }
            barangayDropdown.prop('disabled', false);
        } else {
            barangayDropdown.prop('disabled', true);
        }

        barangayDropdown.on('change', function() {
            var selectedBarangay = $(this).val();
            if (selectedBarangay && barangays[city][selectedBarangay]) {
                var location = barangays[city][selectedBarangay];
                map.setCenter(location);
                map.setZoom(17);
                marker.setPosition(location);

                document.getElementById('latitude').value = location.lat;
                document.getElementById('longitude').value = location.lng();
            }
        });
    }

    populateBarangayDropdown('Trece Martires');

    function useMyLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var userLocation = new google.maps.LatLng(lat, lng);

                map.setCenter(userLocation);
                map.setZoom(17);

                marker.setPosition(userLocation);
                marker.setVisible(true);

                infowindow.setContent('<div>Your current location:<br>' + lat + ', ' + lng + '</div>');
                infowindow.open(map, marker);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            }, function(error) {
                showError(error);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }

    function validateTime() {
        var timeInput = document.getElementById('appointmentTime');
        var timeValue = timeInput.value;
        var minTime = "09:00";
        var maxTime = "17:00";

        if (timeValue < minTime || timeValue > maxTime) {
            alert("Please select a time between 9:00 AM and 5:00 PM.");
            timeInput.value = "";
        }
    }
}

$('#dayModal').on('shown.bs.modal', function () {
    initAutocomplete();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="../../function/script/pagination-history.js"></script>
<script src="../../function/script/calendar.js"></script>
<script src="../../function/script/tab-bar.js"></script>
<script src="../../function/script/service-dropdown.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</html>