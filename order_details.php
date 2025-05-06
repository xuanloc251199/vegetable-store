<?php
session_start();
include('server/connection.php');

// Handle logout
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
        exit;
    }
}


// Fetch order details if order_id is set
$order_details = null;
if (isset($_POST['order_details']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();

    // Check for query execution errors
    if ($order_details === false) {
        echo "Error: " . $conn->error;
        exit;
    }
} else {
    echo "No order ID provided.";
    exit;
}


?>

<?php include('layouts/header.php') ?>
<!--Account page-->
<section class="my-5 py-5">
    <div class="row container mx-auto">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                <li class="breadcrumb-item"><a href="account.php">My Account</a></li>
                <li class="breadcrumb-item"><a href="my_orders.php">My Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">Your Order</li>
            </ol>
        </nav>
        <div class="info text-center col-md-6 col-lg-12 col-sm-12">

            <div class="account-profile">


                <div class="account-update col-lg-12 col-md-12"> <!-- Đã thay đổi col-lg-6 thành col-lg-12 để chiếm toàn bộ chiều rộng -->
                    <h3 class="text-uppercase">Your Orders</h3>

                    <div class="table-container">
                        <table class="orders mt-5">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Product Price</th>
                                    <th>Qty</th>
                                    <th>Order Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($order_details && $order_details->num_rows > 0) {
                                    while ($row = $order_details->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['order_id']; ?></td>
                                            <td>
                                                <div class="product-info">
                                                    <img class="img-fluid" src="./assets/images/<?php echo $row['product_image']; ?>" alt="Product Image">
                                                    <?php echo $row['product_name']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo number_format($row['product_price'], 0, '.', '.'); ?>
                                            </td>

                                            <td><?php echo $row['product_quantity']; ?></td>
                                            <td><?php echo $row['order_date']; ?></td>
                                        </tr>
                                <?php }
                                } else {
                                    echo "<tr><td colspan='6'>No orders found.</td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>