<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    die("User  not logged in");
}

$user_id = intval($_SESSION['user_id']); // current logged-in user

$query = "
    SELECT 
        f.id AS favourite_id,
        f.created_at AS favourite_date,
        p.product_id,
        p.name,
        p.description,
        p.image_path,
        p.price
    FROM favourites f
    JOIN products p ON f.product_id = p.product_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favourites = [];
while ($row = $result->fetch_assoc()) {
    $favourites[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --gray-light: #f3f4f6;
            --gray-dark: #6b7280;
            --text: #1f2937;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background-color: #f9fafb;
        }
        
        .product-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
        }
        
        .favorite-icon {
            color: #ef4444;
            transition: all 0.2s ease;
        }
        
        .favorite-icon:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="min-h-screen">
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
        <a href="../HomePage/index.php" class="text-black me-3" style="font-size: 1.5rem;">
        <i class="fas fa-arrow-left"></i>
        
    </a>
            <h1 class="text-3xl font-bold text-gray-900">My Favorites</h1>
            <div class="flex items-center">
                <span class="text-gray-600 mr-2">Sort by:</span>
                <select id="sort-options" class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="recent">Recently Added</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="rating">Rating</option>
                </select>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="empty-state" class="<?= empty($favourites) ? '' : 'hidden' ?> flex flex-col items-center justify-center py-12">
            <i class="far fa-heart text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No favorites yet</h3>
            <p class="text-gray-500 mb-6">Items you favorite will appear here</p>
            <a href="../ProductPage/productpage.php" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">Start Shopping</a>
        </div>
        
        <!-- Favorites Grid -->
        <div id="favorites-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 <?= empty($favourites) ? 'hidden' : '' ?>">
            <?php foreach ($favourites as $fav): ?>
                <div class="product-card bg-white rounded-lg overflow-hidden flex flex-col">
                    <div class="relative">
                        <img src="../../Admin/Products/<?= htmlspecialchars($fav['image_path']) ?>" 
                             alt="<?= htmlspecialchars($fav['name']) ?>" 
                             class="w-full h-48 object-cover" 
                             onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'" />
                        <button class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md favorite-icon" 
        data-product-id="<?= $fav['product_id'] ?>" 
        onclick="removeFromFavourites(<?= $fav['product_id'] ?>)">
    <i class="fas fa-heart text-red-500"></i>
</button>

                        <span class="absolute bottom-2 left-2 bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Added <?= date("M j, Y", strtotime($fav['favourite_date'])) ?>
                        </span>
                    </div>
                    <div class="p-4 flex-grow flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-gray-900 line-clamp-2"><?= htmlspecialchars($fav['name']) ?></h3>
                            <span class="text-lg font-bold text-gray-900">$<?= number_format($fav['price'], 2) ?></span>
                        </div>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($fav['description']) ?></p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-2 flex">

                        <button onclick="location.href='../OrderPage/orderpage.php?product_id=<?= $fav['product_id'] ?>'" class="flex-1 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-md">
                            Buy now
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

<script>
function removeFromFavourites(productId) {
    if (!confirm("Remove this product from favourites?")) return;

    fetch("remove_favourite.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "product_id=" + productId
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // You can replace with UI update instead of alert
        // Optionally remove the product card from UI
        location.reload();
    })
    .catch(error => console.error("Error:", error));
}
</script>


</body>
</html>
