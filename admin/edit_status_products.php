<?php

include('../server/connection.php');

// Fetch status_products details if status_products_id is set in the GET request
if (isset($_GET['status_products_id'])) {
    $status_products_id = $_GET['status_products_id'];
    $stmt = $conn->prepare('SELECT * FROM status_products WHERE status_products_id = ?');
    $stmt->bind_param('i', $status_products_id);
    $stmt->execute();
    $status_products = $stmt->get_result();
}

// Handle status_products update if update_status_products is set in the POST request
else if (isset($_POST['update_status_products'])) {
    $status_products_name = $_POST['status_products_name'];
    $status_products_id = $_POST['status_products_id'];  // Retrieve status_products_id from the hidden input field

    // Corrected the query: It should use status_products_id instead of product_id
    $stmt1 = $conn->prepare('UPDATE status_products SET status_products_name = ? WHERE status_products_id = ?');
    $stmt1->bind_param('si', $status_products_name, $status_products_id);  // Bind both status_products_name and status_products_id

    if ($stmt1->execute()) {
        header("location:list_status_products.php?message=Status_products updated successfully");
    } else {
        header("location:list_status_products.php?error=Error updating status_products");
    }
}

?>

<?php include('../admin/layouts/app.php') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Status_products</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_status_products.php" class="btn btn-primary">Back</a> <!-- Corrected link -->
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="edit_status_products.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (isset($status_products)) {
                                foreach ($status_products as $categories) { ?>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name">Name</label>
                                            <input type="text" name="status_products_name" id="status_products_name" class="form-control"
                                                placeholder="Name" value="<?php echo $categories['status_products_name'] ?>">
                                            <!-- Hidden input to pass status_products_id -->
                                            <input type="hidden" name="status_products_id"
                                                value="<?php echo $categories['status_products_id']; ?>">

                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_status_products">Update</button>
                    <a href="list_status_products.php" class="btn btn-primary">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>
<?php include('../admin/layouts/sidebar.php') ?>