<?php
include('../config/db.php');
session_start();

// Admin authentication check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if product ID is passed in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare the SQL query to delete the product
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $delete_stmt = $con->prepare($delete_sql);
    $delete_stmt->bind_param("i", $product_id);

    // Execute the query
    if ($delete_stmt->execute()) {
        // Redirect to the dashboard with the desired section
        header("Location: /dashboard.php?section=fifthList&message=Product deleted successfully");
        exit();
    } else {
        // Error message if deletion fails
        echo "❌ Error deleting product: " . $con->error;
    }
} else {
    // Error message if product ID is missing
    echo "❌ Product ID is missing.";
    exit();
}


?>
