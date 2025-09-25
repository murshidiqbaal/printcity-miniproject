<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="orders.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
  body {
    font-family: 'Poppins', sans-serif;
  }
  
  /* Status badge styles (add if not in orders.css) */
  .status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
  }
  .status-pending { background-color: #fff3cd; color: #856404; }
  .status-processing { background-color: #cce5ff; color: #004085; }
  .status-shipped { background-color: #d1ecf1; color: #0c5460; }
  .status-delivered { background-color: #d4edda; color: #155724; }
  .status-cancelled { background-color: #f8d7da; color: #721c24; }

  /* Tab container */
.tab-container {
    margin-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb; /* light gray */
}

/* Tab buttons container */
.tab-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Tab button default */
.tab-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem; /* text-sm */
    font-weight: 500;
    color: #4b5563; /* gray-700 */
    background-color: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: flex;
    align-items: center;
}

/* Tab button hover */
.tab-btn:hover {
    color: #1f2937; /* gray-900 */
}

/* Active tab */
.tab-btn.active {
    color: #2563eb; /* blue-600 */
    border-bottom-color: #2563eb; /* blue underline */
}

/* Icon spacing */
.tab-btn i {
    margin-right: 0.25rem; /* small spacing between icon and text */
}

</style>

</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="bg-white p-4 mb-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-900">Order Management</h2>
            <p class="text-sm text-gray-600 mt-1">Manage both product and custom orders</p>
        </div>
        <!-- Tab Navigation -->
                <div class="tab-container">
                    <div class="tab-buttons">
                        <button id="all-tab" class="tab-btn active" data-tab="all">
                            <i class="fas fa-list mr-1"></i> All Orders
                        </button>
                        <button id="product-tab" class="tab-btn" data-tab="product">
                            <i class="fas fa-shopping-cart mr-1"></i> Product Orders
                        </button>
                        <button id="custom-tab" class="tab-btn" data-tab="custom">
                            <i class="fas fa-file-upload mr-1"></i> Custom Orders
                        </button>
                    </div>
                </div>
        <div class="overflow-auto">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-4 sm:px-6 flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-lg font-medium text-gray-900">All Orders</h3>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <div class="relative">
                            <select id="status-filter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="all">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Processing">Processing</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="relative">
                            <input type="text" id="search" placeholder="Search orders..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table" class="bg-white divide-y divide-gray-200">
                        
<?php
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch regular product orders
$sql_regular = "SELECT 
            o.order_id,
            o.customer_name,
            o.address,
            o.product_id,
            p.name AS product_name,
            o.quantity,
            o.order_date,
            o.status,
            'regular' AS order_type
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        ORDER BY o.order_date DESC";

$result_regular = mysqli_query($conn, $sql_regular);

// Fetch custom orders (join with user_profiles for customer details)
$sql_custom = "SELECT 
            co.id AS order_id,
            up.full_name AS customer_name,
            up.address,
            co.file_path,
            co.quantity,
            co.print_type,
            co.paper_size,
            co.notes,
            co.status,
            co.order_date,
            'custom' AS order_type
        FROM custom_orders co
        LEFT JOIN user_profiles up ON co.user_id = up.user_id
        ORDER BY co.order_date DESC";

$result_custom = mysqli_query($conn, $sql_custom);

// Function to get file extension for custom orders
function getFileExtension($file_path) {
    if (empty($file_path)) return 'unknown';
    return strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
}

// Function to get display name for custom order product
function getCustomProductDisplay($file_path, $print_type, $paper_size, $notes = '') {
    $ext = getFileExtension($file_path);
    $type_label = ucfirst(str_replace('_', ' ', $print_type ?? 'unknown')) . ' on ' . strtoupper($paper_size ?? 'A4');
    $notes_preview = !empty($notes) ? ' - ' . substr($notes, 0, 30) . '...' : '';
    return "Custom: " . strtoupper($ext) . " - " . $type_label . $notes_preview;
}

// Display regular orders first
$has_orders = false;
if (mysqli_num_rows($result_regular) > 0) {
    $has_orders = true;
    while ($order = mysqli_fetch_assoc($result_regular)) {
        $status_lower = strtolower($order['status']);
        echo "<tr class='order-row' data-type='regular'>
                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>#{$order['order_id']}</td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <div class='text-sm text-gray-900'>" . htmlspecialchars($order['customer_name']) . "</div>
                    <div class='text-sm text-gray-500'>" . htmlspecialchars($order['address']) . "</div>
                </td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <div class='text-sm text-gray-900'>" . htmlspecialchars($order['product_name']) . " (#" . $order['product_id'] . ")</div>
                </td>
                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$order['quantity']}</td>
                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . date("Y-m-d", strtotime($order['order_date'])) . "</td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <span class='status-badge status-" . $status_lower . "'>" . htmlspecialchars($order['status']) . "</span>
                </td>
                <td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>
                    <button onclick=\"openStatusModal({$order['order_id']}, '{$order['status']}', 'regular')\" class='text-indigo-600 hover:text-indigo-900 mr-2'>Update</button>
                </td>
            </tr>";
    }
}

// Display custom orders
if (mysqli_num_rows($result_custom) > 0) {
    $has_orders = true;
    while ($custom_order = mysqli_fetch_assoc($result_custom)) {
        $status_lower = strtolower($custom_order['status']);
        $product_display = getCustomProductDisplay($custom_order['file_path'], $custom_order['print_type'], $custom_order['paper_size'], $custom_order['notes']);
        $customer_name = !empty($custom_order['customer_name']) ? $custom_order['customer_name'] : 'Unknown User';
        $customer_address = !empty($custom_order['address']) ? $custom_order['address'] : 'No address provided';
        echo "<tr class='order-row' data-type='custom' style='background-color: #f8f9ff;'>
                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>C{$custom_order['order_id']}</td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <div class='text-sm text-gray-900'>" . htmlspecialchars($customer_name) . "</div>
                    <div class='text-sm text-gray-500'>" . htmlspecialchars($customer_address) . "</div>
                </td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <div class='text-sm text-gray-900 font-medium text-blue-600'>" . htmlspecialchars($product_display) . "</div>
                    <small class='text-gray-500 block'>File: " . htmlspecialchars(basename($custom_order['file_path'])) . "</small>
                </td>
                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$custom_order['quantity']}</td>
                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . date("Y-m-d", strtotime($custom_order['order_date'])) . "</td>
                <td class='px-6 py-4 whitespace-nowrap'>
                    <span class='status-badge status-" . $status_lower . "'>" . htmlspecialchars($custom_order['status']) . "</span>
                </td>
                <td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>
                    <button onclick=\"openStatusModal({$custom_order['order_id']}, '{$custom_order['status']}', 'custom')\" class='text-indigo-600 hover:text-indigo-900 mr-2'>Update</button>
                </td>
            </tr>";
    }
}

if (!$has_orders) {
    echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-500'>No orders found</td></tr>";
}

mysqli_close($conn);
?>
</tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo mysqli_num_rows($result_regular) + mysqli_num_rows($result_custom); ?></span> of <span class="font-medium"><?php echo mysqli_num_rows($result_regular) + mysqli_num_rows($result_custom); ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Update Order Status</h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Order ID: <span id="modalOrderId" class="font-medium"></span></p>
                                <p class="text-sm text-gray-500" id="modalOrderType" style="display: none;"></p> <!-- Hidden by default, shown if custom -->
                                <div class="mt-4">
                                    <label for="statusSelect" class="block text-sm font-medium text-gray-700">New Status</label>
                                    <select id="statusSelect" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="Pending">Pending</option>
                                        <option value="Processing">Processing</option>
                                        <option value="Shipped">Shipped</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="updateOrderStatus()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button" onclick="closeStatusModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    <div id="successNotification" class="fixed bottom-4 right-4 hidden">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-bounce-in" role="alert">
            <strong class="font-bold">Success! </strong>
            <span class="block sm:inline" id="notificationMessage">Order status updated successfully.</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="hideNotification()">
                <i class="fas fa-times cursor-pointer"></i>
            </span>
        </div>
    </div>

    <script>
        let currentOrderId = null;
        let currentOrderType = null;

        // Open status modal (updated to handle order type)
        function openStatusModal(orderId, currentStatus, orderType) {
            currentOrderId = orderId;
            currentOrderType = orderType || 'regular';
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('statusSelect').value = currentStatus;
            const modalOrderType = document.getElementById('modalOrderType');
            if (orderType === 'custom') {
                modalOrderType.textContent = '(Custom Order)';
                modalOrderType.style.display = 'block';
            } else {
                modalOrderType.style.display = 'none';
            }
            document.getElementById('statusModal').classList.remove('hidden');
        }

        // Close status modal
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Update order status (updated to send order type)
        function updateOrderStatus() {
            const orderId = document.getElementById('modalOrderId').innerText;
            const newStatus = document.getElementById('statusSelect').value;
            const orderType = currentOrderType || 'regular';
            fetch('order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: newStatus, order_type: orderType })
            })
            .then(response => response.json())
            .then(data => { 
                if (data.success) {
                    // Update status badge in table
                    const rows = document.querySelectorAll('#orders-table tr');
                    rows.forEach(row => {
                        const idCell = row.querySelector('td:first-child');
                        if (idCell && idCell.textContent.replace('#', '').replace('C', '') == orderId) {
                            const statusCell = row.querySelector('td:nth-child(6) span');
                            if (statusCell) {
                                statusCell.textContent = newStatus;
                                statusCell.className = 'status-badge status-' + newStatus.toLowerCase();
                            }
                        }
                    });
                    showNotification('Order status updated successfully.');
                } else {
                    alert('Error updating status: ' + data.message);
                }
                closeStatusModal();
            })
            .catch(error => {
                alert('Error updating status: ' + error);
                closeStatusModal();
            });
        }
        // Show success notification
        function showNotification(message) {
            const notification = document.getElementById('successNotification');
            document.getElementById('notificationMessage').textContent = message;
            notification.classList.remove('hidden');
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }
        // Hide notification on close icon click
        function hideNotification() {
            document.getElementById('successNotification').classList.add('hidden');
        }
        // Filter orders by status
        document.getElementById('status-filter').addEventListener('change', function() {
            const selectedStatus = this.value;
            const rows = document.querySelectorAll('#orders-table tr');
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(6) span');
                if (statusCell) {
                    const statusText = statusCell.textContent;
                    if (selectedStatus === 'all' || statusText === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
        // Search orders
        document.getElementById('search').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#orders-table tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(query)) {
                        match = true;
                    }
                });
                row.style.display = match ? '' : 'none';
            });
        });
        // Tab functionality
const tabButtons = document.querySelectorAll('.tab-btn');
const orderRows = document.querySelectorAll('.order-row');

tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove 'active' class from all buttons
        tabButtons.forEach(b => b.classList.remove('active'));
        // Add 'active' to clicked button
        btn.classList.add('active');

        const tab = btn.dataset.tab; // 'all', 'product', 'custom'

        orderRows.forEach(row => {
            const type = row.dataset.type; // 'regular' or 'custom'
            if (tab === 'all') {
                row.style.display = '';
            } else if (tab === 'product' && type === 'regular') {
                row.style.display = '';
            } else if (tab === 'custom' && type === 'custom') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

    </script>
</body>
</html>
