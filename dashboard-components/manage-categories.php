<?php
require_once('./config/db.php');

// Define the directory where images will be stored
$uploadDir = 'uploads/categories/'; // Make sure this directory exists and is writable

// Create the upload directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Function to handle image upload and return path
function handleImageUpload($fileInputName, $uploadDir, $existingImagePath = null) {
    $imagePath = $existingImagePath; // Default to existing path

    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileName = $_FILES[$fileInputName]['name'];
        $fileSize = $_FILES[$fileInputName]['size'];
        $fileType = $_FILES[$fileInputName]['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        $allowedFileExtensions = array('jpg', 'gif', 'png', 'jpeg','webp');

        if (in_array($fileExtension, $allowedFileExtensions)) {
            // Delete old image if a new one is being uploaded and an old one exists
            if ($existingImagePath && file_exists($existingImagePath)) {
                unlink($existingImagePath);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = $destPath;
            } else {
                echo "Error uploading the new file.";
                // You might want to handle this error more gracefully, e.g., via session message
            }
        } else {
            echo "Invalid file extension for new upload. Allowed: " . implode(', ', $allowedFileExtensions);
            // You might want to handle this error more gracefully
        }
    } elseif (isset($_POST['remove_existing_image']) && $_POST['remove_existing_image'] == '1' && $existingImagePath) {
        // Remove existing image if checkbox is checked
        if (file_exists($existingImagePath)) {
            unlink($existingImagePath);
        }
        $imagePath = null;
    }

    return $imagePath;
}


// Sanitize and Create Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    $imagePath = handleImageUpload('category_image', $uploadDir); // No existing image for new category

    if ($name !== '') {
        $stmt = $con->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $imagePath);
        $stmt->execute();
        $stmt->close();
    }
}

// Update Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $id = intval($_POST['category_id']);
    $name = trim($_POST['category_name']);

    // Get current image path for this category
    $currentImagePath = null;
    $stmt = $con->prepare("SELECT image_path FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($currentImagePath);
    $stmt->fetch();
    $stmt->close();

    // Handle image update (delete old if new uploaded, or if 'remove' checkbox is checked)
    $imagePath = handleImageUpload('category_image_update', $uploadDir, $currentImagePath);


    if ($name !== '') {
        $stmt = $con->prepare("UPDATE categories SET name = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $imagePath, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete Category and its associated image
if (isset($_GET['delete_category'])) {
    $id = intval($_GET['delete_category']);

    // First, get the image path from the database
    $stmt = $con->prepare("SELECT image_path FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imageToDelete);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file from the server if it exists
    if ($imageToDelete && file_exists($imageToDelete)) {
        unlink($imageToDelete);
    }

    // Now delete the category from the database
    $stmt = $con->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Categories
$categories = $con->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .edit-form-row {
            display: none; /* Hidden by default */
        }
        .table img {
            max-width: 70px; /* Slightly larger for better view */
            max-height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">

        <h4 class="mt-5">Add New Category</h4>
        <form method="POST" class="mb-5" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" name="category_name" class="form-control" placeholder="New Category Name" required>
                <input type="file" name="category_image" class="form-control">
                <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
            </div>
        </form>

        <h4 class="mt-5">Existing Categories</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cat = $categories->fetch_assoc()) { ?>
                    <tr id="category-row-<?= $cat['id'] ?>">
                        <td><?= htmlspecialchars($cat['id']) ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <?php if ($cat['image_path'] && file_exists($cat['image_path'])) { ?>
                                <img src="<?= htmlspecialchars($cat['image_path']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                            <?php } else { ?>
                                No Image
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm me-2" onclick="toggleEditForm(<?= $cat['id'] ?>)">Edit</button>
                            <a href="?section=productCategories&delete_category=<?= $cat['id'] ?>"
                               onclick="return confirm('Delete this category and its image?')"
                               class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                    <tr id="edit-form-row-<?= $cat['id'] ?>" class="edit-form-row">
                        <td colspan="4">
                            <div class="card card-body bg-light mb-3">
                                <h5 class="card-title">Edit Category ID: <?= htmlspecialchars($cat['id']) ?></h5>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="category_id" value="<?= htmlspecialchars($cat['id']) ?>">
                                    <div class="mb-3">
                                        <label for="edit_name_<?= $cat['id'] ?>" class="form-label">Category Name</label>
                                        <input type="text" name="category_name" id="edit_name_<?= $cat['id'] ?>" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_image_<?= $cat['id'] ?>" class="form-label">Category Image</label>
                                        <input type="file" name="category_image_update" id="edit_image_<?= $cat['id'] ?>" class="form-control mb-2">
                                        <?php if ($cat['image_path'] && file_exists($cat['image_path'])) { ?>
                                            <p>Current Image:</p>
                                            <img src="<?= htmlspecialchars($cat['image_path']) ?>" alt="Current Image" class="mb-2" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remove_existing_image" value="1" id="remove_image_<?= $cat['id'] ?>">
                                                <label class="form-check-label" for="remove_image_<?= $cat['id'] ?>">
                                                    Remove current image
                                                </label>
                                            </div>
                                        <?php } else { ?>
                                            <p>No current image.</p>
                                        <?php } ?>
                                    </div>
                                    <button type="submit" name="update_category" class="btn btn-success me-2">Update Category</button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEditForm(<?= $cat['id'] ?>)">Cancel</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleEditForm(categoryId) {
            const row = document.getElementById('category-row-' + categoryId);
            const editFormRow = document.getElementById('edit-form-row-' + categoryId);

            if (editFormRow.style.display === 'none' || editFormRow.style.display === '') {
                row.style.display = 'none'; // Hide the original row
                editFormRow.style.display = 'table-row'; // Show the edit form row
            } else {
                row.style.display = 'table-row'; // Show the original row
                editFormRow.style.display = 'none'; // Hide the edit form row
            }
        }
    </script>
</body>
</html>