<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) die("Connection failed: " . mysqli_connect_error());
// Fetch only from frames (not templates)
$sql_frames = "SELECT * FROM frames ORDER BY uploaded_at DESC";
$result_frames = mysqli_query($conn, $sql_frames);
$frames = [];
while ($row = mysqli_fetch_assoc($result_frames)) {
    $frames[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preview Your Art with Frames | PrintCity</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #7c3aed 0%, #38bdf8 100%);
      min-height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #152035;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
    }
    .custom-header {
      margin: 36px auto 24px auto;
      padding: 20px 40px;
      border-radius: 36px;
      background: rgba(255,255,255,0.14);
      box-shadow: 0 8px 32px rgba(103,58,183,0.12);
      text-align: center;
      max-width: 700px;
    }
    .custom-header h1 {
      font-size: 2.7rem;
      margin: 0 0 10px 0;
      color: #fff;
      letter-spacing: 0.03em;
      text-shadow: 0 6px 32px #3336;
      font-weight: 800;
    }
    .custom-header p {
      font-size: 1.18rem;
      color: #f3f4f7;
      opacity: 0.94;
      margin: 0;
    }
    .custom-uploader {
      background: rgba(255,255,255,0.82);
      padding: 36px 28px 32px 28px;
      border-radius: 22px;
      margin-bottom: 24px;
      box-shadow: 0 6px 24px rgba(60,60,150,0.10);
      max-width: 500px;
      width: 90vw;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
    }
    .custom-uploader label {
      font-size: 1.12rem;
      font-weight: 500;
      color: #8544a4;
      margin-bottom: 10px;
      display: block;
    }
    .custom-uploader input[type="file"] {
      display: none;
    }
    .upload-btn {
      background: linear-gradient(90deg, #38bdf8 0%, #7c3aed 100%);
      color: #fff;
      font-size: 1.1rem;
      border: none;
      border-radius: 36px;
      padding: 16px 34px;
      margin-top: 4px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.2s;
      box-shadow: 0 6px 16px #38bdf822;
    }
    .upload-btn:hover { box-shadow: 0 8px 28px #7c3aed33; background-position: right center; }
    .image-preview {
      margin-top: 12px;
    }
    .image-preview img {
      max-width: 250px;
      max-height: 200px;
      border-radius: 18px;
      box-shadow: 0 6px 20px #3333;
      border: 2px solid #00c4fb22;
      object-fit: cover;
    }
    .carousel-section {
      width: 100vw;
      margin: 0 auto 32px auto;
      padding: 0 0 58px 0;
      max-width: 100vw;
      overflow-x: hidden;
    }
    .carousel-frame-list {
      display: flex;
      flex-direction: row;
      gap: 44px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      padding: 0 0 32px 0;
      transition: box-shadow 0.2s;
    }
    .carousel-frame-list::-webkit-scrollbar {
      height: 15px;
      background: #ecebfa;
      border-radius: 12px;
    }
    .carousel-frame-list::-webkit-scrollbar-thumb {
      background: #ebe2f9;
      border-radius: 12px;
    }
    .frame-card {
      min-width: 330px;
      max-width: 330px;
      min-height: 440px;
      background: rgba(255,255,255,0.93);
      border-radius: 28px;
      box-shadow: 0 6px 42px #7c3aed13;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 32px 18px 18px 18px;
      scroll-snap-align: center;
      position: relative;
      transition: transform 0.32s cubic-bezier(.8,.22,.38,.74), box-shadow 0.18s;
      cursor: pointer;
      border: 2px solid transparent;
    }
    .frame-card.selected {
      border-color: #38bdf8;
      transform: scale(1.045) translateY(-8px);
      box-shadow: 0 16px 64px #38bdf844;
      z-index: 3;
    }
    .frame-preview {
      width: 260px;
      height: 350px;
      position: relative;
      background: transparent;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
    }
    .frame-preview-bg {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      width: 100%; height: 100%;
      z-index: 1;
      border-radius: 18px;
      object-fit: contain;
      pointer-events: none;
    }
    .frame-customer-photo {
      position: absolute;
      left: 0; right: 0; top: 0; bottom: 0;
      margin: auto;
      width: 58%;
      height: 58%;
      z-index: 2;
      object-fit: contain;
      box-shadow: 0 4px 28px #0002;
      border-radius: 13px;
      background: #f4f4f5;
      pointer-events: none;
    }
    .carousel-section .no-photo {
      color: #aaa; font-size: 1.15rem;
      margin-top: 80px;
    }
    @media (max-width: 800px) {
      .carousel-frame-list, .carousel-section {
         max-width: 98vw; min-width: 0; width: 98vw;
      }
      .frame-card, .frame-card.selected {
         min-width: 94vw; max-width: 98vw;
         min-height: 340px;
         padding: 16px 2vw 12px 2vw;
      }
      .frame-preview, .frame-preview-bg, .frame-customer-photo {
         max-width: 90vw; width: 90vw;
         height: 44vw; max-height: 64vw;
      }
    }
  </style>
</head>
<body>
  <div class="custom-header">
    <h1><i class="fas fa-crop"></i> Preview Your Art In Frames</h1>
    <p>1. Upload your photo ➔ 2. Swipe to see your image in every available frame!</p>
  </div>
  <form class="custom-uploader" id="customUploader" enctype="multipart/form-data" onsubmit="return false;">
    <label>Select Photo To Preview *</label>
    <input type="file" id="photoInput" accept="image/*">
    <button type="button" class="upload-btn" onclick="document.getElementById('photoInput').click()">
      <i class="fas fa-upload"></i> Upload Image
    </button>
    <div class="image-preview" id="previewContainer"></div>
  </form>
  <div class="carousel-section">
    <div class="carousel-frame-list" id="carouselList"></div>
  </div>
<script>
const frames = <?php echo json_encode($frames); ?>;
let customerImageURL = null;
document.addEventListener('DOMContentLoaded', function() {
  renderCarousel();
});
const previewEl = document.getElementById('previewContainer');
document.getElementById('photoInput').addEventListener('change', function(e){
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(evt) {
    previewEl.innerHTML = `<img src="${evt.target.result}" alt="uploaded"/>`;
    customerImageURL = evt.target.result;
    renderCarousel();
  };
  reader.readAsDataURL(file);
});
function renderCarousel() {
  const list = document.getElementById('carouselList');
  list.innerHTML = '';
  if (!frames.length) {
    list.innerHTML = `<div class='no-photo'>No frames have been uploaded yet.</div>`;
    return;
  }
  if (!customerImageURL) {
    list.innerHTML = `<div class='no-photo'>Upload your image above to preview it in every frame below.</div>`;
    return;
  }
  frames.forEach((frame, idx) => {
    // Card
    const card = document.createElement('div');
    card.className = 'frame-card';
    if (idx === 0) card.classList.add('selected');
    card.onclick = () => {
      document.querySelectorAll('.frame-card').forEach(c=>c.classList.remove('selected'));
      card.classList.add('selected');
    };
    // Preview section
    const pv = document.createElement('div');
    pv.className = 'frame-preview';
    // Frame background
    const frameImg = document.createElement('img');
    frameImg.className = 'frame-preview-bg';
    frameImg.src = '/miniproject/Admin/Templates/Templates/imgs/' + frame.filename;
    pv.appendChild(frameImg);
    // Customer photo
    const custImg = document.createElement('img');
    custImg.className = 'frame-customer-photo';
    custImg.src = customerImageURL;
    pv.appendChild(custImg);
    card.appendChild(pv);
    list.appendChild(card);
  });
}
</script>
</body>
</html>
