<?php
session_start();

require_once('config/db.php');

if (isset($_POST['submit'])) {
    $inputEmail = $_POST['InputEmail'];
    $inputPassword = $_POST['InputPassword'];

    // ✅ Use prepared statement to prevent SQL injection
    $sql = "SELECT id, password FROM `admin` WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $inputEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Verify the password
        if (password_verify($inputPassword, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];

            // ✅ Redirect with success message
            echo "<script>
                    alert('✅ Admin Login Successful');
                    localStorage.setItem('adminData', '{$row['id']}');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('❌ Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('❌ Email not found. Please check your credentials.');</script>";
    }

    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="card shadow p-4 m-4" style="width: 400px;">
        <h2 class="text-center mb-4">Admin Login</h2>
        <form method="post">
            <div class="mb-3">
                <label for="InputEmail" class="form-label">Email address</label>
                <input type="email" class="form-control" id="InputEmail" name="InputEmail" required>
            </div>
            <div class="mb-3">
                <label for="InputPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="InputPassword" name="InputPassword" required>
            </div>
            <button name="submit" type="submit" class="btn btn-primary w-100">LOGIN</button>
        </form>
        <div class="text-center mt-3">
            <a href="/" class="text-decoration-none">Back to home...</a>
        </div>
    </div>

</body>
</html>
