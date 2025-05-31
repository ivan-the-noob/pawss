<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MY APPOINTMENT | DIGITAL PAWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/appointment.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">
</head>
<?php
session_start();

include '../../../../db.php';

// Check if the user is logged in
if (isset($_SESSION['email']) && isset($_SESSION['profile_picture'])) {
    $email = $_SESSION['email'];
    $profile_picture = $_SESSION['profile_picture'];
} else {
    header("Location: ../../web/api/login.php");
    exit();
}

// Handle POST request for booking an appointment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $ownerName = htmlspecialchars($_POST['ownerName']);
  $contactNum = htmlspecialchars($_POST['contactNum']);
  $email = htmlspecialchars($_POST['ownerEmail']);
  $barangay = htmlspecialchars($_POST['barangayDropdown']);
  $petType = htmlspecialchars($_POST['petType']);
  $breed = htmlspecialchars($_POST['breed']);
  $age = htmlspecialchars($_POST['age']);
  $service = htmlspecialchars($_POST['service']);
  $payment = htmlspecialchars($_POST['payment']);
  $paymentOption = htmlspecialchars($_POST['paymentOption']);  // New field
  $appointmentDate = htmlspecialchars($_POST['appointment_date']);
  $latitude = htmlspecialchars($_POST['latitude']);
  $longitude = htmlspecialchars($_POST['longitude']);
  $addInfo = htmlspecialchars($_POST['add-info']);
  
  $gcashImage = '';
  $gcashReference = '';  // Updated to match the correct name

  // Handle GCash specific fields if payment method is GCash
  if (isset($_FILES['gcash_image']) && $_FILES['gcash_image']['error'] == 0) {
    // Validate image file type
    $fileType = strtolower(pathinfo($_FILES['gcash_image']['name'], PATHINFO_EXTENSION));
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $uploadDir = '../../../../assets/img/gcash/'; // Set your desired upload directory
        $gcashImageFileName = basename($_FILES['gcash_image']['name']);  // Extract file name only
        $gcashImage = $gcashImageFileName;  // Store only the file name
        if (move_uploaded_file($_FILES['gcash_image']['tmp_name'], $uploadDir . $gcashImageFileName)) {
            echo "Image uploaded successfully.<br>";
        } else {
            echo "Failed to upload image.<br>";
        }
    } else {
        echo "Invalid image type. Please upload jpg, jpeg, png, or gif.<br>";
    }
} else {
    echo "No GCash image uploaded or error with the image upload.<br>";
}

  // Get GCash reference
  $gcashReference = isset($_POST['gcash_reference']) ? htmlspecialchars($_POST['gcash_reference']) : ''; // Updated to gcash_reference

  // Console log for debugging (optional)
  echo "<script>console.log('GCash Image: " . $gcashImage . "');</script>";
  echo "<script>console.log('GCash Reference: " . $gcashReference . "');</script>";

  // Prepare the SQL statement to insert into the appointment table
  $stmt = $conn->prepare("INSERT INTO appointment (owner_name, contact_num, email, barangay, pet_type, breed, age, service, payment, payment_option, appointment_date, latitude, longitude, add_info, gcash_image, gcash_reference) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  // Correct bind_param format for 16 parameters
  $stmt->bind_param("ssssssisssssssss", $ownerName, $contactNum, $email, $barangay, $petType, $breed, $age, $service, $payment, $paymentOption, $appointmentDate, $latitude, $longitude, $addInfo, $gcashImage, $gcashReference);

  // Execute the query and check if successful
  if ($stmt->execute()) {
      // Log the appointment booking event in the global_reports table
      $appointmentTime = date("h:i A | m/d/Y"); // Current time of appointment booking
      $message = "$email booked an appointment at $appointmentTime";

      // Prepare the SQL statement to insert into the global_reports table
      $log_sql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
      $log_stmt = $conn->prepare($log_sql);
      $log_stmt->bind_param("s", $message);
      $log_stmt->execute();
      $log_stmt->close();

      // Success message
      echo "New appointment booked successfully.<br>";
  } else {
      echo "Error: " . $stmt->error . "<br>";
  }

  // Close statement and connection
  $stmt->close();
  $conn->close();
}

?>

<?php
$showSuccess = false;
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $showSuccess = true;
}
?>

<?php if ($showSuccess): ?>
  <div id="successToast" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 9999;
    font-size: 16px;
  ">
    Successfully booked!
  </div>

  <script>
    setTimeout(() => {
      const toast = document.getElementById('successToast');
      if (toast) {
        toast.style.transition = 'opacity 0.5s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
      }
    }, 3000);
  </script>
<?php endif; ?>

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
    <!--Header End-->

  <!--Appointment Section-->
  <section class="appointment">
    <div class="content date">
      <div class="col-md-8 col-11 app">
        <div class="appoints">
          <button>Appointment Availability</button>
          <a href="appointment.php" class="appoint text-decoration-underline" id="toggleViewBtn">My Calendar</a>
        </div>
        
      </div>
    </div>
   
  </section>
  <!--Appointment Section End-->
  <?php if (isset($_GET['rated']) && $_GET['rated'] == 'success'): ?>
  <div id="ratingToast" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: fadeOut 0.5s ease-out 2.5s forwards;
  ">
    Rated successfully!
  </div>

  <script>
    setTimeout(() => {
      const toast = document.getElementById('ratingToast');
      if (toast) toast.style.display = 'none';
    }, 3000);
  </script>

  <style>
    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: translateY(10px);
      }
    }
  </style>
<?php endif; ?>

    <!--Book-History Section-->
  <section class="booked-history" id="bookedHistorySection">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-12 col-24">
          <div class="card card-outline card-primary rounded-0 shadow">
            <div class="card-header rounded-0">
              <h4 class="card-title text-center">Booking History</h4>
            </div>
            <div class="tab-bar">
                <button id="currentBtn" class="btn btn-link underlined">Appointment</button>
                <button class="none"> |</button>
                <button id="pastBtn" class="btn btn-link underlined">History</button>
            </div>

            <div class="card-body">
              <ul class="list-group" id="historyList">
                <?php 
                require '../../../../db.php';
                $sql = "SELECT * FROM appointment WHERE status IN ('pending', 'waiting', 'on-going') AND email = ? ORDER BY appointment_date DESC";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email); 
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <li class="list-group-item current-appointment">
                      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h5 class="mb-1">Appointment</h5>
                          <p class="mb-1"><span class="fw-bold">Service:</span> <?php echo $row['service']; ?></p>
                          <p class="mb-1"><span class="fw-bold">Pet:</span> <?php echo $row['pet_type'] . ', ' . $row['age'] . ' Yr Old'; ?></p>
                          <p class="mb-1"><span class="fw-bold">Owner:</span><?php echo $row['owner_name']; ?></p>
                          <p class="mb-1 d-none"><span class="fw-bold">Address:</span><?php echo $row['add_info']; ?></p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-right">
                        <p class="mb-1 status" style="background-color: 
                            <?php 
                                if ($row['status'] == 'pending') {
                                    echo '#007bff';
                                } elseif ($row['status'] == 'waiting') {
                                    echo '#ffc107'; // Yellow background for waiting status
                                } elseif ($row['status'] == 'on-going') {
                                    echo '#28a745'; // Green background for on-going
                                }
                            ?>; color: #000;">
                            <?php 
                                if ($row['status'] == 'waiting') {
                                    echo 'Confirmed'; // Display "Confirmed" for waiting status
                                } else {
                                    echo ucfirst($row['status']); // Capitalize the first letter of other statuses
                                }
                            ?>
                        </p>

                          <p class="mb-1"><span class="fw-bold">Date of Appointment:</span> <?php echo $row['appointment_date']; ?></p>
                          <p class="mb-1"><span class="fw-bold">Booked Time:</span>  <?php echo date('g:i A', strtotime($row['created_at'])); ?> </p>
                          <?php if ($row['status'] === 'pending'): ?>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $row['id']; ?>">
                              Cancel
                            </button>
                          <?php endif; ?>


                          <!-- Modal -->
                          <div class="modal fade" id="cancelModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-md modal-dialog-centered">
                              <form action="../../function/php/appointment_cancel.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel<?php echo $row['id']; ?>">Cancel Appointment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="mb-3">
                                      <label for="cancel_reason<?php echo $row['id']; ?>" class="form-label">Reason for Cancellation</label>
                                      <textarea class="form-control" name="cancel_reason" id="cancel_reason<?php echo $row['id']; ?>" rows="3" required></textarea>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
  
                                    <button type="submit" name="cancel_appointment" class="btn btn-danger">Confirm Cancel</button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <?php
                  }
                } 
                
                $conn->close();
                ?>
               <?php 
                require '../../../../db.php';
                $sql = "SELECT * FROM appointment WHERE status IN ('cancel', 'finish')";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    ?>
                    <li class="list-group-item past-appointment">
                      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div>
                            <h5 class="mb-1">Appointment</h5>
                          <p class="mb-1"><span class="fw-bold">Service:</span> <?php echo $row['service']; ?></p>
                          <p class="mb-1"><span class="fw-bold">Pet:</span> <?php echo $row['pet_type'] . ', ' . $row['age'] . ' Yr Old'; ?></p>
                          <p class="mb-1"><span class="fw-bold">Owner:</span> <?php echo $row['owner_name']; ?></p>
                          <p class="mb-1 d-none"><span class="fw-bold">Address:</span><?php echo $row['add_info']; ?></p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-right">

                        <div class="d-flex gap-1">
                        <p class="mb-1 status" style="background-color: 
                            <?php 
                                if ($row['status'] == 'finish') {
                                    echo 'green';
                                }
                            ?>; color: #000;">
                            <?php 
                                if ($row['status'] == 'finish') {
                                    echo 'Finished';  
                                } else {
                                    echo ucfirst($row['status']); // Capitalize other statuses
                                }
                            ?>
                        </p>
                        <?php if ($row['is_rated'] == 0): ?>
                          <button class="btn btn-warning btn-sm text-white fw-bold" data-bs-toggle="modal" data-bs-target="#ratingModal">
                            Rate our service
                          </button>
                        <?php endif; ?>
                       

                        <!-- Rating Modal -->
                        <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <form action="../../function/php/submit_rating.php" method="POST">
                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">

                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="ratingModalLabel">Rate Our Service</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body">
                                  <!-- Star Rating -->
                                  <div class="mb-3 text-center">
                                    <input type="hidden" name="rating" id="ratingValue">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                      <i class="fa-regular fa-star fa-2x star" data-value="<?php echo $i; ?>" style="cursor:pointer; color: #ccc;"></i>
                                    <?php endfor; ?>
                                  </div>

                                  <!-- Comment -->
                                  <div class="mb-3">
                                    <label for="comment" class="form-label fw-bold">Comments (optional)</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Tell us what you think..."></textarea>
                                  </div>
                                </div>
                                
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-warning text-white fw-bold">Submit</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>

                      </div>

                        <p class="mb-1"><span class="fw-bold">Date of Appointment:</span> <?php echo $row['appointment_date']; ?></p>
                        <p class="mb-1"><span class="fw-bold">Booked Time:</span>  <?php echo date('g:i A', strtotime($row['created_at'])); ?> </p>
                        <?php if ($row['status'] == 'cancel'): ?>
                            <p class="mb-1"><span class="fw-bold">Cancel reason:</span> <?php echo $row['cancel_reason']; ?></p>
                        <?php endif; ?>

                      
                        
                        </div>
                      </div>
                    </li>
                    <?php
                  }
                } 
                
                $conn->close();
                ?>

                <script>
                  const stars = document.querySelectorAll('.star');
                  const ratingValue = document.getElementById('ratingValue');

                  stars.forEach((star, index) => {
                    star.addEventListener('click', () => {
                      ratingValue.value = star.dataset.value;
                      stars.forEach((s, i) => {
                        s.classList.remove('fa-solid');
                        s.classList.add('fa-regular');
                        s.style.color = '#ccc';
                        if (i < star.dataset.value) {
                          s.classList.remove('fa-regular');
                          s.classList.add('fa-solid');
                          s.style.color = '#ffc107';
                        }
                      });
                    });
                  });
                </script>

              </ul>
              <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center mt-3" id="paginationControls">
                  <li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>
                  <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
 

  <!--Book-History Modal Section-->


  <!--Chat Bot-->
  
</body>

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

document.getElementById('appointmentForm').addEventListener('submit', function(event) {
    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;

    if (!latitude || !longitude) {
        alert("Please make sure the location is selected on the map.");
        event.preventDefault();
    }
});


    </script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="../../function/script/pagination-history.js"></script>
<script src="../../function/script/calendar.js"></script>
<script src="../../function/script/toggle-appointment.js"></script>
<script src="../../function/script/tab-bar.js"></script>
<script src="../../function/script/service-dropdown.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</html>
