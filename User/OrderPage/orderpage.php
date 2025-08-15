<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$user_query = "SELECT * FROM user_profiles WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Validate profile details
$valid_profile = !empty($user['full_name']) && !empty($user['email']) && !empty($user['phone']) && !empty($user['address']);

// Fetch selected product
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


// If profile is incomplete, redirect
if (!$valid_profile) {
    header("Location: profile.php?error=Please complete your profile before placing an order.");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order - <?= htmlspecialchars($product['name']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px; }
    .order-box {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    img { width: 100%; height: 250px; object-fit: cover; border-radius: 10px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, textarea {
      width: 100%;
      margin-top: 5px;
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
      font-size: 16px;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<div class="order-box">
  <h2>Order: <?= htmlspecialchars($product['name']) ?></h2>
  <img src="/miniproject/Products/uploads/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

  <form method="post" action="submitorder.php">
      <!-- Hidden product_id for backend -->
      <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

      <!-- Product Name (readonly) -->
      <label>Product Name:</label>
      <input type="text" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" readonly>

      <!-- Amount input (manual entry) -->
      <label>Amount:</label>
      <input type="number" name="amount" min="1" placeholder="Enter amount" required>

      <button type="submit">Place Order</button>
  </form>
</div>


</body>
</html>

<?php mysqli_close($conn); ?>
