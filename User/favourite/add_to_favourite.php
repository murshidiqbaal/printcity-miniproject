<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// check if already favorited
$check = $conn->prepare("SELECT id FROM favourites WHERE user_id=? AND product_id=?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // remove favourite
    $del = $conn->prepare("DELETE FROM favourites WHERE user_id=? AND product_id=?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    echo json_encode(["status" => "removed"]);
} else {
    // add favourite
    $insert = $conn->prepare("INSERT INTO favourites (user_id, product_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
    echo json_encode(["status" => "added"]);
}
