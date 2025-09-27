<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "printcity");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all products
$sql = "SELECT * FROM products LIMIT 6";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PrintCity - Your Online Printshop</title>
  <link rel="stylesheet" href="IndexStyles.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="index.js" defer></script>
  <style>
    /* Stunning Starter Animation Section */
    .starter-animation {
      position: relative;
      height: 100vh;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
      color: white;
      text-align: center;
      z-index: 1;
    }

    .starter-animation::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                  radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                  radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
      animation: backgroundShift 10s ease-in-out infinite;
    }

    @keyframes backgroundShift {
      0%, 100% { transform: scale(1) rotate(0deg); opacity: 1; }
      50% { transform: scale(1.1) rotate(180deg); opacity: 0.8; }
    }

    /* Animated Ink Splatters / Print Effects */
    .print-effects {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }

    .ink-splat {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
      animation: splat 4s ease-out infinite;
      opacity: 0.1;
    }

    .ink-splat:nth-child(1) {
      width: 200px;
      height: 200px;
      top: 20%;
      left: 10%;
      animation-delay: 0s;
    }

    .ink-splat:nth-child(2) {
      width: 150px;
      height: 150px;
      top: 60%;
      right: 20%;
      animation-delay: 1s;
      background: linear-gradient(45deg, #a8e6cf, #ffd93d, #ff6b6b);
    }

    .ink-splat:nth-child(3) {
      width: 100px;
      height: 100px;
      bottom: 20%;
      left: 50%;
      animation-delay: 2s;
      background: linear-gradient(45deg, #4ecdc4, #45b7d1);
    }

    @keyframes splat {
      0% {
        transform: scale(0) rotate(0deg);
        opacity: 0.3;
      }
      50% {
        transform: scale(1.2) rotate(180deg);
        opacity: 0.1;
      }
      100% {
        transform: scale(0) rotate(360deg);
        opacity: 0;
      }
    }

    /* Floating Paper Elements */
    .paper-float {
      position: absolute;
      width: 60px;
      height: 80px;
      background: rgba(255, 255, 255, 0.05);
      clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%);
      animation: paperFloat 8s linear infinite;
      z-index: 1;
    }

    .paper-float:nth-child(1) { left: 10%; animation-delay: 0s; }
    .paper-float:nth-child(2) { left: 30%; animation-delay: 2s; width: 50px; height: 70px; }
    .paper-float:nth-child(3) { left: 70%; animation-delay: 4s; }
    .paper-float:nth-child(4) { left: 90%; animation-delay: 6s; width: 40px; height: 60px; }

    @keyframes paperFloat {
      0% {
        transform: translateY(100vh) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        transform: translateY(-100px) rotate(360deg);
        opacity: 0;
      }
    }

    .starter-animation .content {
      position: relative;
      z-index: 10;
      max-width: 900px;
      padding: 0 2rem;
      animation: heroSlideIn 1.5s ease-out both;
    }

    @keyframes heroSlideIn {
      from {
        opacity: 0;
        transform: translateY(60px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .logo-text {
      font-size: 5rem;
      font-weight: 900;
      margin-bottom: 1.5rem;
      background: linear-gradient(45deg, #fff, #ffd700, #ff6b6b);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: textGlow 3s ease-in-out infinite alternate, typewriter 2s steps(20) 1s both;
      letter-spacing: 0.2em;
      text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
      position: relative;
      overflow: hidden;
    }

    .logo-text::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.9), transparent);
      animation: shine 2s infinite;
    }

    @keyframes textGlow {
      from {
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.5), 0 0 40px rgba(255, 215, 0, 0.3);
      }
      to {
        text-shadow: 0 0 30px rgba(255, 255, 255, 0.8), 0 0 60px rgba(255, 107, 107, 0.5);
      }
    }

    @keyframes typewriter {
      from { width: 0; }
      to { width: 100%; }
    }

    @keyframes shine {
      0% { transform: translateX(-100%) scaleX(0); }
      50% { transform: translateX(100%) scaleX(1); }
      100% { transform: translateX(100%) scaleX(0); }
    }

    .starter-animation .text h1 {
      font-size: 4rem;
      margin-bottom: 1rem;
      animation: fadeInUp 1s ease-out 1.5s both, bounceIn 1s ease-out 1.5s both;
      position: relative;
    }

    .starter-animation .text p {
      font-size: 1.6rem;
      margin-bottom: 2.5rem;
      opacity: 0.95;
      animation: fadeInUp 1s ease-out 2s both;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes bounceIn {
      0% { transform: scale(0.3); opacity: 0; }
      50% { transform: scale(1.05); }
      70% { transform: scale(0.9); }
      100% { transform: scale(1); opacity: 1; }
    }

    .logo {
      width: 180px;
      height: 180px;
      margin: 0 auto 3rem;
      animation: logoSpinIn 2s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.5s both;
      filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
    }

    @keyframes logoSpinIn {
      0% {
        opacity: 0;
        transform: scale(0) rotate(-180deg);
      }
      60% {
        transform: scale(1.1) rotate(20deg);
      }
      100% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
      }
    }

    .logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 20px;
      border: 4px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .logo:hover img {
      border-color: rgba(255, 255, 255, 0.5);
      transform: rotateY(10deg);
    }

    .cta-button {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1.2rem 2.5rem;
      background: linear-gradient(45deg, #ff6b6b, #ee5a24, #ff6b6b);
      background-size: 200% 200%;
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: bold;
      font-size: 1.3rem;
      transition: all 0.4s ease;
      box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
      animation: ctaPulse 2s infinite, gradientShift 3s ease infinite;
      position: relative;
      overflow: hidden;
    }

    .cta-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .cta-button:hover::before {
      left: 100%;
    }

    .cta-button:hover {
      transform: translateY(-5px) scale(1.05);
      box-shadow: 0 20px 50px rgba(255, 107, 107, 0.6);
      animation: none;
    }

    @keyframes ctaPulse {
      0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7); }
      70% { box-shadow: 0 0 0 20px rgba(255, 107, 107, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Print-Themed Icons Animation */
    .print-icons {
      position: absolute;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 2rem;
      animation: iconsFloat 3s ease-in-out infinite;
    }

    .print-icon {
      font-size: 2rem;
      color: rgba(255, 255, 255, 0.6);
      animation: iconBounce 2s ease-in-out infinite;
    }

    .print-icon:nth-child(1) { animation-delay: 0s; }
    .print-icon:nth-child(2) { animation-delay: 0.5s; }
    .print-icon:nth-child(3) { animation-delay: 1s; }

    @keyframes iconsFloat {
      0%, 100% { transform: translateX(-50%) translateY(0px); }
      50% { transform: translateX(-50%) translateY(-10px); }
    }

    @keyframes iconBounce {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }

    
    
    @media (max-width: 768px) {
      .logo-text { font-size: 3rem; }
      .starter-animation .text h1 { font-size: 2.8rem; }
      .starter-animation .text p { font-size: 1.3rem; }
      .logo { width: 120px; height: 120px; }
      .cta-button { padding: 1rem 2rem; font-size: 1.1rem; }
      .print-icons { gap: 1rem; bottom: 1rem; }
      .print-icon { font-size: 1.5rem; }
      .stack-area { flex-direction: column; text-align: center; padding: 4rem 1rem; }
      .ink-splat { display: none; } /* Hide on mobile for performance */
    }


      .stack-area {
        width: 100%;
        height: 500vh;
        position: relative;
        background: rgb(196, 196, 8);
        display: flex;
      }
      .left {
        height: 100vh;
        flex-basis: 50%;
        position: sticky;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        align-items: center;
        flex-direction: column;
      }
      .right {
        height: 100vh;
        flex-basis: 50%;
        position: sticky;
        top: 0;
      }
     
      .title {
        width: 420px;
        font-size: 84px;
        font-family: poppins;
        font-weight: 700;
        line-height: 88px;
      }
      .sub-title {
        width: 420px;
        font-family: poppins;
        font-size: 14px;
        margin-top: 30px;
      }
      .sub-title button {
        font-family: poppins;
        font-size: 14px;
        padding: 15px 30px;
        background: black;
        color: white;
        border-radius: 8mm;
        border: none;
        outline: none;
        cursor: pointer;
        margin-top: 20px;
      }

      .card {
        width: 350px;
        height: 350px;
        border-radius: 25px;
        margin-bottom: 10px;
        position: absolute;
        top: calc(50% - 175px);
        left: calc(50% - 175px);
        transition: 0.5s ease-in-out;
           box-sizing: border-box;
        padding: 35px;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
      }
      .card:nth-child(1) {
        background: rgb(64, 122, 255);
      }
      .card:nth-child(2) {
        background: rgb(221, 62, 88);
      }
      .card:nth-child(3) {
        background: rgb(186, 113, 245);
      }
      .card:nth-child(4) {
        background: rgb(247, 92, 208);
      }
       .card:nth-child(5) {
        background: rgb(54, 161, 88);
      }

    
      .sub {
        font-family: poppins;
        font-size: 20px;
        font-weight: 700;
      }
      .content {
        font-family: poppins;
        font-size: 44px;
        font-weight: 700;
        line-height: 54px;
      }
     
      .away {
        transform-origin: bottom left;
      }
  </style>
</head>
<body>
  <header>
   <div class="nav">
        <nav>
            <ul>
                <li class="active"><a href="#">Home</a></li>
                <li><a href="../myorder/myorder.php">MyOrders</a></li>
                <li><a href="../favourite/favourite.php">Favourites</a></li>
                <li><a href="../myprofile/myprofile.php">MyProfile</a></li>
                <li><a href="../customization/custom_orders.php">Print</a></li>
                <li><a href="#footer-content">About</a></li>
                <li><a href="#products" class="nav-cta">Order Now</a></li>
                
            </ul>
        </nav>
    </div>
   </header>

  <!-- Stunning Animated Starter Section -->
  <section class="starter-animation">
    <!-- Print Effects Background -->
    <div class="print-effects">
      <div class="ink-splat"></div>
      <div class="ink-splat"></div>
      <div class="ink-splat"></div>
    </div>

    <!-- Floating Paper Elements -->
    <div class="paper-float"></div>
    <div class="paper-float"></div>
    <div class="paper-float"></div>
    <div class="paper-float"></div>

    <div class="content">
      <div class="logo-text">Here You Go...</div>
      
      <div class="text">
        <h1>Welcome to PrintCity</h1>
        <p>Your one-stop destination for all printing <br> we bring your ideas to life with precision and creativity.</p>
        <a href="../customization/custom_orders.php" class="cta-button">
          <i class="fas fa-rocket"></i> Start Printing Now
        </a>
      </div>
      
      <div class="logo">
        <img src="../../assets/logo.png" alt="PrintCity Logo" />
      </div>

      <!-- Print-Themed Icons -->
      <div class="print-icons">
        <div class="print-icon"><i class="fas fa-print"></i></div>
        <div class="print-icon"><i class="fas fa-image"></i></div>
        <div class="print-icon"><i class="fas fa-palette"></i></div>
      </div>
    </div>
  </section>

  <!-- Stack Area -->

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
 

 <div>
<?php include("../ProductPage/productpage.php"); ?>
</div>



  <!-- Footer -->
    <footer>
        <div class="footer-content" id="footer-content">
            <div class="footer-col">
                <h3>PrintCity</h3>
                <p>Transforming memories into art with premium prints and custom framing solutions.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/print_city_tdpa?igsh=Zm9meHUyNG83MnJy"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Services</h3>
                <a href="#">Photo Printing</a>
                <a href="#">Custom Framing</a>
                <a href="#">Canvas Prints</a>
                <a href="#">Art Restoration</a>
                <a href="#">Custom Orders</a>
            </div>
            
            <div class="footer-col">
                <h3>Information</h3>
                <a href="#">About Us</a>
                <a href="#">Delivery Information</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">FAQ</a>
            </div>
            
            <div class="footer-col">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Print Street, City, Country</p>
                <p><i class="fas fa-phone"></i> +1 234 567 8900</p>
                <p><i class="fas fa-envelope"></i> info@printcity.com</p>
            </div>
        </div>
        
        <div class="copyright">
            <p>© 2023 PrintCity. All rights reserved.</p>
        </div>
    </footer>

  <!-- Floating Add Button -->
  <button class="floating-button" onclick="scrollToTop()">↑</button>
  <script>
  function scrollToTop() {
    window.scrollTo({
      top: 0,          // scroll to the top
      behavior: 'smooth' // smooth scrolling
    });
  }
</script>



</body>
</html>
