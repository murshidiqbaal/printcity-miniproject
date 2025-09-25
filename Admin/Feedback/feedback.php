<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch feedbacks with user info and product info
$sql_feedbacks = "
    SELECT DISTINCT
        f.id AS feedback_id,
        f.rating,
        f.comments,
        f.submitted_at AS feedback_date,
        u.username,
        up.email,
        f.order_id,
        p.name AS product_name
    FROM feedbacks f
    JOIN users u ON f.user_id = u.user_id
    JOIN user_profiles up ON u.user_id = up.user_id
    LEFT JOIN orders o ON f.order_id = o.order_id
    LEFT JOIN products p ON o.product_id = p.product_id
    ORDER BY f.submitted_at DESC
";

$result_feedbacks = mysqli_query($conn, $sql_feedbacks);
$feedbacks = [];
if ($result_feedbacks && mysqli_num_rows($result_feedbacks) > 0) {
    while ($row = mysqli_fetch_assoc($result_feedbacks)) {
        $feedbacks[] = $row;
    }
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); // prevent null warnings
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>Admin - Product Feedbacks</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: 'Inter', Arial, sans-serif;
    background: #121721;
    color: #e1e8f0;
  }

  .container {
    display: flex;
    height: 100vh;
  }

  /* Sidebar */
  nav.sidebar {
    background: #1f2a40;
    width: 270px;
    padding-top: 40px;
    border-top-right-radius: 25px;
    border-bottom-right-radius: 25px;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
  }

  nav.sidebar .nav-links {
    display: flex;
    flex-direction: column;
    padding: 0 20px;
  }

  nav.sidebar .nav-links a {
    display: flex;
    align-items: center;
    gap: 18px;
    color: #a8b3c5;
    font-weight: 600;
    font-size: 15px;
    padding: 14px 20px;
    margin-bottom: 14px;
    border-radius: 14px 0 0 14px;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  nav.sidebar .nav-links a i {
    font-size: 22px;
  }

  nav.sidebar .nav-links a.active,
  nav.sidebar .nav-links a:hover {
    background: #0072ff;
    color: #ffffff;
    padding-left: 30px;
    border-left: 5px solid #00d0ff;
    box-shadow: 0 0 10px #00d0ffc7;
  }

  /* Main content */
  main {
    margin-left: 270px;
    padding: 40px 50px;
    flex-grow: 1;
    overflow-y: auto;
    height: 100vh;
    display: flex;
    flex-direction: column;
  }

  main h1 {
    font-weight: 700;
    font-size: 32px;
    color: #00b0ff;
    margin-bottom: 30px;
    letter-spacing: 1px;
  }

  /* Feedback table container */
  .feedback-table-wrapper {
    flex-grow: 1;
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 4px 16px rgba(0, 115, 255, 0.3);
  }

  table.feedback-table {
    width: 100%;
    border-collapse: collapse;
    background: #1e2a44;
    color: #e1e8f0;
    min-width: 900px;
  }

  table.feedback-table thead {
    background: #0072ff;
    color: #fff;
  }

  table.feedback-table th,
  table.feedback-table td {
    padding: 14px 18px;
    text-align: center;
    border-bottom: 1px solid #2e3e60;
    vertical-align: middle;
    font-size: 14px;
  }

  table.feedback-table th {
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
  }

  table.feedback-table tbody tr:hover {
    background: #005bb5;
    cursor: default;
    transition: background-color 0.25s ease;
  }

  /* Star rating */
  .stars {
    display: inline-block;
    color: #f7d54a;
    font-size: 18px;
    line-height: 1;
  }

  .stars i.far {
    color: #4a5a6a; /* empty star color */
  }

  /* Responsive adjustments */
  @media (max-width: 1024px) {
    main {
      padding: 30px 20px;
      margin-left: 270px;
    }
    table.feedback-table {
      min-width: 700px;
    }
  }

  @media (max-width: 700px) {
    nav.sidebar {
      width: 60px;
    }
    nav.sidebar .nav-links a span {
      display: none;
    }
    nav.sidebar .nav-links a {
      justify-content: center;
      padding: 14px 0;
    }
    main {
      margin-left: 60px;
      padding: 20px 15px;
    }
    table.feedback-table {
      font-size: 12px;
    }
  }

  /* Scrollbar styling for main content */
  main::-webkit-scrollbar {
    width: 8px;
  }

  main::-webkit-scrollbar-thumb {
    background: #0072ffaa;
    border-radius: 10px;
  }

  main::-webkit-scrollbar-track {
    background: #101720;
  }
</style>
</head>
<body>
<div class="container">
  <nav class="sidebar" aria-label="Admin Sidebar Navigation">
    <div class="nav-links">
      <a href="../Dashboard/dashboard.php" aria-label="Dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a>
      <a href="../Products/product.php" aria-label="Products"><i class="fas fa-box-open"></i><span>Products</span></a>
      <a href="../Orders/orders.php" aria-label="Orders"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
      <a href="../Customers/customers.php" aria-label="Customers"><i class="fas fa-users"></i><span>Customers</span></a>
      <a href="../Revenue/revenue.php" aria-label="Revenue"><i class="fas fa-dollar-sign"></i><span>Revenue</span></a>
      <a href="feedback.php" class="active" aria-current="page"><i class="fas fa-comment-dots"></i><span>Feedbacks</span></a>
    </div>
  </nav>

  <main>
    <h1>Product Feedbacks</h1>
    <div class="feedback-table-wrapper" role="region" aria-live="polite" aria-label="User feedback table">
    <?php if (!empty($feedbacks)): ?>
      <table class="feedback-table" role="table">
        <thead>
          <tr role="row">
            <th role="columnheader" scope="col">ID</th>
            <th role="columnheader" scope="col">User</th>
            <th role="columnheader" scope="col">Email</th>
            <th role="columnheader" scope="col">Order ID</th>
            <th role="columnheader" scope="col">Product</th>
            <th role="columnheader" scope="col">Rating</th>
            <th role="columnheader" scope="col">Comments</th>
            <th role="columnheader" scope="col">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbacks as $fb): ?>
          <tr role="row">
            <td role="cell"><?= h($fb['feedback_id']) ?></td>
            <td role="cell"><?= h($fb['username']) ?></td>
            <td role="cell"><?= h($fb['email']) ?></td>
            <td role="cell"><?= h($fb['order_id']) ?></td>
            <td role="cell"><?= h($fb['product_name']) ?></td>
            <td role="cell">
              <?php 
                $ratingInt = (int)$fb['rating'];
                $maxStars = 5;
                // Output filled stars
                for ($i = 1; $i <= $maxStars; $i++) {
                    if ($i <= $ratingInt) {
                        echo '<i class="fas fa-star"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
              ?>
            </td>
            <td role="cell" style="max-width: 250px; word-break: break-word; text-align:left;"><?= nl2br(h($fb['comments'])) ?: '<em>No comments provided</em>' ?></td>
            <td role="cell"><?= h(date('M d, Y H:i', strtotime($fb['feedback_date']))) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p style="font-size: 18px; color: #9bb1cc;">No feedbacks available.</p>
    <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>