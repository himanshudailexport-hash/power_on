<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login");
    exit;
}

require_once(__DIR__ . '/../config/db.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $blog_id = intval($_GET['id']);

    $sql = "DELETE FROM blog WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $blog_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: ../dashboard?section=thirdList&msg=Blog+deleted+successfully");
        } else {
            header("Location: ../dashboard?section=thirdList&msg=No+blog+found+with+this+ID");
        }
        exit;
    } else {
        echo "❌ Delete failed: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "⚠️ Invalid Blog ID.";
}
