<?php
$conn = new mysqli('localhost', 'root', '', 'printcity');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];

// Optional: Delete the image file from server
$getImage = $conn->query("SELECT image_path FROM products WHERE product_id = $id");
if ($getImage && $getImage->num_rows > 0) {
  $img = $getImage->fetch_assoc()['image_path'];
  if (file_exists($img)) {
    unlink($img);
  }
}

$sql = "DELETE FROM products WHERE product_id = $id";
$conn->query($sql);

$conn->close();

header("Location: product.php"); // Redirect back
exit();
?>
