<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM users ORDER BY created_at DESC"; // Adjust 'created_at' to your table column
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer List</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 30px; background-color: #f4f4f4; }
    h2 { text-align: center; margin-bottom: 30px; }

    .customer-table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .customer-table th, .customer-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    .customer-table th {
      background-color: #343a40;
      color: white;
    }

    .customer-table tr:hover {
      background-color: #f1f1f1;
    }

    .no-users {
      text-align: center;
      padding: 20px;
      background: white;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<h2>👥 Registered Customers</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
<table class="customer-table">
  <thead>
    <tr>
      <th>User ID</th>
      <th>Name</th>
   
    </tr>
  </thead>
  <tbody>
    <?php while ($user = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td><?= $user['user_id'] ?></td>
      <td><?= isset($user['username']) ? htmlspecialchars($user['username']) : '-' ?></td>


 
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
  <div class="no-users">
    <p>No customers found.</p>
  </div>
<?php endif; ?>

</body>
</html>

<?php mysqli_close($conn); ?>
