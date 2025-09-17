<?php
require_once('./config/db.php');

$feedback = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $con->real_escape_string($_POST['name'] ?? '');
    $email = $con->real_escape_string($_POST['email'] ?? '');
    $subject = $con->real_escape_string($_POST['subject'] ?? '');
    $message = $con->real_escape_string($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        $stmt = $con->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $feedback = "<div class='alert alert-success'>✅ Message sent successfully!</div>";
        } else {
            $feedback = "<div class='alert alert-danger'>❌ Failed to send message. Please try again later.</div>";
        }
        $stmt->close();
    } else {
        $feedback = "<div class='alert alert-warning'>❗ Please fill in all fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Aroha Edible Oils - Contact Us</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
      <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>

<?php include("./components/Header.php") ?>

<!-- Contact Hero -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-start">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="display-1 text-dark mb-md-4">Fairs & Exhibition</h1>
            </div>
        </div>
    </div>
</div>
<!-- Image Gallery Section -->
<div class="container py-5">
    <h2 class="text-center mb-4">Pune Food Expo 2023</h2>
    <div class="row g-3">

        <?php
        // Set the path to your gallery folder
        $galleryPath = "assets/img/gallery/Pune Food Expo 2023/";

        // Get all images from the folder
        $images = glob($galleryPath . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

        if ($images) {
            foreach ($images as $img) {
                echo '
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <img src="' . $img . '" class="card-img-top" style="height:250px; object-fit:cover;" alt="Gallery Image">
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-center text-muted">No images found in the gallery.</p>';
        }
        ?>

    </div>
</div>
<div class="container py-5">
    <h2 class="text-center mb-4">Aahar Food Expo 2023</h2>
    <div class="row g-3">

        <?php
        // Set the path to your gallery folder
        $galleryPath = "assets/img/gallery/Aahar Food Expo 2023/";

        // Get all images from the folder
        $images = glob($galleryPath . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

        if ($images) {
            foreach ($images as $img) {
                echo '
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <img src="' . $img . '" class="card-img-top" style="height:250px; object-fit:cover;" alt="Gallery Image">
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-center text-muted">No images found in the gallery.</p>';
        }
        ?>

    </div>
</div>
<div class="container py-5">
    <h2 class="text-center mb-4">Infore Food Expo 2023</h2>
    <div class="row g-3">

        <?php
        // Set the path to your gallery folder
        $galleryPath = "assets/img/gallery/Infore Food Expo 2023/";

        // Get all images from the folder
        $images = glob($galleryPath . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);

        if ($images) {
            foreach ($images as $img) {
                echo '
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm">
                        <img src="' . $img . '" class="card-img-top" style="height:250px; object-fit:cover;" alt="Gallery Image">
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-center text-muted">No images found in the gallery.</p>';
        }
        ?>

    </div>
</div>


<!-- Contact Form Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="bg-light p-4 shadow-sm">
                <h3 class="mb-4">Please Fill Out the Form</h3>

                <?= $feedback ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="col-12">
                            <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                        </div>
                        <div class="col-12">
                            <textarea name="message" class="form-control" placeholder="Message" rows="4" required></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary px-5" type="submit">Send</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Info -->
        
    <div class="col-lg-5">
        <div class="h-100 p-5">
            <h2 class="text-dark mb-4">Get In Touch</h2>
            <div class="d-flex mb-4">

                <div class="ps-3">
                    <h5 class="text-dark">Our Office</h5>
                    <span class="text-dark">Plot no. 51 Near By Sakshi Dhaba Barkhedi Kalan Bhadbhada Road Bhopal Madhya Pradesh India 462044</span>
                </div>
            </div>
            <div class="d-flex mb-4">
                <div class="ps-3">
                    <h5 class="text-dark">Email Us</h5>
                    <span class="text-dark">info@arohaedible.com</span>
                </div>
            </div>
            <div class="d-flex">
                <div class="ps-3">
                    <h5 class="text-dark">Call Us</h5>
                    <span class="text-dark">+917987079251</span>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>


<?php include("./components/Footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
