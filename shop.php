<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "toyverse_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle Add to Cart
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add') {
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity']; // Cast to integer to ensure it's a number
        $user_id = $_SESSION['user_id'];
        
        // Check if quantity is greater than 0
        if ($quantity <= 0) {
            $error_message = "Please select a quantity greater than 0.";
        } else {
            // Get product price
            $stmt = $conn->prepare("SELECT price FROM product WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $total_price = $product['price'] * $quantity;
                
                // Check if user has a cart, if not create one
                $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $cart = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$cart) {
                    $stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
                    $stmt->execute([$user_id]);
                    $cart_id = $conn->lastInsertId();
                } else {
                    $cart_id = $cart['cart_id'];
                }
                
                // Check if item already exists in cart
                $stmt = $conn->prepare("SELECT * FROM cart_item WHERE cart_id = ? AND product_id = ?");
                $stmt->execute([$cart_id, $product_id]);
                $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_item) {
                    // Update existing item
                    $new_quantity = $existing_item['quantity'] + $quantity;
                    $new_total = $product['price'] * $new_quantity;
                    $stmt = $conn->prepare("UPDATE cart_item SET quantity = ?, total_price = ? WHERE cart_item_id = ?");
                    $stmt->execute([$new_quantity, $new_total, $existing_item['cart_item_id']]);
                } else {
                    // Add new item
                    $stmt = $conn->prepare("INSERT INTO cart_item (cart_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$cart_id, $product_id, $quantity, $total_price]);
                }
                
                $success_message = "Item added to cart successfully!";
            } else {
                $error_message = "Product not found.";
            }
        }
    }

    // Category filter
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $orderClause = "ORDER BY product_id DESC";

    if ($filter === 'rating') {
        $orderClause = "ORDER BY rating DESC";
    } elseif ($filter === 'price') {
        $orderClause = "ORDER BY price ASC";
    }

    // Fetch products from database
    $stmt = $conn->prepare("SELECT * FROM product $orderClause");
    $stmt->execute();

    $products = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[] = [
            'id' => $row['product_id'],
            'name' => $row['name'], 
            'price' => $row['price'],
            'img' => $row['image'],
            'rating' => $row['rating'],
            'description' => $row['description'],
        ];
    }

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
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
    <title>Shop - ToyVerse</title>
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

        .shop-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--gradient-card);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .shop-title {
            font-size: 2.5rem;
            color: var(--deep-blue);
            margin: 0;
            font-weight: 700;
        }

        .sort-dropdown {
            position: relative;
        }

        .sort-btn {
            background: var(--white);
            border: 2px solid var(--light-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            color: var(--navy);
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .sort-btn:hover {
            background: var(--deep-blue);
            color: var(--white);
            border-color: var(--deep-blue);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            padding: 0.5rem 0;
            z-index: 1000;
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--navy);
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: var(--light-blue);
            color: var(--navy);
        }

        .dropdown-item.active {
            background: var(--deep-blue);
            color: var(--white);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            padding: 1rem 0;
            align-items: stretch;
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 500px;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: var(--off-white);
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .out-of-stock-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #6c757d;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .hot-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .product-content {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-brand {
            color: #6c757d;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 0.75rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-description {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 1rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            color: #FFD700;
            font-size: 0.9rem;
        }

        .rating-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .product-price {
            font-size: 1.2rem;
            color: var(--deep-blue);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .add-to-cart-form {
            margin-top: auto;
        }

        .add-to-cart-section {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid var(--light-blue);
            border-radius: 8px;
            text-align: center;
            font-size: 0.9rem;
        }

        .add-to-cart-btn {
            flex: 1;
            padding: 0.75rem;
            background: var(--deep-blue);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .add-to-cart-btn:hover {
            background: var(--navy);
            transform: translateY(-2px);
        }

        .alert {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .shop-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .shop-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }

            .product-content {
                padding: 1rem;
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
                    <a class="nav-link active" href="shop.php" style="color: white;">Shop</a>
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
        <div class="shop-header">
            <h1 class="shop-title">Hirana</h1>
            <div class="sort-dropdown">
                <button type="button" class="sort-btn" id="sortDropdown" onclick="toggleDropdown()">
                    Sort By - <?= 
                        $filter === 'all' ? 'Recommend' : 
                        ($filter === 'rating' ? 'Top Rated' : 'Price: Low to High') 
                    ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="?filter=all" class="dropdown-item <?= $filter === 'all' ? 'active' : '' ?>">
                        Recommend
                    </a>
                    <a href="?filter=rating" class="dropdown-item <?= $filter === 'rating' ? 'active' : '' ?>">
                        Top Rated
                    </a>
                    <a href="?filter=price" class="dropdown-item <?= $filter === 'price' ? 'active' : '' ?>">
                        Price: Low to High
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="images/<?= htmlspecialchars($product['img']); ?>"
                                 alt="<?= htmlspecialchars($product['name']); ?>" 
                                 class="product-image">
                        </div>
                        <div class="product-content">
                            <div class="product-brand">POP MART</div>
                            <div class="product-name"><?= htmlspecialchars($product['name']); ?></div>
                            <div class="product-description">
                                <?= htmlspecialchars($product['description']); ?>
                            </div>
                            <div class="product-rating">
                                <div class="stars">
                                    <?php 
                                    $rating = $product['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="rating-text"><?= $rating ?></span>
                            </div>
                            <div class="product-price">₱<?= number_format($product['price'], 2); ?></div>
                            <form action="" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <div class="add-to-cart-section">
                                    <input type="number" name="quantity" value="0" min="1" max="100" class="quantity-input">
                                    <button type="submit" class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-box-open mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p>No products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <h4>ToyVerse</h4>
            <p>© <?= date("Y"); ?> ToyVerse. All rights reserved.</p>
            <p>Contact: <a href="mailto:info@toyverse.com">info@toyverse.com</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const button = document.getElementById('sortDropdown');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>