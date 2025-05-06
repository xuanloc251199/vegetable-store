<?php
// Kết nối với cơ sở dữ liệu
include('../server/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_status_products'])) {
    // Lấy dữ liệu từ form
    $status_products_name = trim($_POST['status_products_name']);

    // Kiểm tra dữ liệu hợp lệ
    if (!empty($status_products_name)) {
        // Chuẩn bị và thực thi câu lệnh SQL
        $stmt = $conn->prepare('INSERT INTO status_products (status_products_name) VALUES (?)');
        $stmt->bind_param('s', $status_products_name);

        if ($stmt->execute()) {
            // Chuyển hướng kèm thông báo thành công
            header('Location: list_status_products.php?message=Status product added successfully');
        } else {
            // Chuyển hướng kèm thông báo lỗi
            header('Location: create_status_products.php?error=Error adding status product');
        }

        $stmt->close();
    } else {
        header('Location: create_status_products.php?error=Please enter a valid name');
    }
}

?>

<?php include('../admin/layouts/app.php'); ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Status Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_status_products.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="create_status_products.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="status_products_name">Name</label>
                            <input type="text" name="status_products_name" id="status_products_name" class="form-control" placeholder="Enter status product name" required>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="create_status_products">Create</button>
                    <a href="list_status_products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>