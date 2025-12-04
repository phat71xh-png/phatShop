<?php
session_start();
require "../backend/config/db.php"; // kết nối PDO $conn

try {
    // Lấy tất cả đơn hàng từ bảng orders, sắp xếp theo ngày giảm dần
    $stmt = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy items của từng đơn
    foreach($orders as &$order){
        $stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt_items->execute([$order['id']]);
        $order['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    }

} catch(PDOException $e){
    die("Lỗi khi lấy đơn hàng: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đơn hàng của tôi</title>
    <style>
        body { font-family:'Segoe UI', sans-serif; margin:20px; background:#fafafa; }
        .container { max-width:800px; margin:auto; background:white; padding:20px; border-radius:6px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px; border:1px solid #ddd; text-align:left; }
        th { background:#1d3557; color:white; }
        .btn { display:inline-block; padding:8px 15px; background:#1d3557; color:white; text-decoration:none; border-radius:4px; margin-bottom:10px; }
        .btn:hover { background:#457b9d; }
    </style>
</head>
<body>

<div class="container">
    <h2>Đơn hàng của tôi</h2>
    <a href="user.php" class="btn">← Quay lại mua sắm</a>

    <?php if(empty($orders)): ?>
        <p>Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <?php foreach($orders as $order): ?>
            <h3>Đơn hàng #<?= $order['id'] ?> (<?= $order['order_date'] ?>)</h3>
            <p><b>Họ tên:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($order['customer_email']) ?></p>
            <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['address']) ?></p>

            <table>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                </tr>
                <?php foreach($order['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format($item['price'],0,',','.') ?> đ</td>
                    <td><?= $item['qty'] ?></td>
                    <td><?= number_format($item['price'] * $item['qty'],0,',','.') ?> đ</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><b>Tổng cộng</b></td>
                    <td><b><?= number_format($order['total'],0,',','.') ?> đ</b></td>
                </tr>
            </table>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
