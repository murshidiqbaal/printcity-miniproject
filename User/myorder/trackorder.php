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
$type = $_GET['type'] ?? 'normal'; // normal | custom

// ========================
// Fetch order details
// ========================
if ($type === 'custom') {
    $stmt = $conn->prepare("
        SELECT 
            co.id AS order_id,
            co.status,
            co.order_date,
            co.quantity,
            co.print_type,
            co.paper_size,
            co.notes,
            co.file_path,
            u.username AS customer_name,
            mp.email,
            mp.phone,
            mp.address
        FROM custom_orders co
        JOIN users u ON co.user_id = u.user_id
        LEFT JOIN user_profiles mp ON co.user_id = mp.user_id
        WHERE co.user_id = ? AND co.id = ?
        LIMIT 1
    ");
} else {
    $stmt = $conn->prepare("
        SELECT 
            o.order_id,
            o.status,
            o.order_date,
            o.customer_name,
            o.quantity,
            o.total_price,
            p.name AS product_name,
            p.image_path,
            mp.phone,
            mp.email,
            mp.address
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        LEFT JOIN user_profiles mp ON o.user_id = mp.user_id
        WHERE o.user_id = ? AND o.order_id = ?
        LIMIT 1
    ");
}


$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found.");
}

// ========================
// Fetch timeline
// ========================
$result = $conn->query("
    SELECT status, updated_at 
    FROM order_status_history 
    WHERE order_id = $order_id
    ORDER BY updated_at ASC
");


while ($row = $result->fetch_assoc()) {
    $history[strtolower($row['status'])] = $row['updated_at'];
}

// ========================
// Build timeline dynamically
// ========================
$status_timeline = [
    'ordered' => [
        'status' => 'Order Confirmed',
        'date' => $history['ordered'] ?? null,
        'completed' => isset($history['ordered']),
        'description' => 'Your order has been confirmed'
    ],
    'processing' => [
        'status' => 'Processing',
        'date' => $history['processing'] ?? null,
        'completed' => isset($history['processing']),
        'description' => 'Your item is being processed'
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

// Mark timeline based on current status
$current_status = strtolower($order['status'] ?? '');
$status_order = ['ordered', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
$current_index = array_search($current_status, $status_order);

foreach ($status_timeline as $key => &$status) {
    $index = array_search($key, $status_order);
    if ($index !== false && $index <= $current_index) {
        $status['completed'] = true;
    } else {
        $status['completed'] = false;
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
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    min-height: 100vh;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

/* Subtle background animation */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 147, 251, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
    animation: backgroundShift 20s ease-in-out infinite alternate;
    z-index: -1;
}

@keyframes backgroundShift {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-20px) translateY(20px); }
}

.container {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Header Enhancements */
.header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px 24px 0 0;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: slideDown 0.6s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.header h1 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
}

.header p {
    color: #6b7280;
    font-size: 1.1rem;
}

/* Order Card Enhancements */
.order-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 0 0 24px 24px;
    padding: 30px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.order-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.order-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: rgba(102, 126, 234, 0.3);
}

.order-card:hover::before {
    transform: scaleX(1);
}

.order-card .product-details {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.product-image {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 16px;
    border: 3px solid #e5e7eb;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.product-image:hover {
    transform: scale(1.05);
    border-color: #667eea;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
}

.order-info h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.order-info p {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 5px;
}

/* Status Badge Enhancements */
.status-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.status-badge::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.status-badge:hover::after {
    left: 100%;
}

.status-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

/* Timeline Enhancements */
.timeline-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 24px;
    position: relative;
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.timeline-container h2 {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
}

.timeline {
    position: relative;
    margin: 40px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 35px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #e5e7eb, #667eea);
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 40px;
    position: relative;
    opacity: 0;
    animation: slideInLeft 0.6s ease-out forwards;
    animation-delay: calc(var(--order) * 0.2s);
}

.timeline-item:nth-child(even) {
    flex-direction: row-reverse;
    --order: 1;
}

.timeline-item:nth-child(odd) {
    --order: 2;
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}

.timeline-item:nth-child(even) .timeline-content {
    border-left: none;
    border-right: 4px solid #3b82f6;
    margin-right: -20px;
}

.timeline-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 4px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 20px;
    z-index: 2;
    flex-shrink: 0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    position: relative;
}

.timeline-icon i {
    font-size: 1.5rem;
    color: #6b7280;
    transition: all 0.3s ease;
}

.timeline-icon.completed {
    background: linear-gradient(135deg, #10b981, #059669);
    border-color: #10b981;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
}

.timeline-icon.completed i {
    color: white;
}

.timeline-icon.current {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-color: #3b82f6;
    color: white;
    animation: pulse 2s infinite, rotate 3s linear infinite;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.timeline-icon.current i {
    color: white;
}

.timeline-content {
    flex: 1;
    background: rgba(248, 250, 252, 0.9);
    backdrop-filter: blur(10px);
    padding: 24px;
    border-radius: 16px;
    border-left: 5px solid #3b82f6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.timeline-content:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.timeline-content.completed {
    border-left-color: #10b981;
    background: rgba(209, 250, 229, 0.9);
}

.timeline-content.pending {
    border-left-color: #9ca3af;
    background: rgba(243, 244, 246, 0.9);
}

.timeline-content h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.timeline-content p {
    color: #6b7280;
    font-size: 0.95rem;
    line-height: 1.6;
}

.timeline-content .date {
    font-size: 0.875rem;
    color: #9ca3af;
    margin-top: 10px;
    font-style: italic;
}

/* Help Section Enhancements */
.help-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 24px;
}

.help-section h2 {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 24px;
}

.help-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.help-card {
    background: linear-gradient(135deg, rgba(248, 250, 252, 0.8), rgba(241, 245, 249, 0.8));
    padding: 24px;
    border-radius: 16px;
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.help-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.help-card:hover {
    transform: translateY(-8px) rotateX(5deg);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    border-color: rgba(102, 126, 234, 0.3);
}

.help-card:hover::before {
    transform: scaleX(1);
}

.help-card i {
    font-size: 2rem;
    color: #667eea;
    margin-bottom: 12px;
    display: block;
}

.help-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.help-card p {
    color: #6b7280;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    .container {
        max-width: 100%;
        padding: 0 10px;
    }
    
    .header, .order-card, .timeline-container, .help-section {
        padding: 20px;
        border-radius: 16px;
    }
    
    .order-card .product-details {
        flex-direction: column;
        text-align: center;
    }
    
    .product-image {
        width: 120px;
        height: 120px;
    }
    
    .timeline::before {
        left: 20px;
    }
    
    .timeline-item {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    
    .timeline-item:nth-child(even) {
        flex-direction: column !important;
    }
    
    .timeline-icon {
        margin: 0 0 15px 0;
    }
    
    .timeline-content {
        margin-left: 0 !important;
        border-left: 5px solid #3b82f6 !important;
        border-right: none !important;
    }
    
    .help-grid {
        grid-template-columns: 1fr;
    }
    
    .status-badge {
        padding: 8px 16px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .header h1 {
        font-size: 1.5rem;
    }
    
    .timeline-content {
        padding: 16px;
    }
    
    .help-card {
        padding: 20px;
    }
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
        <?php if ($type === 'custom'): ?>
        <div class="order-card">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>
    <div class="flex items-center space-x-6">
        <?php if ($type === 'normal'): ?>
            <img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path'] ?? '') ?>" 
                 alt="<?= htmlspecialchars($order['product_name'] ?? '') ?>" 
                 class="product-image">
        <?php else: ?>
            <!-- Default image for custom orders -->
            <img src="/miniproject/User/assets/default_file.png" 
                 alt="Custom Order File" 
                 class="product-image">
        <?php endif; ?>

        <div class="flex-1">
            <?php if ($type === 'normal'): ?>
                <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($order['product_name'] ?? '') ?></h3>
                <p class="text-gray-600 mb-2">Quantity: <?= $order['quantity'] ?></p>
                <p class="text-2xl font-bold text-blue-600">
                    <?= isset($order['total_price']) ? '$' . number_format($order['total_price'], 2) : '' ?>
                </p>
            <?php else: ?>
                <h3 class="text-lg font-semibold text-gray-800">Custom Order File</h3>
                <p class="text-gray-600 mb-2">Quantity: <?= $order['quantity'] ?></p>
                <p class="text-gray-600 mb-2">Print Type: <?= htmlspecialchars($order['print_type'] ?? '') ?></p>
                <p class="text-gray-600 mb-2">Paper Size: <?= htmlspecialchars($order['paper_size'] ?? '') ?></p>
                <p class="text-gray-600 mb-2">
                    File: <a href="/miniproject/User/custom_uploads/<?= htmlspecialchars(basename($order['file_path'] ?? '')) ?>" target="_blank">
                        <?= htmlspecialchars(basename($order['file_path'] ?? '')) ?>
                    </a>
                </p>
                <p class="text-gray-600 mb-2">Notes: <?= htmlspecialchars($order['notes'] ?? '') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-gray-800 mb-2">Shipping Address</h4>
            <p class="text-gray-600"><?= htmlspecialchars($order['address'] ?? 'Not provided') ?></p>
        </div>
        <div>
            <h4 class="font-semibold text-gray-800 mb-2">Contact Information</h4>
            <p class="text-gray-600">Phone: <?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></p>
            <p class="text-gray-600">Email: <?= htmlspecialchars($order['email'] ?? 'Not provided') ?></p>
        </div>
    </div>
</div>

        <?php else: ?>
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
                    <p class="text-gray-600"><?= htmlspecialchars($order['address'] ?? 'Not provided') ?></p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">Contact Information</h4>
                    <p class="text-gray-600">Phone: <?= htmlspecialchars($order['phone'] ?? 'Not provided') ?></p>
                    <p class="text-gray-600">Email: <?= htmlspecialchars($order['email'] ?? 'Not provided') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

