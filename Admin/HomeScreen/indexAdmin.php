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

// Fetch total orders and grouped order count per month for chart
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$result_orders = mysqli_query($conn, $sql_orders);
$row_orders = mysqli_fetch_assoc($result_orders);
$total_orders = $row_orders['total_orders'] ?? 0;

// Orders by month for chart
$sql_orders_month = "SELECT MONTH(order_date) AS month, COUNT(*) AS orders_count FROM orders GROUP BY MONTH(order_date)";
$result_orders_month = mysqli_query($conn, $sql_orders_month);
$orders_by_month = [];
for ($i=1; $i<=12; $i++) {
    $orders_by_month[$i] = 0;
}
while ($row = mysqli_fetch_assoc($result_orders_month)) {
    $orders_by_month[(int)$row['month']] = (int)$row['orders_count'];
}

// Fetch total products and grouped product count by category for pie chart
$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$result_products = mysqli_query($conn, $sql_products);
$row_products = mysqli_fetch_assoc($result_products);
$total_products = $row_products['total_products'] ?? 0;

$sql_products_cat = "SELECT category, COUNT(*) AS count FROM products GROUP BY category";
$result_products_cat = mysqli_query($conn, $sql_products_cat);
$products_category = [];
while ($row = mysqli_fetch_assoc($result_products_cat)) {
    $products_category[$row['category']] = (int)$row['count'];
}

// Fetch total revenue (sum of all order amounts), also revenue by month for chart
$sql_revenue = "SELECT SUM(total_price) AS total_revenue FROM orders";
$result_revenue = mysqli_query($conn, $sql_revenue);
$row_revenue = mysqli_fetch_assoc($result_revenue);
$total_revenue = $row_revenue['total_revenue'] ?? 0;

$sql_revenue_month = "SELECT MONTH(order_date) AS month, SUM(total_price) AS revenue_month FROM orders GROUP BY MONTH(order_date)";
$result_revenue_month = mysqli_query($conn, $sql_revenue_month);
$revenue_by_month = [];
for ($i=1; $i<=12; $i++) {
    $revenue_by_month[$i] = 0;
}
while ($row = mysqli_fetch_assoc($result_revenue_month)) {
    $revenue_by_month[(int)$row['month']] = (float)$row['revenue_month'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PrintCity Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    /* Reset & Fonts */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background: #121721;
      color: #eceff4;
      overflow-x: hidden;
    }
    a {
      text-decoration: none;
      color: inherit;
    }

    /* Container */
    .container {
      display: flex;
      height: 100vh;
      width: 100vw;
    }

    /* Sidebar */
    nav.sidebar {
      width: 260px;
      background: linear-gradient(145deg, #1e2733, #2b3945);
      box-shadow: 0 0 40px #0f141a85;
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      position: fixed;
      height: 100%;
      left: 0;
      transition: width 0.4s ease;
      border-top-right-radius: 30px;
      border-bottom-right-radius: 30px;
    }
    nav.sidebar .nav-links a {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 14px 30px;
      font-size: 17px;
      color: #a8b3c5;
      font-weight: 600;
      border-radius: 14px 0 0 14px;
      margin-bottom: 12px;
      position: relative;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    nav.sidebar .nav-links a i {
      font-size: 20px;
    }
    nav.sidebar .nav-links a.active,
    nav.sidebar .nav-links a:hover {
      color: #fff;
      background: linear-gradient(65deg, #00c6ff, #0072ff);
      box-shadow: 0 0 15px #00aaff80;
      border-left: 4px solid #00d0ff;
      padding-left: 26px;
    }

    /* Main Content */
    main.main-content {
      margin-left: 260px;
      padding: 40px 60px;
      flex-grow: 1;
      background: linear-gradient(145deg, #222831, #1f2733);
      overflow-y: auto;
      height: 100vh;
      border-top-left-radius: 30px;
      border-bottom-left-radius: 30px;
      box-shadow: inset 0 0 40px #000000a1;
    }
    main.main-content h1 {
      font-weight: 700;
      font-size: 2.8rem;
      margin-bottom: 0.25rem;
      background: linear-gradient(45deg, #00c6ff, #0072ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    main.main-content p {
      font-weight: 400;
      font-size: 1.1rem;
      color: #9aa5b1;
      margin-bottom: 40px;
    }

    /* Dashboard Cards Container */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(210px,1fr));
      gap: 30px;
      margin-bottom: 50px;
    }
    /* Card Style */
    .card {
      background: linear-gradient(135deg, #272f3a, #1b2230);
      border-radius: 20px;
      padding: 25px 30px;
      box-shadow: 0 6px 20px #000000b3;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 30px #0099ffcc;
      background: linear-gradient(135deg, #0099ff, #0057b7);
      color: #e0f7ff;
    }
    .card i {
      font-size: 40px;
      margin-bottom: 18px;
      color: #00aaff;
      transition: color 0.3s ease;
    }
    .card:hover i {
      color: #ffffff;
    }
    .card h3 {
      font-size: 2.3rem;
      margin-bottom: 8px;
    }
    .card p {
      font-size: 1.1rem;
      color: #a0b8cc;
      font-weight: 600;
    }

    /* Charts Container */
    .charts {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(320px,1fr));
      gap: 40px;
    }
    .chart-card {
      background: linear-gradient(145deg, #1e2733, #2b3945);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 6px 20px #0008;
      color: #c9d1db;
      min-height: 320px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .chart-card h2 {
      font-weight: 700;
      font-size: 1.7rem;
      margin-bottom: 18px;
      color: #00aaff;
      user-select: none;
    }

    /* Scrollbar style for main content */
    main.main-content::-webkit-scrollbar {
      width: 10px;
    }
    main.main-content::-webkit-scrollbar-track {
      background: #222831;
      border-radius: 10px;
    }
    main.main-content::-webkit-scrollbar-thumb {
      background: #0072ff;
      border-radius: 10px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      nav.sidebar {
        width: 70px;
        border-radius: 0;
        position: fixed;
        height: 100vh;
        padding-top: 10px;
      }
      nav.sidebar .nav-links a span {
        display: none;
      }
      main.main-content {
        margin-left: 70px;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body >
  <div class="container" id="container">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="nav-links">
        <a href="#" class="active"><i class="fas fa-home"></i><span>Dashboard</span></a>
        <a href="../Products/product.php"><i class="fas fa-box-open"></i><span>Products</span></a>
        <a href="../Orders/orders.php"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
        <a href="../Customers/customers.php"><i class="fas fa-users"></i><span>Customers</span></a>
        <a href="../Revenue/revenue.php"><i class="fas fa-dollar-sign"></i><span>Revenue</span></a>
        <a href="../Feedback/feedback.php"><i class="fas fa-comments"></i><span>Feedback</span></a>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="main-content">
      <h1>Welcome to PrintCity Admin</h1>
      <p>This is your admin dashboard. Use the navigation bar to switch between sections.</p>

      <div class="dashboard-cards">
        <div class="card" onclick="location.href='../Customers/customers.php'">
          <i class="fas fa-users"></i>
          <h3><?php echo number_format($total_users); ?></h3>
          <p>Total Users</p>
        </div>

        <div class="card" onclick="location.href='../Orders/orders.php'">
          <i class="fas fa-shopping-cart"></i>
          <h3><?php echo number_format($total_orders); ?></h3>
          <p>Total Orders</p>
        </div>

        <div class="card" onclick="location.href='../Products/product.php'">
          <i class="fas fa-box-open"></i>
          <h3><?php echo number_format($total_products); ?></h3>
          <p>Products Available</p>
        </div>

        <div class="card" onclick="location.href='../Revenue/revenue.php'">
          <i class="fas fa-dollar-sign"></i>
          <h3>₹<?php echo number_format($total_revenue, 2); ?></h3>
          <p>Total Revenue</p>
        </div>
      </div>

      <div class="charts">
        <div class="chart-card">
          <h2>Orders per Month</h2>
          <canvas id="ordersChart" aria-label="Orders per month bar chart" role="img"></canvas>
        </div>

        <div class="chart-card">
          <h2>Revenue per Month (₹)</h2>
          <canvas id="revenueChart" aria-label="Revenue per month line chart" role="img"></canvas>
        </div>

        <div class="chart-card" style="grid-column: span 2;">
          <h2>Products by Category</h2>
          <canvas id="productsCategoryChart" aria-label="Products by category pie chart" role="img"></canvas>
        </div>
      </div>
    </main>
  </div>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Prepare data from PHP arrays
    const ordersByMonth = <?php echo json_encode(array_values($orders_by_month)); ?>;
    const revenueByMonth = <?php echo json_encode(array_values($revenue_by_month)); ?>;
    const productsCategoryLabels = <?php echo json_encode(array_keys($products_category)); ?>;
    const productsCategoryData = <?php echo json_encode(array_values($products_category)); ?>;

    const monthsLabels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

    // Orders Bar Chart
    const ctxOrders = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ctxOrders, {
      type: 'bar',
      data: {
        labels: monthsLabels,
        datasets: [{
          label: 'Orders',
          data: ordersByMonth,
          backgroundColor: 'rgba(0, 198, 255, 0.7)',
          borderColor: 'rgba(0, 198, 255, 1)',
          borderWidth: 1,
          borderRadius: 6,
          maxBarThickness: 40,
          hoverBackgroundColor: 'rgba(0, 198, 255, 1)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: {
            display: false
          },
          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0,0,0,0.7)',
            titleFont: { size: 16 },
            bodyFont: { size: 14 }
          }
        },
        scales: {
          y: {
            beginAtZero:true,
            ticks: {
              color: '#d0e6ff',
              font: {size: 14},
              stepSize: 1
            },
            grid: {
              color: '#2a3b5e',
              borderColor: 'transparent'
            }
          },
          x: {
            ticks: {
              color: '#9fc9ff',
              font: {size: 14}
            },
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Revenue Line Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
      type: 'line',
      data: {
        labels: monthsLabels,
        datasets: [{
          label: 'Revenue (₹)',
          data: revenueByMonth,
          fill: true,
          backgroundColor: 'rgba(0, 198, 255, 0.3)',
          borderColor: 'rgba(0, 198, 255, 1)',
          borderWidth: 3,
          tension: 0.3,
          pointRadius: 5,
          pointBackgroundColor: 'rgba(0, 198, 255, 1)',
          pointHoverRadius: 8,
          pointHoverBackgroundColor: '#0085cc',
          pointBorderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { labels: {color: '#94caff', font: {size:15}} },
          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0,0,0,0.7)',
            titleFont: { size: 16 },
            bodyFont: { size: 14 }
          }
        },
        scales: {
          y: {
            beginAtZero:true,
            ticks: {
              color: '#d0e6ff',
              font: {size: 14},
              callback: function(value) { return '₹' + value.toLocaleString(); }
            },
            grid: {
              color: '#2a3b5e',
              borderColor: 'transparent'
            }
          },
          x: {
            ticks: {
              color: '#9fc9ff',
              font: {size: 14}
            },
            grid: {
              display: false
            }
          }
        }
      }
    });

    // Products Category Pie Chart
    const ctxProductsCat = document.getElementById('productsCategoryChart').getContext('2d');
    const productsCategoryChart = new Chart(ctxProductsCat, {
      type: 'doughnut',
      data: {
        labels: productsCategoryLabels,
        datasets: [{
          label: 'Products by Category',
          data: productsCategoryData,
          backgroundColor: [
            '#00aaff',
            '#ffbb33',
            '#ff4444',
            '#33b5e5',
            '#2ecc71',
            '#9b59b6',
            '#e67e22',
            '#f1c40f'
          ],
          borderWidth: 2,
          borderColor: '#121721',
          hoverOffset: 30
        }]
      },
      options: {
        responsive: true,
        cutout: '60%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#a7c0d8',
              font: {size: 15, weight: '600'}
            }
          },
          tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0,0,0,0.7)',
            titleFont: {size:16},
            bodyFont: {size:14}
          }
        }
      }
    });
  </script>
</body>
</html>
