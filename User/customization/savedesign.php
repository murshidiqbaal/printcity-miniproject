<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $img = $_POST['image'];
  $img = str_replace('data:image/png;base64,', '', $img);
  $img = str_replace(' ', '+', $img);
  $data = base64_decode($img);
  $file = 'uploads/design_' . uniqid() . '.png';
  file_put_contents($file, $data);
  echo json_encode(["status" => "success", "file" => $file]);
}
?>
