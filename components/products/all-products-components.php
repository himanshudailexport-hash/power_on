<?php
include('./config/db.php');

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// ✅ Fetch all products (images stored as file paths)
function fetchAllProducts($con) {
    $sql = "SELECT id, name, price, image1 FROM products ORDER BY id DESC";
    $result = $con->query($sql);

    if (!$result) {
        die("Query failed: " . $con->error);
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price'],
            "pimage" => !empty($row['image1']) ? $row['image1'] : "assets/img/default-product.jpg"
        ];
    }
    return $products;
}

$allProducts = fetchAllProducts($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .section-title {
            text-align: center;
            font-size: 24px;
            margin: 30px 0 20px;
            font-weight: bold;
        }

        .product-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 30px;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

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
            background-color: #ff6600;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .add-to-cart:hover {
            background-color: #cc5200;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="section-title">All Products</h2>
    <div class="row">
        <?php if (!empty($allProducts)): ?>
            <?php foreach ($allProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card text-center">
                        <a href="product-detail.php?id=<?= $product['id']; ?>">
                            <img src="<?= htmlspecialchars($product['pimage']); ?>" 
                                 class="product-img" 
                                 alt="<?= htmlspecialchars($product['name']); ?>">
                        </a>
                        <div class="product-name"><?= htmlspecialchars($product['name']); ?></div>
                        <button class="add-to-cart"
                            data-id="<?= $product['id']; ?>"
                            data-name="<?= htmlspecialchars($product['name']); ?>"
                            data-price="<?= $product['price']; ?>"
                            data-image="<?= htmlspecialchars($product['pimage']); ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <h4>No products available</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
