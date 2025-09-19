<?php
require_once(__DIR__ . '/../config/db.php');

$sql = "SELECT slug, blogtitle, blog_image, created_at FROM blog ORDER BY created_at DESC LIMIT 3";
$result = $con->query($sql);
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h6 class="text-primary text-uppercase">Our Blog</h6>
        <h1 class="display-5">Latest Articles</h1>
    </div>
    <div class="row g-4">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $slug = htmlspecialchars($row['slug']);
                $title = htmlspecialchars($row['blogtitle']);
                $image = 'data:image/jpeg;base64,' . base64_encode($row['blog_image']);
                $date = date('M d, Y', strtotime($row['created_at']));
                  $url = 'blog.php?slug=' . rawurlencode($slug);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= $image ?>" class="card-img-top rounded-top" alt="<?= $title ?>" loading="lazy" style="object-fit: cover; height: 220px;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $title ?></h5>
                            <p class="card-text text-muted mb-2"><?= $date ?></p>
                            <a href="blog.php?slug=<?= urlencode($slug) ?>" class="btn btn-outline-primary mt-auto">Read More</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center'>No blog posts found.</p>";
        }
        ?>
    </div>
</div>
