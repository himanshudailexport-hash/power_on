<?php

// Database connection
require_once('config/db.php');

// PayU Response Parameters
$txnid = $_POST['txnid']; // Transaction ID from PayU
$payu_id = $_POST['mihpayid'];  // PayU transaction ID
$status = $_POST['status'];      // Payment status (e.g., success/failure)
$payment_mode = $_POST['mode'];  // Payment mode (e.g., CC, DC, NetBanking, etc.)
$payudate = $_POST['addedon'];  // PayU Payment Date (from PayU Response)

// Fetch user details from the database using the txnid
$stmt = $conn->prepare("SELECT * FROM user_payments WHERE txnid = ?");
$stmt->bind_param("s", $txnid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Update the payment record with PayU response details and payment date
    if ($status == 'success') {
        $stmt_update = $conn->prepare("UPDATE user_payments SET payu_id = ?, payment_mode = ?, status = ?, payudate = ? WHERE txnid = ?");
        $stmt_update->bind_param("sssss", $payu_id, $payment_mode, $status, $payudate, $txnid);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // Display the invoice and user details
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
        <title>Payment Success - Invoice</title>
        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .invoice { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
            .status { font-weight: bold; color: green; }
            .failure { font-weight: bold; color: red; }
        </style>
    </head>
    <body>

    <div class="invoice" id="invoiceContent">
        <p>Date: <?php echo date("Y-m-d H:i:s", strtotime($payudate)); ?></p>
        <h2 class="text-center">Payment Invoice</h2>
        
        <!-- Invoice Details Table -->
        <table class="table table-bordered">
            <tr>
                <th>Transaction ID</th>
                <td><?php echo $txnid; ?></td>
            </tr>
            <tr>
                <th>PayU Transaction ID</th>
                <td><?php echo $payu_id; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo $status == 'success' ? '<span class="status">Success</span>' : '<span class="failure">Failed</span>'; ?></td>
            </tr>
        </table>

        <h3>User Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
            </tr>
        </table>

        <h3>Product Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Product Name</th>
                <td><?php echo htmlspecialchars($user['product_name']); ?></td>
            </tr>
            <tr>
                <th>Price</th>
                <td>₹<?php echo htmlspecialchars($user['product_price']); ?></td>
            </tr>
        </table>

        <h3>Payment Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Payment Mode</th>
                <td><?php echo htmlspecialchars($payment_mode); ?></td>
            </tr>
            <tr>
                <th>Amount Paid</th>
                <td>₹<?php echo htmlspecialchars($user['product_price']); ?></td>
            </tr>
        </table>

        <!-- Download Invoice Button -->
        <div class="d-flex justify-content-between">


             <button id="downloadInvoice" class="btn btn-success">Download Invoice</button>
     
             <a href="./partner-with-us.php" class="btn btn-primary">Go back</a>
         </div>
    </div>

    <script>
        // Download Invoice as PDF when the button is clicked
        document.getElementById('downloadInvoice').addEventListener('click', function () {
            const element = document.getElementById('invoiceContent');
            
            // Options for the PDF generation
            const opt = {
                margin:       1,
                filename:     'invoice.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { dpi: 192, letterRendering: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate and save the PDF
            html2pdf().from(element).set(opt).save();
        });
    </script>

    </body>
    </html>
    <?php
} else {
    echo "No payment details found for this transaction.";
}

$stmt->close();
$conn->close();
?>
