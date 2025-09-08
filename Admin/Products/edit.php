<?php
// Fetch product data for the form
$conn = new mysqli('localhost', 'root', '', 'printcity');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;
$product = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();

if (!$product) {
    die("<h2 style='color:red; text-align:center;'>Product not found!</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
    body { background:#f5f7fa; display:flex; justify-content:center; align-items:center; min-height:100vh; }
    .form-container { background:white; padding:25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:450px; }
    h2 { text-align:center; margin-bottom:20px; }
    label { display:block; margin-bottom:5px; font-weight:bold; }
    input, textarea { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px; }
    button { width:100%; padding:10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
    button:hover { background:#0056b3; }
    .preview-img { display:block; margin:10px auto; max-width:150px; border-radius:8px; }
  </style>
  <link rel="stylesheet" href="edit.css">
</head>
<body>
<div class="form-container">
    <h2><i class="fa fa-edit"></i> Edit Product</h2>
    <form action="update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['product_id'] ?>">

        <label>Product Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <label>Price (₹)</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ? htmlspecialchars($product['price']) : "" ?>" required>

        <label>Discount (%)</label>
        <input type="number" step="0.01" name="discount" value="<?= isset($product['discount']) ? htmlspecialchars($product['discount']) : '' ?>">

        <label>Stock Quantity</label>
<input type="number" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '') ?>">

        <label>
            <input type="checkbox" name="is_best_seller" value="1" <?= ($product['is_best_seller'] == 1) ? 'checked' : '' ?>>
            Mark as Best Seller
        </label>

        <label>Features</label>
        <textarea name="features" rows="3"><?= htmlspecialchars($product['features'] ?? '') ?></textarea>

        <label>Description</label>
<textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

        <label>Product Image</label>
        <?php if (!empty($product['image_path'])): ?>
            <img src="<?= htmlspecialchars($product['image_path']) ?>" class="preview-img">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Update Product</button>
    </form>
</div>
</body>
</html>
