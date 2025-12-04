<?php
session_start();
require "../backend/config/db.php";

// Kiểm tra admin
if(!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin'){
    header('Location: ../Frontend/login.php');
    exit;
}

// --- Tìm kiếm sản phẩm ---
$keyword = $_GET['keyword'] ?? '';
$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :keyword");
$stmt->execute(['keyword' => "%$keyword%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        .header { background:white; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; position:sticky; top:0; z-index:10; }
        .logo { font-size:24px; font-weight:bold; }
        .search-bar { width:350px; display:flex; background:#f1f1f1; border-radius:6px; overflow:hidden; }
        .search-bar input { width:100%; padding:10px; border:none; outline:none; background:transparent; }
        .search-bar button { background:black; color:white; border:none; padding:0 15px; cursor:pointer; }
        .add-btn { background:#ff4d4d; padding:10px 15px; color:white; text-decoration:none; border-radius:6px; }
        .btn-logout { background:#1d3557; padding:6px 12px; color:white; text-decoration:none; border-radius:5px; }
        .btn-logout:hover { background:#457b9d; }
        .container { padding:30px 40px; }
        table { width:100%; border-collapse:collapse; margin-top:30px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#1d3557; color:white; }
        .btn-edit, .btn-delete { padding:6px 12px; text-decoration:none; border-radius:5px; font-size:13px; color:white; margin:2px; display:inline-block; }
        .btn-edit { background:#f0ad4e; }
        .btn-delete { background:#e63946; }
        .btn-edit:hover { background:#f7b731; }
        .btn-delete:hover { background:#d62828; }
        h2 { margin-bottom:10px; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">ADMIN - FASHION SHOP</div>

    <form class="search-bar" method="GET">
        <input type="text" name="keyword" placeholder="Tìm sản phẩm..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit">Tìm</button>
    </form>

    <div style="display:flex; gap:10px;">
        <a class="add-btn" href="add.php">+ Thêm sản phẩm</a>
        <a class="add-btn" href="orders.php">Đơn hàng</a>
        <a class="btn-logout" href="../Frontend/logout.php">Đăng xuất</a>
    </div>
</div>

</div>

<div class="container">
    <!-- Bảng sản phẩm -->
    <h2>Danh sách sản phẩm</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Tên sản phẩm</th>
            <th>Hình ảnh</th>
            <th>Giá</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
        <?php foreach($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><img src="../uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:80px; height:80px; object-fit:cover;"></td>
            <td><?= number_format($p['price'],0,',','.') ?> đ</td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td>
                <a class="btn-edit" href="edit.php?id=<?= $p['id'] ?>">Sửa</a>
                <a class="btn-delete" href="../backend/delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Xóa sản phẩm?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
