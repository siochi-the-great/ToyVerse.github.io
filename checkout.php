<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Fetch cart items
$cart_query = "SELECT ci.cart_item_id, ci.quantity, ci.total_price, p.name, p.price, p.image
               FROM cart_item ci
               JOIN product p ON ci.product_id = p.product_id
               JOIN cart c ON ci.cart_id = c.cart_id
               WHERE c.user_id = ?";

$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['total_price'];
}
$stmt->close();

// Hardcoded payment methods
$payment_methods = [
    ["payment_id" => 1, "payment_method" => "Cash on Delivery"],
    ["payment_id" => 2, "payment_method" => "Credit Card"],
    ["payment_id" => 3, "payment_method" => "GCash"]
];

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $payment_id = (int)$_POST['payment_id'];

    if (empty($address)) {
        $errors[] = "Shipping address is required.";
    }

    if (!$payment_id) {
        $errors[] = "Please select a payment method.";
    }

    if (empty($errors) && !empty($cart_items)) {
        $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($cart_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO checkout (user_id, cart_id, shipping_address, payment_id, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisid", $user_id, $cart_id, $address, $payment_id, $total_price);
        $stmt->execute();
        $stmt->close();

        $success = true;
        header("Location: receipt.php");
        exit();       
    }
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
    <title>Checkout - ToyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            min-height: 100vh;
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
            background: linear-gradient(45deg, var(--sky-blue), var(--deep-blue));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: var(--gradient-card);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .cart-header h1 {
            font-size: 2.5rem;
            color: var(--deep-blue);
            margin-bottom: 1rem;
        }

        .cart-header p {
            color: var(--navy);
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .cart-section {
            background: var(--white);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .cart-summary {
            background: var(--gradient-card);
            padding: 2rem;
            border-radius: 15px;
            text-align: right;
        }

        .cart-total {
            font-size: 1.5rem;
            color: var(--deep-blue);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .checkout-btn {
            background: var(--deep-blue);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            letter-spacing: 0.05em;
        }

        .checkout-btn:hover {
            background: var(--navy);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.4);
        }

        footer {
            background: var(--navy);
            color: var(--white);
            padding: 2rem 0;
            margin-top: 4rem;
            text-align: center;
        }

        footer a {
            color: var(--light-blue);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--white);
        }

        @media (max-width: 768px) {
            .cart-header h1 {
                font-size: 2rem;
            }
            .main-container {
                padding: 0 0.5rem;
            }
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

<div class="main-container">
    <div class="cart-header">
        <h1><i class="fas fa-credit-card me-3"></i>Checkout</h1>
        <p>Confirm your order details and complete your purchase</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">Order placed successfully! Thank you for shopping with ToyVerse.</div>
    <?php elseif (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="cart-section">
        <h4 class="mb-4">Items in your cart</h4>
        <ul class="list-group mb-4">
            <?php foreach ($cart_items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                        <br><small>Qty: <?= $item['quantity'] ?> × ₱<?= number_format($item['price'], 2) ?></small>
                    </div>
                    <span class="badge bg-primary rounded-pill">₱<?= number_format($item['total_price'], 2) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="POST">
            <div class="mb-3">
                <label for="address" class="form-label fw-bold">Shipping Address</label>
                <textarea name="address" id="address" class="form-control" rows="4" required><?= htmlspecialchars($_POST['address'] ?? $_SESSION['address'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="payment_id" class="form-label fw-bold">Payment Method</label>
                <select name="payment_id" id="payment_id" class="form-select" required>
                    <option value="">Select...</option>
                    <?php foreach ($payment_methods as $method): ?>
                        <option value="<?= $method['payment_id'] ?>" <?= (isset($_POST['payment_id']) && $_POST['payment_id'] == $method['payment_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($method['payment_method']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="cart-summary">
                <div class="cart-total">
                    <i class="fas fa-coins me-2"></i>Total: ₱<?= number_format($total_price, 2) ?>
                </div>
                <button type="submit" class="checkout-btn">
                    <i class="fas fa-check-circle me-2"></i>Place Order
                </button>
            </div>
        </form>
    </div>
</div>

<footer>
    <div class="container">
        <h4>ToyVerse</h4>
        <p>© <?= date("Y") ?> ToyVerse. All rights reserved.</p>
        <p>Contact: <a href="mailto:info@toyverse.com">info@toyverse.com</a></p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
