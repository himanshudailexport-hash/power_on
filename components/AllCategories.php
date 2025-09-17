<?php
require_once('./config/db.php');

// Fetch all categories
$sql = "SELECT id, name, image_path FROM categories ORDER BY id DESC";
$result = $con->query($sql);
?>

<div class="container py-5">
    <h2 class="text-center mb-4">All Categories</h2>
    <div class="row">
        <?php while ($cat = $result->fetch_assoc()) { ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <a href="category.php?category_id=<?= $cat['id'] ?>" class="text-decoration-none">
                    <div class="card h-100 text-center shadow-sm">
                        <?php if (!empty($cat['image_path']) && file_exists($cat['image_path'])): ?>
                            <img src="<?= htmlspecialchars($cat['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($cat['name']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <img src="assets/img/default-category.jpg" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title text-dark"><?= htmlspecialchars($cat['name']) ?></h6>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
