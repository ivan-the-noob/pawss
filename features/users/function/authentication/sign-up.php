<?php
include '../../../../db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];

 

    // Check if the email is already registered
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Account already registered with this email.";
        header("Location: sign-up.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $sql = "INSERT INTO users (name, email, contact_number, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $contact_number, $hashed_password);

    if ($stmt->execute()) {
        $registration_time = date("h:i A | m/d/Y");
        $message = "User $email registered at $registration_time";

        $log_sql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("s", $message);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: sign-up.php");
        exit();
    }

    $stmt->close();
    $check_stmt->close();
}

$conn->close();
?>
