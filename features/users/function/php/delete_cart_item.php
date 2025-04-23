<?php
header('Content-Type: application/json');
require_once '../../../../db.php';

$data = json_decode(file_get_contents("php://input"), true);
$ids = $data['ids'] ?? null;

if ($ids && is_array($ids)) {
    // Prepare placeholders for the IDs
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("DELETE FROM cart WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid or missing IDs"]);
}
?>
