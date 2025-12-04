<?php
$host   = 'localhost';   // vì chạy local
$dbname = 'fashion_shop';     // tên database bạn vừa tạo
$user   = 'root';        // mặc định XAMPP
$pass   = '';            // mặc định XAMPP không có password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>
