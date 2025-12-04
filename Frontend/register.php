<?php
session_start();
require_once __DIR__ . '/../backend/config/db.php'; // kết nối DB

$message = '';

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(empty($username) || empty($password)){
        $message = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute([$username]);

        if($stmt->rowCount() > 0){
            $message = "Username đã tồn tại, vui lòng chọn tên khác!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if($stmt->execute([$username, $hashedPassword, $role])){
                $message = "Đăng ký thành công. <a href='login.php'>Đăng nhập ngay</a>";
            } else {
                $message = "Đăng ký thất bại, thử lại sau!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký - Fashion Shop</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background:#f7f7f7;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            margin:0;
        }
        .register-container {
            background:white;
            padding:40px 30px;
            border-radius:10px;
            box-shadow:0 4px 15px rgba(0,0,0,0.2);
            width:350px;
            text-align:center;
        }
        .register-container h2 {
            margin-bottom:25px;
            color:#1d3557;
        }
        .register-container input {
            width:100%;
            padding:10px;
            margin:10px 0;
            border:1px solid #ccc;
            border-radius:6px;
            font-size:14px;
        }
        .register-container button {
            width:100%;
            padding:10px;
            margin-top:15px;
            border:none;
            border-radius:6px;
            background:#1d3557;
            color:white;
            font-size:16px;
            cursor:pointer;
            transition:0.3s;
        }
        .register-container button:hover {
            background:#457b9d;
        }
        .register-container .back-btn {
            display:inline-block;
            margin-top:15px;
            background:#e63946;
            text-decoration:none;
            color:white;
            padding:8px 15px;
            border-radius:6px;
            transition:0.3s;
        }
        .register-container .back-btn:hover {
            background:#d62828;
        }
        .message {
            color:red;
            margin-top:10px;
            font-size:14px;
        }
        .links {
            margin-top:15px;
            font-size:14px;
        }
        .links a {
            color:#1d3557;
            text-decoration:none;
        }
        .links a:hover {
            text-decoration:underline;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Đăng ký tài khoản</h2>

    <?php if($message) echo "<div class='message'>$message</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Đăng ký</button>
    </form>

    <div class="links">
        Đã có tài khoản? <a href="login.php">Đăng nhập</a>
    </div>

    <a class="back-btn" href="user.php">← Quay về Trang Chủ</a>
</div>

</body>
</html>
