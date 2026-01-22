<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim whitespace from all inputs
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $raw_number = trim($_POST['contactNum']);
    $password = $_POST['password']; // Don't trim password as whitespace could be intentional part of it

    // Validate that fields are not empty after trimming
    if (empty($firstname) || empty($lastname) || empty($email) || empty($raw_number) || empty($password)) {
        $_SESSION['error'] = "All fields are required and cannot be just spaces.";
        header("Location: signup.php");
        exit();
    }

    // Validate firstname and lastname don't contain only whitespace
    if (ctype_space($firstname) || ctype_space($lastname)) {
        $_SESSION['error'] = "First name and last name cannot be just spaces.";
        header("Location: signup.php");
        exit();
    }

    $name = $firstname . ' ' . $lastname; // Combine first and last name
    
    // Validate email format and check if it's not just whitespace
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: signup.php");
        exit();
    }

    // Validate contact number - remove any whitespace
    $raw_number = preg_replace('/\s+/', '', $raw_number);
    
    if (preg_match('/^\d{10}$/', $raw_number)) {
        // Add leading zero to make it local Philippine format
        $contact_number = '0' . $raw_number;
    } else {
        $_SESSION['error'] = "Invalid contact number. Please enter exactly 10 digits.";
        header("Location: signup.php");
        exit();
    }

    // Password validation
    $uppercase = preg_match('@[A-Z]@', $password);
    $specialChars = preg_match('@[\W_]@', $password);

    if (strlen($password) < 8 || !$uppercase || !$specialChars) {
        $_SESSION['error'] = "Password must be at least 8 characters, include one uppercase letter and one special character.";
        header("Location: signup.php");
        exit();
    }

    // Check if the email is already registered
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Account already registered with this email.";
        header("Location: signup.php");
        exit();
    }

    // Continue with the rest of your code...
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

         $_SESSION['success'] = "Sign Up Successful! You can now login with your credentials.";

        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: signup.php");
        exit();
    }

    $stmt->close();
    $check_stmt->close();
}

$conn->close();
?>