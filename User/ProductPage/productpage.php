<!-- productpage.php -->
<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<section id="products" style="padding: 40px; background-color: #f9f9f9;">
  <h2 style="text-align:center; font-size: 32px; margin-bottom: 20px;">Explore Our Products</h2>
  <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <div style="width: 220px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; text-align: center; cursor: pointer;" onclick="location.href='../OrderPage/orderpage.php?product_id=<?= $row['product_id'] ?>'">
<img src="/miniproject/<?= $row['image_path'] ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 100%; height: 150px; object-fit: cover;">
        <div style="padding: 10px;">
          <h3 style="margin: 10px 0;"><?= htmlspecialchars($row['name']) ?></h3>
          <p style="margin: 5px 0;"><?= htmlspecialchars($row['category']) ?></p>
          <p style="color: green; font-weight: bold;">₹<?= number_format($row['price'], 2) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php
mysqli_close($conn);
?>
