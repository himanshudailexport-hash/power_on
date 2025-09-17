<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slug = $_POST['slug'] ?? '';
    $metatitle = $_POST['metatitle'] ?? '';
    $metadescription = $_POST['metadescription'] ?? '';
    $blogtitle = $_POST['blogtitle'] ?? '';
    $blogcontent = $_POST['blogcontent'] ?? '';

    // Check for duplicate slug
    $check = $con->prepare("SELECT id FROM blog WHERE slug = ?");
    $check->bind_param("s", $slug);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo '<div class="alert alert-danger">Slug already exists. Please use a different one.</div>';
    } else {
        // Proceed with insert
        $blog_image = null;
        if (!empty($_FILES['blog_image']['tmp_name']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
            $blog_image = file_get_contents($_FILES['blog_image']['tmp_name']);
        }

        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;

        $sql = "INSERT INTO blog (slug, metatitle, metadescription, blogtitle, blogcontent, blog_image, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("ssssssss", $slug, $metatitle, $metadescription, $blogtitle, $blogcontent, $blog_image, $created_at, $updated_at);

            if ($stmt->execute()) {
                echo '<div class="alert alert-success">Blog added successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Error executing query: ' . $stmt->error . '</div>';
            }

            $stmt->close();
        } else {
            echo '<div class="alert alert-danger">Error preparing query: ' . $con->error . '</div>';
        }
    }

    $check->close();
}

?>

<!-- Blog Creation Form -->
<div class="card p-4">
  <h2><i class="bi bi-journal-plus me-2"></i>Create Blog Post</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="text" class="form-control mb-2" name="slug" placeholder="Blog URL Slug" required>
    <input type="text" class="form-control mb-2" name="metatitle" placeholder="Meta Title" required>
    <input type="text" class="form-control mb-2" name="metadescription" placeholder="Meta Description" required>
    <input type="text" class="form-control mb-2" name="blogtitle" placeholder="Blog Title" required>
    <input type="file" class="form-control mb-2" name="blog_image" accept="image/*" required>
    
    <label class="mb-1">Content</label>
    <textarea name="blogcontent" rows="5" class="form-control"></textarea>
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>CKEDITOR.replace('blogcontent');</script>
    
    <button type="submit" class="btn btn-primary mt-3 w-100">Submit Blog</button>
  </form>
</div>
