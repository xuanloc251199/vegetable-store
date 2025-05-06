<?php
session_start();
require_once 'server/connection.php'; // File kết nối cơ sở dữ liệu

// Kiểm tra nếu form checkout được gửi
if (isset($_POST['checkout'])) {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {

        // Thông tin khách hàng
        $customer_name = $_POST['customer_name'];
        $customer_email = $_POST['customer_email'];
        $customer_address = $_POST['customer_address'];

        // Kiểm tra xem các trường thông tin có đầy đủ không
        if (empty($customer_name) || empty($customer_email) || empty($customer_address)) {
            echo "<script>alert('Please fill in all the required fields.'); window.location.href='place_order.php';</script>";
            exit(); // Dừng thực hiện nếu thiếu thông tin
        }

        // Lưu thông tin khách hàng vào bảng `customers`
        $query = "INSERT INTO customers (name, email, address) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $customer_name, $customer_email, $customer_address);
        $stmt->execute();
        $customer_id = $stmt->insert_id;

        // Lưu thông tin đơn hàng vào bảng `orders`
        $order_date = date("Y-m-d H:i:s");
        $order_query = "INSERT INTO orders (customer_id, order_date, status) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("is", $customer_id, $order_date);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Lưu từng sản phẩm trong giỏ hàng vào bảng `order_details`
        // $detail_query = "INSERT INTO order_details (order_id, product_id, product_name,product_size_id, product_price, quantity) VALUES (?,?, ?, ?, ?, ?)";
        // $stmt = $conn->prepare($detail_query);
        // foreach ($_SESSION['cart'] as $item) {
        //     $stmt->bind_param("iisidi", $order_id, $item['product_id'], $item['product_name'],$item['product_size_id'], $item['product_price'], $item['product_quantity']);
        //     $stmt->execute();
        // }

        // Xóa giỏ hàng và chuyển hướng về trang cảm ơn hoặc trang chính
        unset($_SESSION['cart']);
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Your cart is empty!'); window.location.href='cart.php';</script>";
    }
}

function calculateTotalCart()
{
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            // Ưu tiên lấy giá khuyến mãi nếu có
            $raw_price = !empty($item['product_price_discount']) ? $item['product_price_discount'] : $item['product_price'];

            // Chuyển giá sang số (loại bỏ dấu chấm phân tách nghìn)
            $price = floatval(str_replace('.', '', $raw_price));
            $quantity = intval($item['product_quantity']);

            $total += $price * $quantity;
        }
        $_SESSION['total'] = $total;
    } else {
        $_SESSION['total'] = 0;
    }
}

calculateTotalCart();

include('layouts/header.php')

?>

<!--Checkout page-->

<section class="my-5 py-5">
    <div class="container mt-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Your Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Check Out</li>
            </ol>
        </nav>

    </div>
    <h2 class="text-uppercase text-center">Check out</h2>
    <hr class="mx-auto">
    <form action="place_order.php" method="POST"></form>
    <div class="container">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_GET['message'] ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <form id="check-out" method="POST" action="place_order.php">
                <div class="d-flex justify-content-between w-100">
                    <!-- Shipping Address -->
                    <div class="col-md-6">
                        <div class="sub-title">
                            <h3>Shipping Address</h3>
                        </div>

                        <div class="card-body shadow-lg border-0 checkout-form">
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="mb-3">

                                        <!-- <input type="text" class="form-control" id="name" name="name" placeholder="Name"> -->
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Name" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="address" id="address" class="form-control" placeholder="Address" required></textarea>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-md-5">
                        <div class="sub-title">
                            <h3>Order Summary</h3>
                        </div>

                        <div class="cart-summery shadow-lg border-0">
                            <?php foreach ($_SESSION['cart'] as $key => $value) {
                                // Dùng giá khuyến mãi nếu có
                                $raw_price = !empty($value['product_price_discount']) ? $value['product_price_discount'] : $value['product_price'];
                                // Chuyển giá sang số và định dạng
                                $price = floatval(str_replace('.', '', $raw_price));
                                $formatted_price = number_format($price, 0, '.', '.');
                            ?>
                                <div class="d-flex justify-content-between pb-2">
                                    <h6><?php echo $value['product_name'] ?> x <?php echo $value['product_quantity'] ?></h6>
                                    <h6><?php echo $formatted_price . ' VND'; ?></h6>
                                </div>
                            <?php } ?>
                            <div class="d-flex justify-content-between pb-2">
                                <h6>Shipping</h6>
                                <h6>0 VND</h6>
                            </div>
                            <div class="d-flex justify-content-between summery-end">
                                <h6>Total</h6>
                                <h6><strong><?php echo number_format($_SESSION['total'], 0, '.', '.') . ' VND'; ?></strong></h6>
                            </div>
                            <button class="btn btn-success" name="place_order">Checkout</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</section>



<?php include('layouts/footer.php') ?>