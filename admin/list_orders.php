<?php
session_start();
// Kết nối đến cơ sở dữ liệu
include('../server/connection.php');
// Biến để lưu thông báo sau khi xoá

// Kiểm tra nếu người dùng gửi yêu cầu POST để xoá tất cả đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xoá tất cả các bản ghi trong bảng orders
    $stmt = $conn->prepare('DELETE FROM orders');
    $stmt->execute();

    // Kiểm tra kết quả và gửi thông báo
    if ($stmt->affected_rows > 0) {
        // Nếu thành công, chuyển hướng về trang với thông báo thành công
        header("Location: list_orders.php?message=All orders have been deleted.");
    } else {
        // Nếu không có đơn hàng nào để xoá
        header("Location: delete_orders.php?message=No orders found to delete.");
    }
    exit;
}

// Xác định số bản ghi trên mỗi trang
$limit = 12;

// Lấy số trang hiện tại từ URL, nếu không có thì mặc định là 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán vị trí bắt đầu của bản ghi trong câu truy vấn
$offset = ($page - 1) * $limit;

// Truy vấn lấy đơn hàng kết hợp với thông tin người dùng, có phân trang
$stmt = $conn->prepare('
    SELECT orders.*, users.user_name, users.user_email 
    FROM orders 
    INNER JOIN users ON orders.user_id = users.user_id
    ORDER BY orders.order_id DESC
    LIMIT ?, ?
');
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$orders = $stmt->get_result();

// Truy vấn lấy tổng số đơn hàng để tính toán số trang
$stmt_total = $conn->prepare('SELECT COUNT(*) AS total FROM orders');
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_orders = $total_row['total'];

// Tính số trang
$total_pages = ceil($total_orders / $limit);
?>




<?php include('../admin/layouts/app.php') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <form action="list_orders.php" method="POST" onsubmit="return confirm('Are you sure you want to delete all orders? This action cannot be undone.');">
                        <button type="submit" class="btn btn-danger">Delete All Orders</button>
                    </form>

                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <form class="d-flex" action="../admin/search_order.php" method="GET">
                            <input class="form-control me-2" type="search" name="query_admin" placeholder="Search Products" aria-label="Search" required>
                            <button class="btn btn-outline-dark" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_GET['message'] ?>
                        </div>
                    <?php endif; ?>
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>SĐT</th>
                                <th>Cost đơn hàng</th>
                                <th>Trạng thái</th>
                                <th>Ngày oder</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = $total_orders - (($page - 1) * $limit);
                            foreach ($orders as $order) { ?>
                                <tr>
                                    <td><?php echo  $stt--; ?></td>
                                    <td><?php echo $order['user_name'] ?></td>
                                    <td><?php echo $order['user_phone'] ?></td>
                                    <td><?php echo number_format($order['order_cost'], 0, '.', '.'); ?></td>

                                    <td>

                                        <?php
                                        $status = $order['order_status'];
                                        // Xác định màu nền dựa trên trạng thái đơn hàng
                                        $statusClass = 'bg-danger'; // Mặc định là màu đỏ cho "pending"

                                        if ($status === 'shipped') {
                                            $statusClass = 'bg-warning'; // Màu cam cho "shipped"
                                        } elseif ($status === 'delivered') {
                                            $statusClass = 'bg-success'; // Màu xanh cho "delivered"
                                        } elseif ($order['order_status'] === 'cancelled') {
                                            $statusClass = 'bg-primary'; // Màu xanh dương cho "cancelled"
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?> p-2 text-uppercase">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                        <br>
                                    </td>
                                    <td><?php echo $order['order_date'] ?></td>
                                    <td>
                                        <form action="../admin/order_details.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <input type="submit" name="order_details"
                                                style="background-color: coral; color: aliceblue; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;"
                                                value="Details">
                                        </form>


                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <ul class="pagination pagination m-0 float-right">
                        <!-- Previous Page Link -->
                        <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">«</a>
                        </li>

                        <!-- Page Number Links -->
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>

                        <!-- Next Page Link -->
                        <li class="page-item <?php echo ($page == $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">»</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>