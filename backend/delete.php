<?php
require __DIR__ . "/config/db.php";  // Sửa đường dẫn tuyệt đối

// Lấy id sản phẩm cần xóa
$id = $_GET['id'] ?? '';

if (!$id) {
    echo "Không tìm thấy ID sản phẩm!";
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Chuyển về danh sách sản phẩm FE
    header("Location: ../Frontend/index.php");
    exit;

} catch (PDOException $e) {
    echo "Lỗi database: " . $e->getMessage();
}
