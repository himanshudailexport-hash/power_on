<?php
require_once('./config/db.php'); // Ensure the correct relative path

$footerCategories = [];
$result = $con->query("SELECT id, name FROM categories ORDER BY name ASC LIMIT 6");
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $footerCategories[] = $row;
  }
}
?>
<footer id="footer" class="footer">

  <div class="footer-newsletter">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-6">
          <h4>Join Our Newsletter</h4>
          <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form action="forms/newsletter.php" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-4 col-md-6 footer-about">
        <a href="#" class="logo d-flex align-items-center me-auto">
          <img src="assets/img/images/logo.png" alt="Logo" style="width: 90px;" />
        </a>
        <div class="footer-contact pt-3">
          <p><strong>Address:</strong> 501/2, 4th Main, Opp. Bank of Baroda, P J Extension, Nittuvalli, Davanagere, Karnataka, India - 577002</p>
          <p class="mt-3"><strong>Phone:</strong> <span>+91 9901998067</span></p>
          <p><strong>Email:</strong> <span>poweron.dvg@gmail.com</span></p>
        </div>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Useful Links</h4>
        <ul>
          <li><i class="bi bi-chevron-right"></i> <a href="index.php">Home</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="about.php">About us</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="certifications.php">Certifications</a></li>
          <li><i class="bi bi-chevron-right"></i> <a href="testimonial.php">Testimonials</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Our Categories</h4>
        <ul>
          <?php if (!empty($footerCategories)) : ?>
            <?php foreach ($footerCategories as $cat) : ?>
              <li>
                <i class="bi bi-chevron-right"></i>
                <a href="category.php?category_id=<?= $cat['id'] ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else : ?>
            <li><i class="bi bi-chevron-right"></i> No categories found</li>
          <?php endif; ?>
        </ul>
      </div>


      <div class="col-lg-4 col-md-12">
        <h4>Follow Us</h4>
        <p>Stay connected with us for the latest updates, offers, and healthy living tips!</p>
        <div class="social-links d-flex">
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

    </div>
  </div>

  <div class="container copyright text-center mt-4">
    <p>© <span>Copyright</span> <strong class="px-1 sitename">PowerOn</strong> <span>All Rights Reserved</span></p>
    <div class="credits">
      Designed by <a href="https://www.dialexportmart.com/">Innodem Private limited</a>
    </div>
  </div>

</footer>