<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$fullname = $_SESSION['fullname'];
$email = $_SESSION['email'];
$username = $_SESSION['username'];
$dob = $_SESSION['dob'];
$gender = $_SESSION['gender'];
$address = $_SESSION['address'];
$phone = $_SESSION['phone'];

if (isset($_SESSION['user_id'])) {
    $cart_count = 0; // Initialize default value
} else {
    header("Location: login.php");
    exit();
}
    $conn = new mysqli('localhost', 'root', '', 'toyverse_db');
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("
            SELECT SUM(quantity) AS total_items
            FROM cart_item
            INNER JOIN cart ON cart.cart_id = cart_item.cart_id
            WHERE cart.user_id = ?
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $cart_count = $data['total_items'] ?? 0;

        $stmt->close();
        $conn->close();
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-light);
            color: var(--navy);
            line-height: 1.6;
            overflow-x: hidden;
        }

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
            font-family: 'Poppins', sans-serif;
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

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: var(--gradient-light);
            position: relative;
            overflow: hidden;
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

        .btn-obsidian {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            letter-spacing: 0.1em;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-primary {
            background: var(--deep-blue);
            color: var(--white);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 144, 255, 0.4);
            color: var(--white);
        }

        footer {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
            color: var(--navy);
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 5rem;
            border-top: 2px solid var(--sky-blue);
            font-family: 'Poppins', sans-serif;
        }

        footer a {
            color: var(--deep-blue);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--navy);
        }

        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.4rem;
            }
            
            .btn-group {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-obsidian {
                width: 100%;
                max-width: 300px;
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            ToyVerse
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" href="homepage.php" style="color: white;">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="account.php">Account</a>
                </li>
                <li class="nav-item position-relative">
                    <a class="nav-link" href="cart.php">
                        Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cart_count ?>
                                <span class="visually-hidden">cart items</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <section class="hero">
        <div class="container hero-content">
            <h1 class="fade-in">welcome <?php echo htmlspecialchars($username); ?></h1>
            <p class="hero-subtitle fade-in">collect the cutest designer figurines</p>
            
            <div class="exclusive-deals fade-in" style="background: rgba(135, 206, 235, 0.2); padding: 2rem; border-radius: 15px; margin: 2rem 0; border: 2px solid var(--deep-blue);">
                <h2 style="color: var(--deep-blue); font-size: 1.8rem; margin-bottom: 1rem;">exclusive deals</h2>
                <p style="color: var(--navy); font-size: 1.2rem; margin-bottom: 1.5rem;">
                    limited time offer: up to 25% off on premium figurines<br>
                    special discounts for collectors
                </p>
            </div>

            <div class="text-center mt-4">
                <a href="shop.php" class="btn-obsidian btn-primary" style="margin-right: 1rem;">view shop</a>
                <a href="cart.php" class="btn-obsidian btn-primary" style="margin-right: 1rem;">my cart</a>
                <a href="account.php" class="btn-obsidian btn-primary">account info</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4 style="color: var(--deep-blue); margin-bottom: 1rem; font-weight: 700;">ToyVerse's Hirana</h4>
                    <p style="margin-bottom: 1.5rem;">collect the cutest designer figurines</p>
                    <div style="margin-bottom: 1.5rem;">
                        <p>© <?php echo date("Y"); ?> ToyVerse Hirana. all rights reserved.</p>
                        <p>contact: <a href="mailto:info@toyversehirana.com">info@toyversehirana.com</a> | <a href="tel:+18005551234">+1 800 555 1234</a></p>
                    </div>
                    <div>
                        <a href="about.php">about us</a> | 
                        <a href="#">privacy policy</a> | 
                        <a href="#">terms of service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .main-container').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>