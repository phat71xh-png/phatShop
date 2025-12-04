<?php
session_start();

/* ================================
   🔒 BẮT ĐĂNG NHẬP TRƯỚC KHI VÀO GIỎ
=================================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Vui lòng đăng nhập để sử dụng giỏ hàng");
    exit;
}

/* ================================
   🛒 THÊM SẢN PHẨM TỪ user.php
=================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'qty' => 1
        ];
    }

    header("Location: cart.php");
    exit;
}

/* ================================
   ➕ ➖  TĂNG / GIẢM SỐ LƯỢNG
=================================== */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        if ($_GET['action'] === 'increase') {
            $_SESSION['cart'][$id]['qty'] += 1;
        } elseif ($_GET['action'] === 'decrease') {
            $_SESSION['cart'][$id]['qty'] -= 1;
            if ($_SESSION['cart'][$id]['qty'] < 1) unset($_SESSION['cart'][$id]);
        }
    }
    header("Location: cart.php");
    exit;
}

/* ================================
   ❌ XOÁ SẢN PHẨM
=================================== */
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

/* ================================
   ĐẾM TỔNG SỐ LƯỢNG SẢN PHẨM
=================================== */
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $total_items += $item['qty'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giỏ Hàng</title>
    <style>
        body { font-family:'Segoe UI', sans-serif; margin:0; padding:20px; background:#fafafa; }
        .header { margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
        .btn { padding:6px 12px; background:#1d3557; color:white; text-decoration:none; border-radius:4px; }
        .btn:hover { background:#457b9d; }
        table { width:100%; border-collapse:collapse; background:white; }
        th, td { padding:10px; border-bottom:1px solid #ddd; text-align:center; }
        img { width:80px; height:80px; object-fit:cover; }
        .btn-remove { padding:4px 8px; background:#e63946; color:white; text-decoration:none; border-radius:4px; }
        .btn-remove:hover { background:#d62828; }
        .qty-btn { padding: 5px 10px; margin: 0 3px; background: #1d3557; color: white; border: none;
                   border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; text-decoration:none; }
        .qty-btn:hover { background:#457b9d; }
        .total { font-weight:bold; }
    </style>
</head>
<body>

<div class="header">
    <h2>Giỏ Hàng</h2>
    <div>
        <a class="btn" href="user.php">Tiếp tục mua sắm</a>
        <a class="btn" href="cart.php">Giỏ Hàng (<?= $total_items ?>)</a>
        <a class="btn" href="my_orders.php">Xem Đơn</a>
    </div>
</div>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Giỏ hàng trống.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng</th>
            <th>Hành động</th>
        </tr>

        <?php $total = 0; ?>
        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
        <tr>
            <td><img src="uploads/<?= $item['image'] ?>" alt="<?= $item['name'] ?>"></td>
            <td><?= $item['name'] ?></td>
            <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
            <td>
                <a class="qty-btn" href="cart.php?action=decrease&id=<?= $id ?>">-</a>
                <?= $item['qty'] ?>
                <a class="qty-btn" href="cart.php?action=increase&id=<?= $id ?>">+</a>
            </td>
            <td><?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?> đ</td>
            <td><a class="btn-remove" href="cart.php?remove=<?= $id ?>">Xóa</a></td>
        </tr>
        <?php $total += $item['price'] * $item['qty']; ?>
        <?php endforeach; ?>

        <tr>
            <td colspan="4" class="total">Tổng cộng</td>
            <td colspan="2" class="total"><?= number_format($total, 0, ',', '.') ?> đ</td>
        </tr>

        <tr>
            <td colspan="6" style="text-align:right;">
                <a class="btn" href="checkout.php">Mua hàng</a>
            </td>
        </tr>

    </table>
<?php endif; ?>

</body>
</html>
