<?php
$conn = new mysqli('localhost', 'root', '', 'printcity');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE product_id = $id")->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
</head>
<body>
  <h2>Edit Product</h2>
  <form action="update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required />
    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required />
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" />
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
    <input type="file" name="image" accept="image/*" />
    <button type="submit">Update</button>
  </form>
</body>
</html>
