<?php
// revenue.php

// Database connection settings
$host = 'localhost';
$db   = 'printcity';  // your DB name
$user = 'root';       // your DB user
$pass = '';           // your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize filters
$filterMonth = $_GET['month'] ?? '';
$filterYear = $_GET['year'] ?? '';
$filterProduct = $_GET['product'] ?? '';
$filterStatus = $_GET['status'] ?? '';

// Prepare filter conditions and parameters
$whereClauses = [];
$params = [];

// Filter by month/year on order_date
if ($filterYear !== '') {
    $whereClauses[] = "YEAR(order_date) = :year";
    $params[':year'] = $filterYear;
}
if ($filterMonth !== '') {
    $whereClauses[] = "MONTH(order_date) = :month";
    $params[':month'] = $filterMonth;
}

// Filter by product_id
if ($filterProduct !== '') {
    $whereClauses[] = "product_id = :product_id";
    $params[':product_id'] = $filterProduct;
}

// Filter by status
if ($filterStatus !== '') {
    $whereClauses[] = "status = :status";
    $params[':status'] = $filterStatus;
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Fetch distinct product_ids for product filter dropdown
$productStmt = $pdo->query("SELECT DISTINCT product_id FROM orders ORDER BY product_id");
$products = $productStmt->fetchAll(PDO::FETCH_COLUMN);

// Define possible statuses (adjust if your DB has different statuses)
$statuses = ['Pending', 'Completed', 'Cancelled'];

// Fetch filtered orders with user info (left join users for future support)
$sql = "
    SELECT 
        o.order_id,
        o.customer_name,
        o.product_id,
        o.quantity,
        o.total_price,
        o.order_date,
        o.status,
        u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    $whereSQL
    ORDER BY o.order_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Calculate revenue summary
$summarySql = "
    SELECT 
        COUNT(*) AS total_orders,
        COALESCE(SUM(quantity),0) AS total_quantity,
        COALESCE(SUM(total_price),0) AS total_revenue
    FROM orders o
    $whereSQL
";
$summaryStmt = $pdo->prepare($summarySql);
$summaryStmt->execute($params);
$summary = $summaryStmt->fetch();

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Generate month and year options for filters
$currentYear = (int)date('Y');
$years = range($currentYear, $currentYear - 10);
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Revenue Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body class="bg-gray-100 min-h-screen p-6">


  <div class="max-w-7xl mx-auto relative">
  
     <!-- Back Arrow -->
    <a href="../HomeScreen/indexAdmin.php" 
       class="absolute left-0 -top-2 text-gray-700 hover:text-indigo-600"
       style="font-size: 1.8rem; text-decoration: none;">
      <i class="fas fa-arrow-left"></i>
    </a>

    <!-- Heading -->
    <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Revenue Dashboard</h1>

    <!-- Filters -->
    <form method="GET" class="bg-white p-6 rounded-lg shadow mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
      <div>
        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
        <select name="month" id="month" class="w-full border-gray-300 rounded-md shadow-sm">
          <option value="">All</option>
          <?php foreach ($months as $num => $name): ?>
            <option value="<?= $num ?>" <?= ($filterMonth == $num) ? 'selected' : '' ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
        <select name="year" id="year" class="w-full border-gray-300 rounded-md shadow-sm">
          <option value="">All</option>
          <?php foreach ($years as $year): ?>
            <option value="<?= $year ?>" <?= ($filterYear == $year) ? 'selected' : '' ?>><?= $year ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="product" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
        <select name="product" id="product" class="w-full border-gray-300 rounded-md shadow-sm">
          <option value="">All</option>
          <?php foreach ($products as $productId): ?>
            <option value="<?= h($productId) ?>" <?= ($filterProduct == $productId) ? 'selected' : '' ?>><?= h($productId) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm">
          <option value="">All</option>
          <?php foreach ($statuses as $status): ?>
            <option value="<?= h($status) ?>" <?= ($filterStatus == $status) ? 'selected' : '' ?>><?= h($status) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-4 text-right">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Apply Filters</button>
        <a href="revenue.php" class="ml-2 text-gray-600 hover:underline">Reset</a>
      </div>
    </form>

    <!-- Revenue Summary -->
    <div class="bg-white p-6 rounded-lg shadow mb-6 flex flex-wrap gap-6 justify-center md:justify-start">
      <div class="text-center md:text-left">
        <div class="text-sm text-gray-500">Total Revenue</div>
        <div class="text-2xl font-bold text-green-600"><?= number_format($summary['total_revenue'], 2) ?></div>
      </div>
      <div class="text-center md:text-left">
        <div class="text-sm text-gray-500">Total Orders</div>
        <div class="text-2xl font-bold text-blue-600"><?= number_format($summary['total_orders']) ?></div>
      </div>
      <div class="text-center md:text-left">
        <div class="text-sm text-gray-500">Total Quantity Sold</div>
        <div class="text-2xl font-bold text-purple-600"><?= number_format($summary['total_quantity']) ?></div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php if (count($orders) === 0): ?>
            <tr>
              <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['order_id']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['customer_name']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['username'] ?? '-') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['product_id']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['quantity']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($order['total_price'] ?? 0, 2) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h(date('Y-m-d', strtotime($order['order_date']))) ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= h($order['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>