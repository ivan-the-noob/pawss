<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $contact_id = (int)$_POST['contact_id'];

    // Insert into notification table
    $insert = "INSERT INTO notification (email, message) VALUES ('$email', '$message')";
    mysqli_query($conn, $insert);

    // Update contact status to 0
    $update = "UPDATE contact SET status = 0 WHERE id = $contact_id";
    mysqli_query($conn, $update);

    // Redirect back (adjust path if needed)
    header("Location: ../../web/api/contact-section.php?success=reply_sent");
    exit();
}
?>
