<?php
session_start();
require_once __DIR__ . '../config/db.php'; // file kết nối PDO $conn

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(empty($_SESSION['cart'])) {
        header("Location: user.php");
        exit;
    }

    // Tính tổng tiền
    $total = 0;
    foreach($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Insert vào bảng orders
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, address, total, order_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'] ?? '',
            $_POST['address'],
            $total
        ]);

        // Lấy id đơn hàng vừa tạo
        $order_id = $conn->lastInsertId();

        // Insert từng sản phẩm vào order_items
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)");
        foreach($_SESSION['cart'] as $item) {
            $stmt_item->execute([
                $order_id,
                $item['id'],         // id sản phẩm
                $item['name'],       // tên sản phẩm
                $item['price'],      // giá sản phẩm
                $item['qty']         // số lượng
            ]);
        }

        // Commit transaction
        $conn->commit();

        // Lưu đơn vào session để hiển thị order_success
        $_SESSION['order'] = [
            'id' => $order_id,
            'customer_name' => $_POST['name'],
            'customer_email' => $_POST['email'] ?? '',
            'address' => $_POST['address'],
            'items' => $_SESSION['cart'],
            'total' => $total,
            'order_date' => date("d/m/Y H:i:s")
        ];

        // Xóa giỏ hàng
        unset($_SESSION['cart']);

        header("Location: ../Frontend/order_success.php");
        exit;

    } catch(PDOException $e) {
        $conn->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
