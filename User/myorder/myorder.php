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

/* New: actions alignment */
.card-actions {
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
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

@keyframes cardFadeIn {
    0% { opacity: 0; transform: translateY(16px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.order-grid .order-card {
    opacity: 0;
    transform: translateY(16px) scale(0.98);
}
.order-grid .order-card.reveal {
    animation: cardFadeIn .6s ease forwards;
}

@media (prefers-reduced-motion: reduce) {
    .order-grid .order-card, .order-grid .order-card.reveal { animation: none; opacity: 1; transform:none; }
}

/* ... rest existing styles ... */
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
                    <div class="actions-row">
                        <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($order['status']) ?></span>
                        <a href="trackorder.php?order_id=<?= $order['order_id'] ?>&type=product" class="track-btn">
                            <i class="fas fa-truck"></i> Track Order
                        </a>
                    </div>
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
        <div class="order-card <?= $card_class ?> custom-order-card <?= $file_type_class ?>">
            <div class="custom-order-image <?= $file_type_class ?>" data-file-type="<?= $file_type_label ?>">
                <?php
                    // Display hardcoded icons for each file type
                    switch ($file_extension) {
                        case 'pdf':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#dc3545" rx="8"/>
                                    <text x="50" y="35" font-size="24" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">PDF</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Document</text>
                                  </svg>';
                            break;
                        case 'doc':
                        case 'docx':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#0d6efd" rx="8"/>
                                    <text x="50" y="35" font-size="20" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">DOC</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Word</text>
                                  </svg>';
                            break;
                        case 'txt':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#6c757d" rx="8"/>
                                    <text x="50" y="35" font-size="18" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">TXT</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Text</text>
                                  </svg>';
                            break;
                        case 'rtf':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#8b5cf6" rx="8"/>
                                    <text x="50" y="35" font-size="20" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">RTF</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Rich Text</text>
                                  </svg>';
                            break;
                        case 'xls':
                        case 'xlsx':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#198754" rx="8"/>
                                    <text x="50" y="35" font-size="18" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">XLS</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Excel</text>
                                  </svg>';
                            break;
                        case 'ppt':
                        case 'pptx':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#fd7e14" rx="8"/>
                                    <text x="50" y="35" font-size="18" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">PPT</text>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">PowerPoint</text>
                                  </svg>';
                            break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                        case 'bmp':
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#20c997" rx="8"/>
                                    <circle cx="35" cy="25" r="6" fill="white"/>
                                    <path d="M 15 40 L 30 25 L 45 35 L 60 20 L 75 35 L 80 30 L 80 50" stroke="white" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">Image</text>
                                  </svg>';
                            break;
                        default:
                            echo '<svg class="file-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="#f5f5f5" rx="8"/>
                                    <rect width="100" height="60" fill="#999" rx="8"/>
                                    <path d="M 35 25 L 35 45 L 65 45 L 65 25 M 40 35 L 60 35" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    <text x="50" y="80" font-size="12" fill="#666" text-anchor="middle">File</text>
                                  </svg>';
                            break;
                    }
                ?>
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
                <div class="actions-row">
                    <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($status) ?></span>
                    <a href="trackorder.php?order_id=<?= intval($custom_order['order_id']) ?>&type=custom" class="view-details-btn">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
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
<script>
// IntersectionObserver to reveal cards on scroll with stagger
(function(){
    const cards = document.querySelectorAll('.order-grid .order-card');
    if (!('IntersectionObserver' in window) || !cards.length) {
        cards.forEach(c => c.classList.add('reveal'));
        return;
    }
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                if (!el.dataset.staggerSet) {
                    const siblings = Array.from(el.parentElement.children);
                    const idx = siblings.indexOf(el);
                    el.style.animationDelay = (idx * 0.07) + 's';
                    el.dataset.staggerSet = '1';
                }
                el.classList.add('reveal');
                io.unobserve(el);
            }
        });
    }, { threshold: 0.12 });
    cards.forEach(c => io.observe(c));
})();
</script>
</body>
</html>
