<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id'] ?? 0);
$status = $data['status'] ?? '';
$order_type = $data['order_type'] ?? 'regular';

if ($order_type === 'regular') {
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
} else {
    $stmt = $conn->prepare("UPDATE custom_orders SET status=? WHERE id=?");
}

if ($stmt) {
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
}

$conn->close();
?>
