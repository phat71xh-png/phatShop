<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'qty' => 1
        ];
    }

    // Đếm tổng số lượng giỏ hàng
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['qty'];
    }

    echo json_encode([
        'success' => true,
        'total_items' => $total
    ]);

    exit;
}
?>
