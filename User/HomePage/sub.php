<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrintCity - Premium Prints & Frames</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Reset & Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    :root {
        --primary-color: rgb(57, 181, 198);
        --secondary-color: rgb(233, 233, 229);
        --text-color: rgb(0, 0, 0);
        --accent-color: rgb(196, 196, 8);
        --dark-bg: #0a0a0a;
        --light-text: #ffffff;
        --gray-text: #b0b0b0;
        --card-bg: #1a1a1a;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--dark-bg);
        color: var(--light-text);
        line-height: 1.6;
        overflow-x: hidden;
    }
    
    /* Navigation */
    .nav {
        width: 100%;
        height: 80px;
        background: rgba(10, 10, 10, 0.95);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5%;
        position: fixed;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .logo img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    
    .logo-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--light-text);
    }
    
    .logo-text span {
        color: var(--primary-color);
    }
    
    nav ul {
        display: flex;
        gap: 10px;
    }
    
    nav li {
        list-style: none;
    }
    
    nav li a {
        text-decoration: none;
        color: var(--gray-text);
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
        padding: 10px 20px;
        border-radius: 30px;
        transition: all 0.3s ease;
    }
    
    nav li a:hover, nav li.active a {
        background: var(--primary-color);
        color: var(--light-text);
    }
    
    .nav-cta {
        background: var(--accent-color);
        color: var(--dark-bg) !important;
        font-weight: 600;
    }
    
    .nav-cta:hover {
        background: #d4d40a !important;
        transform: translateY(-2px);
    }
    
    /* Hero Section */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        padding: 0 5%;
        margin-top: 80px;
    }
    
    .hero-content {
        max-width: 1200px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
    }
    
    .hero-text {
        flex: 1;
        animation: fadeInUp 1s ease-out;
    }
    
    .hero-title {
        font-family: 'Poppins', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 20px;
        background: linear-gradient(to right, var(--light-text), var(--primary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
        color: var(--gray-text);
        margin-bottom: 30px;
        max-width: 500px;
    }
    
    .hero-cta {
        display: flex;
        gap: 15px;
    }
    
    .btn {
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: var(--dark-bg);
        border: none;
    }
    
    .btn-primary:hover {
        background: #3fb9cc;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(57, 181, 198, 0.4);
    }
    
    .btn-secondary {
        background: transparent;
        color: var(--light-text);
        border: 2px solid var(--primary-color);
    }
    
    .btn-secondary:hover {
        background: rgba(57, 181, 198, 0.1);
        transform: translateY(-3px);
    }
    
    .hero-visual {
        flex: 1;
        position: relative;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .floating-frame {
        position: absolute;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        transition: transform 0.5s ease;
    }
    
    .floating-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .frame-1 {
        width: 250px;
        height: 300px;
        top: 50px;
        left: 0;
        transform: rotate(-5deg);
        z-index: 3;
        animation: float 8s ease-in-out infinite;
    }
    
    .frame-2 {
        width: 280px;
        height: 200px;
        top: 150px;
        right: 50px;
        transform: rotate(3deg);
        z-index: 2;
        animation: float 10s ease-in-out infinite 1s;
    }
    
    .frame-3 {
        width: 220px;
        height: 280px;
        bottom: 50px;
        left: 80px;
        transform: rotate(-3deg);
        z-index: 1;
        animation: float 12s ease-in-out infinite 2s;
    }
    
    /* Stack Area */
    .stack-area {
        width: 100%;
        min-height: 100vh;
        position: relative;
        background: var(--secondary-color);
        display: flex;
        padding: 100px 5%;
    }
    
    .left {
        height: 100%;
        flex-basis: 50%;
        position: sticky;
        top: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: var(--text-color);
    }
    
    .title {
        font-family: 'Poppins', sans-serif;
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 20px;
    }
    
    .sub-title {
        font-size: 1.1rem;
        margin-bottom: 30px;
        max-width: 420px;
    }
    
    .stack-btn {
        padding: 15px 30px;
        background: var(--dark-bg);
        color: var(--light-text);
        border-radius: 50px;
        border: none;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: fit-content;
    }
    
    .stack-btn:hover {
        background: #333;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    
    .right {
        flex-basis: 50%;
        position: relative;
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
        color: var(--light-text);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }
    
    .card:nth-child(1) {
        background: linear-gradient(135deg, #407AFF, #7040FF);
    }
    
    .card:nth-child(2) {
        background: linear-gradient(135deg, #DD3E58, #FF4069);
    }
    
    .card:nth-child(3) {
        background: linear-gradient(135deg, #BA71F5, #8A2FE0);
    }
    
    .card:nth-child(4) {
        background: linear-gradient(135deg, #F75CD0, #D42A9C);
    }
    
    .card:nth-child(5) {
        background: linear-gradient(135deg, #36A158, #2D8C4F);
    }
    
    .card-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .sub {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .content {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    
    .away {
        transform-origin: bottom left;
    }
    
    /* Features Section */
    .features {
        padding: 100px 5%;
        background: var(--dark-bg);
        text-align: center;
    }
    
    .section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .section-subtitle {
        font-size: 1.1rem;
        color: var(--gray-text);
        max-width: 600px;
        margin: 0 auto 50px;
        text-align: center;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .feature-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
    }
    
    .feature-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    
    .feature-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .feature-desc {
        color: var(--gray-text);
    }
    
    /* Footer */
    footer {
        width: 100%;
        background: #050505;
        padding: 60px 5% 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .footer-content {
        max-width: 1200px;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }
    
    .footer-col h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: var(--light-text);
    }
    
    .footer-col p, .footer-col a {
        color: var(--gray-text);
        margin-bottom: 10px;
        display: block;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .footer-col a:hover {
        color: var(--primary-color);
    }
    
    .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    
    .social-icons a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--card-bg);
        color: var(--light-text);
        transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
        background: var(--primary-color);
        transform: translateY(-3px);
    }
    
    .copyright {
        text-align: center;
        color: var(--gray-text);
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        max-width: 1200px;
    }
    
    /* Animations */
    @keyframes float {
        0% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(3deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInLogo {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes fadeInText {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInMainText {
        to { opacity: 1; }
    }
    
    /* Responsive Styles */
    @media (max-width: 1024px) {
        .hero-content {
            flex-direction: column;
            text-align: center;
        }
        
        .hero-title {
            font-size: 2.8rem;
        }
        
        .hero-cta {
            justify-content: center;
        }
        
        .stack-area {
            flex-direction: column;
        }
        
        .left {
            position: relative;
            top: 0;
            margin-bottom: 50px;
            text-align: center;
            align-items: center;
        }
        
        .right {
            height: 600px;
        }
    }
    
    @media (max-width: 768px) {
        .nav {
            padding: 0 20px;
        }
        
        nav ul {
            display: none;
        }
        
        .hero-title {
            font-size: 2.2rem;
        }
        
        .title {
            font-size: 2.5rem;
        }
        
        .floating-frame {
            width: 200px !important;
            height: 250px !important;
        }
        
        .frame-1 {
            top: 20px;
            left: 10px;
        }
        
        .frame-2 {
            top: 100px;
            right: 10px;
        }
        
        .frame-3 {
            bottom: 20px;
            left: 50px;
        }
    }
    
    @media (max-width: 480px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .btn {
            padding: 12px 25px;
            font-size: 0.9rem;
        }
        
        .title {
            font-size: 2rem;
        }
        
        .sub-title {
            font-size: 1rem;
        }
        
        .card {
            width: 280px;
            height: 280px;
            left: calc(50% - 140px);
            top: calc(50% - 140px);
            padding: 25px;
        }
        
        .content {
            font-size: 2rem;
        }
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="nav">
        <div class="logo">
            <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/50edd432-a426-434c-91f5-6914e448d666.png" alt="PrintCity Logo">
            <div class="logo-text">Print<span>City</span></div>
        </div>
        <nav>
            <ul>
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Prints</a></li>
                <li><a href="#">Frames</a></li>
                <li><a href="#">Gallery</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#" class="nav-cta">Order Now</a></li>
            </ul>
        </nav>
    </div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Transform Your Memories Into Art</h1>
                <p class="hero-subtitle">Premium quality prints and custom frames to preserve your precious moments. Fast shipping and exceptional customer service.</p>
                <div class="hero-cta">
                    <a href="#" class="btn btn-primary">Create Your Print</a>
                    <a href="#" class="btn btn-secondary">Browse Frames</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-frame frame-1">
                    <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&h=500&q=80" alt="Modern art print in a minimalist frame">
                </div>
                <div class="floating-frame frame-2">
                    <img src="https://images.unsplash.com/photo-1579546929662-711aa81148cf?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&h=300&q=80" alt="Colorful abstract print in a contemporary frame">
                </div>
                <div class="floating-frame frame-3">
                    <img src="https://images.unsplash.com/photo-1544787219-7f47ccb76574?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&h=500&q=80" alt="Landscape photography in a classic wooden frame">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stack Area -->
    <section class="stack-area">
        <div class="left">
            <h2 class="title">Why Choose PrintCity?</h2>
            <p class="sub-title">We combine exceptional quality with innovative technology to deliver prints that truly stand out. Our team of experts ensures every detail is perfect.</p>
            <button class="stack-btn">Explore Our Process</button>
        </div>
        <div class="right">
            <div class="card">
                <div class="card-icon">🖼️</div>
                <div class="sub">Premium Quality</div>
                <div class="content">Archival Grade Materials</div>
            </div>
            <div class="card away">
                <div class="card-icon">🚚</div>
                <div class="sub">Fast Shipping</div>
                <div class="content">Worldwide Delivery</div>
            </div>
            <div class="card away">
                <div class="card-icon">✨</div>
                <div class="sub">Custom Designs</div>
                <div class="content">Tailored to You</div>
            </div>
            <div class="card away">
                <div class="card-icon">🛡️</div>
                <div class="sub">Satisfaction</div>
                <div class="content">100% Guaranteed</div>
            </div>
            <div class="card away">
                <div class="card-icon">🏆</div>
                <div class="sub">Award Winning</div>
                <div class="content">Industry Recognition</div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">From custom framing to professional printing, we offer a wide range of services to meet all your needs.</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-print"></i></div>
                <h3 class="feature-title">Professional Printing</h3>
                <p class="feature-desc">High-quality prints with vibrant colors and sharp details on various materials.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-border-all"></i></div>
                <h3 class="feature-title">Custom Framing</h3>
                <p class="feature-desc">Handcrafted frames designed to complement and protect your artwork.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-paint-brush"></i></div>
                <h3 class="feature-title">Art Restoration</h3>
                <p class="feature-desc">Expert restoration services to preserve and enhance your valuable artwork.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3 class="feature-title">Fast Delivery</h3>
                <p class="feature-desc">Quick and secure shipping options to get your prints to you safely.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-hands-helping"></i></div>
                <h3 class="feature-title">Consultation</h3>
                <p class="feature-desc">Professional advice on choosing the right options for your specific needs.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-certificate"></i></div>
                <h3 class="feature-title">Quality Guarantee</h3>
                <p class="feature-desc">We stand behind our work with a comprehensive satisfaction guarantee.</p>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-col">
                <h3>PrintCity</h3>
                <p>Transforming memories into art with premium prints and custom framing solutions.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
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

    <script>
    // Stack area animation
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        const awayCards = document.querySelectorAll('.away');
        
        // Initial animation for hero section
        setTimeout(() => {
            document.querySelector('.hero-text').style.opacity = '1';
            document.querySelector('.hero-text').style.transform = 'translateY(0)';
        }, 300);
        
        // Floating frames animation
        const frames = document.querySelectorAll('.floating-frame');
        frames.forEach(frame => {
            const randomRotate = (Math.random() * 6) - 3;
            frame.style.transform = `rotate(${randomRotate}deg)`;
        });
        
        // Stack cards animation on scroll
        window.addEventListener('scroll', function() {
            const stackSection = document.querySelector('.stack-area');
            const stackSectionTop = stackSection.offsetTop;
            const scrollPosition = window.scrollY + window.innerHeight;
            
            if (scrollPosition > stackSectionTop + 300) {
                awayCards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.remove('away');
                    }, index * 200);
                });
            }
        });
        
        // Trigger scroll event to check initial position
        window.dispatchEvent(new Event('scroll'));
    });
    </script>
</body>
</html>