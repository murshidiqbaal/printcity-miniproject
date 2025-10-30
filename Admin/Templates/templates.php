<?php
// DB connect
$pdo = new PDO("mysql:host=localhost;dbname=printcity", "root", "");
$targetDir = "Templates/imgs/";
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['frame'])) {
  $file = $_FILES['frame'];
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if ($ext !== 'png') die("Only PNG files allowed.");
  $filename = uniqid('frame_', true) . ".png";
  $targetFile = $targetDir . $filename;
  if (!move_uploaded_file($file['tmp_name'], $targetFile)) die("Upload failed.");
  $stmt = $pdo->prepare("INSERT INTO frames (filename) VALUES (?)");
  $stmt->execute([$filename]);
  echo "<div style='padding:6px;color:green'>Frame image uploaded.</div>";
}
// Gallery
$frames = $pdo->query("SELECT * FROM frames ORDER BY uploaded_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Upload New Frame (Transparent PNG)</h2>
<form method="post" enctype="multipart/form-data" style="background:#f8f8fa;padding:18px 22px;border-radius:14px;max-width:400px;">
  <div style="margin-bottom:10px;">Frame Image:<br><input type="file" name="frame" accept="image/png" required></div>
  <button type="submit" style="padding:8px 24px">Upload Frame</button>
</form>
<hr>
<h2>All Uploaded Frame Images</h2>
<div style="display:flex;flex-wrap:wrap;gap:26px;align-items:flex-end;">
<?php
if ($frames) {
  foreach ($frames as $frame) {
    $filename = htmlspecialchars($frame['filename']);
    $when = htmlspecialchars($frame['uploaded_at']);
    $img = '/miniproject/Admin/Templates/Templates/imgs/' . $filename;
    echo "<div style='border-radius:14px;box-shadow:0 6px 15px #9991;padding:12px;background:#fff;min-width:140px;text-align:center;max-width:160px;'>";
    echo "<img src='$img' alt='Frame' style='width:100%;max-width:110px;height:110px;object-fit:contain;background:#fafdff;border-radius:10px;border:1px solid #eee;box-shadow:0 2px 6px #9991;margin-bottom:8px;'>";
    echo "<div style='font-size:0.98em;color:#595;'>Uploaded: $when</div>";
    echo "</div>";
  }
} else {
  echo "No frames found.";
}
?>
</div>


