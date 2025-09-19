<?php
// components/CategoryProducts.php

require_once('./config/db.php');

// ✅ Fetch all categories
$categories = $con->query("SELECT id, name FROM categories ORDER BY name ASC");

while ($cat = $categories->fetch_assoc()) {
    $categoryId = $cat['id'];
    $categoryName = $cat['name'];

    // ✅ Fetch products by category
    $products = $con->query("SELECT id, name, price, discount_price, image1 
                             FROM products 
                             WHERE category = $categoryId 
                             ORDER BY id DESC 
                             LIMIT 8");

    if ($products && $products->num_rows > 0): ?>
        <div class="container mt-5">
            <h3 class="mb-4"><?= htmlspecialchars($categoryName) ?></h3>
            <div class="row">
                <?php while ($product = $products->fetch_assoc()):
                    // ✅ Use image path or fallback
                    $img = !empty($product['image1']) ? $product['image1'] : "assets/img/default-product.jpg";
                ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="product-card text-center">
                            <a href="product-detail.php?id=<?= $product['id'] ?>">
                                <img src="<?= htmlspecialchars($img) ?>" 
                                     class="product-img" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     loading="lazy">
                            </a>
                            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <button class="add-to-cart"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-price="<?= $product['price'] ?>"
                                data-image="<?= htmlspecialchars($img) ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif;
}
?>

<style>
.product-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    transition: transform 0.2s;
}
.product-card:hover { transform: translateY(-5px); }
.product-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
.product-name {
    font-size: 18px;
    font-weight: bold;
    margin-top: 10px;
}
.product-price {
    font-size: 16px;
    color: #e74c3c;
}
.add-to-cart {
    width: 100%;
    padding: 10px;
    background-color: rgb(90, 1, 109);
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
    margin-top: 10px;
}
.add-to-cart:hover {
     background-color: rgba(156, 42, 182, 1);

      }
</style>
