<?php

// Connect to DB
$pdo = new PDO("mysql:host=localhost;dbname=printcity", "root", "");
// Fetch all frames
$stmt = $pdo->query("SELECT filename FROM frames ORDER BY uploaded_at DESC");
$frames = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>All Uploaded Frames:</h3>";
if ($frames) {
    echo '<div style="display:flex; flex-wrap:wrap; gap:10px;">';
    foreach ($frames as $frame) {
        // Build image URL relative to web root
        $imgUrl = 'Templates/imgs/' . htmlspecialchars($frame['filename']);
        echo '<img src="' . $imgUrl . '" alt="Frame" style="height:100px; border:1px solid #ccc; cursor:pointer;" />';
    }
    echo '</div>';
} else {
    echo "No frames found.";
}
// Simple admin upload script for frames (transparent PNGs)
$targetDir = "Templates/imgs/";  // Updated folder path

// Ensure the target directory exists
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['frame'])) {
    $file = $_FILES['frame'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'png') {
        die("Only PNG files allowed.");
    }
    $filename = uniqid() . ".png";
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Save to DB
        $pdo = new PDO("mysql:host=localhost;dbname=printcity", "root", "");
        $stmt = $pdo->prepare("INSERT INTO frames (filename) VALUES (?)");
        $stmt->execute([$filename]);
        echo "Frame uploaded successfully.<br>";

        // Display the uploaded image
        echo '<img src="' . htmlspecialchars($targetFile) . '" alt="Uploaded Frame" style="max-width:300px; margin-top:10px; border:1px solid #ccc;" />';
    } else {
        echo "Upload failed.";
    }
} else {
?>
<form method="post" enctype="multipart/form-data">
    Upload Frame PNG: <input type="file" name="frame" accept="image/png" required>
    <button type="submit">Upload</button>
</form>
<?php } ?>


