<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all products
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<section id="products" style="padding: 40px; background-color: #f5f7fa;">
    <h2 style="text-align:center; font-size: 32px; margin-bottom: 30px; color: #000000ff;">
        🌟 Explore Our Products
    </h2>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div style="
                width: 240px; 
                border: 1px solid #e0e0e0; 
                border-radius: 12px; 
                box-shadow: 0 4px 10px rgba(0,0,0,0.08); 
                overflow: hidden; 
                background: white; 
                text-align: center; 
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                cursor: pointer;
            " 
            onclick="location.href='../OrderPage/orderpage.php?product_id=<?= urlencode($row['product_id']) ?>'"
            onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)';"
            onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.08)';"
            >
                <img src="/miniproject/Admin/Products/<?= htmlspecialchars($row['image_path']) ?>" 
                     alt="<?= htmlspecialchars($row['name']) ?>" 
                     style="width: 100%; height: 160px; object-fit: cover;">
                <div style="padding: 15px;">
                    <h3 style="margin: 10px 0; font-size: 18px; font-weight: bold;"><?= htmlspecialchars($row['name']) ?></h3>
                    <p style="margin: 5px 0; color: #777;"><?= htmlspecialchars($row['category']) ?></p>
                    <p style="color: #28a745; font-size: 16px; font-weight: bold;">₹<?= number_format($row['price'], 2) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php mysqli_close($conn); ?>
