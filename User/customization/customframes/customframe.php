<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch available templates from DB (assuming a 'templates' table with columns: id, name, image_path, type (e.g., 'tshirt', 'frame'), sizes_allowed (JSON array))
$sql_templates = "SELECT * FROM templates ORDER BY name";
$result_templates = mysqli_query($conn, $sql_templates);
$templates = [];
while ($row = mysqli_fetch_assoc($result_templates)) {
    $templates[] = $row;
}

// If no templates table exists, you can hardcode some for demo (uncomment below)
// $templates = [
//     ['id' => 1, 'name' => 'White T-Shirt', 'image_path' => 'templates/white-tshirt.png', 'type' => 'tshirt', 'sizes_allowed' => json_encode(['S', 'M', 'L', 'XL'])],
//     ['id' => 2, 'name' => 'Black T-Shirt', 'image_path' => 'templates/black-tshirt.png', 'type' => 'tshirt', 'sizes_allowed' => json_encode(['S', 'M', 'L', 'XL'])],
//     ['id' => 3, 'name' => 'Wooden Frame', 'image_path' => 'templates/wooden-frame.png', 'type' => 'frame', 'sizes_allowed' => json_encode(['8x10', '11x14', '16x20'])],
//     ['id' => 4, 'name' => 'Modern Frame', 'image_path' => 'templates/modern-frame.png', 'type' => 'frame', 'sizes_allowed' => json_encode(['8x10', '11x14', '16x20'])],
// ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrintCity - Customize Your Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .header {
            grid-column: 1 / -1;
            text-align: center;
            color: white;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .upload-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            height: fit-content;
        }

        .upload-section h2 {
            margin-bottom: 20px;
            color: #667eea;
            text-align: center;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
        }

        .file-input {
            display: none;
        }

        .upload-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .upload-btn:hover {
            transform: scale(1.05);
        }

        .image-preview {
            width: 100%;
            max-height: 300px;
            border-radius: 15px;
            object-fit: cover;
            border: 3px dashed #667eea;
            display: none;
            margin: 20px 0;
        }

        .options-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .option-group {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid #667eea;
        }

        .option-group h3 {
            margin-bottom: 15px;
            color: #667eea;
        }

        .size-select, .template-select {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }

        .size-btn, .template-btn {
            background: white;
            border: 2px solid #e0e0e0;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .size-btn:hover, .template-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .size-btn.active, .template-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .template-btn img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .preview-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }

        .preview-section h2 {
            margin-bottom: 20px;
            color: #667eea;
            text-align: center;
        }

        .template-preview {
            width: 100%;
            max-width: 400px;
            height: 400px;
            margin: 0 auto 20px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            border: 2px solid #ddd;
            border-radius: 15px;
            overflow: hidden;
            cursor: move;
        }

        .uploaded-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5);
            max-width: 80%;
            max-height: 80%;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            cursor: grab;
            transition: transform 0.3s ease;
        }

        .uploaded-image:active {
            cursor: grabbing;
        }

        .controls {
            text-align: center;
            margin-top: 20px;
        }

        .zoom-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .zoom-btn:hover {
            background: #5a6fd8;
        }

        .add-to-cart {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border: none;
            border-radius: 50px;
            font-size: 1.5em;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-top: 20px;
        }

        .add-to-cart:hover {
            transform: scale(1.02);
        }

        .add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .template-preview {
                max-width: 300px;
                height: 300px;
            }
        }

        /* Loading Spinner */
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-palette"></i> Customize Your Print</h1>
            <p>Upload your image, choose a size and template, and see it come to life!</p>
        </div>

        <div class="upload-section">
            <h2><i class="fas fa-upload"></i> Upload Your Image</h2>
            <div class="file-input-wrapper">
                <input type="file" id="imageUpload" class="file-input" accept="image/*">
                <button class="upload-btn" onclick="document.getElementById('imageUpload').click()">
                    <i class="fas fa-cloud-upload-alt"></i> Choose Image
                </button>
            </div>
            <img id="imagePreview" class="image-preview" alt="Preview">
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing...</p>
            </div>

            <div class="options-section">
                <div class="option-group">
                    <h3><i class="fas fa-expand-arrows-alt"></i> Select Size</h3>
                    <div class="size-select" id="sizeSelect">
                        <!-- Sizes will be populated dynamically based on selected template -->
                    </div>
                </div>

                <div class="option-group">
                    <h3><i class="fas fa-images"></i> Choose Template</h3>
                    <div class="template-select" id="templateSelect">
                        <?php foreach ($templates as $template): ?>
                            <div class="template-btn" data-id="<?php echo $template['id']; ?>" data-name="<?php echo $template['name']; ?>" data-path="<?php echo $template['image_path']; ?>" data-type="<?php echo $template['type']; ?>" data-sizes='<?php echo $template['sizes_allowed']; ?>'>
                                <img src="<?php echo $template['image_path']; ?>" alt="<?php echo $template['name']; ?>">
                                <p><?php echo $template['name']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="preview-section">
            <h2><i class="fas fa-eye"></i> Live Preview</h2>
            <div class="template-preview" id="templatePreview" style="background-image: none;">
                <!-- Uploaded image will be placed here -->
            </div>
            <div class="controls">
                <button class="zoom-btn" onclick="zoomIn()"><i class="fas fa-search-plus"></i> Zoom In</button>
                <button class="zoom-btn" onclick="zoomOut()"><i class="fas fa-search-minus"></i> Zoom Out</button>
                <button class="zoom-btn" onclick="resetPosition()"><i class="fas fa-sync-alt"></i> Reset</button>
            </div>
            <button class="add-to-cart" id="addToCart" onclick="addToCart()" disabled>
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
        </div>
    </div>

    <script>
        let uploadedImage = null;
        let currentTemplate = null;
        let imageScale = 0.5;
        let imageX = 0;
        let imageY = 0;
        let isDragging = false;
        let dragStart = { x: 0, y: 0 };

        // Handle image upload
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('imagePreview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    uploadedImage = e.target.result;
                    document.getElementById('loading').style.display = 'none';
                    updatePreview(); // Update if template is already selected
                };
                reader.readAsDataURL(file);
                document.getElementById('loading').style.display = 'block';
            }
        });

        // Handle template selection
        document.querySelectorAll('.template-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.template-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentTemplate = {
                    id: this.dataset.id,
                    name: this.dataset.name,
                    path: this.dataset.path,
                    sizes: JSON.parse(this.dataset.sizes)
                };
                document.getElementById('templatePreview').style.backgroundImage = `url('${this.dataset.path}')`;
                populateSizes(this.dataset.sizes);
                if (uploadedImage) {
                    updatePreview();
                }
                updateAddToCartButton();
            });
        });

        // Populate sizes based on template
        function populateSizes(sizesJson) {
            const sizes = JSON.parse(sizesJson);
            const container = document.getElementById('sizeSelect');
            container.innerHTML = '';
            sizes.forEach(size => {
                const btn = document.createElement('div');
                btn.className = 'size-btn';
                btn.textContent = size;
                btn.onclick = () => {
                    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentTemplate.size = size; // Store selected size
                    updateAddToCartButton();
                };
                container.appendChild(btn);
            });
            // Select first size by default
            if (sizes.length > 0) {
                container.children[0].click();
            }
        }

        // Update preview with uploaded image on template
        function updatePreview() {
            if (!uploadedImage || !currentTemplate) return;
            const preview = document.getElementById('templatePreview');
            let img = preview.querySelector('.uploaded-image');
            if (img) img.remove();
            img = document.createElement('img');
            img.src = uploadedImage;
            img.className = 'uploaded-image';
            img.style.transform = `translate(-50%, -50%) scale(${imageScale}) translate(${imageX}px, ${imageY}px)`;
            preview.appendChild(img);

            // Add drag functionality
            img.addEventListener('mousedown', startDrag);
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
        }

        // Drag functionality
        function startDrag(e) {
            isDragging = true;
            dragStart.x = e.clientX - imageX;
            dragStart.y = e.clientY - imageY;
        }

        function drag(e) {
            if (isDragging) {
                imageX = e.clientX - dragStart.x;
                imageY = e.clientY - dragStart.y;
                updateImagePosition();
            }
        }

        function stopDrag() {
            isDragging = false;
        }

        function updateImagePosition() {
            const img = document.querySelector('.uploaded-image');
            if (img) {
                img.style.transform = `translate(-50%, -50%) scale(${imageScale}) translate(${imageX}px, ${imageY}px)`;
            }
        }

        // Zoom controls
        function zoomIn() {
            imageScale = Math.min(imageScale + 0.1, 2);
            updateImagePosition();
        }

        function zoomOut() {
            imageScale = Math.max(imageScale - 0.1, 0.2);
            updateImagePosition();
        }

        function resetPosition() {
            imageScale = 0.5;
            imageX = 0;
            imageY = 0;
            updateImagePosition();
        }

        // Update Add to Cart button
        function updateAddToCartButton() {
            const btn = document.getElementById('addToCart');
            if (uploadedImage && currentTemplate && currentTemplate.size) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        // Add to Cart (simulate or integrate with your cart system)
        function addToCart() {
            if (!uploadedImage || !currentTemplate || !currentTemplate.size) return;
            const formData = new FormData();
            formData.append('action', 'add_custom_product');
            formData.append('template_id', currentTemplate.id);
            formData.append('size', currentTemplate.size);
            formData.append('image', document.getElementById('imageUpload').files[0]); // Actual file

            // Simulate AJAX post to your backend (e.g., process.php)
            fetch('process_custom.php', { // Create this file to handle DB insert/cart
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Added to cart successfully!');
                    // Redirect to cart or reset form
                } else {
                    alert('Error adding to cart.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Initial state
        updateAddToCartButton();
    </script>
</body>
</html>
