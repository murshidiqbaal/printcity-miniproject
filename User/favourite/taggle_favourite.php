<?php
session_start();
include "db.php"; // your DB connection

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Check if already in favourites
$stmt = $conn->prepare("SELECT COUNT(*) FROM favourites WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    // Remove from favourites
    $stmt = $conn->prepare("DELETE FROM favourites WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(["status" => "removed"]);
} else {
    // Add to favourites
    $stmt = $conn->prepare("INSERT INTO favourites (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(["status" => "added"]);
}
?>
