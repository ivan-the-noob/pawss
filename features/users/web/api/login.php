<?php
session_start();
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
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="error mb-3"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>
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
