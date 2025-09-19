<?php
require_once('config/db.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the 'slug' parameter is set in the URL
if (isset($_GET['slug'])) {
    // Get the 'slug' parameter value
    $blog_slug = $_GET['slug'];

    // Prevent SQL injection by using prepared statements
    $query = "SELECT * FROM `blog` WHERE slug = ?";
    $stmt = $con->prepare($query);

    // Corrected: 's' for string, as slug is typically a string
    $stmt->bind_param('s', $blog_slug);
    $stmt->execute();

    $result = $stmt->get_result();

    // Check if any row is returned
    if ($result->num_rows > 0) {
        $blog = $result->fetch_assoc();
    } else {
        // Redirect to a 404 page or show a more user-friendly message
        header("Location: 404.php"); // Assuming you have a 404.php
        exit;
    }

    $stmt->close();
} else {
    // Redirect to the blog listing page or homepage
    header("Location: blog.php"); // Assuming you have a blog listing page
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?php echo htmlspecialchars($blog['blogtitle']); ?></title>

    <!-- ✅ Meta Description -->
    <meta name="description" content="<?php 
        if (!empty($blog['meta_description'])) {
            echo htmlspecialchars($blog['meta_description']);
        } else {
            echo htmlspecialchars(substr(strip_tags($blog['blogcontent']), 0, 160));
        }
    ?>">

    <!-- ✅ Canonical URL -->
    <link rel="canonical" href="https://arohaedible.com/<?php echo htmlspecialchars($blog['slug']); ?>" />

    <link href="../../assets/img/favicon.png" rel="icon">
    <link href="../../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Noto+Sans:wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Questrial:wght@400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="../../assets/css/main.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">


    <style>
        /* Custom Styles for Blog Page */
        .blog-hero {
            position: relative;
            height: 600px; /* Adjust as needed */
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            margin-bottom: 3rem;
            border-radius: 8px;
            overflow: hidden; /* Ensures image corners are rounded */
        }

        .blog-hero img {
            margin-top: 10px;
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
        }

        .blog-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.3); /* Slightly darker overlay for text */
            border-radius: 10px;
        }

        .blog-content h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1.5rem;
            /* border-bottom: 3px solid #007bff;  */
            padding-bottom: 10px;
        }

        .blog-content p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 1rem;
            text-align: justify; /* Justify text for a cleaner look */
        }

        .blog-meta {
            font-size: 0.9rem;
            color: #777;
            margin-top: -1rem; /* Adjust spacing */
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 15px; /* Spacing between meta items */
        }

        .blog-meta span {
            display: flex;
            align-items: center;
        }

        .blog-meta i {
            margin-right: 5px;
            color: #007bff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .blog-hero {
                height: 300px;
            }
            .blog-hero h1 {
                font-size: 2.5rem;
            }
            .blog-content h2 {
                font-size: 2rem;
            }
            .blog-content p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <?php include 'components/Header.php'; ?>

    <main id="main">
        <?php if (!empty($blog['blog_image'])): ?>
            <section class="blog-hero" style="background-image: url('data:image/jpeg;base64,<?php echo base64_encode($blog['blog_image']); ?>');">
                <div class="container">
                    <!--<h1 data-aos="fade-up" data-aos-delay="200"><?php echo htmlspecialchars($blog['blogtitle']); ?></h1>-->
                </div>
            </section>
        <?php else: ?>
            <section class="container mt-5">
                <!--<h1 class="display-4 text-center"><?php echo htmlspecialchars($blog['blogtitle']); ?></h1>-->
            </section>
        <?php endif; ?>

       <section id="blog-details" class="blog-details section-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="blog-content">
                    <div class="blog-meta mb-4">
                        <?php if (!empty($blog['author'])): ?>
                            <span><i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($blog['author']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($blog['created_at'])): ?>
                            <span><i class="bi bi-calendar-event-fill"></i> <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                        <?php endif; ?>
                        </div>

                    <div class="blog-article-body" data-aos="fade-up" data-aos-delay="100">
                        <?php echo $blog['blogcontent']; ?>
                    </div>

                    <?php /*
                    <h3 class="mt-5 mb-3" data-aos="fade-up">A Subheading Example</h3>
                    <p data-aos="fade-up">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    <blockquote class="blockquote" data-aos="fade-up">
                        <p class="mb-0">"The only way to do great work is to love what you do."</p>
                        <footer class="blockquote-footer">Steve Jobs</footer>
                    </blockquote>
                    */ ?>
                </div>
            </div>
        </div>
    </div>
</section>
    </main>

    <?php include 'components/Footer.php'; ?>

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/vendor/aos/aos.js"></script>
    <script src="../../assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="../../assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="../../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="../../assets/vendor/php-email-form/validate.js"></script>

    <script src="../../assets/js/main.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>

</body>
</html>