<?php
session_start();
require_once 'config.php';
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items for the current user with calculated total price
$cart_query = "SELECT ci.cart_item_id, ci.quantity, ci.total_price, p.name, p.price, p.image, p.product_id
               FROM cart_item ci
               JOIN product p ON ci.product_id = p.product_id 
               JOIN cart c ON ci.cart_id = c.cart_id
               WHERE c.user_id = ?
               ORDER BY ci.cart_item_id DESC";

$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['total_price'];
}

$stmt->close();

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
    <title>Shopping Cart - ToyVerse</title>
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
            background: var(--deep-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

         .nav-link.active {
            color: var(--white);
            background: var(--deep-blue);
            transform: translateY(-2px);
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

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .cart-table th,
        .cart-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-blue);
        }

        .cart-table th {
            background: var(--gradient-card);
            color: var(--deep-blue);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cart-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-name {
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 0.5rem;
        }

        .product-price {
            color: var(--deep-blue);
            font-weight: 500;
        }

        .qty-input {
            width: 80px;
            padding: 0.5rem;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
            text-align: center;
            font-weight: 500;
        }

        .qty-input:focus {
            outline: none;
            border-color: var(--deep-blue);
            box-shadow: 0 0 0 2px rgba(30, 144, 255, 0.2);
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-remove:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--navy);
            opacity: 0.7;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--light-blue);
        }

        .continue-shopping {
            background: var(--sky-blue);
            color: var(--navy);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            background: var(--deep-blue);
            color: white;
            transform: translateY(-2px);
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
            .cart-table {
                font-size: 0.9rem;
            }

            .cart-img {
                width: 60px;
                height: 60px;
            }

            .cart-header h1 {
                font-size: 2rem;
            }

            .main-container {
                padding: 0 0.5rem;
            }
            
            .cart-table th,
            .cart-table td {
                padding: 0.5rem;
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
                    <a class="nav-link active" href="cart.php" style="color: white;">
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
            <h1><i class="fas fa-shopping-cart me-3"></i>Your Shopping Cart</h1>
            <p>Review your selected items and proceed to checkout</p>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-section">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added any toys to your cart yet.</p>
                    <a href="shop.php" class="continue-shopping">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-section">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" 
                                     class="cart-img" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </td>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            </td>
                            <td>
                                <div class="product-price">₱<?php echo number_format($item['price'], 2); ?></div>
                            </td>
                            <td>
                                <form action="update_cart.php" method="POST" class="d-inline">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="99" class="qty-input" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td>
                                <div class="product-price">₱<?php echo number_format($item['total_price'], 2); ?></div>
                            </td>
                            <td>
                                <form action="remove_item.php" method="POST" class="d-inline">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                    <button type="submit" class="btn-remove" onclick="return confirm('Remove this item from cart?')">
                                        <i class="fas fa-trash me-1"></i>Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <div class="cart-total">
                        <i class="fas fa-calculator me-2"></i>Total: ₱<?php echo number_format($total, 2); ?>
                    </div>
                    <a href="checkout.php" class="checkout-btn">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container">
            <h4>ToyVerse</h4>
            <p>© <?= date("Y"); ?> ToyVerse. All rights reserved.</p>
            <p>Contact: <a href="mailto:info@toyverse.com">info@toyverse.com</a> | +63 2 8123 4567</p>
            <div class="mt-2">
                <a href="about.php">About Us</a> | 
                <a href="#">Privacy Policy</a> | 
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>