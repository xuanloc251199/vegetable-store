<?php

include('../server/connection.php');



$stmt = $conn->prepare('SELECT * FROM status_products');
$stmt->execute();
$status_product = $stmt->get_result();


// Truy vấn tạo số thứ tự
$sql = "SELECT 
            ROW_NUMBER() OVER (ORDER BY status_products_id ASC) AS stt,
            status_products_id,
            status_products_name
        FROM status_products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Khởi tạo số thứ tự
    $counter = 1;
}
?>

<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Status Products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="create_status_products.php" class="btn btn-primary">Create Status Products</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <?php if (isset($_GET['message'])): ?>
                        <div>
                            <p class="alert alert-success"><?= $_GET['message'] ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div>
                            <p class="alert alert-danger"><?= $_GET['error'] ?></p>
                        </div>
                    <?php endif; ?>
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Name</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status_product as $status_products) { ?>
                                <tr>
                                    <td><?php echo  $counter++ ?></td>
                                    <td><?php echo $status_products['status_products_name'] ?></td>
                                    <td>
                                        <a href="edit_status_products.php?status_products_id=<?php echo $status_products['status_products_id'] ?>">
                                            <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="delete_status_products.php?status_products_id=<?php echo $status_products['status_products_id'] ?>"
                                            class="text-danger w-4 h-4 mr-1">
                                            <svg wire:loading.remove.delay="" wire:target=""
                                                class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path ath fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>



<?php include('../admin/layouts/sidebar.php') ?>