<?php
session_start();
include '../../../../db.php';

if (isset($_SESSION['email'])) {
  $email = $_SESSION['email'];

  // Update query to fetch latitude, longitude, and other details
  $query = "SELECT name, latitude, longitude, contact_number, home_street, address_search, profile_picture FROM users WHERE email = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($name, $latitude, $longitude, $contact_number, $home_street, $address_search, $profile_picture);
  $stmt->fetch();
  $stmt->close();

  if (empty($latitude) || empty($longitude)) {
    // If latitude or longitude is empty, set default coordinates
    $latitude = 14.2928;  // Default latitude for Happy Vet Animal Clinic
    $longitude = 120.8982;  // Default longitude for Happy Vet Animal Clinic
  }
} else {
  echo "User not logged in.";
  exit;
}

// Pass latitude and longitude to JavaScript
echo "<script>
        var userLatitude = $latitude;
        var userLongitude = $longitude;
      </script>";
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DASHBOARD | DIGITAL PAWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">

</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand d-none d-md-block" href="#">
        <img src="../../../../assets/img/logo.png" alt="Logo" width="30" height="30">
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
          style="stroke: black; fill: none;">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
          </path>
        </svg>

      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="../../../../index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Profile</a>
          </li>
          
        </ul>

        <div class="d-flex ml-auto">
          <div class="dropdown">
            <button class=" dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <img src="../../../../assets/img/<?= htmlspecialchars($profile_picture) ?>" class="rounded-circle profile" alt="Profile Picture">
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="#">Profile</a>
              <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <!--Dashboard Section-->

  <div class="container custom-container mt-auto">
    <h1 class="text-center mb-4">Profile</h1>
    <div class="row justify-content-center">
      <!-- Left Side: Profile Information -->
      <div class="text-center mb-4">
        <img src="../../../../assets/img/<?= htmlspecialchars($profile_picture) ?>" class="rounded-circle" alt="Profile Picture"
          style="width: 150px; height: 150px;">
        <h4 class="mt-3"><?= htmlspecialchars($name) ?></h4>
      </div>

      <form action="../../function/php/update_profile.php" method="POST" enctype="multipart/form-data"
        class="row justify-content-center">
        <!-- Profile Picture and Password Fields -->
        <div class="col-12 col-md-4">
          <div class="mb-4">
            <label for="changeProfile" class="form-label">Change Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control" id="changeProfile">
          </div>
          <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" id="currentPassword"
              placeholder="Enter current password">
          </div>
          <div class="mb-4">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" id="newPassword"
              placeholder="Enter new password">
          </div>
        </div>

        <!-- Address and Contact Fields -->
        <div class="col-12 col-md-4">
          <div class="mb-4">
            <label for="addressSearch" class="form-label">Search for Address</label>
            <input type="text" name="address_search" id="addressSearch" class="form-control"
              placeholder="Enter address within Cavite" value="<?= htmlspecialchars($address_search) ?>">
          </div>

          <!-- Google Maps API integration -->
          <div id="map" style="height: 300px; width: 100%;"></div>

          <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($latitude) ?>">
          <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($longitude) ?>">

          <div class="mb-4">
            <label for="contactNumber" class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control" id="contactNumber"
              placeholder="Enter contact number" value="<?= htmlspecialchars($contact_number) ?>">
          </div>
          <div class="mb-2">
            <label for="addressSearch" class="form-label">Home Street</label>
            <input type="text" name="home_street" class="form-control" id="addressSearch"
              placeholder="Enter address" value="<?= htmlspecialchars($home_street) ?>">
          </div>
          <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
              <button type="submit" class="btn btn-primary mt-4 w-100">Save</button>
            </div>
          </div>

          <script>
            function initMap() {
              // Check if userLatitude and userLongitude are defined, otherwise use default coordinates
              const latitude = typeof userLatitude !== 'undefined' ? userLatitude : 14.2928;
              const longitude = typeof userLongitude !== 'undefined' ? userLongitude : 120.8982;

              // Initialize the map with the dynamic or default coordinates
              const map = new google.maps.Map(document.getElementById('map'), {
                center: {
                  lat: latitude,
                  lng: longitude
                },
                zoom: 16, // Set a higher zoom level to include more details
                mapTypeId: 'roadmap',
              });

              // Create autocomplete input
              const input = document.getElementById('addressSearch');

              // Create autocomplete without restrictions
              const autocomplete = new google.maps.places.Autocomplete(input, {
                fields: ['geometry', 'name', 'formatted_address'] // Added fields
              });

              const marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29),
                draggable: true,
                visible: false
              });

              const infowindow = new google.maps.InfoWindow();

              // When a place is selected in the autocomplete search box
              autocomplete.addListener('place_changed', function() {
                infowindow.close();
                const place = autocomplete.getPlace();

                // Check if the selected place has geometry
                if (place.geometry) {
                  // Set the marker position and visibility
                  marker.setPosition(place.geometry.location);
                  marker.setVisible(true);

                  // Center the map on the location
                  if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                  } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15); // Set zoom level to 15 for better visibility
                  }

                  // Update latitude and longitude fields
                  document.getElementById('latitude').value = place.geometry.location.lat();
                  document.getElementById('longitude').value = place.geometry.location.lng();

                  const address = place.formatted_address || '';
                  infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                  infowindow.open(map, marker);
                } else {
                  console.log("No details available for input: '" + place.name + "'");
                }
              });

              // Handle marker drag events to update latitude and longitude fields
              marker.addListener('dragend', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                // Update the hidden fields for latitude and longitude
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                infowindow.setContent('<div>Your selected location:<br>' + lat + ', ' + lng +
                  '</div>');
                infowindow.open(map, marker);
              });

              // Center the marker on the initial location (user's coordinates or default)
              marker.setPosition(new google.maps.LatLng(latitude, longitude));
              marker.setVisible(true);
            }
          </script>


          <!-- Load Google Maps API asynchronously with a callback -->
          <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places&callback=initMap"
            async defer></script>







          <!--Dashboard Section End-->



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
                <button>How to book?</button>
                <button>?</button>
                <button>How to book?</button>
                <button>How to book?</button>
                <button>How to book?</button>
                <button>How to book?</button>
              </div>
            </div>
            <div class="line"></div>
            <div class="admin mt-3">
              <div class="admin-chat">
                <img src="../../../../assets/img/vet logo.jpg" alt="Admin">
                <p>Admin</p>
              </div>
              <p class="text">Hello, I am Chat Bot. Please Ask me a question just by pressing the question
                buttons.</p>
            </div>
          </div>
          <!--Chat Bot End-->


</body>
<script src="../../function/script/chatbot_questionslide.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</html>