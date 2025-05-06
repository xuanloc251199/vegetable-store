<?php
include('../server/connection.php');


// Xác định limit và offset mặc định
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Số bản ghi trên một trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Trang hiện tại
$offset = ($page - 1) * $limit; // Tính offset

// Truy vấn dữ liệu từ bảng slider
$sql = "
    SELECT 
        slider_id,
        slider_name,
        slider_image
    FROM slider
    ORDER BY slider_id ASC
    LIMIT ?, ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include('../admin/layouts/app.php'); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Slider</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="create_slider.php" class="btn btn-primary">New Slider</a>
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
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_GET['message']; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_GET['error']; ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="60">ID</th>

                                <th width="500">Slider Image</th>
                                <th width="10">Slider Name</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0):
                                $stt = $offset + 1; // Tính STT bắt đầu từ số thứ tự tương ứng trang
                                while ($row = $result->fetch_assoc()):
                            ?>

                                    <tr>
                                        <td><?php echo $stt++; ?></td>

                                        <td>
                                            <img src="../assets/images/<?php echo $row['slider_image']; ?>"
                                                alt="Slider Image"
                                                style="width: 400px; height: 150px; object-fit: cover; border-radius: 8px;">
                                        </td>
                                        <td><?php echo $row['slider_name']; ?></td>
                                        <td>
                                            <a href="edit_slider.php?slider_id=<?php echo $row['slider_id'] ?>">
                                                <svg class="filament-link-icon w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="delete_slider.php?slider_id=<?php echo $row['slider_id'] ?>"
                                                class="text-danger w-6 h-6 mr-1">
                                                <svg wire:loading.remove.delay="" wire:target=""
                                                    class="filament-link-icon w-6 h-6 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No Slider Found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>