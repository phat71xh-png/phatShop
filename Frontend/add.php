<!DOCTYPE html>
<html>
<head>
    <title>Thêm sản phẩm</title>
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
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
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
    </style>
</head>
<body>

<div class="container">
    <h2>Thêm sản phẩm</h2>

    <form action="../backend/insert.php" method="POST" enctype="multipart/form-data">
        <label for="name">Tên sản phẩm</label>
        <input type="text" name="name" id="name" required>

        <label for="price">Giá</label>
        <input type="number" name="price" id="price" required>

        <label for="description">Mô tả</label>
        <textarea name="description" id="description" required></textarea>

        <label for="image">Ảnh</label>
        <input type="file" name="image" id="image" required>

        <button type="submit">Thêm sản phẩm</button>
    </form>

    <a class="back-link" href="index.php">&larr; Quay lại danh sách sản phẩm</a>
</div>

</body>
</html>
