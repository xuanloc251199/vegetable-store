<?php

include('../server/connection.php');

// Fetch category details if category_id is set in the GET request
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $conn->prepare('SELECT * FROM category WHERE category_id = ?');
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $category = $stmt->get_result();
}

// Handle category update if update_category is set in the POST request
else if (isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $category_id = $_POST['category_id'];  // Retrieve category_id from the hidden input field

    // Corrected the query: It should use category_id instead of product_id
    $stmt1 = $conn->prepare('UPDATE category SET category_name = ? WHERE category_id = ?');
    $stmt1->bind_param('si', $category_name, $category_id);  // Bind both category_name and category_id

    if ($stmt1->execute()) {
        header("location:list_categories.php?message=Category updated successfully");
    } else {
        header("location:list_categories.php?error=Error updating category");
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
                    <h1>Update Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_categories.php" class="btn btn-primary">Back</a> <!-- Corrected link -->
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="edit_category.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (isset($category)) {
                                foreach ($category as $categories) { ?>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name">Name</label>
                                            <input type="text" name="category_name" id="category_name" class="form-control"
                                                placeholder="Name" value="<?php echo $categories['category_name'] ?>">
                                            <!-- Hidden input to pass category_id -->
                                            <input type="hidden" name="category_id"
                                                value="<?php echo $categories['category_id']; ?>">

                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_category">Update</button>
                    <a href="list_categories.php" class="btn btn-primary">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>
<?php include('../admin/layouts/sidebar.php') ?>