<?php
session_start();
require "../backend/config/db.php";

// Kiểm tra admin
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header('Location: ../Frontend/login.php');
    exit;
}

// --- Lấy danh sách đơn hàng ---
$stmt_orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// --- Tính tổng doanh thu ---
$stmt_revenue = $conn->query("SELECT SUM(total) AS total_revenue FROM orders");
$total_revenue = $stmt_revenue->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Fashion Shop</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin:0; background:#fafafa; }
        .header { background:white; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; }
        .logo { font-size:24px; font-weight:bold; }
        .add-btn { background:#ff4d4d; padding:10px 15px; color:white; text-decoration:none; border-radius:6px; }
        .btn-logout { background:#1d3557; padding:6px 12px; color:white; text-decoration:none; border-radius:5px; }
        .btn-logout:hover { background:#457b9d; }
        .container { padding:30px 40px; }
        table { width:100%; border-collapse:collapse; margin-top:30px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#1d3557; color:white; }
        .btn-delete { padding:6px 12px; text-decoration:none; border-radius:5px; font-size:13px; display:inline-block; background:#e63946; color:white; }
        .btn-delete:hover { background:#d62828; }
        h2 { margin-bottom:10px; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">ADMIN - FASHION SHOP</div>

    <div style="display:flex; gap:10px;">
        <a class="add-btn" href="index.php">Danh sách sản phẩm</a>
        <a class="add-btn" href="add.php">+ Thêm sản phẩm</a>
        <a class="add-btn" href="orders.php">Đơn hàng</a>
        <a class="btn-logout" href="../Frontend/logout.php">Đăng xuất</a>
    </div>
</div>

<div class="container">

    <!-- Thông báo -->
    <?php if(isset($_GET['deleted'])): ?>
        <div style="padding: 10px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:6px;">
            ✔ Đã xóa đơn hàng thành công.
        </div>
    <?php endif; ?>

 
    <!-- Bảng danh sách đơn hàng -->
    <h2>Danh sách đơn hàng</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Email</th>
            <th>Địa chỉ</th>
            <th>Điện thoại</th>
            <th>Ghi chú</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
            <th>Hành động</th>
        </tr>

        <?php foreach($orders as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= htmlspecialchars($o['customer_email']) ?></td>
            <td><?= htmlspecialchars($o['address']) ?></td>
            <td><?= htmlspecialchars($o['phone']) ?></td>
            <td><?= htmlspecialchars($o['note']) ?></td>
            <td><?= number_format($o['total'],0,',','.') ?> đ</td>
            <td><?= $o['order_date'] ?></td>
            <td>
                <a class="btn-delete" 
                   href="../backend/delete_order.php?id=<?= $o['id'] ?>" 
                   onclick="return confirm('Bạn chắc chắn muốn xóa đơn hàng này?');">
                   Xóa
                </a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

       <!-- Tổng doanh thu -->
    <h2>Tổng doanh thu: <?= number_format($total_revenue,0,',','.') ?> đ</h2>


</div>

</body>
</html>
