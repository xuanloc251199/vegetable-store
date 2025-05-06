<?php
include('../server/connection.php');

// Kiểm tra nếu có tìm kiếm từ người dùng
if (isset($_GET['query_admin'])) {
    $query = $_GET['query_admin'];

    // Cập nhật truy vấn SQL để tìm kiếm trong bảng users
    $stmt = $conn->prepare('SELECT * FROM users WHERE user_name LIKE ? OR user_email LIKE ?');
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    header("Location: list_users.php");
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
                    <a href="list_users.php" class="btn btn-primary">Back</a>
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
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users->num_rows > 0) {
                                while ($user = $users->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo $user['user_name']; ?></td>
                                        <td><?php echo $user['user_email']; ?></td>
                                        <td>
                                            <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="text-primary w-4 h-4 mr-1">
                                                <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                                </svg>
                                            </a>
                                            <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" class="text-danger w-4 h-4 mr-1">
                                                <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center">No results found</td>
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