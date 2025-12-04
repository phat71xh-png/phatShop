<?php
session_start();
require_once __DIR__ . '/../backend/config/db.php';

$message = '';

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin'){
            header('Location: ../Frontend/index.php');
        } else {
            header('Location: user.php');
        }
        exit;
    } else {
        $message = "Sai username hoặc password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập - Fashion Shop</title>
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
        .login-container {
            background:white;
            padding:40px 30px;
            border-radius:10px;
            box-shadow:0 4px 15px rgba(0,0,0,0.2);
            width:350px;
            text-align:center;
        }
        .login-container h2 {
            margin-bottom:25px;
            color:#1d3557;
        }
        .login-container input {
            width:100%;
            padding:10px;
            margin:10px 0;
            border:1px solid #ccc;
            border-radius:6px;
            font-size:14px;
        }
        .login-container button {
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
        .login-container button:hover {
            background:#457b9d;
        }
        .login-container .back-btn {
            display:inline-block;
            margin-top:15px;
            background:#e63946;
            text-decoration:none;
            color:white;
            padding:8px 15px;
            border-radius:6px;
            transition:0.3s;
        }
        .login-container .back-btn:hover {
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

<div class="login-container">
    <h2>Đăng nhập</h2>

    <?php if($message) echo "<div class='message'>$message</div>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Đăng nhập</button>
    </form>

    <div class="links">
        Chưa có tài khoản? <a href="register.php">Đăng ký</a>
    </div>

    <a class="back-btn" href="user.php">← Quay về Trang Chủ</a>
</div>

</body>
</html>
