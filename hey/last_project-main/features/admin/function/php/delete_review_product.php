<?php
include '../../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        die("No ID provided.");
    }

    $id = intval($_POST['id']); // Ensure it's an integer

    // Delete from correct table (likely "rating")
    $query = "DELETE FROM rating WHERE id = $id LIMIT 1";

    if (mysqli_query($conn, $query)) {
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        echo "Error deleting review: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
