<?php
session_start();
include '../../../../db.php';

// Use Composer's autoloader
require '../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect if already logged in
if (isset($_SESSION['email'])) {
    header("Location: ../../../../index.php");
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Remove previous tokens for this email
        $conn->query("DELETE FROM password_resets WHERE email = '$email'");

        // Insert new token
        $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $email, $token, $expires);
        $insert->execute();

        // Replace this with your actual domain path
        $resetLink = "https://digitalpaw.icu/features/users/web/api/recover_password.php?token=$token";

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username   = 'ejivancablanida@gmail.com'; // your Gmail
            $mail->Password   = 'uvnm vnfh griv hzlr';       // your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ejivancablanida@gmail.com', 'PAWS System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Click the link below to reset your password:<br><a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $_SESSION['success'] = "A reset link has been sent to your email.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWS | Forgot Password</title>
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
                        <h5 class="mb-3">Forgot Password</h5>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-50 d-flex mx-auto text-center justify-content-center align-items-center">Send Link</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
