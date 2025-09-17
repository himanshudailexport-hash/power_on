<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../config/db.php');

$uploadDir = __DIR__ . "/../uploads/products/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Fetch categories and brands
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

$product = null;
$product_id = 0;

// Product fetch function
function fetchProduct($con, $product_id) {
    $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}

// GET method => initial load
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $product = fetchProduct($con, $product_id);
    if (!$product) {
        echo "<div class='alert alert-danger'>Product not found!</div>";
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<div class='alert alert-danger'>No product ID provided for update!</div>";
    exit();
}

// POST method => update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id'] ?? 0);

    // Re-fetch old product to get old image paths
    $product = fetchProduct($con, $product_id);
    if (!$product) {
        echo "<div class='alert alert-danger'>Invalid Product!</div>";
        exit();
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $discount_price = floatval($_POST['discount_price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category = intval($_POST['category'] ?? 0);
    $brand = intval($_POST['brand'] ?? 0);
    $rating = floatval($_POST['rating'] ?? 0);
    $tags = trim($_POST['tags'] ?? '');

    $isTrendingCategory = intval($_POST['isTrendingCategory'] ?? 0);
    $isBestSeller = intval($_POST['isBestSeller'] ?? 0);
    $isNewArrival = intval($_POST['isNewArrival'] ?? 0);
    $isLimitedStock = intval($_POST['isLimitedStock'] ?? 0);

    // Old images default
    $images = [
        $product['image1'] ?? null,
        $product['image2'] ?? null,
        $product['image3'] ?? null
    ];

    // New uploads overwrite old ones
    if (!empty($_FILES['images']['tmp_name'])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($i >= 3) break;
            if (is_uploaded_file($tmpName)) {
                $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                $newFileName = uniqid("prod_") . "." . $ext;
                $destination = $uploadDir . $newFileName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $images[$i] = "uploads/products/" . $newFileName;
                }
            }
        }
    }

    // UPDATE query
    $stmt = $con->prepare("UPDATE products SET
        name = ?,
        description = ?,
        price = ?,
        discount_price = ?,
        stock = ?,
        category = ?,
        brand = ?,
        rating = ?,
        isTrendingCategory = ?,
        isBestSeller = ?,
        isNewArrival = ?,
        isLimitedStock = ?,
        tags = ?,
        image1 = ?,
        image2 = ?,
        image3 = ?
        WHERE id = ?");

$stmt->bind_param(
    "ssddiiidiiiissssi",
    $name,               // s
    $description,        // s
    $price,              // d
    $discount_price,     // d
    $stock,              // i
    $category,           // i
    $brand,              // i
    $rating,             // d
    $isTrendingCategory, // i
    $isBestSeller,       // i
    $isNewArrival,       // i
    $isLimitedStock,     // i
    $tags,               // s
    $images[0],          // s
    $images[1],          // s
    $images[2],          // s
    $product_id          // i
);


    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Product updated successfully!</div>";
        $product = fetchProduct($con, $product_id);
    } else {
        echo "<div class='alert alert-danger'>❌ Error updating product: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Update Product (ID: <?= htmlspecialchars($product_id) ?>)</h2>
    <hr>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Category:</label>
                <select name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($product['category'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Brand:</label>
                <select name="brand" class="form-select" required>
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>" <?= ($product['brand'] == $brand['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?? 0 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Discount Price:</label>
                <input type="number" step="0.01" name="discount_price" class="form-control" value="<?= $product['discount_price'] ?? 0 ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Stock:</label>
                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? 0 ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Rating:</label>
                <input type="number" step="0.1" name="rating" class="form-control" min="0" max="5" value="<?= $product['rating'] ?? 0 ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Tags:</label>
                <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($product['tags'] ?? '') ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Description:</label>
                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Upload Images (max 3):</label>
                <input type="file" name="images[]" class="form-control" multiple>
                <?php
                for ($i = 1; $i <= 3; $i++) {
                    $imgCol = 'image' . $i;
                    if (!empty($product[$imgCol])) {
                        echo '<div class="mt-2">Current Image ' . $i . ': <img src="../' . htmlspecialchars($product[$imgCol]) . '" width="100"></div>';
                    }
                }
                ?>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Is Trending?</label>
                <select name="isTrendingCategory" class="form-select">
                    <option value="0" <?= ($product['isTrendingCategory'] == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($product['isTrendingCategory'] == 1) ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Best Seller?</label>
                <select name="isBestSeller" class="form-select">
                    <option value="0" <?= ($product['isBestSeller'] == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($product['isBestSeller'] == 1) ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">New Arrival?</label>
                <select name="isNewArrival" class="form-select">
                    <option value="0" <?= ($product['isNewArrival'] == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($product['isNewArrival'] == 1) ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Limited Stock?</label>
                <select name="isLimitedStock" class="form-select">
                    <option value="0" <?= ($product['isLimitedStock'] == 0) ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($product['isLimitedStock'] == 1) ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary px-4 py-2 shadow-lg">Update Product</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
