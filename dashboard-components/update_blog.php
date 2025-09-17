<?php  
require_once(__DIR__ . '/../config/db.php');


// Check if the blog ID is passed in the URL
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Fetch the current blog details from the database
    $sql = "SELECT * FROM blog WHERE id = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $blog_id); // Bind the blog_id parameter
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $blog = $result->fetch_assoc();
        } else {
            echo "<p>Blog not found!</p>";
            exit;
        }
    } else {
        echo "Error fetching data: " . $con->error;
        exit;
    }
} else {
    echo "No blog ID provided!";
    exit;
}

// Check if the form is submitted for updating the blog
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = $_POST['slug'];
    $blogtitle = $_POST['blogtitle'];
    $metatitle = $_POST['metatitle'];
    $metadescription = $_POST['metadescription'];
    $blogcontent = $_POST['blogcontent'];

    $blog_image = null;

    // If an image is uploaded, process it
    if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
        $blog_image = file_get_contents($_FILES['blog_image']['tmp_name']);
    }

    // Prepare the update SQL query
    if ($blog_image) {
        // If a new image is uploaded, update the image field
        $update_sql = "UPDATE blog SET slug = ?, blogtitle = ?, metatitle = ?, metadescription = ?, blogcontent = ?, blog_image = ? WHERE id = ?";
        if ($stmt = $con->prepare($update_sql)) {
            $stmt->bind_param("ssssssi", $slug, $blogtitle, $metatitle, $metadescription, $blogcontent, $blog_image, $blog_id);
        }
    } else {
        // If no new image is uploaded, keep the existing image
        $update_sql = "UPDATE blog SET slug = ?, blogtitle = ?, metatitle = ?, metadescription = ?, blogcontent = ? WHERE id = ?";
        if ($stmt = $con->prepare($update_sql)) {
            $stmt->bind_param("sssssi", $slug, $blogtitle, $metatitle, $metadescription, $blogcontent, $blog_id);
        }
    }

    // Execute the update query
    if (isset($stmt) && $stmt->execute()) {
         // Redirect to the blog list page after successful deletion
         header("Location: ../dashboard.php?section=thirdList");
         exit;
    } else {
        echo "Error updating blog: " . $stmt->error;
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Blog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Update Blog</h2>

        <!-- Update Blog Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="slug">Blog Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($blog['slug']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="blogtitle">Blog Title</label>
                    <input type="text" class="form-control" id="blogtitle" name="blogtitle" value="<?php echo htmlspecialchars($blog['blogtitle']); ?>" required>
                </div>
                <div class="col-12 mb-3">
                    <label for="metatitle">Meta Title</label>
                    <input type="text" class="form-control" id="metatitle" name="metatitle" value="<?php echo htmlspecialchars($blog['metatitle']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="metadescription">Meta Description</label>
                <textarea class="form-control" id="metadescription" name="metadescription" rows="4" required><?php echo htmlspecialchars($blog['metadescription']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="blog_image">Blog Image</label>
                <?php if ($blog['blog_image']): ?>
                    <br>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($blog['blog_image']); ?>" alt="Blog Image" style="width: 150px; height: auto;">
                    <br><br>
                <?php endif; ?>
                <input type="file" class="form-control-file" id="blog_image" name="blog_image">
            </div>

            <div class="form-group">
                <label>Content</label><br>
                <textarea name="blogcontent" rows="5" cols="80"><?php echo htmlspecialchars($blog['blogcontent']); ?></textarea>
                <script>CKEDITOR.replace('content');</script>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-success">Update Blog</button>
                <a href="../dashboard.php" class="btn btn-danger ml-3">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
