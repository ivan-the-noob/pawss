<?php
session_start();
require '../../../../db.php'; // Ensure this path is correct and includes db.php with $conn

if (!isset($_SESSION['email'])) {
    echo "User is not logged in.";
    exit;
}

$email = $_SESSION['email'];

// Fetch the user ID and current values based on the email
$result = $conn->query("SELECT id, password, contact_number, home_street, address_search, latitude, longitude, profile_picture FROM users WHERE email = '$email'");
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

$user_id = $user['id'];

// Retrieve and sanitize form data
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$home_street = $_POST['home_street'] ?? '';
$address_search = $_POST['address_search'] ?? ''; // Get the address_search value
$latitude = $_POST['latitude'] ?? 14.2928; // Default to Cavite latitude
$longitude = $_POST['longitude'] ?? 120.8982; // Default to Cavite longitude

// Verify and update profile picture
$profile_picture_path = $_FILES['profile_picture']['name'] ?? NULL;
if ($profile_picture_path) {
    $target_directory = "../../../../assets/img/";
    $target_file = $target_directory . basename($profile_picture_path);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
}

// Change password if current password is correct
if (!empty($current_password) && !empty($new_password)) {
    if (password_verify($current_password, $user['password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'");
    } else {
        echo "Current password is incorrect.";
        exit;
    }
}

$stmt = $conn->prepare("UPDATE users SET 
    profile_picture = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), profile_picture), 
    latitude = ?, 
    longitude = ?, 
    contact_number = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), contact_number), 
    home_street = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), home_street), 
    address_search = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), address_search) 
WHERE id = ?");
$stmt->bind_param("sddsssi", $profile_picture_path, $latitude, $longitude, $contact_number, $home_street, $address_search, $user_id);


if ($stmt->execute()) {
    header('Location: ../../web/api/dashboard.php');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
