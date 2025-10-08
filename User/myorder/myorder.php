<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch regular product orders
$stmt_regular = $conn->prepare("
    SELECT 
        o.order_id,
        o.customer_name,
        o.address,
        o.order_date,
        o.status,
        p.name AS product_name,
        p.image_path,
        o.quantity
    FROM orders o
    JOIN products p ON o.product_id = p.product_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");

$stmt_regular->bind_param("i", $user_id);
$stmt_regular->execute();
$regular_result = $stmt_regular->get_result();

// Fetch custom orders
$stmt_custom = $conn->prepare("
    SELECT 
        co.order_id,
        co.file_name AS file_path,
        co.quantity,
        co.print_type,
        co.paper_size,
        co.notes,
        co.status,
        co.created_at
    FROM custom_orders co
    WHERE co.user_id = ?
    ORDER BY co.created_at DESC
");

$stmt_custom->bind_param("i", $user_id);
$stmt_custom->execute();
$custom_result = $stmt_custom->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders | PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="myorder.css">
    <style>
        /* Enhanced Styles for Custom Orders Section - Modern & Awesome UI */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 2.5rem 0 1.5rem 0;
    padding: 1rem 0;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
    border-bottom: 3px solid #8b5cf6;
    border-radius: 12px 12px 0 0;
    color: #8b5cf6;
    font-size: 1.75rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
    position: relative;
    overflow: hidden;
    animation: slideInDown 0.6s ease-out;
}

.section-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #8b5cf6, #7c3aed, #8b5cf6);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.section-header:hover::before {
    transform: scaleX(1);
}

@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-header i {
    font-size: 2rem;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.custom-order-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-left: 5px solid #8b5cf6;
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.custom-order-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #8b5cf6, #7c3aed, #8b5cf6);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.custom-order-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(139, 92, 246, 0.25);
    border-color: rgba(139, 92, 246, 0.4);
}

.custom-order-card:hover::before {
    opacity: 1;
}

.custom-order-card.status-delivered {
    border-left-color: #7c3aed;
    box-shadow: 0 8px 32px rgba(124, 58, 237, 0.2);
}

.custom-order-card.status-cancelled {
    border-left-color: #dc3545;
    box-shadow: 0 8px 32px rgba(220, 53, 69, 0.15);
    background: rgba(248, 215, 218, 0.1);
}

.custom-order-image {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: #8b5cf6;
    font-size: 2.8rem;
    position: relative;
    overflow: hidden;
    height: 140px; /* Reduced size for smaller card */
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.custom-order-image::before {
    content: attr(data-file-type);
    position: absolute;
    bottom: 0.75rem;
    right: 0.75rem;
    font-size: 0.8rem;
    color: white;
    font-weight: 700;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5));
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
    z-index: 3;
}

.custom-order-image:hover::before {
    opacity: 1;
    transform: translateY(0);
}

.custom-order-image i {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 2;
    position: relative;
    filter: drop-shadow(0 2px 4px rgba(139, 92, 246, 0.3));
}

.custom-order-image:hover i {
    color: #7c3aed;
    transform: scale(1.1) rotate(5deg);
    filter: drop-shadow(0 4px 8px rgba(124, 58, 237, 0.4));
}

/* Enhanced Icon-specific colors with gradients and effects */
.file-icon-pdf i { 
    color: #dc3545; 
    background: radial-gradient(circle, rgba(220, 53, 69, 0.2) 0%, transparent 70%);
    padding: 0.75rem;
    border-radius: 50%;
}
.file-icon-pdf .custom-order-image {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}
.file-icon-pdf:hover i { color: #c82333; }

.file-icon-doc i { 
    color: #007bff; 
    background: radial-gradient(circle, rgba(0, 123, 255, 0.2) 0%, transparent 70%);
    padding: 0.75rem;
    border-radius: 50%;
}
.file-icon-doc .custom-order-image {
    background: linear-gradient(135deg, #cce7ff 0%, #b3daff 100%);
}
.file-icon-doc:hover i { color: #0056b3; }

.file-icon-txt i { 
    color: #6c757d; 
    background: radial-gradient(circle, rgba(108, 117, 125, 0.2) 0%, transparent 70%);
    padding: 0.75rem;
    border-radius: 50%;
}
.file-icon-txt .custom-order-image {
    background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
}
.file-icon-txt:hover i { color: #495057; }

.file-icon-rtf i { 
    color: #8b5cf6; 
    background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
    padding: 0.75rem;
    border-radius: 50%;
}
.file-icon-rtf .custom-order-image {
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
}
.file-icon-rtf:hover i { color: #7c3aed; }

.custom-order-details {
    flex: 1;
    padding: 1rem; /* Reduced padding for smaller card */
    display: flex;
    flex-direction: column;
    gap: 0.5rem; /* Slightly reduced gap */
}

.custom-product-name {
    color: #8b5cf6;
    font-weight: 700;
    font-size: 1.1rem; /* Slightly smaller font */
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 1px 2px rgba(139, 92, 246, 0.2);
}

.custom-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0.5rem 0; /* Reduced margin */
    font-size: 0.9rem; /* Slightly smaller font */
    color: #495057;
    background: rgba(248, 250, 252, 0.8);
    padding: 0.5rem; /* Reduced padding */
    border-radius: 8px;
    border-left: 3px solid #8b5cf6;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.custom-meta:hover {
    background: rgba(139, 92, 246, 0.05);
    transform: translateX(5px);
    box-shadow: 0 4px 8px rgba(139, 92, 246, 0.1);
}

.custom-meta span {
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.custom-meta span::before {
    content: '';
    width: 4px;
    height: 4px;
    background: #8b5cf6;
    border-radius: 50%;
    flex-shrink: 0;
}

.view-details-btn {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    padding: 0.6rem 1.2rem; /* Slightly reduced padding */
    border: none;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    position: relative;
    overflow: hidden;
    align-self: flex-start;
    margin-top: auto;
}

.view-details-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
}

.view-details-btn:hover::before {
    left: 100%;
}

.view-details-btn:hover {
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
}

.view-details-btn i {
    margin-right: 0.5rem;
    transition: transform 0.3s ease;
}

.view-details-btn:hover i {
    transform: scale(1.2);
}

.no-orders {
    text-align: center;
    padding: 3rem 2rem;
    color: #6c757d;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.no-orders::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #8b5cf6, #7c3aed);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.no-orders:hover {
    border-color: #8b5cf6;
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(139, 92, 246, 0.1);
}

.no-orders:hover::before {
    transform: scaleX(1);
}

.no-orders i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.6;
    color: #8b5cf6;
    animation: bounce 2s infinite, iconRotate 4s linear infinite;
    filter: drop-shadow(0 4px 8px rgba(139, 92, 246, 0.2));
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-15px); }
    60% { transform: translateY(-8px); }
}

@keyframes iconRotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.no-orders h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
}

.no-orders p {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.no-orders a {
    color: #8b5cf6;
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border: 2px solid #8b5cf6;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(139, 92, 246, 0.05);
}

.no-orders a:hover {
    background: #8b5cf6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.file-extension-badge {
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.2), rgba(0, 123, 255, 0.1));
    color: #007bff;
    padding: 0.4rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-left: 0.75rem;
    border: 1px solid rgba(0, 123, 255, 0.3);
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    transition: all 0.3s ease;
    position: relative;
    top: -2px;
}

.file-extension-badge:hover {
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.3), rgba(0, 123, 255, 0.2));
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

/* Status Badge Enhancements (for consistency) */
.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 1rem 0;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.status-pending { background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404; border: 1px solid #ffd700; }
.status-processing { background: linear-gradient(135deg, #cce5ff, #99ccff); color: #004085; border: 1px solid #007bff; }
.status-completed, .status-shipped { background: linear-gradient(135deg, #d1f2eb, #a8e6cf); color: #0f5132; border: 1px solid #20c997; }
.status-delivered { background: linear-gradient(135deg, #d4edda, #b8e6b8); color: #0f5132; border: 1px solid #198754; box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3); }
.status-cancelled { background: linear-gradient(135deg, #f8d7da, #f5c2c7); color: #721c24; border: 1px solid #dc3545; }

.status-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .section-header {
        font-size: 1.4rem;
        margin: 1.5rem 0 1rem 0;
        padding: 0.75rem 0;
    }
    
    .custom-order-image {
        height: 120px; /* Even smaller on mobile */
        font-size: 2.2rem;
    }
    
    .custom-order-image i {
        font-size: 2.2rem;
    }

    .custom-order-details {
        padding: 0.75rem;
        gap: 0.4rem;
    }
    .custom-product-name {
        font-size: 1rem;
    }
    .custom-meta {
        font-size: 0.85rem;
        margin: 0.4rem 0;
        padding: 0.4rem;
    }
    .view-details-btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    .no-orders {
        padding: 2rem 1rem;
    }
    .no-orders h3 {
        font-size: 1.25rem;
    }
    .no-orders p {
        font-size: 0.9rem;
    }
    .no-orders i {
        font-size: 3rem;
    }
    .file-extension-badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
}

    </style>
</head>
<body>
<header class="bg-primary text-white p-3 d-flex align-items-center">
    <!-- Back Arrow -->
    <a href="../HomePage/index.php" class="text-white me-3" style="font-size: 1.5rem;">
        <i class="fas fa-arrow-left"></i>
    </a>
</header>

<div class="container">
    <h1 class="page-title">My Orders</h1>

    <!-- Regular Product Orders Section -->
    <div class="section-header">
        <i class="fas fa-shopping-cart"></i>
        Product Orders
    </div>
    <div class="order-grid">
        <?php if ($regular_result->num_rows > 0): ?>
            <?php while ($order = $regular_result->fetch_assoc()): 
                // Determine status class for badge
                $status_class = '';
                switch(strtolower($order['status'])) {
                    case 'pending': $status_class = 'status-pending'; break;
                    case 'processing': $status_class = 'status-processing'; break;
                    case 'shipped': case 'completed': $status_class = 'status-completed'; break;
                    case 'cancelled': $status_class = 'status-cancelled'; break;
                    case 'delivered': $status_class = 'status-delivered'; break;
                }
            ?>
            <?php
                $card_class = '';
                $status_lower = strtolower($order['status']);
                if ($status_lower === 'delivered') {
                    $card_class = 'status-delivered';
                } elseif ($status_lower === 'cancelled') {
                    $card_class = 'status-cancelled';
                }
            ?>
            <div class="order-card <?= $card_class ?>">
                <img src="/miniproject/Admin/Products/<?= htmlspecialchars($order['image_path']) ?>" 
                     alt="<?= htmlspecialchars($order['product_name']) ?>" class="order-image">
                <div class="order-details">
                    <h3 class="product-name"><?= htmlspecialchars($order['product_name']) ?></h3>
                    <div class="order-meta">
                        <span>Qty: <?= $order['quantity'] ?></span>
                        <span><?= date('M d, Y', strtotime($order['order_date'])) ?></span>
                    </div>
                    <span class="status-badge <?= $status_class ?>">
                        <?= htmlspecialchars($order['status']) ?>
                    </span>
                    <a href="trackorder.php?order_id=<?= $order['order_id'] ?>&type=product" class="track-btn">
                        <i class="fas fa-truck"></i> Track Order
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-shopping-cart"></i>
                <h3>No product orders found.</h3>
                <p>You haven't placed any product orders yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Custom Orders Section -->
<div class="section-header">
    <i class="fas fa-file-upload"></i>
    Custom Orders
</div>
<div class="order-grid">
    <?php if ($custom_result->num_rows > 0): ?>
        <?php while ($custom_order = $custom_result->fetch_assoc()): 

            // Use default status if not set
            $status = $custom_order['status'] ?? 'Pending';
            $status_lower = strtolower($status);

            // Determine status class for badge
            $status = $custom_order['status'] ?? 'Pending'; // Fallback
$status_class = '';
switch(strtolower($status)) {
    case 'pending': $status_class = 'status-pending'; break;
    case 'processing': $status_class = 'status-processing'; break;
    case 'shipped': case 'completed': $status_class = 'status-completed'; break;
    case 'cancelled': $status_class = 'status-cancelled'; break;
    case 'delivered': $status_class = 'status-delivered'; break;
}


            // Determine card class
            $card_class = '';
            if ($status_lower === 'delivered') {
                $card_class = 'status-delivered';
            } elseif ($status_lower === 'cancelled') {
                $card_class = 'status-cancelled';
            }

            // Determine file icon
            $file_extension = '';
            $icon_class = 'fas fa-file-alt'; // Default fallback
            $file_type_class = ''; 
            $file_type_label = 'File';
            if (!empty($custom_order['file_name'])) {
                $path_info = pathinfo($custom_order['file_name']);
                $file_extension = strtolower($path_info['extension'] ?? '');
                
                switch ($file_extension) {
                    case 'pdf':
                        $icon_class = 'fas fa-file-pdf';
                        $file_type_class = 'file-icon-pdf';
                        $file_type_label = 'PDF';
                        break;
                    case 'doc':
                    case 'docx':
                        $icon_class = 'fas fa-file-word';
                        $file_type_class = 'file-icon-doc';
                        $file_type_label = 'Word';
                        break;
                    case 'txt':
                        $icon_class = 'fas fa-file-alt';
                        $file_type_class = 'file-icon-txt';
                        $file_type_label = 'Text';
                        break;
                    case 'rtf':
                        $icon_class = 'fas fa-file-alt';
                        $file_type_class = 'file-icon-rtf';
                        $file_type_label = 'RTF';
                        break;
                    default:
                        $icon_class = 'fas fa-file';
                        $file_type_label = strtoupper($file_extension);
                        break;
                }
            }

            // Safely handle order date
            $order_date_display = !empty($custom_order['order_date']) ? 
                date('M d, Y', strtotime($custom_order['order_date'])) : 
                date('M d, Y', strtotime($custom_order['created_at'] ?? 'now'));
        ?>
        <div class="order-card <?= $card_class ?> custom-order-card">
            <div class="custom-order-image <?= $file_type_class ?>" data-file-type="<?= $file_type_label ?>">
                <i class="<?= $icon_class ?>"></i>
            </div>
            <div class="custom-order-details">
                <h3 class="custom-product-name">
                    Custom Print Order <span class="file-extension-badge"><?= $file_type_label ?></span>
                </h3>
                <div class="custom-meta">
                    <span>Qty: <?= intval($custom_order['quantity'] ?? 1) ?></span>
                    <span>Type: <?= ucfirst(str_replace('_', ' ', $custom_order['print_type'] ?? 'N/A')) ?></span>
                </div>
                <div class="custom-meta">
                    <span>Size: <?= htmlspecialchars($custom_order['paper_size'] ?? 'N/A') ?></span>
                    <span><?= $order_date_display ?></span>
                </div>
                <?php if (!empty($custom_order['notes'])): ?>
                    <div class="custom-meta">
                        <span>Notes: <?= htmlspecialchars(substr($custom_order['notes'], 0, 50)) ?><?= strlen($custom_order['notes']) > 50 ? '...' : '' ?></span>
                    </div>
                <?php endif; ?>
                <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($status) ?></span>
                <a href="trackorder.php?order_id=<?= intval($custom_order['order_id']) ?>&type=custom" class="view-details-btn">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-orders">
            <i class="fas fa-file-upload"></i>
            <h3>No custom orders found.</h3>
            <p>You haven't placed any custom orders yet. <a href="custom_order.php">Create one now</a>.</p>
        </div>
    <?php endif; ?>
</div>

</div>

<?php
$stmt_regular->close();
$stmt_custom->close();
$conn->close();
?>
</body>
</html>
