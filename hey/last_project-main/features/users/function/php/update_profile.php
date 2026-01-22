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

// Handle profile picture upload - IMAGES ONLY
$profile_picture_path = NULL;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_picture'];
    
    // Check if file is actually uploaded (not empty)
    if ($file['size'] > 0) {
        // Get file info
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed image extensions
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp');
        
        // Check if file is an image
        if (in_array($file_ext, $allowed_ext)) {
            // Check file size (max 5MB)
            if ($file_size <= 5242880) { // 5MB in bytes
                // Generate unique filename to prevent overwriting
                $new_filename = uniqid('profile_', true) . '_' . $user_id . '.' . $file_ext;
                $target_directory = "../../../../assets/img/";
                $target_file = $target_directory . $new_filename;
                
                // Create directory if it doesn't exist
                if (!is_dir($target_directory)) {
                    mkdir($target_directory, 0755, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $profile_picture_path = $new_filename;
                } else {
                    echo "Failed to move uploaded file.";
                    // Continue with other updates even if image upload fails
                }
            } else {
                echo "File too large. Maximum size is 5MB.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF, WEBP, BMP allowed.";
        }
    }
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

// Prepare and execute update statement
$stmt = $conn->prepare("UPDATE users SET 
    profile_picture = COALESCE(?, profile_picture), 
    latitude = ?, 
    longitude = ?, 
    contact_number = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), contact_number), 
    home_street = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), home_street), 
    address_search = COALESCE(NULLIF(CONVERT(?, CHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci), ''), address_search) 
WHERE id = ?");

$stmt->bind_param("sddsssi", $profile_picture_path, $latitude, $longitude, $contact_number, $home_street, $address_search, $user_id);

if ($stmt->execute()) {
    header('Location: ../../web/api/dashboard.php?reload=1');
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>