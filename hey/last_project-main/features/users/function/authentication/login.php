<?php
session_start();
include '../../../../db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ((int)$user['is_ban'] === 1) {
            $_SESSION['error'] = "Account disabled. Contact administrator.";
        } else if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? null;
            $_SESSION['role'] = $user['role'];

            // Log the login
            $login_time = date('Y-m-d H:i:s');
            $message = ucfirst($user['role']) . " $email logged in at $login_time";
            $log_sql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("s", $message);
            $log_stmt->execute();
            $log_stmt->close();

            // Redirect by role
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../../../../features/admin/web/api/admin.php");
                    break;
                case 'staff':
                    header("Location: ../../../../features/Staff/web/api/admin.php");
                    break;
                case 'user':
                default:
                    header("Location: ../../../../index.php");
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid credentials.";
        }
    } else {
        $_SESSION['error'] = "Invalid credentials.";
    }

    $stmt->close();
}

$conn->close();
?>
