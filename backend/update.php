<?php
require "../backend/config/db.php";

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

// Kiểm tra người dùng có upload file mới không
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
} else {
    // Nếu không upload, giữ nguyên ảnh cũ
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $image = $row['image'];
}

// Cập nhật sản phẩm
$sql = "UPDATE products SET name = :name, price = :price, description = :description, image = :image WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([
    'name' => $name,
    'price' => $price,
    'description' => $description,
    'image' => $image,
    'id' => $id
]);

header("Location: ../Frontend/index.php");
exit;
?>
