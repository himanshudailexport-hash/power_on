<?php
// Display success message if set
if (isset($_GET['message'])) {
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
}

// Fetch all product records
$sql = "SELECT * FROM products";
$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}

// Fetch categories
$categories = [];
$catResult = $con->query("SELECT id, name FROM categories ORDER BY name ASC");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch brands
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
?>

<section class="mt-4 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <span class="badge bg-light text-dark fs-6">Total: <?= $result->num_rows ?> products</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Discount</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Rating</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): 
                            $id = (int)$row['id'];
                            $name = htmlspecialchars($row['name']);
                            $desc = htmlspecialchars(substr($row['description'], 0, 50)) . '...';
                            $price = number_format($row['price'], 2);
                            $discount = number_format($row['discount_price'], 2);
                            $stock = (int)$row['stock'];
                            $rating = (float)$row['rating'];
                            $categoryName = getCategoryNameById($row['category'], $categories);
                            $brandName = getBrandNameById($row['brand'], $Brands);

                            // Product Image (fallback if empty)
                            $image = !empty($row['image1']) ? "../" . $row['image1'] : "https://via.placeholder.com/80x80?text=No+Image";

                            // Stock status
                            $stockClass = $stock < 10 ? 'text-danger' : ($stock < 30 ? 'text-warning' : 'text-success');
                            $stockIcon = $stock < 10 ? 'fa-exclamation-circle' : ($stock < 30 ? 'fa-info-circle' : 'fa-check-circle');

                            // Status Badges
                            $badges = [];
                            if ($row['isTrendingCategory']) $badges[] = '<span class="badge bg-danger text-white me-1"><i class="fas fa-bolt me-1"></i>Trending</span>';
                            if ($row['isBestSeller']) $badges[] = '<span class="badge bg-warning text-dark me-1"><i class="fas fa-trophy me-1"></i>Best Seller</span>';
                            if ($row['isNewArrival']) $badges[] = '<span class="badge bg-info text-white me-1"><i class="fas fa-certificate me-1"></i>New</span>';
                            if ($row['isLimitedStock']) $badges[] = '<span class="badge bg-secondary text-white me-1"><i class="fas fa-hourglass-half me-1"></i>Limited</span>';
                            $statusBadges = implode('', $badges);
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $id ?></td>
                            <td>
                                <img src="<?= $image ?>" alt="Product Image" width="80" height="80" class="rounded border object-fit-cover">
                            </td>
                            <td><?= $name ?></td>
                            <td class="text-muted"><?= $desc ?></td>
                            <td class="text-end fw-semibold text-dark">₹<?= $price ?></td>
                            <td class="text-end text-danger fw-semibold">₹<?= $discount ?></td>
                            <td class="text-center">
                                <span class="<?= $stockClass ?>"><i class="fas <?= $stockIcon ?> me-1"></i><?= $stock ?></span>
                            </td>
                            <td class="text-center"><?= $rating ?></td>
                            <td><span class="badge bg-light text-dark"><?= $categoryName ?></span></td>
                            <td><span class="badge bg-light text-dark"><?= $brandName ?></span></td>
                            <td><?= $statusBadges ?></td>
                            <td class="text-center">
                                <a href="dashboard-components/update_product.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="dashboard-components/delete-product.php?id=<?= $id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing <?= $result->num_rows ?> products</small>
            <a href="dashboard.php?section=fourthList" class="btn btn-success btn-sm">
                <i class="fas fa-plus me-1"></i> Add Product
            </a>
        </div>
    </div>
</section>

<?php if ($result->num_rows === 0): ?>
<div class="container text-center mt-5">
    <div class="card shadow-sm">
        <div class="card-body py-5">
            <i class="fas fa-box-open text-primary fa-3x mb-3"></i>
            <h4 class="mb-2 text-dark">No Products Found</h4>
            <p class="text-muted">You haven't added any products yet. Start by adding one.</p>
            <a href="dashboard.php?section=fourthList" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
