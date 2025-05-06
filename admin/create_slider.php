<?php
include('../server/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slider_name = $_POST['slider_name'];

    // Handle the uploaded file
    $slider_image = $_FILES['slider_image']['name'];
    $slider_image_tmp = $_FILES['slider_image']['tmp_name'];
    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($slider_image);

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($slider_image_tmp, $target_file)) {
        // Insert slider info into the database
        $sql = "INSERT INTO slider (slider_name, slider_image) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $slider_name, $slider_image);
        if ($stmt->execute()) {
            header("Location: list_slider.php?message=Slider created successfully");
        } else {
            header("Location: create_slider.php?error=Failed to create slider");
        }
    } else {
        header("Location: create_slider.php?error=Failed to upload image");
    }
}
?>
<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Slider</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_slider.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="create_slider.php" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Slider Name -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="slider_name">Slider Name</label>
                                    <input type="text" name="slider_name" id="slider_name" class="form-control" placeholder="Name" required>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Image -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Upload Slider Image</h2>
                                <input type="file" name="slider_image" id="slider_image" class="form-control" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pb-5 pt-3">
                            <button class="btn btn-primary" name="create_slider">Create</button>
                            <a href="list_slider.php" class="btn btn-primary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>