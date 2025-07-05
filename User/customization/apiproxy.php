<?php
// Store your token securely (you could load from env file or database)
$token = 'YOUR_CANVAS_API_TOKEN';
$canvasBaseUrl = 'https://yourschool.instructure.com/api/v1/';

if (!isset($_GET['endpoint'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing endpoint']);
  exit;
}

$endpoint = $_GET['endpoint'];
$url = $canvasBaseUrl . $endpoint;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $token",
  "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;
