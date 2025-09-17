<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once('./config/db.php');

$section = isset($_GET['section']) ? $_GET['section'] : 'allPayments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- SwiperJS (if slider is used) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

  <!-- CKEditor -->
  <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>

  <!-- Custom Styles -->
  <style>
    body {
      background-color: #f8f9fa;
    }

    /* Sidebar shared styles */
.sidebar {
  background-color: #343a40 !important;
  min-height: 100vh;
  color: white;
}

/* Force white text color inside sidebar menu */
.sidebar .nav-link {
  color: #fff !important;
  font-weight: 500;
  padding: 10px 20px;
  transition: all 0.2s;
}

/* Hover and active styles */
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
  background-color: #495057;
  border-radius: 5px;
}

/* Offcanvas specific override for mobile */
.offcanvas.sidebar {
  background-color: #343a40 !important;
  color: #fff !important;
}

.offcanvas.sidebar .offcanvas-header {
  background-color: #343a40 !important;
  color: #fff !important;
}

.offcanvas.sidebar .btn-close {
  filter: invert(1); /* Make close button white */
}

    .main-content {
      padding: 20px;
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
      border-radius: 5px;
    }

    h2 {
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .main-content {
        padding: 10px;
      }

      .swiper-slide img {
        width: 100%;
        height: auto;
      }
    }
  </style>
</head>
<body>

<!-- Mobile Navbar -->
<nav class="navbar navbar-dark bg-dark d-md-none">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <span class="navbar-brand">Admin Panel</span>
  </div>
</nav>

<!-- Mobile Sidebar -->
<div class="offcanvas offcanvas-start sidebar text-white d-md-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <?php include('./dashboard-components/sidebar-menu.php'); ?>
  </div>
</div>

<!-- Main Layout -->
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar (Desktop) -->
    <nav class="col-md-3 d-none d-md-block sidebar">
      <?php include('./dashboard-components/sidebar-menu.php'); ?>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 main-content">
        <?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_GET['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

      <h1 style="font-weight: bold; text-align:center; font-size: 25px; background-color: black; color: white; padding:10px;">Admin Panel</h1>
      <?php if ($section === 'allPayments') { ?>
        <h2 class="mb-4"><i class="bi bi-currency-rupee me-2"></i>All Orders & Payments</h2>
        <?php include('./dashboard-components/all-payments.php'); ?>
      <?php } ?>
      <?php if ($section === 'secondList') { ?>
        <h2><i class="bi bi-journals me-2"></i>All Blogs</h2>
        <?php include('./dashboard-components/insert_blog.php'); ?>
      <?php } ?>
      <?php if ($section === 'thirdList') { ?>
        <h2><i class="bi bi-journals me-2"></i>All Blogs</h2>
        <?php include('./dashboard-components/all-blogs.php'); ?>
      <?php } ?>

      <?php if ($section === 'fourthList') { ?>
        <h2><i class="bi bi-plus-square me-2"></i>Create New Product</h2>
        <?php include('./dashboard-components/insert_product.php'); ?>
      <?php } ?>

      <?php if ($section === 'productCategories') { ?>
        <h2><i class="bi bi-tags me-2"></i>Manage Product Categories</h2>
        <?php include('./dashboard-components/manage-categories.php'); ?>
      <?php } ?>

      <?php if ($section === 'productBrands') { ?>
        <h2><i class="bi bi-briefcase me-2"></i>Manage Product Brands</h2>
        <?php include('./dashboard-components/manage-brands.php'); ?>
      <?php } ?>
      <?php if ($section === 'ContactLeads') { ?>
        <h2><i class="bi bi-briefcase me-2"></i>Contact Leads</h2>
        <?php include('./dashboard-components/All-contacts.php'); ?>
      <?php } ?>

      <?php if ($section === 'fifthList') { ?>
        <h2><i class="bi bi-card-list me-2"></i>All Products</h2>
        <?php include('./dashboard-components/all-products.php'); ?>
      <?php } ?>
    </main>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Swiper JS (optional if used in sub files) -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('.swiper-container')) {
      new Swiper(".swiper-container", {
        slidesPerView: 1,
        spaceBetween: 10,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
      });
    }
  });
</script>
</body>
</html>
