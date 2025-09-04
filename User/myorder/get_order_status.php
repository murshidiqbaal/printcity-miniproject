<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die(json_encode(["error" => "DB connection failed"]));
}

$order_id = intval($_GET['order_id'] ?? 0);

$query = $conn->query("SELECT status FROM orders WHERE order_id = $order_id LIMIT 1");
$order = $query->fetch_assoc();
$current_status = strtolower($order['status'] ?? '');

$result = $conn->query("SELECT status, updated_at FROM order_status_history WHERE order_id = $order_id ORDER BY updated_at ASC");
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[strtolower($row['status'])] = $row['updated_at'];
}

echo json_encode([
    "current_status" => $current_status,
    "history" => $history
]);
?>
