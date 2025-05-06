<?php
session_start();

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "vegetable_store");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['code'])) {
    $reset_code = $_GET['code'];

    // Truy vấn mã reset mật khẩu
    $sql = "SELECT * FROM admins WHERE reset_code = ? AND reset_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reset_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email hợp lệ, hiển thị form nhập mật khẩu mới
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu mới và xóa mã reset
            $update_sql = "UPDATE admins SET admin_password = ?, reset_code = NULL, reset_expiry = NULL WHERE reset_code = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $reset_code);
            if ($update_stmt->execute()) {
                $_SESSION['status_message'] = "Mật khẩu đã được thay đổi thành công!";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['status_message'] = "Đã xảy ra lỗi khi thay đổi mật khẩu!";
            }
        }
    } else {
        $_SESSION['status_message'] = "Mã reset không hợp lệ hoặc đã hết hạn!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">Đặt Lại Mật Khẩu</h1>
        <?php if (isset($_SESSION['status_message'])): ?>
            <div class="alert alert-info mt-3">
                <?php echo $_SESSION['status_message']; ?>
                <?php unset($_SESSION['status_message']); ?>
            </div>
        <?php endif; ?>

        <form action="reset_password.php?code=<?php echo $_GET['code']; ?>" method="POST">
            <div class="input-group">
                <label for="new_password">Mật Khẩu Mới</label>
                <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
            </div>
            <button type="submit" class="btn">Đặt Lại Mật Khẩu</button>
        </form>
    </div>
</body>

</html>