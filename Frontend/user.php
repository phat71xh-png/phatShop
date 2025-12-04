<?php
session_start();
require "../backend/config/db.php";

// Tìm kiếm sản phẩm
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

$sql = "SELECT * FROM products WHERE name LIKE :keyword";
$stmt = $conn->prepare($sql);
$stmt->execute(['keyword' => "%$keyword%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng số lượng giỏ hàng
$total_items = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_items += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fashion Shop - Trang Người Dùng</title>
    <style>
        body { font-family:'Segoe UI', sans-serif; margin:0; background:#fafafa; }
        .header {
            background:white; padding:15px 40px;
            display:flex; justify-content:center; align-items:center;
            border-bottom:1px solid #eee; position:sticky; top:0; z-index:10;
        }
        .logo { font-size:24px; font-weight:bold; letter-spacing:1px; }
        .search-bar { width:350px; display:flex; background:#f1f1f1;
            border-radius:6px; overflow:hidden; margin-left:30px; }
        .search-bar input { width:100%; padding:10px; border:none; background:transparent; outline:none; }
        .search-bar button { background:black; color:white; border:none; padding:0 15px; cursor:pointer; }
        .add-btn {
            display:inline-block; background:#1d3557; padding:10px 15px; color:white;
            text-decoration:none; border-radius:6px; margin-left:10px; transition:0.3s;
        }
        .add-btn:hover { background:#457b9d; }
        .container { padding:30px 40px; }
        .product-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:25px; }
        .card {
            background:white; border-radius:10px; overflow:hidden;
            box-shadow:0 3px 8px rgba(0,0,0,0.1); transition:0.3s;
        }
        .card:hover { transform:translateY(-5px); }
        .card img { width:100%; height:250px; object-fit:cover; }
        .card-body { padding:15px; }
        .card-title { font-size:16px; font-weight:bold; margin-bottom:8px; }
        .price { font-size:18px; color:#e63946; font-weight:bold; margin-bottom:8px; }
        .desc { font-size:14px; color:#555; height:40px; overflow:hidden; }
        .actions { margin-top:10px; display:flex; justify-content:center; }
        .btn-view {
            padding:6px 12px; text-decoration:none; border-radius:5px;
            font-size:13px; background:#1d3557; color:white; cursor:pointer;
        }
        .btn-add { background:#e63946; }
        .btn-add:hover { background:#d62828; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">FASHION SHOP</div>

    <form class="search-bar" method="GET">
        <input type="text" name="keyword" placeholder="Tìm sản phẩm..." value="<?= htmlspecialchars($keyword) ?>">
        <button type="submit">Tìm</button>
    </form>

    <?php if(isset($_SESSION['username'])): ?>
        <span style="margin-left:20px; font-weight:bold;">Xin chào, <?= $_SESSION['username'] ?></span>
        <?php if($_SESSION['role']=='admin'): ?>
            <a class="add-btn" href="../backend/index.php">Trang Admin</a>
        <?php endif; ?>
        <a class="add-btn" href="cart.php">Giỏ Hàng (<span id="cart-count"><?= $total_items ?></span>)</a>
        <a class="add-btn" href="logout.php" style="background:#e63946;">Đăng xuất</a>
    <?php else: ?>
        <a class="add-btn" href="login.php">Đăng nhập</a>
        <a class="add-btn" href="register.php" style="background:#457b9d;">Đăng ký</a>
        <a class="add-btn" href="cart.php">Giỏ Hàng (<span id="cart-count"><?= $total_items ?></span>)</a>
    <?php endif; ?>
</div>

<!-- Danh sách sản phẩm -->
<div class="container">
    <div class="product-grid">
        <?php if(empty($products)): ?>
            <p>Không tìm thấy sản phẩm.</p>
        <?php else: ?>
            <?php foreach($products as $p): ?>
            <div class="card">
                <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="card-body">
                    <div class="card-title"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="price"><?= number_format($p['price'],0,',','.') ?> đ</div>
                    <div class="desc"><?= htmlspecialchars($p['description']) ?></div>

                    <div class="actions">
                        <a class="btn-view" href="detail.php?id=<?= $p['id'] ?>">Xem chi tiết</a>

                        <!-- Nút thêm giỏ hàng AJAX -->
                        <button 
                            class="btn-view btn-add add-cart-btn"
                            data-id="<?= $p['id'] ?>"
                            data-name="<?= htmlspecialchars($p['name']) ?>"
                            data-price="<?= $p['price'] ?>"
                            data-image="<?= htmlspecialchars($p['image']) ?>"
                            style="margin-left:5px;"
                        >
                            Thêm vào giỏ
                        </button>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX -->
<script>
document.querySelectorAll('.add-cart-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        const formData = new FormData();
        formData.append("id", this.dataset.id);
        formData.append("name", this.dataset.name);
        formData.append("price", this.dataset.price);
        formData.append("image", this.dataset.image);

        fetch("add_to_cart.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                // cập nhật số lượng giỏ hàng
                document.getElementById("cart-count").innerText = data.total_items;

                // thông báo
                alert("Đã thêm sản phẩm vào giỏ!");
            }
        })
    });
});
</script>

</body>
</html>
