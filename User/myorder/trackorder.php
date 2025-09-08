<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");
$history = [];
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_GET['order_id'] ?? 0);
// Fetch order details
$stmt = $conn->prepare("
    SELECT 
        o.order_id,
        o.status,
        o.order_date,
        o.customer_name,
        o.address,
        o.quantity,
        o.total_price,
        p.name AS product_name,
        p.image_path
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.user_id = ? AND o.order_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

$stmt->close();


if (!$order) {
    die("Order not found.");
}

// ✅ Define current status here (before HTML starts)
$current_status = strtolower($order['status'] ?? '');

$result = $conn->query("SELECT status, updated_at FROM order_status_history WHERE order_id = $order_id ORDER BY updated_at ASC");

while ($row = $result->fetch_assoc()) {
    $history[strtolower($row['status'])] = $row['updated_at'];
}


// Build timeline dynamically
$status_timeline = [
    'ordered' => [
        'status' => 'Order Confirmed',
        'date' => $history['ordered'] ?? null,
        'completed' => isset($history['ordered']),
        'description' => 'Your order has been confirmed'
    ],
    'processing' => [
        'status' => 'Processing',
        'date' => $history['processing'] ?? ($history['processed'] ?? null),
        'completed' => isset($history['processing']) || isset($history['processed']),
        'description' => 'Your item is being prepared for shipment'
    ],
    'shipped' => [
        'status' => 'Shipped',
        'date' => $history['shipped'] ?? null,
        'completed' => isset($history['shipped']),
        'description' => 'Your item has been shipped'
    ],
    'out_for_delivery' => [
        'status' => 'Out for Delivery',
        'date' => $history['out_for_delivery'] ?? null,
        'completed' => isset($history['out_for_delivery']),
        'description' => 'Your item is out for delivery'
    ],
    'delivered' => [
        'status' => 'Delivered',
        'date' => $history['delivered'] ?? null,
        'completed' => isset($history['delivered']),
        'description' => 'Your item has been delivered'
    ]
];

// ✅ Mark timeline steps as completed or pending
// Current status from orders table
$current_status = strtolower($order['status'] ?? '');

// Define correct order of statuses
$status_order = ['ordered', 'processing', 'shipped', 'out_for_delivery', 'delivered'];

// Find current index
$current_index = array_search($current_status, $status_order);

// Mark timeline steps based on current status
foreach ($status_timeline as $key => &$status) {
    $index = array_search($key, $status_order);

    if ($index !== false && $index <= $current_index) {
        $status['completed'] = true;   // Mark green
    } else {
        $status['completed'] = false;  // Keep gray
    }
}
unset($status);



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?= $order['order_id'] ?> | PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 16px 16px 0 0;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .order-card {
            background: white;
            border-radius: 0 0 16px 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid #e5e7eb;
        }
        
        .status-badge {
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .timeline {
            position: relative;
            margin: 40px 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e5e7eb;
        }
        
        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
        }
        
        .timeline-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            z-index: 2;
            flex-shrink: 0;
        }
        
        .timeline-icon.completed {
            background: #10b981; /* Green for completed */
            border-color: #10b981;
            color: white;
        }
        
        .timeline-icon.current {
            background: #3b82f6; /* Blue for current */
            border-color: #3b82f6;
            color: white;
            animation: pulse 2s infinite;
        }
        
        .timeline-content {
            flex: 1;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
        }
        
        .timeline-content.completed {
            border-left-color: #10b981; /* Green for completed */
        }
        
        .timeline-content.pending {
            border-left-color: #9ca3af;
            background: #f3f4f6;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .help-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .help-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }
        
        .help-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<header class="bg-primary text-white p-3 d-flex align-items-center">
    <!-- Back Arrow -->
    <a href="myorder.php" class="text-white me-3" style="font-size: 1.5rem;">
        <i class="fas fa-arrow-left"></i>
    </a>
</header>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Order Tracking</h1>
                <div class="status-badge">
                    <?= htmlspecialchars($status_timeline[$current_status]['status'] ?? 'Unknown') ?>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <p class="font-semibold">Order #<?= $order['order_id'] ?></p>
                    <p>Placed on <?= date('M d, Y', strtotime($order['order_date'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="order-card">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>
            <div class="flex items-center space-x-6">
                <img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path']) ?>" 
                     alt="<?= htmlspecialchars($order['product_name']) ?>" 
                     class="product-image">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($order['product_name']) ?></h3>
                    <p class="text-gray-600 mb-2">Quantity: <?= $order['quantity'] ?></p>
<p class="text-2xl font-bold text-blue-600">
    <?php echo '$' . number_format($order['total_price'], 2); ?>
</p>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Shipping Address</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($order['address']) ?></p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Contact Information</h4>
                    <p class="text-gray-600">Phone: <?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></p>
                    <p class="text-gray-600">Email: <?= htmlspecialchars($order['email'] ?? 'Not provided') ?></p>
                </div>
            </div>
        </div>

        <!-- Timeline -->
       <div class="timeline-container">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Status Timeline</h2>
   <div class="timeline">
    <?php foreach ($status_timeline as $key => $status): ?>
        <?php $is_current = ($key === $current_status); ?>
        <div class="timeline-item">
            <div class="timeline-icon 
                <?= $status['completed'] ? 'completed' : '' ?> 
                <?= $is_current ? 'current' : '' ?>">
                
                <?php if ($status['completed']): ?>
                    <i class="fas fa-check text-lg"></i>
                <?php elseif ($is_current): ?>
                    <i class="fas fa-truck text-lg"></i>
                <?php else: ?>
                    <i class="far fa-clock text-lg"></i>
                <?php endif; ?>
            </div>
            <div class="timeline-content 
                <?= $status['completed'] ? 'completed' : 'pending' ?>
                <?= $is_current ? 'current' : '' ?>">
                <h3 class="font-semibold text-gray-800 mb-2"><?= $status['status'] ?></h3>
                <?php if ($status['date']): ?>
                    <p class="text-sm text-gray-600 mb-2"><?= date('M d, Y g:i A', strtotime($status['date'])) ?></p>
                <?php endif; ?>
                <p class="text-gray-700"><?= $status['description'] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</div>


        <!-- Help Section -->
        <div class="help-section">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Need Help?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="help-card">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-blue-600"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800">Call Support</h3>
                    </div>
                    <p class="text-gray-600 text-sm">24/7 customer support available</p>
                    <p class="text-blue-600 font-semibold mt-2">+91 7994051281</p>
                </div>
              
            </div>
        </div>
    </div>

    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for timeline
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach(item => {
                item.addEventListener('click', () => {
                    item.querySelector('.timeline-content').classList.toggle('bg-blue-50');
                });
            });

            // Auto-update status every 30 seconds (simulated)
            setInterval(() => {
                console.log('Checking for status updates...');
                // Here you would make an AJAX call to check for status updates
            }, 30000);
        });
    </script>
</body>
</html>

