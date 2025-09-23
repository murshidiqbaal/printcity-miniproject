<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Auth/login/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quantity = intval($_POST['quantity'] ?? 1);
    $print_type = mysqli_real_escape_string($conn, $_POST['print_type'] ?? 'color');
    $paper_size = mysqli_real_escape_string($conn, $_POST['paper_size'] ?? 'A4');
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    // Handle file upload
    $uploaded_file = '';
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "custom_uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Validate file type (PDF, DOC, DOCX, etc.)
        $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        $file_extension = strtolower(pathinfo($_FILES["file_upload"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            $error_message = "Invalid file type. Allowed: PDF, DOC, DOCX, TXT, RTF.";
        } elseif ($_FILES['file_upload']['size'] > 5000000) { // 5MB limit
            $error_message = "File too large. Maximum 5MB allowed.";
        } else {
            // Secure filename
            $file_name = time() . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
                $uploaded_file = $target_file;
            } else {
                $error_message = "Error uploading file.";
            }
        }
    } else {
        $error_message = "Please select a file to upload.";
    }

    // If no errors, insert into custom_orders table
    if (empty($error_message) && !empty($uploaded_file)) {
        $sql = "INSERT INTO custom_orders 
        (user_id, file_path, quantity, print_type, paper_size, notes, status, order_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isisss", $user_id, $uploaded_file, $quantity, $print_type, $paper_size, $notes);

        if ($stmt->execute()) {
            $success_message = "Custom order placed successfully! Order ID: " . $stmt->insert_id;
            // Clear form data
            $_POST = array();
            //navigate to orders page after a delay
            header("refresh:3;url=../myorder/myorder.php");
        } else {
            $error_message = "Error placing order: " . $stmt->error;
            // Delete uploaded file if DB insert fails
            if (file_exists($uploaded_file)) {
                unlink($uploaded_file);
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Print Order - PrintCity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background: #f8f9fa;
            --white: #ffffff;
            --text-dark: #212529;
            --border-color: #dee2e6;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
        }

        header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-lg);
        }

        header a {
            color: var(--white);
            text-decoration: none;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .upload-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: var(--shadow-lg);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .file-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
            background: #f8f9fa;
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: #e3f2fd;
        }

        .file-upload-area input[type="file"] {
            display: none;
        }

        .file-upload-btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .file-upload-btn:hover {
            background: #0056b3;
        }

        .file-info {
            margin-top: 1rem;
            padding: 0.5rem;
            background: var(--white);
            border-radius: 0.25rem;
            border: 1px solid var(--border-color);
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .radio-option input[type="radio"] {
            margin: 0;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .order-summary {
            background: #e7f3ff;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        @media (max-width: 768px) {
            .container { margin: 1rem auto; padding: 0 0.5rem; }
            .upload-form { padding: 1.5rem; }
            .radio-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header>
        <a href="../HomePage/index.php">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Custom Print Order</h1>
    </header>

    <div class="container">
        <h2 class="page-header">
            <i class="fas fa-file-upload"></i> Upload Your Document for Printing
        </h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="upload-form" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Upload Document *</label>
                <div class="file-upload-area">
                    <input type="file" id="file_upload" name="file_upload" accept=".pdf,.doc,.docx,.txt,.rtf" required onchange="showFileInfo(this)">
                    <label for="file_upload" class="file-upload-btn">
                        <i class="fas fa-cloud-upload-alt"></i> Choose File
                    </label>
                    <p style="margin-top: 0.5rem; color: var(--secondary-color);">Supported: PDF, DOC, DOCX, TXT, RTF (Max 5MB)</p>
                    <div id="file-info" class="file-info" style="display: none;"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Quantity (Number of Copies) *</label>
                <input type="number" class="form-control" name="quantity" min="1" max="100" value="1" required>
            </div>

            <div class="form-group">
                <label class="form-label">Print Type *</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="color" name="print_type" value="color" checked>
                        <label for="color">Color Printing</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="bw" name="print_type" value="black_white">
                        <label for="bw">Black & White</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Paper Size *</label>
                <select class="form-control" name="paper_size" required>
                    <option value="A4">A4</option>
                    <option value="A3">A3</option>
                    <option value="Letter">Letter</option>
                    <option value="Legal">Legal</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Special Notes (Optional)</label>
                <textarea class="form-control" name="notes" placeholder="e.g., Double-sided, Stapling, Specific binding..."></textarea>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-print"></i> Place Custom Order
            </button>
        </form>

        <div class="order-summary">
            <h4><i class="fas fa-info-circle"></i> Order Summary</h4>
            <p>Once submitted, your custom order will be reviewed by our team. You'll receive an email confirmation with pricing and estimated delivery time.</p>
            <p><strong>Status:</strong> Pending Review</p>
        </div>
    </div>

    <script>
        function showFileInfo(input) {
            const fileInfo = document.getElementById('file-info');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                fileInfo.innerHTML = `
                    <strong>Selected:</strong> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)<br>
                    <small>Type: ${file.type}</small>
                `;
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        }
    </script>
</body>
</html>


