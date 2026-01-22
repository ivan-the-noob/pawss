<?php
session_start();
require '../../../../db.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$action = $_POST['action'] ?? '';

if (!$email || !in_array($action, ['ban', 'unban'])) {
    die('Invalid request');
}

$banStatus = ($action === 'ban') ? 1 : 0;

$stmt = $conn->prepare("UPDATE users SET is_ban = ? WHERE email = ?");
$stmt->bind_param("is", $banStatus, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $msg = ($banStatus === 1) ? "banned" : "unbanned";
    header("Location: ../../web/api/users.php?status=$msg");
    exit();
} else {
    header("Location: ../../web/api/users.php?status=error");
    exit();
}
?>
