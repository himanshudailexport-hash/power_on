<?php
include('./config/db.php');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get image path (with fallback)
function getImagePath($path) {
    return (!empty($path)) ? $path : "assets/img/default-product.jpg";
}

// Fetch products by category ID
function fetchProductsByCategoryID($con, $categoryId) {
    $sql = "SELECT id, name, price, stock, brand, rating, image1, image2, image3
            FROM products
            WHERE category = ?
            ORDER BY id DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price'],
            "stock" => $row['stock'],
            "brand" => $row['brand'],
            "rating" => $row['rating'],
            "images" => [
                getImagePath($row['image1']),
                getImagePath($row['image2']),
                getImagePath($row['image3'])
            ]
        ];
    }

    $stmt->close();
    return $products;
}

// Target categories
$categoryNames = ["D-CUT NON WOVEN BAG", "W-CUT NON WOVEN BAG", "CAKE TOTE BAG"];
$categorySections = [];

// Get category IDs dynamically
$escapedNames = array_map(fn($name) => "'" . $con->real_escape_string($name) . "'", $categoryNames);
$inClause = implode(",", $escapedNames);

$result = $con->query("SELECT id, name FROM categories WHERE name IN ($inClause)");

while ($row = $result->fetch_assoc()) {
    $categorySections[$row['name']] = fetchProductsByCategoryID($con, $row['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Category Sections</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>
  <style>
    body { font-family: Arial; margin: 0; background: #f9f9f9; }
    .section-title {
        text-align: center;
        font-size: 22px;
        margin: 30px 0 10px;
        font-weight: bold;
    }
    .swiper-container {
        max-width: 1300px;
        margin: auto;
        padding: 20px 10px;
    }
    .swiper-slide {
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }
    .product-name { font-weight: bold; margin-top: 8px; font-size: 16px; }
    .product-price { color: #e74c3c; font-size: 15px; margin-top: 4px; }
    .add-to-cart {
        width: 90%;
        padding: 10px;
        background-color: #ff6600;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 6px;
        transition: background 0.3s;
    }
    .add-to-cart:hover {
        background-color: #cc5200;
    }
    @media (max-width: 768px) {
        .product-img { height: 150px; }
        .product-name { font-size: 14px; }
    }
    @media (max-width: 480px) {
        .product-img { height: 120px; }
        .product-name, .product-price, .add-to-cart { font-size: 12px; }
    }
  </style>
</head>
<body>

<?php foreach ($categorySections as $catName => $products): ?>
  <h2 class="section-title"><?= htmlspecialchars($catName) ?>s</h2>
  <div class="swiper-container <?= strtolower(str_replace(' ', '-', $catName)) ?>">
    <div class="swiper-wrapper">
      <?php foreach ($products as $product): ?>
        <div class="swiper-slide">
          <a href="product-detail.php?id=<?= $product['id'] ?>">
            <img src="../<?= $product['images'][0] ?>" 
                 class="product-img" 
                 alt="<?= htmlspecialchars($product['name']) ?>" />
          </a>
          <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
          <div class="product-price">₹<?= number_format($product['price'], 2) ?></div>
          <button class="add-to-cart"
            data-id="<?= $product['id'] ?>"
            data-name="<?= htmlspecialchars($product['name']) ?>"
            data-price="<?= $product['price'] ?>"
            data-image="../<?= $product['images'][0] ?>">Add to Cart</button>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
<?php foreach ($categorySections as $catName => $products): ?>
new Swiper('.<?= strtolower(str_replace(' ', '-', $catName)) ?>', {
    slidesPerView: 4,
    spaceBetween: 20,
    pagination: {
        el: '.<?= strtolower(str_replace(' ', '-', $catName)) ?> .swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.<?= strtolower(str_replace(' ', '-', $catName)) ?> .swiper-button-next',
        prevEl: '.<?= strtolower(str_replace(' ', '-', $catName)) ?> .swiper-button-prev',
    },
    breakpoints: {
        1024: { slidesPerView: 4 },
        768: { slidesPerView: 2 },
        480: { slidesPerView: 1 }
    }
});
<?php endforeach; ?>
</script>

</body>
</html>
