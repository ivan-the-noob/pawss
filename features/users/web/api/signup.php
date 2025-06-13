<?php
session_start();
include '../../../../db.php';
include '../../function/authentication/sign-up.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWS | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/signup.css">
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
                        <h5 class="mb-3">Sign Up</h5>
                        <form method="POST" action="signup.php">
                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="forgot-password">
                                
                            </div>
                            <div class="mb-3">
                                <input type="text" name="contact_number" class="form-control" placeholder="Enter contact number" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="showPassword">
                                <label class="form-check-label" for="showPassword">Show Password</label>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Sign Up</button>
                            <div class="text-center mt-3">
                                <a href="login.php">Have an account? <span class="sign-up">Login</span></a>
                            </div>
                            <div class="forgot-password">
                             
                            </div>
                        </form>
                        <script>
                            document.getElementById('showPassword').addEventListener('change', function () {
                                const passwordInput = document.getElementById('password');
                                passwordInput.type = this.checked ? 'text' : 'password';
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ivan updated this on June 13 -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
