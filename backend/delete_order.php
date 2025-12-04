<?php
session_start();
require "config/db.php";

// Kiểm tra quyền admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Frontend/login.php");
    exit;
}

// Kiểm tra ID hợp lệ
if (!isset($_GET['id'])) {
    die("Thiếu ID đơn hàng");
}

$order_id = $_GET['id'];

// Xóa đơn hàng
$stmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
$stmt->execute(['id' => $order_id]);

// Sau khi xóa quay lại trang orders.php
header("Location: ../Frontend/orders.php?deleted=1");
exit;
