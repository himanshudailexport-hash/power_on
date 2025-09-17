<?php
require_once 'config/db.php';

// DEBUG: Log raw input (can be removed in production)
$rawInput = file_get_contents('php://input');
file_put_contents('debug_log.txt', $rawInput); // Optional debugging line

$data = json_decode($rawInput, true);

// Validate JSON parsing
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data"]);
    exit;
}

if ($data) {
    $payment_id = $data['payment_id'] ?? '';
    $first_name = $data['firstName'] ?? '';
    $last_name = $data['lastName'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $address = $data['address'] ?? '';
    $landmark = $data['landmark'] ?? '';
    $city = $data['city'] ?? '';
    $state = $data['state'] ?? '';
    $pincode = $data['pincode'] ?? '';
    $total = $data['total'] ?? 0;
    $cart = isset($data['cart']) ? json_encode($data['cart']) : '[]';

    $stmt = $conn->prepare("INSERT INTO payments 
        (payment_id, first_name, last_name, email, phone, address, landmark, city, state, pincode, cart_data, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssssssssd", 
        $payment_id, $first_name, $last_name, $email, $phone, 
        $address, $landmark, $city, $state, $pincode, $cart, $total
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>
