<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // Ensure it's an integer

    $query = "DELETE FROM review WHERE id = $id LIMIT 1";

    if (mysqli_query($conn, $query)) {
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        echo "Error deleting review: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
