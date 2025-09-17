<?php
session_start();

require_once('config/db.php');

if (isset($_POST['submit'])) {
    $inputEmail = $_POST['InputEmail'];
    $inputPassword = $_POST['InputPassword'];

    // ✅ Hash the password
    $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);

    // ✅ Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO `admin` (email, password) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $inputEmail, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Signup Successful. You can now log in.');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<script>alert('❌ Signup failed. Email may already exist.');</script>";
    }
    
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="card shadow p-4" style="width: 400px;">
        <h2 class="text-center mb-4">Admin Signup</h2>
        <form method="post">
            <div class="mb-3">
                <label for="InputEmail" class="form-label">Email address</label>
                <input type="email" class="form-control" id="InputEmail" name="InputEmail" required>
            </div>
            <div class="mb-3">
                <label for="InputPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="InputPassword" name="InputPassword" required>
            </div>
            <button name="submit" type="submit" class="btn btn-primary w-100">SIGN UP</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Already have an account? Login here.</a>
        </div>
    </div>

</body>
</html>
