<?php
session_start();
require "../backend/config/db.php"; // Kết nối PDO $conn

// Kiểm tra session order
if(empty($_SESSION['order'])) {
    header("Location: user.php");
    exit;
}

$order = $_SESSION['order'];

// Lấy dữ liệu từ session với fallback
$order_name    = $order['customer_name'] ?? '';
$order_email   = $order['customer_email'] ?? '';
$order_phone   = $order['phone'] ?? '';
$order_note    = $order['note'] ?? '';
$order_address = $order['address'] ?? '';
$order_items   = $order['items'] ?? [];
$order_total   = $order['total'] ?? 0;

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // Lưu đơn hàng vào bảng orders
    $stmt = $conn->prepare("
        INSERT INTO orders (customer_name, customer_email, address, phone, note, total, order_date)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $order_name,
        $order_email,
        $order_address,
        $order_phone,
        $order_note,
        $order_total
    ]);

    // Lấy id đơn hàng vừa tạo
    $order_id = $conn->lastInsertId();

    // Lưu từng sản phẩm vào order_items
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, price, qty)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach($order_items as $item){
        $item_id    = $item['id'] ?? $item['product_id'] ?? 0;
        $item_name  = $item['name'] ?? $item['product_name'] ?? '';
        $item_price = $item['price'] ?? 0;
        $item_qty   = $item['qty'] ?? 0;

        $stmt_item->execute([
            $order_id,
            $item_id,
            $item_name,
            $item_price,
            $item_qty
        ]);
    }

    // Commit transaction
    $conn->commit();

    // Xóa giỏ hàng và session order
    unset($_SESSION['cart']);
    unset($_SESSION['order']);

} catch(PDOException $e) {
    $conn->rollBack();
    die("Lỗi khi lưu đơn hàng: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đặt hàng thành công</title>
    <style>
        body { font-family:'Segoe UI', sans-serif; margin:20px; background:#fafafa; }
        .container { max-width:600px; margin:auto; background:white; padding:20px; border-radius:6px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        .btn { padding:10px 20px; background:#1d3557; color:white; text-decoration:none; border-radius:4px; display:inline-block; margin-top:20px; }
        .btn:hover { background:#457b9d; }
        ul { padding-left:20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Đặt hàng thành công!</h2>

    <p><b>Họ tên:</b> <?= htmlspecialchars($order_name) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($order_email) ?></p>
    <p><b>Địa chỉ:</b> <?= htmlspecialchars($order_address) ?></p>
    <p><b>Số điện thoại:</b> <?= htmlspecialchars($order_phone) ?></p>
    <p><b>Ghi chú:</b> <?= htmlspecialchars($order_note) ?></p>

    <h3>Sản phẩm:</h3>
    <ul>
        <?php foreach($order_items as $item): 
            $item_name  = $item['name'] ?? $item['product_name'] ?? '';
            $item_qty   = $item['qty'] ?? 0;
            $item_price = $item['price'] ?? 0;
        ?>
            <li>
                <?= htmlspecialchars($item_name) ?> x <?= $item_qty ?> = <?= number_format($item_price * $item_qty,0,',','.') ?> đ
            </li>
        <?php endforeach; ?>
    </ul>

    <p><b>Tổng cộng:</b> <?= number_format($order_total,0,',','.') ?> đ</p>

    <a href="user.php" class="btn">Tiếp tục mua sắm</a>
</div>

</body>
</html>
