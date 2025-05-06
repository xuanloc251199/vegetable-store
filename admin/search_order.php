<?php
include('../server/connection.php');

if (isset($_GET['query_admin'])) {
    $query = $_GET['query_admin'];
    $stmt = $conn->prepare('
        SELECT orders.*, users.user_name, users.user_email 
        FROM orders 
        INNER JOIN users ON orders.user_id = users.user_id 
        WHERE users.user_name LIKE ? OR users.user_email LIKE ?
    ');
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    header("Location: list_order.php");
    exit();
}
?>

<?php include('../admin/layouts/app.php'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_orders.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Order Cost</th>
                                <th>Order Status</th>
                                <th>Order Date</th>
                                <th>Order Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0) {
                                while ($order = $orders->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo $order['user_name']; ?></td>
                                        <td><?php echo $order['user_email']; ?></td>
                                        <td><?php echo $order['order_cost']; ?></td>
                                        <td>
                                            <?php
                                            $status = $order['order_status'];
                                            $statusClass = 'bg-danger';

                                            if ($status === 'shipped') {
                                                $statusClass = 'bg-warning';
                                            } elseif ($status === 'delivered') {
                                                $statusClass = 'bg-success';
                                            } elseif ($status === 'cancelled') {
                                                $statusClass = 'bg-primary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?> p-2 text-uppercase">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $order['order_date']; ?></td>
                                        <td>
                                            <form action="../admin/order_details.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <input type="submit" name="order_details"
                                                    style="background-color: coral; color: aliceblue; border-radius: 8px; padding: 8px 16px; border: none; cursor: pointer;"
                                                    value="Details">
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center">No results found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>