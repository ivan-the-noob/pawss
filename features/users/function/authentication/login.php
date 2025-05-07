<?php

include '../../../../db.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to check if the user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? null;
            $_SESSION['role'] = $user['role'];

            // Record login in global_reports
            $message = ucfirst($user['role']) . " $email logged in at " . $login_time;
            
            // Insert login details into global_reports (including current time)
            $log_sql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("s", $message);
            $log_stmt->execute();
            $log_stmt->close();

            // Redirect based on user role
            if ($user['role'] === 'user') {
                header("Location: ../../../../index.php");
            } else {
                header("Location: ../../../../features/admin/web/api/admin.php");
            }
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Invalid credentials.";
        }
    } else {
        // User not found
        $_SESSION['error'] = "Invalid credentials.";
    }

    // Close statement and connection
    $stmt->close();
}

$conn->close();
?>
