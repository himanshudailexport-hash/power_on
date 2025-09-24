<?php
// insert_product.php

require_once('./config/db.php');

// Fetch categories and brands for dropdowns
$categories = [];
$brands = [];

$catResult = $con->query("SELECT id, name FROM categories ORDER BY name ASC");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

$brandResult = $con->query("SELECT id, name FROM brands ORDER BY name ASC");
while ($row = $brandResult->fetch_assoc()) {
    $brands[] = $row;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $con->real_escape_string($_POST['name'] ?? '');
    $description = $con->real_escape_string($_POST['description'] ?? '');
    $features = $con->real_escape_string($_POST['features'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount_price = floatval($_POST['discount_price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category = intval($_POST['category'] ?? 0);
    $brand = intval($_POST['brand'] ?? 0);
    $rating = floatval($_POST['rating'] ?? 0);
    $warranty = intval($_POST['warranty'] ?? 0);
    $rang = $con->real_escape_string($_POST['rang'] ?? '');
    $location = $con->real_escape_string($_POST['location'] ?? 'Anywhere in India');
    $tags = $con->real_escape_string($_POST['tags'] ?? '');

    $isTrendingCategory = intval($_POST['isTrendingCategory'] ?? 0);
    $isBestSeller = intval($_POST['isBestSeller'] ?? 0);
    $isNewArrival = intval($_POST['isNewArrival'] ?? 0);
    $isLimitedStock = intval($_POST['isLimitedStock'] ?? 0);

    // Directory to save images
    $uploadDir = __DIR__ . "/../uploads/products/"; // adjust if needed
    $imagePaths = [null, null, null];

    if (!empty($_FILES['images']['tmp_name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($i >= 3) break;
            if (is_uploaded_file($tmpName)) {
                $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                $filename = uniqid("prod_", true) . "." . strtolower($ext);
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imagePaths[$i] = "uploads/products/" . $filename; // save relative path in DB
                }
            }
        }
    }

    // Insert into database
    $stmt = $con->prepare("INSERT INTO products 
    (name, description, price, discount_price, stock, warranty, rang, features, location, category, brand, rating, 
    isTrendingCategory, isBestSeller, isNewArrival, isLimitedStock, tags, image1, image2, image3) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("❌ SQL Prepare failed: " . $con->error);
    }

    $stmt->bind_param(
        "ssddiisssiidiiiissss",
        $name,
        $description,
        $price,
        $discount_price,
        $stock,
        $warranty,
        $rang,
        $features,
        $location,
        $category,
        $brand,
        $rating,
        $isTrendingCategory,
        $isBestSeller,
        $isNewArrival,
        $isLimitedStock,
        $tags,
        $imagePaths[0],
        $imagePaths[1],
        $imagePaths[2]
    );


    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Product added successfully! Product ID: " . $stmt->insert_id . "</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Product Name:</label>
                        <input type='text' name='name' class='form-control'>
                    </div>
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Category:</label>
                        <select name='category' class='form-select'>
                            <option value=''>Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Brand:</label>
                        <select name='brand' class='form-select'>
                            <option value=''>Select Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= htmlspecialchars($brand['id']) ?>"><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Price:</label>
                        <input type='number' step='0.01' name='price' class='form-control'>
                    </div>
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Discount Price:</label>
                        <input type='number' step='0.01' name='discount_price' class='form-control'>
                    </div>
                    <div class='col-md-6 mb-3'>
                        <label class='form-label'>Stock:</label>
                        <input type='number' name='stock' class='form-control'>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rating:</label>
                        <input type="number" step="0.1" name="rating" class="form-control" min="0" max="5">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Warranty:</label>
                        <input type="number" step="0.1" name="warranty" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Range Available:</label>
                        <input type="text" step="0.1" name="rang" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location:</label>
                        <input type="text" step="0.1" name="location" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Features:</label>
                        <textarea name="features" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Product Tags (comma-separated):</label>
                        <input type="text" name="tags" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Upload Images (max 3):</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>

                    <!-- Category Flags -->
                    <div class='col-md-3 mb-3'>
                        <label class='form-label'>Is Trending?</label>
                        <select name='isTrendingCategory' class='form-select'>
                            <option value='0'>No</option>
                            <option value='1'>Yes</option>
                        </select>
                    </div>
                    <div class='col-md-3 mb-3'>
                        <label class='form-label'>Best Seller?</label>
                        <select name='isBestSeller' class='form-select'>
                            <option value='0'>No</option>
                            <option value='1'>Yes</option>
                        </select>
                    </div>
                    <div class='col-md-3 mb-3'>
                        <label class='form-label'>New Arrival?</label>
                        <select name='isNewArrival' class='form-select'>
                            <option value='0'>No</option>
                            <option value='1'>Yes</option>
                        </select>
                    </div>
                    <div class='col-md-3 mb-3'>
                        <label class='form-label'>Limited Stock?</label>
                        <select name='isLimitedStock' class='form-select'>
                            <option value='0'>No</option>
                            <option value='1'>Yes</option>
                        </select>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-lg">Add Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>