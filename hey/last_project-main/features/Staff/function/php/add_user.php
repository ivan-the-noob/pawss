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
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $role = $_POST['role'];

    $checkEmailStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmailStmt->bind_param('s', $email);
    $checkEmailStmt->execute();
    $result = $checkEmailStmt->get_result();

    if ($result->num_rows > 0) {
        $error = "The email address is already in use.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $password, $role);

        if ($stmt->execute()) {
            $successMessage = "User added successfully!";
            header("Location: ../../web/api/admin-user.php");
            exit();
        } else {
            $error = "Error: Could not add user. " . $stmt->error;
            error_log($error); 
        }
        $stmt->close();
    }

    $checkEmailStmt->close();
}

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE role IN ('admin', 'staff')");
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $error = "Error: " . $e->getMessage();
    error_log($error); 
}

$conn->close();
?>
