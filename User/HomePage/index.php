
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PrintCity - Your Online Printshop</title>
  <link rel="stylesheet" href="IndexStyles.css"/>
  <script src="index.js" defer></script>
</head>
<body>
  <header>
<div class="nav">
  <nav>
    <ul>
      
      <li><a href="#products">Products</a></li>
      <li><a href="#custom">Custom</a></li>
      <li><a href="../myorder/myorder.php">My Orders</a></li>
      <li><a href="#aboutus">About Us</a></li>
    </ul>
  </nav>
</div>
   </header>

    

  <div class="starter-animation">
    <div class="logo-text" id="fade-text">
    Initializing PrintCity...
   </div>
   <div class="text" style="opacity: 0;">
    <h1>Welcome to PrintCity</h1>
    <p>Your one-stop destination for all printing needs</p>
   </div>
   <div class="logo">
    <img src="../../assets/logo.png" alt="PrintCity Logo" />
  </div>


    

 <div class="stack-area">
      <div class="left">
        <div class="title">Print City</div>
        <div class="sub-title">
         One-stop destination for all printing needs, offering high-quality and customized printing services for businesses, students, and individuals. Whether you need brochures, business cards, banners, flyers, or personalized gifts, PrintCity delivers professional results with quick turnaround times. We combine the latest printing technology with creative design to bring your ideas to life, ensuring every print is sharp, vibrant, and impactful. At PrintCity, customer satisfaction is our top priority, and we are committed to providing affordable prices, friendly service, and reliable solutions for every project.
          <br />
          <button>See More Details</button>
        </div>
      </div>
      <div class="right">
        <div class="card">
          <div class="sub">Posters</div>
          <div class="content">Attractive posters</div>
        </div>
        <div class="card">
          <div class="sub">Greeting Cards</div>
          <div class="content">All cards</div>
        </div>
        <div class="card">
          <div class="sub">Adhaar Card</div>
          <div class="content">Duplicates</div>
        </div>
        <div class="card">
          <div class="sub">Prints</div>
          <div class="content">Now its 24/7 support</div>
        </div>
         <div class="card">
          <div class="sub">PDFs</div>
          <div class="content">Now its 24/7 support</div>
        </div>
      </div>
    </div>

 
<?php include("../ProductPage/productpage.php"); ?>




  <footer class="footer">
    <p>&copy; 2025 PrintCity. All Rights Reserved.</p>
  </footer>


</body>
</html>
