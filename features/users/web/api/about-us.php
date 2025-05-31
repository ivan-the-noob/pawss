
<?php 
session_start();

require '../../../../db.php';
if (isset($_SESSION['email']) && isset($_SESSION['profile_picture'])) {
    $email = $_SESSION['email'];
    $profile_picture = $_SESSION['profile_picture'];
} else {
    header("Location: features/users/web/api/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | DIGITAL PAWS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">

  <link rel="stylesheet" href="../../css/about-us.css">

</head>


<body>
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
                        <li class="nav-item">
                            <a class="nav-link" href="#">About Us</a>
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
    <!--About Us Section-->
  <section class="about-us py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <img src="../../../../assets/img/map.PNG" alt="Map Image" class="img-fluid">
          <h3 class="mt-4">About Us</h3>
          <p class="about-text">
            <span class="font-weight-bold">The Happy Vet Animal Clinic</span> is an animal care facility
            dedicated to providing high customer satisfaction by rendering quality pet care while furnishing a fun,
            clean, thematic, enjoyable atmosphere at an acceptable price. Our experienced team is passionate about
            animals and committed to their well-being. We offer a range of services tailored to meet the unique needs of
            each pet, ensuring they leave happy and healthy.
          </p>
        </div>
      </div>
    </div>
    <div class="contact">
      <div class=" text-center my-5">
        <div class="row mt-4">
          <div class="col-lg-2 col-md-12 mb-4 d-flex flex-column align-items-center">
            <div class="contact-card">
              <div class="contact-icon">
                <img src="../../../../assets/svg/call-icon.svg" alt="Call Icon">
              </div>
              <div class="contact-title">Call</div>
              <div class="contact-info">4091254</div>
            </div>
          </div>

          <div class="col-lg-2 col-md-12 mb-4 d-flex flex-column align-items-center">
            <div class="contact-card">
              <div class="contact-icon">
                <img src="../../../../assets/svg/email-icon.svg" alt="Email Icon">
              </div>
              <div class="contact-title">Email</div>
              <div class="contact-info">happyvetanimalclinic@gmail.com</div>
            </div>
          </div>
          <div class="col-lg-2 col-md-12 mb-4 d-flex flex-column align-items-center">
            <div class="contact-card">
              <div class="contact-icon">
                <img src="../../../../assets/svg/location-icon.svg" alt="Location Icon">
              </div>
              <div class="contact-title">Location</div>
              <div class="contact-info">
                <p>Zoneth Commercial Building, Unit E, Purok 9, Governor's drive, San Agustin, Trece Martires, Philippines, 4109</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
    <!--About Us Section End-->
  <div class="wave-container1" id="about-us">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 160" class="wave1">
      <path fill="#7A3015" fill-opacity="1"
        d="M0,80L40,72C80,64,160,48,240,56C320,64,400,96,480,98.65C560,101.5,640,74.5,720,69.35C800,64,880,80,960,77.35C1040,74.5,1120,53.5,1200,48C1280,42.5,1360,53.5,1400,58.65L1440,64L1440,160L1400,160C1360,160,1280,160,1200,160C1120,160,1040,160,960,160C880,160,800,160,720,160C640,160,560,160,480,160C400,160,320,160,240,160C160,160,80,160,40,160L0,160Z">
      </path>
    </svg>
  </div>

     <!--Why Choose Us Section-->
  <section class="why-choose-us py-5">
    <div class="container">
      <h3>Heres A Reason Why Choose Us</h3>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="icon mb-3">
                <i class="fas fa-heart fa-2x" style="color: #7A3015;"></i>
              </div>
              <h5 class="card-title h-5">Expert Care & Compassion</h5>
              <p class="card-text">Our experienced team is passionate about animals and dedicated to providing top-notch
                care. We treat every pet like our own, ensuring they receive the love and attention they deserve.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="icon mb-3">
                <i class="fas fa-paw fa-2x" style="color: #7A3015;"></i>
              </div>
              <h5 class="card-title">Comprehensive Services</h5>
              <p class="card-text">We offer a wide range of services, from grooming to veterinary care, all under one
                roof. Whether your pet needs a spa day, a routine check-up, or specialized treatment, we've got you
                covered.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <div class="icon mb-3">
                <i class="fas fa-home fa-2x" style="color: #7A3015;"></i>
              </div>
              <h5 class="card-title">Friendly & Safe Environment</h5>
              <p class="card-text">Our facility is designed to be a welcoming and safe space for pets and their owners.
                We prioritize cleanliness and create a stress-free atmosphere, making your pet's visit comfortable and
                enjoyable.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
    <!--Why Choose Us Section End-->
  <div class="wave-container">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 160" class="wave2">
      <path fill="#7A3015" fill-opacity="1"
        d="M0,80L40,72C80,64,160,48,240,56C320,64,400,96,480,98.65C560,101.5,640,74.5,720,69.35C800,64,880,80,960,77.35C1040,74.5,1120,53.5,1200,48C1280,42.5,1360,53.5,1400,58.65L1440,64L1440,160L1400,160C1360,160,1280,160,1200,160C1120,160,1040,160,960,160C880,160,800,160,720,160C640,160,560,160,480,160C400,160,320,160,240,160C160,160,80,160,40,160L0,160Z">
      </path>
    </svg>
  </div>
  <section class="discount">
    <h3 class="text-center">Get 5% OFF On All Services Today!</h3>
    <a href="#"><button>Book Now!</button></a>
  </section>
  <div class="wave-container1" id="about-us">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 160" class="wave1">
      <path fill="#7A3015" fill-opacity="1"
        d="M0,80L40,72C80,64,160,48,240,56C320,64,400,96,480,98.65C560,101.5,640,74.5,720,69.35C800,64,880,80,960,77.35C1040,74.5,1120,53.5,1200,48C1280,42.5,1360,53.5,1400,58.65L1440,64L1440,160L1400,160C1360,160,1280,160,1200,160C1120,160,1040,160,960,160C880,160,800,160,720,160C640,160,560,160,480,160C400,160,320,160,240,160C160,160,80,160,40,160L0,160Z">
      </path>
    </svg>
  </div>
    <!--Footer-->
    <footer class="footer" id="reviews">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Happy Vet Animal Clinic </h5>
                    <ul class="list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Our Services</a></li>
                        <li><a href="#review">Review</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <ul class="list-unstyled">
                        <li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i>
                                Facebook</a></li>
                        <li><a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i>
                                Instagram</a></li>
                        <li><a href="https://youtube.com" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>Email: happyvetanimalclinic@gmail.com</p>
                    <p>Phone: 4091254</p>
                </div>
            </div>
            <div class="row">
                <div class="col text-center">
                    <p>&copy; 2024 Happy Vet Animal Clinic. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!--Footer End-->

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
<script src="../../function/script/chat-bot.js"></script>
<script src="../../function/script/chatbot_questionslide.js"></script>
<script src="../../function/script/chatbot-toggle.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</html>