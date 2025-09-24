<?php
// category.php

require_once('./config/db.php');

// Get category ID from URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$category_name = '';
$products = [];

// Get category name
if ($category_id > 0) {
    $result = $con->query("SELECT name FROM categories WHERE id = $category_id");
    if ($result && $result->num_rows > 0) {
        $category_name = $result->fetch_assoc()['name'];
    } else {
        $category_name = "Unknown Category";
    }

    // Get products of this category
    $productQuery = "SELECT id, name, price, discount_price, image1 
                     FROM products 
                     WHERE category = $category_id 
                     ORDER BY id DESC";
    $productResult = $con->query($productQuery);
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $category_name = "Invalid Category";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($category_name) ?> - PowerOn Products</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php include('components/Header.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4"><?= htmlspecialchars($category_name) ?></h2>

    <?php if (count($products) > 0): ?>
        <div class="row">
            <?php foreach ($products as $product): 
                $imgPath = !empty($product['image1']) ? htmlspecialchars($product['image1']) : "assets/img/no-image.png";
            ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?= $imgPath ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body text-center">
                            <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                            <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary mb-2 w-100">View</a>
                            <button
                                class="btn btn-sm btn w-100 add-to-cart" style="background-color: #5a016d; color:white;"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-price="<?= $product['price'] ?>"
                                data-image="<?= $imgPath ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No products found in this category.</div>
    <?php endif; ?>
</div>

<?php include('components/Footer.php'); ?>

</body>
</html>
