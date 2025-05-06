<?php
include('../server/connection.php');
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $users = $stmt->get_result();
} else if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];

    $stmt1 = $conn->prepare('UPDATE users SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?');
    $stmt1->bind_param('sssi', $user_name, $user_email, $user_password, $user_id);
    if ($stmt1->execute()) {
        header('location:list_users.php?message=User updated successfully');
    } else {
        header('location:list_users.php?error=Error updating user');
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
                    <h1>Update User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_users.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="edit_user.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <?php foreach ($users as $user) { ?>
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id'] ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="user_name" id="user_name" class="form-control"
                                            placeholder="Name" value="<?php echo $user['user_name'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" name="user_email" id="user_email" class="form-control"
                                            placeholder="Email" value="<?php echo $user['user_email'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password">Password</label>
                                        <input type="password" name="user_password" id="user_password" class="form-control"
                                            placeholder="Email" value="<?php echo $user['user_password'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_user">Update</button>
                    <a href="list_users.php" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
</div>
<?php include('../admin/layouts/sidebar.php');
