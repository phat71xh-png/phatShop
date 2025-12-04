<?php
require __DIR__ . "/config/db.php";   // đúng đường dẫn tuyệt đối

// Lấy dữ liệu form
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? '';
$desc = $_POST['description'] ?? '';

// Xử lý upload ảnh
$image = $_FILES['image']['name'];

// Đường dẫn lưu ảnh: shop/uploads/
$uploadPath = __DIR__ . "/../uploads/" . $image;

// Kiểm tra upload file có lỗi không
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
} else {
    echo "Lỗi upload file!";
    exit;
}

// Insert vào database
$sql = "INSERT INTO products (name, price, description, image)
        VALUES (:name, :price, :description, :image)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':price' => $price,
        ':description' => $desc,
        ':image' => $image
    ]);

    // Chuyển về danh sách sản phẩm ở FE
    header("Location: ../Frontend/index.php");
    exit;

} catch (PDOException $e) {
    echo "Lỗi Database: " . $e->getMessage();
}
