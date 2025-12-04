<?php
session_start();
require "../backend/config/db.php"; // kết nối PDO $conn

// Nếu giỏ hàng trống, quay về trang mua sắm
if(empty($_SESSION['cart'])) {
    header("Location: user.php");
    exit;
}

$cart = $_SESSION['cart'];

// Xử lý submit form
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form POST
    $order_name    = $_POST['name'] ?? '';
    $order_email   = $_POST['email'] ?? '';
    $order_address = $_POST['address'] ?? '';
    $order_phone   = $_POST['phone'] ?? '';
    $order_note    = $_POST['note'] ?? '';

    // Tính tổng tiền
    $order_total = 0;
    foreach($cart as $item) {
        $order_total += $item['price'] * $item['qty'];
    }

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
        foreach($cart as $item){
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

        // Lưu thông tin đơn hàng vào session để hiển thị order_success.php
        $_SESSION['order'] = [
            'customer_name'  => $order_name,
            'customer_email' => $order_email,
            'address'        => $order_address,
            'phone'          => $order_phone,
            'note'           => $order_note,
            'items'          => $cart,
            'total'          => $order_total,
            'order_date'     => date("Y-m-d H:i:s")
        ];

        // Xóa giỏ hàng
        unset($_SESSION['cart']);

        // Chuyển sang trang thành công
        header("Location: order_success.php");
        exit;

    } catch(PDOException $e) {
        $conn->rollBack();
        die("Lỗi khi lưu đơn hàng: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thanh Toán</title>
    <style>
        body { font-family:'Segoe UI', sans-serif; margin:20px; background:#fafafa; }
        .container { max-width:600px; margin:auto; background:white; padding:20px; border-radius:6px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        input, textarea { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:4px; }
        .btn { padding:10px 20px; background:#1d3557; color:white; text-decoration:none; border-radius:4px; cursor:pointer; border:none; }
        .btn:hover { background:#457b9d; }
        .btn-back { background:#e63946; display:inline-block; margin-bottom:15px; text-decoration:none; padding:8px 15px; border-radius:4px; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#f1f1f1; }
    </style>
</head>
<body>

<div class="container">
    <h2>Thông tin giao hàng</h2>

    <!-- Nút quay lại giỏ hàng -->
    <a href="cart.php" class="btn-back">← Quay lại giỏ hàng</a>

    <!-- Form thông tin khách hàng -->
    <form action="" method="post">
        <label>Họ và tên:</label>
        <input type="text" name="name" required placeholder="Nguyễn Văn A">

        <label>Email:</label>
        <input type="email" name="email" required placeholder="email@example.com">

        <label>Địa chỉ:</label>
        <textarea name="address" rows="3" required placeholder="Số nhà, đường, phường/xã, quận/huyện, TP"></textarea>

        <label>Số điện thoại:</label>
        <input type="text" name="phone" required placeholder="0901234567">

        <label>Ghi chú:</label>
        <textarea name="note" rows="2" placeholder="Ví dụ: Giao giờ hành chính"></textarea>

        <!-- Tóm tắt giỏ hàng -->
        <h3>Giỏ hàng của bạn</h3>
        <table>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
            <?php foreach($cart as $item): 
                $item_total = $item['price'] * $item['qty'];
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= number_format($item['price'],0,',','.') ?> đ</td>
                <td><?= number_format($item_total,0,',','.') ?> đ</td>
            </tr>
            <?php endforeach; ?>
           
        </table>

        <!-- Nút xác nhận đặt hàng -->
        <button type="submit" class="btn" style="margin-top:15px;">Xác nhận đặt hàng</button>
    </form>
</div>

</body>
</html>

