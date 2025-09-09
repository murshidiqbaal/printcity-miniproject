<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch total users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $sql_users);
$row_users = mysqli_fetch_assoc($result_users);
$total_users = $row_users['total_users'] ?? 0;

// Fetch total orders
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$result_orders = mysqli_query($conn, $sql_orders);
$row_orders = mysqli_fetch_assoc($result_orders);
$total_orders = $row_orders['total_orders'] ?? 0;

// Fetch total products
$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$result_products = mysqli_query($conn, $sql_products);
$row_products = mysqli_fetch_assoc($result_products);
$total_products = $row_products['total_products'] ?? 0;

// Fetch total revenue (sum of all order amounts)
$sql_revenue = "SELECT SUM(total_price) AS total_revenue FROM orders";
$result_revenue = mysqli_query($conn, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($result_revenue);
$total_revenue = $row_revenue['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PrintCity Dashboard</title>
  <link rel="stylesheet" href="indexAdmin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <div class="container">
    <!-- Fixed Left Nav -->
    <nav class="sidebar">
      <div class="nav-links">
        <a href="#" onclick="showSection('dashboard');" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
        <a href="../Products/product.php"><i class="fas fa-box-open"></i><span>Products</span></a>
        <a href="../Orders/orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
        <a href="../Customers/customers.php"><i class="fas fa-users"></i><span>Customers</span></a>
        <a href="../Settings/settings.php"><i class="fas fa-cog"></i><span>Settings</span></a>
      </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
      <h1>Welcome to PrintCity Admin</h1>
      <p>This is your admin dashboard. Use the navigation bar to switch between sections.</p>

      <div class="dashboard-cards">
        <div class="card" onclick="window.location.href='../Customers/customers.php'">
          <i class="fas fa-users"></i>
          <h3><?php echo number_format($total_users); ?></h3>
          <p>Total Users</p>
        </div>
        <div class="card" onclick="window.location.href='../Orders/orders.php'">
          <i class="fas fa-shopping-cart"></i>
          <h3><?php echo number_format($total_orders); ?></h3>
          <p>Total Orders</p>
        </div>
        <div class="card" onclick="window.location.href='../Products/product.php'">
          <i class="fas fa-box-open"></i>
          <h3><?php echo number_format($total_products); ?></h3>
          <p>Products Available</p>
        </div>
        <div class="card" onclick="window.location.href='../Revenue/revenue.php'">
          <i class="fas fa-dollar-sign"></i>
          <h3>₹<?php echo number_format($total_revenue); ?></h3>
          <p>Total Revenue</p>
        </div>
      </div>
    </main>
  </div>
</body>
</html>


