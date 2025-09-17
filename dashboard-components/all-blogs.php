<?php
// Fetch all blog posts from the database
$sql = "SELECT id, blogtitle, metatitle, metadescription, created_at, blog_image FROM blog"; // Fetch blog_image
$result = $con->query($sql);

if ($result->num_rows > 0) {
    ?>
    <section class="mt-4 mb-5">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Blog Image</th> <!-- Add a new column for the image -->
                        <th>Blog Name</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        // Check if the image exists in the database and encode it to Base64 if needed
                        $image_data = $row['blog_image'];
                        if (!empty($image_data)) {
                            $image_base64 = base64_encode($image_data);
                            $image_src = "data:image/jpeg;base64," . $image_base64; // Assuming the image is JPEG
                            $image_tag = "<img src='" . $image_src . "' alt='Blog Image' style='width: 100px; height: auto;'>";
                        } else {
                            $image_tag = "No Image"; // Or you can put a placeholder image
                        }

                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= $image_tag ?></td> <!-- Display image -->
                            <td><?= htmlspecialchars($row['blogtitle']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <a href="./dashboard-components/update_blog.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm w-100">Edit</a>
                                <a href="./dashboard-components/delete_blog.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm w-100 mt-2" onclick="return confirm('Are you sure you want to delete this blog?')">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php
} else {
    ?>
    <div class="container text-center mt-3">
        <p class="alert alert-warning">No blog posts found.</p>
    </div>
    <?php
}
?>
