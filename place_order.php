<?php
session_start();
include('server/connection.php');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('location: checkout.php?message=Please login/register to place an order');
    exit();
} else {
    if (isset($_POST['place_order'])) {

        // 1. Lấy thông tin người dùng và lưu vào database
        $name = $_POST['customer_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        if (!isset($_SESSION['user_id'])) {
            die("Error: user_id is missing from the session.");
        }

        $user_id = $_SESSION['user_id'];
        $order_status = "Pending";
        $order_cost = $_SESSION['total'];
        $order_date = date('Y-m-d H:i:s');

        // Thêm đơn hàng vào bảng orders
        $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Lỗi chuẩn bị truy vấn SQL: " . $conn->error);
        }

        $stmt->bind_param('dsisss', $order_cost, $order_status, $user_id, $phone, $address, $order_date);

        if (!$stmt->execute()) {
            die("Lỗi khi thêm đơn hàng: " . $stmt->error);
        }

        $order_id = $stmt->insert_id;

        // 3. Lặp qua giỏ hàng và lưu từng sản phẩm vào bảng order_items
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $product_quantity = $item['product_quantity'];

            // 3.1 Kiểm tra số lượng sản phẩm trong kho
            $stmt2 = $conn->prepare("SELECT quantity FROM products WHERE product_id = ?");
            if (!$stmt2) {
                die("Lỗi chuẩn bị truy vấn SQL kiểm tra sản phẩm: " . $conn->error);
            }

            $stmt2->bind_param('i', $product_id);
            $stmt2->execute();
            $stmt2->bind_result($quantity);
            $stmt2->fetch();
            $stmt2->close();

            if ($quantity < $product_quantity) {
                die("Lỗi: Sản phẩm có ID $product_id không đủ hàng trong kho.");
            }

            // 3.2 Cập nhật số lượng tồn kho
            $new_quantity = $quantity - $product_quantity;
            $stmt3 = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
            if (!$stmt3) {
                die("Lỗi chuẩn bị truy vấn SQL cập nhật số lượng sản phẩm trong kho: " . $conn->error);
            }

            $stmt3->bind_param('ii', $new_quantity, $product_id);
            if (!$stmt3->execute()) {
                die("Lỗi khi cập nhật số lượng sản phẩm trong kho: " . $stmt3->error);
            }

            // 3.3 Lưu vào bảng order_items
            $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, user_id, order_date, product_quantity) VALUES (?, ?, ?, ?, ?)");

            if (!$stmt1) {
                die("Lỗi chuẩn bị truy vấn SQL cho order_items: " . $conn->error);
            }

            $stmt1->bind_param('iiisi', $order_id, $product_id, $user_id, $order_date, $product_quantity);

            if (!$stmt1->execute()) {
                die("Lỗi khi thêm sản phẩm vào order_items: " . $stmt1->error);
            }
        }

        // 4. Xóa giỏ hàng
        unset($_SESSION['cart']);

        // 5. Chuyển hướng
        header("location:payment.php?order_status=Order successfully");
        exit();
    }
}
?>
