<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-processing {
            background-color: #E0E7FF;
            color: #3730A3;
        }
        .status-shipped {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-delivered {
            background-color: #DCFCE7;
            color: #166534;
        }
        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .order-row:hover {
            background-color: #F9FAFB;
        }
        .animate-bounce-in {
            animation: bounceIn 0.5s;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.95); opacity: 0; }
            50% { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="bg-white p-4 mb-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-900">Order Management</h2>
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

$sql = "SELECT 
            o.order_id,
            o.customer_name,
            o.address,
            o.product_id,
            p.name AS product_name,
            o.quantity,
            o.order_date,
            o.status
        FROM orders o
        JOIN products p ON o.product_id = p.product_id
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($order = mysqli_fetch_assoc($result)) {
        echo "<tr class='order-row'>
                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>{$order['order_id']}</td>
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
                    <span class='status-badge status-" . strtolower($order['status']) . "'>" . htmlspecialchars($order['status']) . "</span>
                </td>
                <td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>
                    <button onclick=\"openStatusModal({$order['order_id']}, '{$order['status']}')\" class='text-indigo-600 hover:text-indigo-900 mr-2'>Update</button>
                </td>
            </tr>";
    }
} else {
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
                                Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span class="font-medium">2</span> results
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

        // Fetch orders from the API
        async function fetchOrders() {
            try {
                const response = await fetch('/api/orders'); // Replace with your API endpoint
                const orders = await response.json();
                const ordersTable = document.getElementById('orders-table');
                ordersTable.innerHTML = ''; // Clear existing rows

                orders.forEach(order => {
                    const row = document.createElement('tr');
                    row.className = 'order-row';
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${order.order_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${order.customer_name}</div>
                            <div class="text-sm text-gray-500">${order.address}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Product #${order.product_id}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${order.quantity}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(order.order_date).toLocaleDateString()}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="openStatusModal(${order.order_id}, '${order.status}')" class="text-indigo-600 hover:text-indigo-900 mr-2">Update</button>
                        </td>
                    `;
                    ordersTable.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        // Call fetchOrders on page load
        window.onload = fetchOrders;

        // Open status modal
        function openStatusModal(orderId, currentStatus) {
            currentOrderId = orderId;
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('statusSelect').value = currentStatus;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        // Close status modal
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Update order status
       function updateOrderStatus() {
    const orderId = document.getElementById('modalOrderId').innerText;
    const newStatus = document.getElementById('statusSelect').value;

    fetch('order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${orderId}&status=${encodeURIComponent(newStatus)}`
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // check for "success" or error
        closeStatusModal();
        location.reload(); // refresh to see the updated status
    })
    .catch(error => console.error('Error:', error));
}

        // Hide notification
        function hideNotification() {
            document.getElementById('successNotification').classList.add('hidden');
        }

        // Filter orders by status
        document.getElementById('status-filter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.order-row');
            rows.forEach(row => {
                const rowStatus = row.querySelector('td:nth-child(6) span').textContent;
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Search functionality
        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.order-row');
            rows.forEach(row => {
                const orderId = row.querySelector('td:first-child').textContent.toLowerCase();
                const customer = row.querySelector('td:nth-child(2) div:first-child').textContent.toLowerCase();
                if (orderId.includes(searchTerm) || customer.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });
    </script>
</body>
</html>
