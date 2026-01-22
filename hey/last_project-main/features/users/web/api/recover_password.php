<?php
session_start();
include '../../../../db.php';

if (isset($_SESSION['email'])) {
    header("Location: ../../../../index.php");
    exit();
}

// Token validation
$token = $_GET['token'] ?? '';
$showForm = false;
$email = '';

if ($token) {
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $showForm = true;
    } else {
        $_SESSION['error'] = "The reset link is invalid or has expired.";
    }
}

// Handle new password submission
// Handle new password submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'], $_POST['token'])) {
    $password = $_POST['password'];
    $token = $_POST['token'];

    // Validate password strength
    if (!preg_match('/^(?=.*[A-Z]).{8,}$/', $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one uppercase letter.";
        header("Location: recover_password.php?token=" . urlencode($token));
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashedPassword, $email);
        $update->execute();

        $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $delete->bind_param("s", $email);
        $delete->execute();

        $_SESSION['success'] = "Password changed successfully. You can now log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired token.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PAWS | Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <h5 class="mb-3">Reset Password</h5>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <?php if ($showForm): ?>
                        <form method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="mb-3">
                              <input type="password" name="password" class="form-control" placeholder="Enter new password"
                                pattern="(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters and include at least 1 uppercase letter" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-50 d-flex mx-auto text-center justify-content-center align-items-center">Reset Password</button>
                        </form>
                    <?php else: ?>
                        <div class="text-center">
                            <a href="forgot_password.php" class="btn btn-link">Request another link</a>
                        </div>
                    <?php endif; ?>

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
