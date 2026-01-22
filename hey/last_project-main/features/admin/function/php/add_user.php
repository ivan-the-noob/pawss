<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../users/web/api/login.php");
    exit();
}

include '../../../../db.php';

$error = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Server-side password validation (in case JavaScript is disabled)
    $uppercase = preg_match('@[A-Z]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);
    
    if (strlen($password) < 8 || !$uppercase || !$specialChars) {
        $_SESSION['error'] = "Password must be at least 8 characters, include one uppercase letter and one special character.";
        header("Location: ../../web/api/admin-user.php");
        exit();
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    $checkEmailStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmailStmt->bind_param('s', $email);
    $checkEmailStmt->execute();
    $result = $checkEmailStmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "The email address is already in use.";
        header("Location: ../../web/api/admin-user.php");
        exit();
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: ../../web/api/admin-user.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: Could not add user.";
            error_log("SQL Error: " . $stmt->error); 
        }
        $stmt->close();
    }

    $checkEmailStmt->close();
}

$conn->close();
?>