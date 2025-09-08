<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Product Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="product.css">
</head>
<body>

  <h2>Product Page</h2>

  <!-- Floating Add Button -->
  <button class="floating-button" onclick="openForm()">+</button>

  <!-- Overlay -->
  <div class="overlay" id="overlay" onclick="closeForm()"></div>

  <!-- Popup Form -->
  <div class="popup-form" id="popupForm">
    <form action="productlog.php" method="POST" enctype="multipart/form-data">
      <h3>Add Product</h3>

      <input type="text" name="name" placeholder="Product Name" required />
      <input type="text" name="category" placeholder="Category" required />
      <input type="file" name="image" accept="image/*" required />

      <input type="number" step="0.01" name="price" placeholder="Price" required />
      <input type="number" step="0.01" name="discount" placeholder="Discount (%)" />

      <input type="number" name="stock" placeholder="Stock Quantity" required />

      <label>
       Mark as Best Seller <input type="checkbox" name="is_best_seller" value="1" />
        
      </label>

      <textarea name="description" placeholder="Description"></textarea>
      <textarea name="features" placeholder="Features (one per line)"></textarea>

      <button type="submit">Add Product</button>
    </form>
  </div>

  <!-- Product List Section -->
  <section id="products">
    <div class="product-list">
      <?php
        // MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'printcity');

        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<div class="product-card">';

            // Product image
            if (!empty($row['image_path']) && file_exists($row['image_path'])) {
              echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Product Image">';
            } else {
              echo '<img src="placeholder.jpg" alt="No image">';
            }

            // Product info
            echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
            echo '<p><strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>';

            // Price + discount
            $price = $row['price'];
            $discount = $row['discount'] ?? 0;
            $finalPrice = $price;
            if ($discount > 0) {
              $finalPrice = $price - ($price * ($discount / 100));
              echo '<p class="price">₹' . number_format($finalPrice, 2) . 
                   ' <span style="text-decoration: line-through; color:#888; font-size:14px;">₹' . number_format($price, 2) . '</span>' . 
                   ' <span style="color:green; font-size:14px;">' . $discount . '% Off</span></p>';
            } else {
              echo '<p class="price">₹' . number_format($price, 2) . '</p>';
            }

            echo '<p><strong>Stock:</strong> ' . intval($row['stock']) . '</p>';
            if (!empty($row['is_best_seller']) && $row['is_best_seller'] == 1) {
              echo '<p style="color:blue; font-weight:bold;">Best Seller</p>';
            }

            echo '<p>' . htmlspecialchars($row['description']) . '</p>';

            if (!empty($row['features'])) {
              echo "<ul>";
              $features = explode("\n", $row['features']);
              foreach ($features as $f) {
                if (trim($f) !== '') echo "<li>" . htmlspecialchars($f) . "</li>";
              }
              echo "</ul>";
            }

            // Edit & Delete buttons
            echo '<form action="edit.php" method="GET" style="display:inline-block;">
                    <input type="hidden" name="id" value="' . $row['product_id'] . '">
                    <button type="submit">Edit</button>
                  </form>';

            echo '<form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                    <input type="hidden" name="id" value="' . $row['product_id'] . '">
                    <button type="submit" class="delete-button">Delete</button>
                  </form>';

            echo '</div>';
          }
        } else {
          echo "<p>No products available.</p>";
        }

        $conn->close();
      ?>
    </div>
  </section>

  <script>
    function openForm() {
      document.getElementById('popupForm').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
    }

    function closeForm() {
      document.getElementById('popupForm').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
  </script>

</body>
</html>
