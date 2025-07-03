<?php
session_start();
require_once 'database.php'; // or your DB connection

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit();
}

$cart_item_id = (int)$_POST['cart_item_id'];
$new_quantity = (int)$_POST['quantity'];

if ($new_quantity > 0) {
    $stmt = $conn->prepare("
        UPDATE cart_item 
        SET quantity = ?, 
            total_price = (SELECT price FROM product WHERE product.product_id = cart_item.product_id) * ?
        WHERE cart_item_id = ?
    ");
    $stmt->bind_param("iii", $new_quantity, $new_quantity, $cart_item_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: cart.php");
exit();
