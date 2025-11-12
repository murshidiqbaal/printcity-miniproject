<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location:../../Auth/login/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$user_query = "SELECT * FROM user_profiles WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Validate profile details
$valid_profile = !empty($user['full_name']) && !empty($user['email']) && !empty($user['phone']) && !empty($user['address']);

// Determine product type
$product = null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

$product_id = null;

// Check for normal product
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
}
// Check for offer product
elseif (isset($_GET['offer_product_id'])) {
    $product_id = intval($_GET['offer_product_id']);
    $sql = "SELECT * FROM offer_products WHERE offer_product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
}

// If neither exists or product not found
if (!$product) {
    echo "<p>No product selected!</p>";
    exit;
}


// Redirect if profile incomplete
if (!$valid_profile) {
    header("Location: ../myprofile/myprofile.php?redirect=orderpage.php&product_id=" . $product_id . (isset($_GET['type']) ? "&type=".$_GET['type'] : ""));
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .quantity-btn {
            transition: all 0.2s ease;
        }
        .quantity-btn:active {
            transform: scale(0.95);
        }
        #cart-items {
            max-height: 60vh;
            overflow-y: auto;
        }
        .cart-item {
            transition: all 0.2s ease;
        }
        .cart-item:hover {
            background-color: rgba(243, 244, 246, 0.8);
        }
        .cart-empty {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Pulse animation for qty icons */
        .iconPulse {
            animation: qtyPulse 0.25s ease;
        }
        @keyframes qtyPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->

    <?php

$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    
    // Count favourites for this user
    $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM favourites WHERE user_id = ?");
    $countQuery->bind_param("i", $user_id);
    $countQuery->execute();
    $countResult = $countQuery->get_result()->fetch_assoc();
    $cart_count = $countResult['total'] ?? 0;
}
?>

    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">PrintCity</h1>
            <a href="../favourite/favourite.php?user_id=<?= $_SESSION['user_id']; ?>" 
   class="relative p-2 rounded-full hover:bg-blue-500 transition">
    <i class="fas fa-heart text-xl"></i>
    <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full w-5 h-5 flex items-center justify-center">
        <?= $cart_count; ?>
    </span>
</a>

                <div id="cart-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div id="cart-items" class="p-4">
                        <div id="empty-cart" class="cart-empty text-center py-8">
                            <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/c2718097-d82c-4727-bbae-1e1117c8341a.png" alt="Empty shopping cart with a sad face" class="mx-auto mb-4 rounded-lg">
                            <p class="text-gray-500 mb-2">Your cart is empty</p>
                            <p class="text-sm text-gray-400">Start adding products to see them here</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 p-4 bg-gray-50">
                        <div class="flex justify-between mb-4">
                            <span class="font-semibold">Total:</span>
                            <span id="cart-total" class="font-bold text-blue-600">$0.00</span>
                        </div>
                        <button id="checkout-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md disabled:bg-blue-300">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
document.addEventListener("DOMContentLoaded", function () {
    function updateCartCount() {
        fetch("../favourite/get_count.php")
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("cart-count").innerText = data.count;
                }
            })
            .catch(err => console.error("Error fetching cart count:", err));
    }

    updateCartCount(); // run on page load
});
</script>

    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 md:p-6 lg:p-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Product Details -->
            <div class="product-card lg:w-2/3 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <!-- Product Image -->
                    <div class="md:w-1/2 p-4">
                        <img src="../../Admin/Products/<?php echo $product['image_path']; ?>" alt="Premium wireless headphones with black matte finish and adjustable headband" class="w-full h-auto rounded-lg">
                    </div>
                    
            <!-- Product Info -->
<div class="md:w-1/2 p-6">
    <!-- Best Seller Badge -->
    <?php if (!empty($product['is_best_seller']) && $product['is_best_seller'] == 1): ?>
        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-2 inline-block">Best Seller</span>
    <?php endif; ?>

    <!-- Product Name -->
    <h2 class="text-2xl font-bold text-gray-800 mb-2">
        <?php echo htmlspecialchars($product['name']); ?>
    </h2>

    <!-- Dummy Reviews (can be dynamic later) -->
    <div class="flex items-center mb-3">
        <div class="flex text-yellow-400">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
        </div>
        <span class="text-gray-500 ml-2">(382 reviews)</span>
    </div>

    <!-- Pricing -->
    <div class="mb-4">
        <?php 
        $price = $product['price'] ?? 0;
        $discount = $product['discount'] ?? 0;
        $final_price = $price;
        if ($discount > 0) {
            $final_price = $price - ($price * ($discount / 100));
        }
        ?>
        <span class="text-3xl font-bold text-gray-900">
            ₹<?php echo number_format($final_price, 2); ?>
        </span>

        <?php if ($discount > 0): ?>
            <span class="text-sm text-gray-500 line-through ml-2">
                ₹<?php echo number_format($price, 2); ?>
            </span>
            <span class="text-sm text-green-600 ml-2">
                <?php echo htmlspecialchars($discount); ?>% Off
            </span>
        <?php endif; ?>
    </div>

    <!-- Features -->
    <div class="mb-5">
        <p class="text-gray-600 mb-2">Features:</p>
        <ul class="list-disc pl-5 text-gray-600 space-y-1">
            <?php 
            if (!empty($product['features'])) {
                $features = explode("\n", $product['features']); // store features in DB separated by newlines
                foreach ($features as $f) {
                    if (trim($f) !== '') {
                        echo "<li>" . htmlspecialchars($f) . "</li>";
                    }
                }
            } else {
                echo "<li>No features available</li>";
            }
            ?>
        </ul>
    </div>

    <!-- Quantity Selector -->
<div class="mb-6">
  <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
  <div class="flex items-center">
    <button type="button" id="qty-minus"
      class="quantity-btn bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-l-lg">
      <i class="fas fa-minus"></i>
    </button>

    <input type="number" id="quantity" value="1" min="1" 
           max="<?php echo $product['stock']; ?>"
           class="w-16 text-center border-t border-b border-gray-300 py-1">

    <button type="button" id="qty-plus"
      class="quantity-btn bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-r-lg">
      <i class="fas fa-plus"></i>
    </button>

    <div class="ml-4 text-sm text-gray-500">
      <?php if (isset($product['stock'])): ?>
        <?php if ($product['stock'] <= 0): ?>
          <button disabled class="btn btn-secondary text-red-600">Out of Stock</button>
        <?php else: ?>
          <span class="text-green-600"><?php echo $product['stock']; ?> in stock</span>
        <?php endif; ?>
      <?php else: ?>
        <span class="text-gray-500">Stock info not available</span>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const qtyInput = document.getElementById("quantity");
  const qtyMinus = document.getElementById("qty-minus");
  const qtyPlus = document.getElementById("qty-plus");
  const hiddenQuantityInput = document.getElementById("hidden-quantity");
  const summaryHiddenInput = document.getElementById("summary-hidden-quantity");
  const productPrice = <?php echo floatval($product['price']); ?>;
  const maxStock = <?php echo intval($product['stock']); ?>;

  // Helper: update hidden fields & totals
  function syncQuantity() {
    const quantity = parseInt(qtyInput.value) || 1;
    if (hiddenQuantityInput) hiddenQuantityInput.value = quantity;
    if (summaryHiddenInput) summaryHiddenInput.value = quantity;
    updateOrderSummary();
  }

  // Quantity +/- buttons
  qtyMinus.addEventListener("click", () => {
    let value = parseInt(qtyInput.value) || 1;
    value = Math.max(1, value - 1);
    qtyInput.value = value;
    pulse(qtyMinus);
    syncQuantity();
  });

  qtyPlus.addEventListener("click", () => {
    let value = parseInt(qtyInput.value) || 1;
    if (maxStock > 0) value = Math.min(maxStock, value + 1);
    else value = value + 1;
    qtyInput.value = value;
    pulse(qtyPlus);
    syncQuantity();
  });

  // Typing manually also updates total
  qtyInput.addEventListener("input", syncQuantity);

  // Update totals on load
  function updateOrderSummary() {
    const subtotalEl = document.getElementById("order-subtotal");
    const totalEl = document.getElementById("order-total");
    const q = parseInt(qtyInput.value) || 1;
    const subtotal = productPrice * q;
    if (subtotalEl) subtotalEl.textContent = "₹" + subtotal.toFixed(2);
    if (totalEl) totalEl.textContent = "₹" + subtotal.toFixed(2);
  }

  // Add pulse animation to buttons
  function pulse(el) {
    el.classList.remove("iconPulse");
    void el.offsetWidth; // force reflow
    el.classList.add("iconPulse");
  }

  updateOrderSummary();
});
</script>


    
    <!-- Action Buttons -->
    <div class="flex gap-3">
        <button id="add-to-favourite"
            data-product-id="<?= htmlspecialchars($product_id) ?>
"
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition favourite-btn">
            <i class="fas fa-heart mr-2"></i> Favourite
        </button>

     



<script src="../favourite/favourite.js"></script>

                            <form action="Payment/payment.php" method="POST" id="order-form">
    <?php if (!empty($product['product_id'])): ?>
        <!-- Normal Product -->
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
    <?php elseif (!empty($product['offer_product_id'])): ?>
        <!-- Offer Product -->
        <input type="hidden" name="offer_product_id" value="<?= htmlspecialchars($product['offer_product_id']) ?>">
    <?php endif; ?>
    <input type="hidden" name="quantity" id="hidden-quantity" value="1">
</form>

<?php if ($product['stock'] > 0): ?>
    <button id="place-order" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md transition">
        <i class="fas fa-bolt mr-2"></i> Buy Now
    </button>
<?php else: ?>
    <button disabled class="flex-1 bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed opacity-70">
        Out of Stock
    </button>
<?php endif; ?>

                        </div>
                    </div>
                </div>
                <script>
    const buyNowBtn = document.getElementById('place-order');
    const orderForm = document.getElementById('order-form');
    const quantityInput = document.getElementById('quantity');
    const hiddenQuantityInput = document.getElementById('hidden-quantity');

    if (buyNowBtn && orderForm) {
        buyNowBtn.addEventListener('click', function() {
            // Update quantity from visible input before submitting
            if (quantityInput && hiddenQuantityInput) {
                hiddenQuantityInput.value = quantityInput.value;
            }
            orderForm.submit(); // Submit the form
        });
    }
</script>
               <!-- Product Description -->
<div class="border-t border-gray-200 p-6">
    <h3 class="font-bold text-lg mb-3 text-gray-800">Product Description</h3>
    <p class="text-gray-600 mb-4">
        <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : "No description available."; ?>
    </p>
</div>
<hr>
            
            <!-- Order Summary -->
            <div class="lg:w-1/3 lg:ml-auto">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Order Summary</h3>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span id="order-subtotal"><?php echo number_format($product['price'], 2); ?></span>
                            <script>
                                const productPrice = <?php echo floatval($product['price']); ?>;
                            </script>

                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Discount</span>
                            <span class="text-green-600">-$00.00</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <?php
// Set $quantity safely
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Now you can safely use it
$total = $product['price'] * $quantity;
?>
<div class="border-t border-gray-200 pt-2">
    <div class="flex justify-between font-bold text-gray-900">
        <span>Total</span>
        <span id="order-total"><?php echo $total; ?></span>
    </div>
</div>
                    </div>
                    
        <!-- Form with Hidden Input -->
<?php if ($product['stock'] > 0): ?>
    <form action="Payment/payment.php" method="POST" id="summary-order-form">
        <?php if (!empty($product['product_id'])): ?>
            <!-- Normal Product -->
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
        <?php elseif (!empty($product['offer_product_id'])): ?>
            <!-- Offer Product -->
            <input type="hidden" name="offer_product_id" value="<?= htmlspecialchars($product['offer_product_id']) ?>">
        <?php endif; ?>

        <!-- Quantity -->
        <input type="hidden" name="quantity" id="summary-hidden-quantity" value="1">

        <!-- Submit Button -->
        <button type="submit" id="summary-place-order"
            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-md font-semibold transition">
            Place Order
        </button>
    </form>
<?php else: ?>
    <button disabled class="w-full bg-gray-400 text-white py-3 rounded-md font-semibold opacity-70 cursor-not-allowed">
        Out of Stock
    </button>
<?php endif; ?>


<script>
const visibleInput = document.getElementById('quantity');
const summaryHiddenInput = document.getElementById('summary-hidden-quantity');

const summaryOrderForm = document.getElementById('summary-order-form');
const summaryPlaceOrderBtn = document.getElementById('summary-place-order');


// Sync quantity input with both hidden inputs
if (visibleInput) {
    visibleInput.addEventListener('input', function() {
        if (summaryHiddenInput) {
            summaryHiddenInput.value = visibleInput.value;
        }
        if (hiddenQuantityInput) {
            hiddenQuantityInput.value = visibleInput.value;
        }
        updateOrderSummary(); // Update order summary display
    });
}

// Place Order button acts like Buy Now
// Ensure quantity sync on submit
if (summaryOrderForm) {
    summaryOrderForm.addEventListener('submit', function() {
        if (visibleInput && summaryHiddenInput) {
            summaryHiddenInput.value = visibleInput.value;
        }
    });
}

// Sync hidden input right before form submit (extra safety)
if (orderForm && hiddenQuantityInput && visibleInput) {
    orderForm.addEventListener('submit', function() {
        hiddenQuantityInput.value = visibleInput.value;
    });
}
</script>
                    
                    <div class="mt-4 text-xs text-gray-500">
                        <p class="flex items-center mb-1">
                            <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                            Secure Checkout
                        </p>
                        <p class="flex items-center mb-1">
                            <i class="fas fa-undo mr-2 text-blue-500"></i>
                            30-Day Return Policy
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-truck mr-2 text-blue-500"></i>
                            Free Shipping Worldwide
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Cart functionality
        let cart = [];
        
        // DOM Elements
        const cartToggle = document.getElementById('cart-toggle');
        const cartDropdown = document.getElementById('cart-dropdown');
        const cartCount = document.getElementById('cart-count');
        const cartItems = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        const cartTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        const addToCartBtn = document.getElementById('add-to-cart');
        
        const placeOrderBtn = document.getElementById('place-order');
        
        const orderSubtotal = document.getElementById('order-subtotal');
        const orderTotal = document.getElementById('order-total');
        
        // Toggle cart dropdown
        cartToggle.addEventListener('click', function() {
            cartDropdown.classList.toggle('hidden');
        });
        
        // Close cart when clicking outside
        document.addEventListener('click', function(e) {
            if (!cartDropdown.contains(e.target) && e.target !== cartToggle) {
                cartDropdown.classList.add('hidden');
            }
        });
        
// Update quantity
function updateQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const hiddenQuantityInput = document.getElementById('hidden-quantity');
    const summaryHiddenInput = document.getElementById('summary-hidden-quantity');
    const maxStock = <?php echo intval($product['stock']); ?>; // max stock from PHP
    
    if (!quantityInput) return;
    
    let current = parseInt(quantityInput.value);
    let newValue = current + change;

    if (newValue < 1) newValue = 1;
    // Only cap to stock when stock is a positive number
    if (maxStock > 0 && newValue > maxStock) newValue = maxStock;

    quantityInput.value = newValue;
    
    // Sync with both hidden inputs
    if (hiddenQuantityInput) {
        hiddenQuantityInput.value = newValue;
    }
    if (summaryHiddenInput) {
        summaryHiddenInput.value = newValue;
    }
    
    updateOrderSummary();
}

function updateOrderSummary() {
    const quantityInput = document.getElementById('quantity');
    const orderSubtotal = document.getElementById('order-subtotal');
    const orderTotal = document.getElementById('order-total');
    
    if (!quantityInput || !orderSubtotal || !orderTotal) return;
    
    const quantity = parseInt(quantityInput.value) || 1;
    const subtotal = productPrice * quantity;

    // Update DOM
    orderSubtotal.textContent = "₹" + subtotal.toFixed(2);
    orderTotal.textContent = "₹" + subtotal.toFixed(2);
}

// Run once on page load
document.addEventListener("DOMContentLoaded", () => {
    updateOrderSummary();
    // Attach pulse animation on icon click and keep quantity in sync
    const plusBtn = document.getElementById('qty-plus');
    const minusBtn = document.getElementById('qty-minus');
    const plusIcon = document.getElementById('qty-plus-icon');
    const minusIcon = document.getElementById('qty-minus-icon');

    function pulse(el) {
        if (!el) return;
        el.classList.remove('iconPulse');
        // Force reflow to restart animation
        void el.offsetWidth;
        el.classList.add('iconPulse');
    }

    if (plusBtn) {
        plusBtn.addEventListener('click', () => {
            pulse(plusIcon);
        });
    }
    if (minusBtn) {
        minusBtn.addEventListener('click', () => {
            pulse(minusIcon);
        });
    }
});
        
        // Add to cart
        addToCartBtn.addEventListener('click', function() {
            const product = {
                id: Date.now(),
                name: "Premium Wireless Headphones",
                price: product['price'],
                image: "https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/450d23b8-30f2-43b2-a1ad-feeb1d6827b8.png",
                quantity: parseInt(quantityInput.value),
                discount: 50.00
            };
            
            cart.push(product);
            updateCart();
            
            // Show feedback
            const feedback = document.createElement('div');
            feedback.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg';
            feedback.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Added to cart!';
            document.body.appendChild(feedback);
            
            setTimeout(() => {
                feedback.style.transition = 'opacity 0.5s';
                feedback.style.opacity = '0';
                setTimeout(() => feedback.remove(), 500);
            }, 2000);
        });
        
        // Buy now
        buyNowBtn.addEventListener('click', function() {
            addToCartBtn.click();
            setTimeout(() => {
                placeOrderBtn.click();
            }, 1000);
        });
        
        // Update cart display
        function updateCart() {
            cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `$${total.toFixed(2)}`;
            
            if (cart.length > 0) {
                emptyCart.style.display = 'none';
                checkoutBtn.disabled = false;
                
                // Clear existing items
                cartItems.innerHTML = '';
                
                // Add cart items
                cart.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.className = 'cart-item flex justify-between items-center mb-3 pb-3 border-b border-gray-100 last:border-0';
                    cartItem.innerHTML = `
                        <div class="flex items-center">
                            <img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded-md mr-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-800">${item.name}</h4>
                                <p class="text-xs text-gray-500">$${item.price.toFixed(2)} x ${item.quantity}</p>
                            </div>
                        </div>
                        <div class="text-gray-800 font-medium">
                            $${(item.price * item.quantity).toFixed(2)}
                        </div>
                    `;
                    cartItems.appendChild(cartItem);
                });
            } else {
                emptyCart.style.display = 'block';
                checkoutBtn.disabled = true;
            }
        }
        
    


    </script>
</body>
</html>

<?php mysqli_close($conn); ?>





