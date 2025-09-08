<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteUserId = intval($_POST['delete_user_id']);
    if ($deleteUserId > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $deleteUserId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect to avoid resubmission and preserve filters
        $redirectUrl = $_SERVER['PHP_SELF'];
        $queryParams = [];
        foreach (['filter_username', 'filter_month', 'filter_user_id'] as $param) {
            if (!empty($_GET[$param])) {
                $queryParams[$param] = $_GET[$param];
            }
        }
        if ($queryParams) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }
        header("Location: $redirectUrl");
        exit;
    }
}

// Get filter values from GET
$filterUsername = $_GET['filter_username'] ?? '';
$filterMonth = $_GET['filter_month'] ?? '';
$filterUserId = $_GET['filter_user_id'] ?? '';

// Build WHERE clauses and params
$whereClauses = [];
$params = [];
$paramTypes = '';

if ($filterUsername !== '') {
    $whereClauses[] = "username LIKE ?";
    $params[] = '%' . $filterUsername . '%';
    $paramTypes .= 's';
}
if ($filterMonth !== '') {
    // Filter by month of created_at (format YYYY-MM)
    // We'll use DATE_FORMAT(created_at, '%Y-%m') = ?
    $whereClauses[] = "DATE_FORMAT(created_at, '%Y-%m') = ?";
    $params[] = $filterMonth;
    $paramTypes .= 's';
}
if ($filterUserId !== '') {
    if (ctype_digit($filterUserId)) {
        $whereClauses[] = "user_id = ?";
        $params[] = (int)$filterUserId;
        $paramTypes .= 'i';
    } else {
        // Invalid user_id filter, ignore or you can handle error
    }
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

$sql = "SELECT * FROM users $whereSQL ORDER BY created_at DESC";

if (count($params) > 0) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Generate month options for filter (last 12 months)
$months = [];
for ($i = 0; $i < 12; $i++) {
    $time = strtotime("-$i month");
    $months[date('Y-m')] = date('Y-m');
    $months[date('Y-m', $time)] = date('Y-m', $time);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer List</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 30px; background-color: #f4f4f4; }
    h2 { text-align: center; margin-bottom: 30px; }

    .filter-form {
      max-width: 700px;
      margin: 0 auto 20px auto;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .filter-input, .filter-select {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      min-width: 150px;
    }
    .filter-button, .reset-button {
      background-color: #2563eb;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.2s ease;
      min-width: 100px;
    }
    .filter-button:hover {
      background-color: #1e40af;
    }
    .reset-button {
      background-color: #6b7280;
    }
    .reset-button:hover {
      background-color: #4b5563;
    }

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

    .delete-button {
      background-color: #dc2626;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.9rem;
      transition: background-color 0.2s ease;
    }
    .delete-button:hover {
      background-color: #b91c1c;
    }
  </style>
</head>
<body>

<h2>👥 Registered Customers</h2>

<form method="GET" class="filter-form" action="">
  <input 
    type="text" 
    name="filter_username" 
    placeholder="Search by username..." 
    class="filter-input" 
    value="<?= h($filterUsername) ?>"
    autocomplete="off"
  />
  <input 
    type="text" 
    name="filter_user_id" 
    placeholder="Filter by User ID" 
    class="filter-input" 
    value="<?= h($filterUserId) ?>"
    autocomplete="off"
  />
  <select name="filter_month" class="filter-select">
    <option value="">All Months</option>
    <?php
    // Generate last 12 months options
    for ($i = 0; $i < 12; $i++):
        $monthVal = date('Y-m', strtotime("-$i month"));
        $monthLabel = date('F Y', strtotime("-$i month"));
    ?>
      <option value="<?= $monthVal ?>" <?= ($filterMonth === $monthVal) ? 'selected' : '' ?>><?= $monthLabel ?></option>
    <?php endfor; ?>
  </select>
  <button type="submit" class="filter-button">Filter</button>
  <a href="<?= $_SERVER['PHP_SELF'] ?>" class="reset-button" style="display:flex; align-items:center; justify-content:center; text-decoration:none;">Reset</a>
</form>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<table class="customer-table">
  <thead>
    <tr>
      <th>User ID</th>
      <th>Name</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($user = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td><?= h($user['user_id']) ?></td>
      <td><?= isset($user['username']) ? h($user['username']) : '-' ?></td>
      <td><?= h($user['created_at']) ?></td>
      <td>
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');" style="margin:0;">
          <input type="hidden" name="delete_user_id" value="<?= (int)$user['user_id'] ?>" />
          <button type="submit" class="delete-button">Delete</button>
        </form>
      </td>
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

<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
