<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit();
}

$cart_item_id = (int)$_POST['cart_item_id'];

$stmt = $conn->prepare("DELETE FROM cart_item WHERE cart_item_id = ?");
$stmt->bind_param("i", $cart_item_id);
$stmt->execute();
$stmt->close();

header("Location: cart.php");
exit();
