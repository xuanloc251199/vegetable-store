<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào Mừng Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            font-family: 'Arial', sans-serif;
            color: #fff;
            text-align: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .welcome-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .welcome-title {
            font-size: 2rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        .welcome-text {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .options a {
            display: block;
            text-decoration: none;
            color: #fff;
            background: #6a70fc;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 1rem;
            margin: 10px 0;
            transition: background 0.3s ease-in-out;
        }

        .options a:hover {
            background: #4c52d2;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <h1 class="welcome-title">Chào Mừng Bạn!</h1>
        <p class="welcome-text">Bạn đã đến với trang quản trị. Hãy chọn hành động bạn muốn thực hiện:</p>
        <div class="options">
            <a href="login.php">Đăng Nhập</a>
            <a href="register_admin.php">Đăng Ký</a>
            <a href="change_password.php">Đổi Mật Khẩu</a>
        </div>
        <div class="footer">
            &copy; 2024 Trang Quản Trị
        </div>
    </div>
</body>

</html>