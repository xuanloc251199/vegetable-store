<?php
session_start();

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "vegetable_store");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $check_sql = "SELECT * FROM admins WHERE admin_email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Email đã được sử dụng. Vui lòng chọn email khác.";
    } else {
        // Thêm tài khoản mới
        $sql = "INSERT INTO admins (admin_name, admin_email, admin_password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $admin_name, $admin_email, $admin_password);

        if ($stmt->execute()) {
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['logged_in'] = true;
            header("Location: dashboard.php");
            exit;
        } else {
            $error_message = "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">Đăng Ký</h1>
        <form action="register_admin.php" method="POST">
            <div class="input-group">
                <label for="name">Tên</label>
                <input type="text" id="name" name="admin_name" placeholder="Nhập tên của bạn" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="admin_email" placeholder="Nhập email của bạn" required>
            </div>
            <div class="input-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="admin_password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn">Đăng Ký</button>
        </form>
        <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    </div>
</body>

</html>