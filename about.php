<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - ToyVerse Hirana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Same CSS styles as index.php -->
    <style>
            :root {
            --sky-blue: #87CEEB;
            --light-blue: #ADD8E6;
            --deep-blue: #1E90FF;
            --navy: #000080;
            --white: #FFFFFF;
            --off-white: #F8F9FA;
            --gradient-light: linear-gradient(135deg, #FFFFFF 0%, #E6F3FF 50%, #CCE6FF 100%);
            --gradient-card: linear-gradient(145deg, #FFFFFF 0%, #F0F8FF 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'poppins', sans-serif;
            background: var(--gradient-light);
            color: var(--navy);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Enhanced Header */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid var(--light-blue);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(135, 206, 235, 0.2);
        }

        .navbar-brand {
            font-family: 'poppins', sans-serif;
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.8rem;
            background: var(--deep-blue);
            color: white;
            text-decoration: none;
            letter-spacing: 0.1em;
            text-shadow: 0 2px 15px rgba(135, 206, 235, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .navbar-brand:hover {
            color: white;
        }

        .navbar-logo {
            height: 80px;
            margin-right: 1rem;
            filter: drop-shadow(0 0 10px rgba(30, 144, 255, 0.3));
        }

        .navbar-nav .nav-link {
            color: var(--navy);
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
    
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: var(--white);
            background: var(--deep-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

         .nav-link.active {
            color: var(--white);
            background: var(--deep-blue);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: var(--gradient-light);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%231E90FF" stroke-width="0.3" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(10px, 10px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--navy), var(--deep-blue), var(--sky-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            line-height: 1.2;

            letter-spacing: 0.05em;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            color: var(--navy);
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 400;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Enhanced Carousel */
        .carousel {
            margin: 3rem 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.3);
        }

        .carousel-inner img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            filter: brightness(1.1) contrast(1.1);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 8%;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-size: 100%, 100%;
            border-radius: 50%;
            background-color: rgba(30, 144, 255, 0.8);
            padding: 20px;
        }

        /* Feature Cards */
        .feature-cards {
            margin: 4rem 0;
        }

        .feature-card {
            background: var(--gradient-card);
            border: 1px solid rgba(30, 144, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(135, 206, 235, 0.2), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 20px 40px rgba(30, 144, 255, 0.2);
            border-color: var(--deep-blue);
        }

        .feature-card h5 {
            color: var(--deep-blue);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: var(--navy);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Team Cards */
        .team-card {
            background: var(--gradient-card);
            border: 1px solid rgba(30, 144, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(135, 206, 235, 0.2), transparent);
            transition: left 0.5s;
        }

        .team-card:hover::before {
            left: 100%;
        }

        .team-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 20px 40px rgba(30, 144, 255, 0.2);
            border-color: var(--deep-blue);
        }

        .team-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            border: 4px solid var(--deep-blue);
            object-fit: cover;
            display: block;
            transition: all 0.3s ease;
        }

        .team-card:hover .team-photo {
            transform: scale(1.05);
            border-color: var(--sky-blue);
        }

        .team-card h5 {
            color: var(--deep-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1.4rem;
        }

        .team-card .role {
            color: var(--sky-blue);
            font-weight: 500;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .team-card p {
            color: var(--navy);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Enhanced Buttons */
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .btn-obsidian {
            font-family: 'poppins', sans-serif;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            text-transform: lowercase;
            letter-spacing: 0.1em;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--sky-blue), var(--deep-blue));
            color: var(--white);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 144, 255, 0.4);
            color: var(--white);
        }

        .btn-outline {
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--deep-blue);
        }

        .btn-outline:hover {
            background: var(--deep-blue);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 144, 255, 0.2);
        }

        /* Content Container */
        .main-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem;
            margin: 3rem auto;
            max-width: 1000px;
            border: 1px solid rgba(30, 144, 255, 0.3);
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.2);
        }

        /* Team Section */
        .team-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem;
            margin: 3rem auto;
            max-width: 1200px;
            border: 1px solid rgba(30, 144, 255, 0.3);
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.2);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
            color: var(--navy);
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 5rem;
            border-top: 2px solid var(--sky-blue);
            font-family: 'poppins', sans-serif;
        }

        footer a {
            color: var(--deep-blue);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--navy);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            
            .navbar-logo {
                height: 100px;
            }
            
            .main-container, .team-section {
                margin: 2rem 1rem;
                padding: 2rem;
                border-radius: 20px;
            }
            
            .btn-group {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-obsidian {
                width: 100%;
                max-width: 300px;
            }
            
            .carousel-inner img {
                height: 250px;
            }
            
            .feature-card, .team-card {
                margin-bottom: 2rem;
            }

            .team-photo {
                width: 120px;
                height: 120px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--obsidian-black);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(var(--deep-purple), var(--electric-purple));
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(var(--electric-purple), var(--deep-purple));
        }
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                ToyVerse
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                     <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php" style="color: white;">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="account.php">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <h1 class="fade-in">About ToyVerse Hirana</h1>
            <p class="hero-subtitle fade-in">Your Ultimate Destination for Designer Figurines</p>
        </div>
    </section>

    <!-- About Content -->
    <div class="main-container fade-in">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center mb-4">Our Story</h2>
                <p>ToyVerse Hirana was founded with a passion for bringing joy through collectible designer figurines. We curate the most unique and adorable pieces from around the world, ensuring each item in our collection meets the highest standards of quality and design.</p>
                
                <h3 class="mt-5 mb-4">Our Mission</h3>
                <p>To create a vibrant community of collectors and enthusiasts, providing them with exclusive access to limited-edition designer toys and figurines that bring joy and inspiration to their collections.</p>

                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <h5>Quality Assurance</h5>
                            <p>Every figurine is carefully inspected to ensure authenticity and premium quality.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <h5>Exclusive Collection</h5>
                            <p>Access to limited edition and rare designer figurines from top artists.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <h5>Community Focus</h5>
                            <p>Join our growing community of passionate collectors and enthusiasts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meet the Team Section -->
    <div class="team-section fade-in">
        <div class="container">
            <h2 class="text-center mb-5">Meet the Team</h2>
            <p class="text-center mb-5" style="color: var(--navy); font-size: 1.1rem;">
                ToyVerse Hirana was created as a passion project by two dedicated Computer Science students from the University of the East. 
                Combining their love for technology and collectibles, they built this platform to share their enthusiasm for designer figurines with fellow collectors.
            </p>
            
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 mb-4">
                    <div class="team-card">
                        <img src="profile/franz.jpg" alt="Team Member 1" class="team-photo">
                        <h5>Franz Josef Siochi</h5>
                        <p class="role">Lead Developer & Co-Founder</p>
                       <p>Also a 3rd-year Computer Science student at UE, Franz focuses on UI/UX design and digital marketing. With an eye for aesthetics and a deep understanding of collector culture, he curates the visual identity of ToyVerse Hirana and manages product sourcing. His design philosophy centers on creating engaging, immersive experiences for collectors.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5 mb-4">
                    <div class="team-card">
                        <img src="profile/sula.jpg" alt="Team Member 2" class="team-photo">
                        <h5>Timothy Adam Sula</h5>
                        <p class="role">Designer & Co-Founder</p>
                        <p>A 3rd-year Computer Science student at the University of the East with a specialization in web development and database management. Timothy is passionate about creating user-friendly interfaces and has been collecting designer figurines since high school. he handles the technical architecture and ensures seamless user experience across the platform.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <h4 style="color: var(--deep-blue); margin-bottom: 1rem;">University of the East</h4>
                <p style="color: var(--navy); font-size: 1rem;">
                    Both founders are proud students of the University of the East's College of Computer Studies and Engineering, 
                    where they developed their technical skills and entrepreneurial mindset. This project represents their commitment 
                    to combining academic excellence with real-world application.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4 style="color: var(--deep-blue); margin-bottom: 1rem; font-weight: 700;">ToyVerse Hirana</h4>
                    <p style="margin-bottom: 1.5rem;">Your Ultimate Destination for Designer Figurines</p>
                    <div style="margin-bottom: 1.5rem;">
                        <p>© <?php echo date("Y"); ?> ToyVerse Hirana. All rights reserved.</p>
                        <p>Contact: <a href="mailto:info@toyversehirana.com">info@toyversehirana.com</a> | <a href="tel:+18005551234">+1 800 555 1234</a></p>
                    </div>
                    <div>
                        <a href="about.php">About Us</a> | 
                        <a href="#">Privacy Policy</a> | 
                        <a href="#">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Keep all the existing JavaScript
    </script>
</body>
</html>