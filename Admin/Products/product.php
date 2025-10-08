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


  <a href="../HomeScreen/indexAdmin.php" 
     style="position: absolute; top: 15px; left: 15px; font-size: 1.5rem; color: black; text-decoration: none;">
    <i class="fas fa-arrow-left"></i>
  </a>

 <!-- Heading Center -->
    <h2 class="mb-0">Product Page</h2>



  <!-- Floating Add Button -->
  <button class="floating-button" onclick="openForm()">+</button>

  <!-- New Add Offer Product Button -->
<button class="floating-button offer" title="Add Offer Product" onclick="openForm('offer')" style="right: 80px; background: #10b981;">
  <i class="fas fa-tags" style="font-size: 24px; color: white;"></i>
</button>


  <!-- Overlay -->
 <div class="overlay" id="overlay" onclick="closeForms()"></div>

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

  <!-- Popup Form for Offer Product -->
<div class="popup-form" id="popupFormOffer" style="display:none;">
  <form action="offerproductlog.php" method="POST" enctype="multipart/form-data">
    <h3>Add Offer Product</h3>

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

    <button type="submit">Add Offer Product</button>
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

  <!-- Offer Product List Section -->
<section id="offer-products" style="margin-top: 50px;">
  <h2>Offer Products</h2>
  <div class="product-list">
    <?php
      // Reuse the existing connection or create a new one
      $conn = new mysqli('localhost', 'root', '', 'printcity');
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql_offer = "SELECT * FROM offer_products ORDER BY created_at DESC";
      $result_offer = $conn->query($sql_offer);

      if ($result_offer->num_rows > 0) {
        while ($row = $result_offer->fetch_assoc()) {
          echo '<div class="product-card">';

          // Offer product image
    if (!empty($row['image_path'])) {
  echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Offer Product Image">';
} else {
  echo '<img src="placeholder.jpg" alt="No image">';
}


          // Offer product info
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

          // Edit & Delete buttons for offer products
          echo '<form action="offer_edit.php" method="GET" style="display:inline-block;">
                  <input type="hidden" name="id" value="' . $row['offer_product_id'] . '">
                  <button type="submit">Edit</button>
                </form>';

          echo '<form action="offer_delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                  <input type="hidden" name="id" value="' . $row['offer_product_id'] . '">
                  <button type="submit" class="delete-button">Delete</button>
                </form>';

          echo '</div>';
        }
      } else {
        echo "<p>No offer products available.</p>";
      }

      $conn->close();
    ?>
  </div>
</section>


  <script>
  function openForm(type) {
  if (type === 'product') {
    document.getElementById('popupForm').style.display = 'block';
  } else if (type === 'offer') {
    document.getElementById('popupFormOffer').style.display = 'block';
  }
  document.getElementById('overlay').style.display = 'block';
}

function closeForms() {
  document.getElementById('popupForm').style.display = 'none';
  document.getElementById('popupFormOffer').style.display = 'none';
  document.getElementById('overlay').style.display = 'none';
}

  </script>

</body>
</html>
