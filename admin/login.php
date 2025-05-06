<?php
session_start();

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "vegetable_store");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];

    // Truy vấn thông tin tài khoản
    $sql = "SELECT * FROM admins WHERE admin_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Xác thực mật khẩu
        if (password_verify($admin_password, $row['admin_password'])) {
            $_SESSION['admin_name'] = $row['admin_name']; // Lưu tên
            $_SESSION['admin_email'] = $row['admin_email']; // Lưu email
            $_SESSION['logged_in'] = true; // Đánh dấu đã đăng nhập
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Mật khẩu không đúng!";
        }
    } else {
        $error_message = "Email không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">Đăng Nhập</h1>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="admin_email" placeholder="Nhập email của bạn" required>
            </div>
            <div class="input-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="admin_password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn">Đăng Nhập</button>
        </form>
        <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
        <div class="options">
            <a href="register_admin.php">Đăng Ký</a>
            <a href="forgot_password.php">Quên Mật Khẩu</a>
        </div>
    </div>
</body>

</html>