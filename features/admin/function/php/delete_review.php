<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);

    $query = "DELETE FROM review WHERE email = '$email' AND review = '$review' LIMIT 1";

    if (mysqli_query($conn, $query)) {
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        echo "Error deleting review: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
