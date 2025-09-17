<?php
require_once('config/db.php');

// Fetch all categories
$categories = [];
$catResult = $con->query("SELECT id, name FROM categories ORDER BY name ASC");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch all brands
$Brands = [];
$brandResult = $con->query("SELECT id, name FROM brands ORDER BY name ASC");
while ($row = $brandResult->fetch_assoc()) {
    $Brands[] = $row;
}

// Helper functions
function getCategoryNameById($id, $categories) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $id) return $cat['name'];
    }
    return 'Unknown Category';
}

function getBrandNameById($id, $Brands) {
    foreach ($Brands as $brand) {
        if ($brand['id'] == $id) return $brand['name'];
    }
    return 'Unknown Brand';
}

function getImagePath($path) {
    return (!empty($path)) ? $path : "assets/img/default-product.jpg";
}

// Validate product ID
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($product_id <= 0) die("Invalid Product ID.");

// Fetch product details
$stmt = $con->prepare("SELECT id, name, description, price, stock, category, brand, image1, image2, image3 FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$product = null;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $images = [];

    foreach (['image1', 'image2', 'image3'] as $imgField) {
        if (!empty($row[$imgField])) {
            $images[] = getImagePath($row[$imgField]);
        }
    }

    if (empty($images)) $images[] = 'assets/img/default-product.jpg';

    $product = [
        "id" => $row['id'],
        "name" => $row['name'],
        "description" => $row['description'],
        "price" => $row['price'],
        "stock" => $row['stock'],
        "category" => $row['category'],
        "brand" => $row['brand'],
        "images" => $images
    ];
} else {
    die("Product not found.");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($product['name']) ?> | Product Detail</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="https://digitalexportsmarketing.com" name="keywords">
    <meta content="https://digitalexportsmarketing.com" name="description">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        .thumbnail img {
            width: 70px;
            cursor: pointer;
            border: 2px solid transparent;
            object-fit: cover;
        }
        .thumbnail img:hover, .thumbnail img.active {
            border: 2px solid #007bff;
        }
        #main-product-image {
            max-height: 500px;
            object-fit: contain;
        }
    </style>
</head>
<body>

<?php include("components/Header.php"); ?>

<div class="container py-5">
  <div class="row g-4">
    <!-- Thumbnails -->
    <div class="col-md-2 d-none d-md-flex flex-column align-items-center thumbnail">
      <?php foreach ($product['images'] as $index => $image): ?>
        <div class="mb-2 <?= $index === 0 ? 'border border-primary' : '' ?>">
          <img 
            src="<?= htmlspecialchars($image) ?>" 
            onmouseover="changeMainImage('<?= htmlspecialchars($image, ENT_QUOTES) ?>')" 
            class="img-thumbnail <?= $index === 0 ? 'active' : '' ?>"
            width="80" height="80"
            alt="Thumbnail">
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Main Image -->
    <div class="col-md-6">
      <div class="border rounded p-2 bg-white shadow-sm">
        <img id="main-product-image" src="<?= htmlspecialchars($product['images'][0]) ?>" class="img-fluid w-100 rounded" alt="Main Product Image">
      </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-4">
      <h2 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h2>
      <p class="fs-4 text-danger fw-bold">₹<?= number_format($product['price'], 2) ?> <small class="text-muted">+ Shipping</small></p>

      <span class="badge bg-success mb-2">In Stock</span>
      <span class="ms-2">SKU: <strong>PRD<?= str_pad($product['id'], 4, '0', STR_PAD_LEFT) ?></strong></span>

      <table class="table table-sm mt-3 border">
        <tr><th>Category</th><td><?= htmlspecialchars(getCategoryNameById($product['category'], $categories)) ?></td></tr>
        <tr><th>Brand</th><td><?= htmlspecialchars(getBrandNameById($product['brand'], $Brands)) ?></td></tr>
        <tr><th>Stock</th><td><?= (int)$product['stock'] ?> units</td></tr>
      </table>

      <div class="my-3">
        <h5>Description</h5>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
      </div>

      <button id="add-to-cart-btn" class="btn btn-success btn-lg w-100">
        <i class="fas fa-cart-plus me-2"></i> Add to Cart
      </button>
    </div>
  </div>
</div>

<?php include("components/Footer.php"); ?>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
function changeMainImage(src) {
    const mainImg = document.getElementById("main-product-image");
    mainImg.src = src;

    document.querySelectorAll('.thumbnail img').forEach(img => {
        img.classList.remove('active');
        if (img.src === mainImg.src) img.classList.add('active');
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    addToCartBtn.addEventListener("click", function () {
        const product = {
            id: <?= (int)$product['id'] ?>,
            name: "<?= addslashes(htmlspecialchars($product['name'])) ?>",
            price: <?= (float)$product['price'] ?>,
            image: "<?= addslashes(htmlspecialchars($product['images'][0])) ?>",
            quantity: 1
        };

        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const existing = cart.findIndex(item => item.id === product.id);
        if (existing !== -1) {
            cart[existing].quantity += 1;
        } else {
            cart.push(product);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        window.location.href = "checkout.php";
    });
});
</script>

</body>
</html>
