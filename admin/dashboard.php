<?php

session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include('../admin/layouts/app.php');

// Kết nối cơ sở dữ liệu với mysqli
$conn = mysqli_connect('localhost', 'root', '', 'vegetable_store');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Truy vấn tổng số đơn hàng
$resultOrders = mysqli_query($conn, "SELECT COUNT(*) as total_orders FROM orders");
if (!$resultOrders) {
    die('Query Error: ' . mysqli_error($conn));
}
$totalOrders = mysqli_fetch_assoc($resultOrders)['total_orders'];

// Truy vấn tổng số khách hàng
$resultUsers = mysqli_query($conn, "SELECT COUNT(*) as total_customers FROM users");
if (!$resultUsers) {
    die('Query Error: ' . mysqli_error($conn));
}
$totalCustomers = mysqli_fetch_assoc($resultUsers)['total_customers'];

// Truy vấn tổng doanh thu, sử dụng cột `order_cost`
$resultSales = mysqli_query($conn, "SELECT SUM(order_cost) as total_sales FROM orders");
if (!$resultSales) {
    die('Query Error: ' . mysqli_error($conn));
}
$totalSales = mysqli_fetch_assoc($resultSales)['total_sales'];

// Truy vấn dữ liệu doanh thu hàng tháng
$resultMonthlySales = mysqli_query($conn, "
    SELECT MONTH(order_date) as month, SUM(order_cost) as sales 
    FROM orders 
    GROUP BY MONTH(order_date)
    ORDER BY MONTH(order_date)
");
if (!$resultMonthlySales) {
    die('Query Error: ' . mysqli_error($conn));
}

$months = [];
$salesData = [];
while ($row = mysqli_fetch_assoc($resultMonthlySales)) {
    $months[] = date('F', mktime(0, 0, 0, $row['month'], 10)); // Lấy tên tháng từ số tháng
    $salesData[] = $row['sales'];
}

mysqli_close($conn); // Đóng kết nối sau khi truy vấn xong
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6"></div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box card">
                        <div class="inner">
                            <h3><?php echo $totalOrders; ?></h3>
                            <p>Total Orders</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer text-dark">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box card">
                        <div class="inner">
                            <h3><?php echo $totalCustomers; ?></h3>
                            <p>Total Customers</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer text-dark">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box card">
                        <div class="inner">
                            <h3><?php echo number_format($totalSales ?? 0); ?> VND</h3>
                            <p>Total Sale</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="javascript:void(0);" class="small-box-footer">&nbsp;</a>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="myChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>
<?php include('../admin/layouts/sidebar.php') ?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu cho biểu đồ
    const data = {
        labels: <?php echo json_encode($months); ?>, // Nhãn tháng
        datasets: [{
            label: 'Total Sales',
            data: <?php echo json_encode($salesData); ?>, // Dữ liệu doanh thu
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Cấu hình biểu đồ cột
    const config = {
        type: 'bar', // Loại biểu đồ là bar (cột)
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true // Bắt đầu từ giá trị 0
                }
            }
        }
    };

    // Khởi tạo biểu đồ
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>