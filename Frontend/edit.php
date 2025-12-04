<?php
require "../backend/config/db.php";

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"], 
        input[type="number"], 
        textarea, 
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .current-image {
            margin-bottom: 15px;
        }
        .current-image img {
            width: 120px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Sửa sản phẩm</h2>

    <form action="../backend/update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">

        <label for="name">Tên sản phẩm</label>
        <input type="text" name="name" id="name" value="<?= $p['name'] ?>" required>

        <label for="price">Giá</label>
        <input type="number" name="price" id="price" value="<?= $p['price'] ?>" required>

        <label for="description">Mô tả</label>
        <textarea name="description" id="description" required><?= $p['description'] ?></textarea>

        <div class="current-image">
            <label>Ảnh hiện tại</label><br>
            <img src="uploads/<?= $p['image'] ?>" alt="<?= $p['name'] ?>">
        </div>

        <label for="image">Ảnh mới (nếu muốn đổi)</label>
        <input type="file" name="image" id="image">

        <button type="submit">Cập nhật sản phẩm</button>
    </form>

    <a class="back-link" href="index.php">&larr; Quay lại danh sách sản phẩm</a>
</div>

</body>
</html>
