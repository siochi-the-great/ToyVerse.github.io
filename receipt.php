<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the latest checkout for the user
$stmt = $conn->prepare("SELECT * FROM checkout WHERE user_id = ? ORDER BY checkout_id DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$checkout = $result->fetch_assoc();
$stmt->close();

if (!$checkout) {
    echo "<div style='text-align:center;padding:2rem;'>No recent checkout found.</div>";
    exit();
}

// Fetch cart items for the checkout
$stmt = $conn->prepare("SELECT p.name, ci.quantity, p.price, ci.total_price
                         FROM cart_item ci
                         JOIN product p ON ci.product_id = p.product_id
                         WHERE ci.cart_id = ?");
$stmt->bind_param("i", $checkout['cart_id']);
$stmt->execute();
$items_result = $stmt->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Clear the cart items after checkout
$conn->query("DELETE FROM cart_item WHERE cart_id = " . (int)$checkout['cart_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - ToyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #F0F8FF;
            color: #000080;
            padding: 2rem;
        }
        .receipt-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #1E90FF;
        }
        .total {
            font-size: 1.2rem;
            font-weight: bold;
        }
        footer {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <h2 class="text-center mb-4">Order Receipt</h2>
    <p><strong>Order ID:</strong> #<?= $checkout['checkout_id'] ?></p>
    <p><strong>Shipping Address:</strong> <?= htmlspecialchars($checkout['shipping_address']) ?></p>
    <p><strong>Payment Method:</strong> <?= ($checkout['payment_id'] == 1 ? 'Cash on Delivery' : ($checkout['payment_id'] == 2 ? 'Credit Card' : 'GCash')) ?></p>
    <hr>
    <h5>Items Purchased:</h5>
    <ul class="list-group mb-3">
        <?php foreach ($items as $item): ?>
            <li class="list-group-item d-flex justify-content-between">
                <div><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</div>
                <div>₱<?= number_format($item['total_price'], 2) ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="total text-end">Total Paid: ₱<?= number_format($checkout['total_price'], 2) ?></p>
    <div class="text-center mt-4">
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>
<footer>
    <p>© <?= date("Y") ?> ToyVerse. Thank you for your purchase!</p>
</footer>
</body>
</html>
