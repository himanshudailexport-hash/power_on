<?php
// checkout.php

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Toggle COD vs Razorpay (true = Razorpay, false = COD)
$useRazorpay = false;

// 1) Include DB connection (must define $con as mysqli and set utf8mb4)
require_once __DIR__ . '/config/db.php';

// 2) If POST → handle JSON payload & insert, then exit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only accept JSON
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'Invalid JSON']);
        exit;
    }

    // Required fields
    $required = [
      'payment_id','firstName','lastName','email','phone',
      'address','landmark','city','state','pincode',
      'cart','total'
    ];
    foreach ($required as $f) {
      if (empty($data[$f]) && $data[$f] !== '0') {
          http_response_code(422);
          echo json_encode(['status'=>'error','message'=>"Missing field: $f"]);
          exit;
      }
    }

    // Sanitize
    $payment_id = $con->real_escape_string(trim($data['payment_id']));
    $first_name = $con->real_escape_string(trim($data['firstName']));
    $last_name  = $con->real_escape_string(trim($data['lastName']));
    $email      = $con->real_escape_string(trim($data['email']));
    $phone      = $con->real_escape_string(trim($data['phone']));
    $address    = $con->real_escape_string(trim($data['address']));
    $landmark   = $con->real_escape_string(trim($data['landmark']));
    $city       = $con->real_escape_string(trim($data['city']));
    $state      = $con->real_escape_string(trim($data['state']));
    $pincode    = $con->real_escape_string(trim($data['pincode']));
    $total      = floatval($data['total']);

    // Encode cart
    $cart_json = json_encode($data['cart'], JSON_UNESCAPED_UNICODE);
    if ($cart_json === false) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>'Failed to encode cart JSON']);
        exit;
    }

    // Insert into payments
    $sql = "INSERT INTO `payments`
      (`payment_id`,`first_name`,`last_name`,`email`,`phone`,
       `address`,`landmark`,`city`,`state`,`pincode`,
       `cart_data`,`total_amount`)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>'Prepare failed: '.$con->error]);
        exit;
    }

    $stmt->bind_param(
      'sssssssssssd',
       $payment_id, $first_name, $last_name, $email, $phone,
       $address,    $landmark,   $city,      $state, $pincode,
       $cart_json,  $total
    );

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>'Execute failed: '.$stmt->error]);
        exit;
    }

    echo json_encode([
      'status'  => 'success',
      'message' => 'Order saved',
      'orderId' => $con->insert_id
    ]);
    $stmt->close();
    
    exit;
}

// 3) If GET → render the checkout page (HTML + JS)
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <title>Aroha Edible Oils</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="https://digitalexportsmarketing.com" name="keywords">
    <meta content="https://digitalexportsmarketing.com" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
  
  <?php if ($useRazorpay): ?>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <?php endif; ?>
</head>
<body>
  <?php include __DIR__ . '/components/Header.php'; ?>

  <main class="main py-5">
    <div class="container">
      <h2 class="mb-4">Shopping Cart</h2>
      <div class="row">
        <!-- Cart Table -->
        <div class="col-md-7">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Image</th><th>Name</th><th>Price</th><th>Qty</th><th>Total</th>
                </tr>
              </thead>
              <tbody id="checkout-cart-body"></tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end"><strong>Total:</strong></td>
                  <td><strong id="checkout-cart-total">₹0</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
          <button class="btn btn-danger mt-3" onclick="clearCart()">Clear Cart</button>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-5">
          <div class="card shadow p-4">
            <h3 class="mb-3 text-center">Checkout</h3>
            <div class="border rounded p-3 mb-4 bg-light">
              <h5>Order Summary</h5>
              <p>Total Items: <span id="total-items">0</span></p>
              <p><strong>Total Price: ₹<span id="total-price">0</span></strong></p>
            </div>
            <form id="checkout-form" onsubmit="return false;">
              <div class="row mb-3">
                <div class="col"><input type="text" id="firstName" class="form-control" placeholder="First Name" required></div>
                <div class="col"><input type="text" id="lastName" class="form-control" placeholder="Last Name" required></div>
              </div>
              <div class="mb-3"><input type="email" id="email" class="form-control" placeholder="Email Address" required></div>
              <div class="mb-3"><input type="tel" id="phone" class="form-control" placeholder="Phone number" required></div>
              <div class="mb-3 d-none"><input type="hidden" id="raz-total-price"></div>
              <div class="mb-3"><input type="text" id="address" class="form-control" placeholder="House/Flat/Office No." required></div>
              <div class="mb-3"><input type="text" id="landmark" class="form-control" placeholder="Landmark/Society/Colony" required></div>
              <div class="row mb-3">
                <div class="col"><input type="text" id="city" class="form-control" placeholder="City" required></div>
                <div class="col">
                  <select id="state" class="form-select" required>
                    <option value="">Select State</option>
                    <option value="Delhi">Delhi</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                    <option value="West Bengal">West Bengal</option>
                  </select>
                </div>
                <div class="col"><input type="text" id="pincode" class="form-control" placeholder="Pin Code" required></div>
              </div>
            </form>
            <button id="placeOrderBtn" class="btn btn-success w-100">Place Order</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/components/Footer.php'; ?>

  <script>
    const useRazorpay = <?= $useRazorpay ? 'true' : 'false'; ?>;

    document.addEventListener("DOMContentLoaded", () => {
      renderCheckoutCart();
      displayCart();
      populateFormData();
      document.getElementById("placeOrderBtn").addEventListener("click", submitOrder);
      document.querySelectorAll("#checkout-form input, #checkout-form select")
              .forEach(el => el.addEventListener("input", saveFormData));
    });

    function getCart() {
      return JSON.parse(localStorage.getItem("cart")) || [];
    }

    function renderCheckoutCart() {
      const cart = getCart();
      const body = document.getElementById("checkout-cart-body");
      const totalEl = document.getElementById("checkout-cart-total");
      body.innerHTML = ""; let total = 0;
      cart.forEach(i => {
        total += i.price * i.quantity;
        body.insertAdjacentHTML("beforeend", `
          <tr>
            <td><img src="${i.image}" style="width:50px; height:50px; object-fit:cover;"></td>
            <td>${i.name}</td>
            <td>₹${i.price}</td>
            <td>${i.quantity}</td>
            <td>₹${i.price * i.quantity}</td>
          </tr>`);
      });
      totalEl.innerText = "₹" + total;
      document.getElementById("raz-total-price").value = total;
    }

    function displayCart() {
      const cart = getCart();
      let total = 0, items = 0;
      cart.forEach(i => { total += i.price * i.quantity; items += i.quantity; });
      document.getElementById("total-items").innerText = items;
      document.getElementById("total-price").innerText = total.toFixed(2);
      document.getElementById("raz-total-price").value = total.toFixed(2);
    }

    function saveFormData() {
      const fields = ["firstName","lastName","email","phone","address","landmark","city","state","pincode"];
      const data = {};
      fields.forEach(f => data[f] = document.getElementById(f).value);
      localStorage.setItem("checkoutFormData", JSON.stringify(data));
    }

    function populateFormData() {
      const data = JSON.parse(localStorage.getItem("checkoutFormData") || "{}");
      for (let k in data) {
        const el = document.getElementById(k);
        if (el) el.value = data[k];
      }
    }

    function clearCart() {
      localStorage.removeItem("cart");
      location.reload();
    }

    async function submitOrder() {
      const formData = JSON.parse(localStorage.getItem("checkoutFormData") || "{}");
      const cart = getCart();
      const total = parseFloat(document.getElementById("raz-total-price").value);

      // Validate
      if (Object.values(formData).some(v => !v)) {
        return alert("Please fill all fields correctly.");
      }
      if (!cart.length || total <= 0) {
        return alert("Cart is empty or invalid amount.");
      }

      // Build payload
      const payload = {
        payment_id: useRazorpay ? "" : "COD_" + Date.now(),
        ...formData,
        cart,
        total
      };

      if (useRazorpay) {
        // integrate Razorpay here...
      } else {
        try {
          const res = await fetch(window.location.href, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
          });
          const json = await res.json();
          if (json.status === "success") {
            alert("Order placed! ID: " + json.orderId);
            localStorage.clear();
            window.location.href = "index.php?order=success";
          } else {
            alert("Error: " + json.message);
          }
        } catch (err) {
          alert("Network error.");
        }
      }
    }
  </script>
</body>
</html>
