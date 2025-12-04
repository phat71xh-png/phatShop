<?php
require "../backend/config/db.php";

// Lấy id sản phẩm từ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

$sql = "SELECT * FROM products WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $product['name'] ?> - Fashion Shop</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fafafa; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }
        
        .product-img {
            width: 100%;
            height: auto; /* Giữ tỉ lệ */
            max-height: 500px;
            object-fit: contain; /* Hiển thị toàn bộ ảnh, có thể có viền trống */
            border-radius: 10px;
            background: #f0f0f0; /* Nền cho khoảng trống */
        }

        .product-info { margin-top: 20px; }
        .product-title { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .price { font-size: 24px; color: #e63946; font-weight: bold; margin-bottom: 20px; }
        .description { font-size: 16px; color: #555; line-height: 1.5; }
        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #333; color: white; text-decoration: none; border-radius: 6px; }
    </style>
</head>
<body>

<div class="container">
    <img class="product-img" src="../uploads/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
    <div class="product-info">
        <div class="product-title"><?= $product['name'] ?></div>
        <div class="price"><?= number_format($product['price'], 0, ',', '.') ?> đ</div>
        <div class="description"><?= nl2br($product['description']) ?></div>

        <!-- Nút Thêm vào giỏ hàng -->
        <form method="POST" action="cart.php" style="margin-top:20px;">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
            <input type="hidden" name="price" value="<?= $product['price'] ?>">
            <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
            <input type="hidden" name="add_to_cart" value="1">
            <button type="submit" style="
                padding: 10px 20px;
                background: #e63946;
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                cursor: pointer;
            ">
                Thêm vào giỏ hàng
            </button>
        </form>
    </div>
    <a class="back-btn" href="user.php">← Quay lại danh sách</a>
</div>
</body>
</html>
