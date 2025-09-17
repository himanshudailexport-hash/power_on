<?php
require_once('./config/db.php');

// Create brand
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
    $name = trim($_POST['brand_name']);
    if ($name !== '') {
        mysqli_query($con, "INSERT INTO brands (name) VALUES ('$name')");
    }
}

// Delete brand
if (isset($_GET['delete_brand'])) {
    $id = intval($_GET['delete_brand']);
    mysqli_query($con, "DELETE FROM brands WHERE id=$id");
}

// Fetch brands
$brands = mysqli_query($con, "SELECT * FROM brands ORDER BY id DESC");
?>

<form method="POST" class="mb-3">
    <div class="input-group">
        <input type="text" name="brand_name" class="form-control" placeholder="New Brand Name" required>
        <button type="submit" name="add_brand" class="btn btn-success">Add Brand</button>
    </div>
</form>

<table class="table table-bordered">
    <thead><tr><th>#</th><th>Brand</th><th>Actions</th></tr></thead>
    <tbody>
        <?php while ($brand = mysqli_fetch_assoc($brands)) { ?>
            <tr>
                <td><?= $brand['id'] ?></td>
                <td><?= $brand['name'] ?></td>
                <td>
                    <a href="?section=productBrands&delete_brand=<?= $brand['id'] ?>" onclick="return confirm('Delete this brand?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
