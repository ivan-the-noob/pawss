<?php
// Start session and check admin authentication
session_start();

// Include database connection
require_once('../../../../db.php');

// Check if ID is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $contact_id = (int)$_POST['id'];
    
    try {
        // Prepare and execute delete statement
        $stmt = $conn->prepare("DELETE FROM contact WHERE id = ?");
        $stmt->bind_param("i", $contact_id);
        
        if ($stmt->execute()) {
            // Success - redirect with success message
            header("Location: ../../web/api/contact-section.php?status=success&message=Contact+message+deleted");
        } else {
            // Error - redirect with error message
            header("Location: ../../web/api/contact-section.php?status=error&message=Failed+to+delete+contact+message");
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // Database error - redirect with error message
        header("Location: ../../web/api/contact-section.php?status=error&message=Database+error");
    }
} else {
    // Invalid request - redirect with error message
    header("Location: ../../web/api/contact-section.php?status=error&message=Invalid+request");
}

// Close connection
$conn->close();
exit();
?>