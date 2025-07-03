<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, gender, dob, phone, email, address, username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}



// to show the items in the cart
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
    <title>My Account - ToyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sky-blue: #87CEEB;
            --light-blue: #ADD8E6;
            --deep-blue: #1E90FF;
            --navy: #000080;
            --white: #FFFFFF;
            --gradient-light: linear-gradient(135deg, #FFFFFF 0%, #E6F3FF 50%, #CCE6FF 100%);
            --gradient-card: linear-gradient(145deg, #FFFFFF 0%, #F0F8FF 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-light);
            color: var(--navy);
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
            font-weight: 700;
            font-size: 1.8rem;
            background: var(--deep-blue);
            color: white;
            text-decoration: none;
            letter-spacing: 0.1em;
            text-shadow: 0 2px 15px rgba(135, 206, 235, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
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

        .container-custom {
            max-width: 800px;
            margin: 3rem auto;
            padding: 2rem;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: var(--deep-blue);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        footer {
            background: var(--navy);
            color: var(--white);
            padding: 2rem 0;
            text-align: center;
            margin-top: 4rem;
        }

        footer a {
            color: var(--light-blue);
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">ToyVerse</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                     <li class="nav-item">
                    <a class="nav-link" href="homepage.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="account.php" style="color: white;">Account</a>

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

<div class="container-custom">
    <h2 class="section-title text-center">My Account Information</h2>
    <div class="row g-3">
        <div class="col-md-6"><strong>Full Name:</strong> <?= htmlspecialchars($user['fullname']) ?></div>
        <div class="col-md-6"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></div>
        <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
        <div class="col-md-6"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></div>
        <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></div>
        <div class="col-md-6"><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob']) ?></div>
        <div class="col-12"><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></div>
    </div>
</div>

<footer>
    <div class="container">
        <h4>ToyVerse</h4>
        <p>&copy; <?= date("Y") ?> ToyVerse. All rights reserved.</p>
        <p>Contact: <a href="mailto:info@toyverse.com">info@toyverse.com</a></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
