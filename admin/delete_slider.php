<?php
include('../server/connection.php');

// Kiểm tra xem có slider_id không trong URL
if (isset($_GET['slider_id'])) {
    $slider_id = $_GET['slider_id'];

    // Lấy thông tin ảnh slider trước khi xóa
    $sql = "SELECT slider_image FROM slider WHERE slider_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $slider_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $slider_image = $row['slider_image'];

        // Xóa ảnh khỏi thư mục (nếu ảnh tồn tại)
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($slider_image);
        if (file_exists($target_file)) {
            unlink($target_file); // Xóa file ảnh
        }

        // Xóa slider khỏi cơ sở dữ liệu
        $sql_delete = "DELETE FROM slider WHERE slider_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $slider_id);
        if ($stmt_delete->execute()) {
            header("Location: list_slider.php?message=Slider deleted successfully");
        } else {
            header("Location: list_slider.php?error=Failed to delete slider");
        }
    } else {
        header("Location: list_slider.php?error=Slider not found");
    }
} else {
    header("Location: list_slider.php?error=Invalid slider ID");
}
