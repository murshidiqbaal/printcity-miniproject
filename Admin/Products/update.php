<?php
$conn = new mysqli('localhost', 'root', '', 'printcity');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$desc = $_POST['description'];

if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
  $imagePath = "uploads/" . basename($_FILES["image"]["name"]);
  move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
  $sql = "UPDATE products SET name='$name', category='$category', price='$price', description='$desc', image_path='$imagePath' WHERE product_id=$id";
} else {
  $sql = "UPDATE products SET name='$name', category='$category', price='$price', description='$desc' WHERE product_id=$id";
}

$conn->query($sql);
$conn->close();

header("Location: product.php");
exit();
?>
