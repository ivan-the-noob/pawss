<?php
session_start(); // Make sure session_start() is at the top
include '../../../../db.php';
include '../../function/authentication/login.php';

if (isset($_SESSION['email'])) {
    header("Location: ../../../../index.php");
    exit(); 
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWS| Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="icon" href="../../../../assets/img/logo.png" type="image/x-icon">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="row login-container">
                    <div class="col-md-5 login-left text-center">
                        <img src="../../../../assets/img/logo.png" alt="Logo">
                    </div>
                    <div class="col-md-7 login-right">
                        <h5 class="mb-3">Login</h5>
                        
                        <!-- Display success message if exists -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']); // Clear the message after displaying
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Display error message if exists -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']); // Clear the message after displaying
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php">
                            <div class="mb-1">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="text-end">
                                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                            </div>
                          
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="showPassword">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                            <div class="text-center mt-3">
                                <a href="signup.php">Don't have an account? <span class="sign-up">Sign Up</span></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('showPassword').addEventListener('change', function() {
            var passwordInput = document.querySelector('input[name="password"]');
            if (this.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });
    </script>
</body>
</html>