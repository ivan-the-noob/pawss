<?php
session_start();  // Start the session to store error messages
include '../../../../db.php';

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email is already registered
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Account already exists
        $_SESSION['error'] = "Account already registered with this email.";
        header("Location: sign-up.php");  // Redirect back to sign-up page with error
        exit();
    }

    // Insert the user into the 'users' table if email is not already registered
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        // Insert registration info into the 'global_reports' table
        $registration_time = date("h:i A | m/d/Y"); // Format for displaying registration time
        $message = "User $email registered at $registration_time";
        
        // Insert the log message into global_reports
        $log_sql = "INSERT INTO global_reports (message, cur_time) VALUES (?, NOW())";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("s", $message);
        $log_stmt->execute();
        $log_stmt->close();

        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        // Display error message if registration fails
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: sign-up.php");  // Redirect back to sign-up page with error
        exit();
    }

    // Close prepared statement
    $stmt->close();
    $check_stmt->close();
}

// Close database connection
$conn->close();
?>
