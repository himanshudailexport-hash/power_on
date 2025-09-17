<?php
// 2) Fetch all payments
$sql    = "SELECT * FROM `payments` ORDER BY `id` DESC";
$result = $con->query($sql);

// 3) Handle query error
if ($result === false) {
    die("Database query failed: " . $con->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Orders & Payments</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f9f9f9; }
    .order-card { background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px; }
    .order-header { display: flex; justify-content: space-between; align-items: center; }
    .order-details p { margin: 0; }
    .table th, .table td { vertical-align: middle; }
    .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
  </style>
</head>
<body>
  <div class="container py-5">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          // Decode cart JSON
          $cart = json_decode($row['cart_data'], true);
        ?>
        <div class="order-card">
          <div class="order-header mb-2">
            <h5>
              Payment ID:
              <span class="text-primary"><?= htmlspecialchars($row['payment_id']) ?></span>
            </h5>
            <small>
              <i class="bi bi-calendar-event"></i>
              <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?>
            </small>
          </div>

          <div class="row">
            <div class="col-md-6 order-details">
              <p><strong>Name:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></p>
              <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
              <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
              <p>
                <strong>Address:</strong>
                <?= htmlspecialchars($row['address']) ?>
                <?php if ($row['landmark']): ?>, <?= htmlspecialchars($row['landmark']) ?><?php endif; ?>
                , <?= htmlspecialchars($row['city']) ?>, <?= htmlspecialchars($row['state']) ?>
                - <?= htmlspecialchars($row['pincode']) ?>
              </p>
            </div>

            <div class="col-md-6">
              <h6>🛒 Ordered Products</h6>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Image</th>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (is_array($cart)): ?>
                      <?php foreach ($cart as $item): ?>
                        <?php
                          $price     = floatval($item['price'] ?? 0);
                          $quantity  = intval($item['quantity'] ?? 0);
                          $itemTotal = $price * $quantity;
                          $imgSrc    = htmlspecialchars($item['image'] ?? '');
                        ?>
                        <tr>
                          <td><img src="<?= $imgSrc ?>" alt="" class="product-img"></td>
                          <td><?= htmlspecialchars($item['name'] ?? '') ?></td>
                          <td>₹<?= number_format($price, 2) ?></td>
                          <td><?= $quantity ?></td>
                          <td>₹<?= number_format($itemTotal, 2) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center text-danger">Invalid cart data</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <p class="text-end fw-bold fs-5">
                Total Amount: ₹<?= number_format($row['total_amount'], 2) ?>
              </p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No orders found.</p>
    <?php endif; ?>
  </div>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
