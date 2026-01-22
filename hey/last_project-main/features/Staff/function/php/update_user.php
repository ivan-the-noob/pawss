<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update the user's data
    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $userId);

    if ($stmt->execute()) {
        header("Location: ../../web/api/users.php?updated=success");
    } else {
        header("Location: your_page.php?msg=Error updating user");
    }
}
?>
