<!-- order.php -->
<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        echo "<p>Product not found!</p>";
        exit;
    }
} else {
    echo "<p>No product selected!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order - <?= htmlspecialchars($product['name']) ?></title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 30px; }
    .order-box {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    img { width: 100%; height: 250px; object-fit: cover; border-radius: 10px; }
    input, textarea {
      width: 100%;
      margin-top: 10px;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background-color: #28a745;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      margin-top: 15px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<div class="order-box">
  <h2>Order: <?= htmlspecialchars($product['name']) ?></h2>
  <img src="/miniproject/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
  <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
  <p><strong>Price:</strong> ₹<?= number_format($product['price'], 2) ?></p>

<form method="post" action="submitorder.php">
    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
    <input type="hidden" name="price" value="<?= $product['price'] ?>"> <!-- Pass price -->

    <label>Your Name:</label>
    <input type="text" name="customer_name" required>

    <label>Your Address:</label>
    <textarea name="address" required></textarea>

    <label>Quantity:</label>
    <input type="number" name="quantity" min="1" value="1" required>

    <button type="submit">Place Order</button>
</form>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
