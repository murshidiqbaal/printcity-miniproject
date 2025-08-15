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
<script>
let hasAnimated = false; // prevent reversing
let shownames = false;
window.addEventListener("scroll", () => {
    if (hasAnimated) return; // stop running after first animation

    const scrollPos = window.scrollY;
    const triggerPoint = window.innerHeight * 0.1;
    const triggerNames = window.innerHeight * 0.6;

    if (scrollPos >= triggerPoint) {
        const leftCards = document.querySelectorAll(".left-content .card");
        const rightCards = document.querySelectorAll(".right-content .card");

        function animateCards(cards, direction) {
            cards.forEach((card, index) => {
                const spreadX = index * 220 * direction;
                card.style.transform = `translateX(${spreadX}px)`;
                card.style.opacity = 1;
            });
        }

        animateCards(leftCards, -1);
        animateCards(rightCards, 1);

        hasAnimated = true; // lock it so it doesn't reverse
    }

     if (hasAnimated && !showNames && scrollPos >= triggerNames) {
        document.querySelectorAll(".product-name").forEach(name => {
            name.style.opacity = 1;
        });
        showNames = true;
    }
});



</script>

<style>
 
    body {
      background-color: black;
  margin: 0;
  height: 100vh;
  display: flex;
  justify-content: center; /* horizontal center */
  align-items: center;     /* vertical center */
}

 

@keyframes slideInUp {
  0% {
    opacity:.5;
    transform: translateY(100px);
  }
  100% {
    opacity: 1;
    transform: translateY(-50px);
  }
}
    
.card {
  width: 200px;
  height: 250px;
  border-radius: 25px;
  overflow: hidden; /* prevents image from spilling out */
  position: absolute;
  transform: translate(-50%, -50%);
  transition: transform 0.2s ease-out, opacity 0.2s ease-out;
  box-sizing: border-box;
  opacity: 1;
}

.card img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* makes image fill container while keeping aspect ratio */
  display: block;
}


/* Left-moving cards */
.card.move-left {
  transform: translate(-150%, -50%);
  opacity: 0.8;
}

/* Right-moving cards */
.card.move-right {
  transform: translate(50%, -50%);
  opacity: 0.8;
}

      /* .card:nth-child(1) {
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
       .card:nth-child(6) {
        background: rgb(205, 18, 102);
      } */

    
.sub {
  font-family: poppins;
  font-size: 14px; /* reduced from 20px */
  font-weight: 700;
}

.content {
  font-family: poppins;
  font-size: 20px; /* reduced from 44px */
  font-weight: 700;
  line-height: 26px; /* scaled line-height */
}

     /* .away {
        transform-origin: bottom left;
      }

       @keyframes slideIn {
      0% {
        transform: translateY(100%);
      }
      100% {
        transform: translateY(0);
      }
    } */
    
     body {
  padding-bottom: 100px; /* Adds 300px of extra scroll space */
}
.container {
  opacity: 0;
  transform: translateY(0); /* was 100px */
  animation: slideInUp .5s ease-out forwards;
  animation-delay: 0.3s;
  height: auto; /* remove full height to avoid pushing content */
  flex-basis: auto;
  display: flex;
  justify-content: flex-start; /* start from top */
  align-items: center;
  flex-direction: column;
  position: fixed;
  top: 30%; /* small offset from top */
 }

.left, .right {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.left .card {
  animation: slideLeft 0.6s forwards;
}

.right .card {
  animation: slideRight 0.6s forwards;
}

.left .card:nth-child(1) { animation-delay: 0.1s; }
.left .card:nth-child(2) { animation-delay: 0.3s; }
.left .card:nth-child(3) { animation-delay: 0.5s; }

.right .card:nth-child(1) { animation-delay: 0.1s; }
.right .card:nth-child(2) { animation-delay: 0.3s; }
.right .card:nth-child(3) { animation-delay: 0.5s; }

/* Keyframes */
@keyframes slideLeft {
  from { transform: translateX(-100px); opacity: 0; }
  to   { transform: translateX(0); opacity: 1; }
}

@keyframes slideRight {
  from { transform: translateX(100px); opacity: 0; }
  to   { transform: translateX(0); opacity: 1; }
}
</style>
  <script src="../HomePage/index.js" defer></script>
</head>
<body>
     
       <div class="container">
    <div class="right-content">
        <?php $count = 0; ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php if ($count % 2 == 0): ?>
                <div class="card">
                    <img src="../../Admin/Products/<?= htmlspecialchars($row['image_path']) ?>" 
                         alt="<?= htmlspecialchars($row['name']) ?>">
                </div>
            <?php endif; ?>
            <?php $count++; ?>
        <?php endwhile; ?>
    </div>

    <div class="left-content">
        <?php mysqli_data_seek($result, 0); // reset pointer ?>
        <?php $count = 0; ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php if ($count % 2 == 1 || $count == 0): ?> <!-- add first item to left -->
                <div class="card">
                    <img src="../../Admin/Products/<?= htmlspecialchars($row['image_path']) ?>" 
                         alt="<?= htmlspecialchars($row['name']) ?>">
                </div>
            <?php endif; ?>
            <?php $count++; ?>
        <?php endwhile; ?>
    </div>
</div>

     
     
</body>
</html>
