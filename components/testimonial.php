<section class="testimonial-section py-5" style="background-image: url('./img/bg-testimonial.jpg'); background-size: cover; background-position: center;">
  <div class="container">
    <h2 class="text-center text-dark mb-4">Testimonials</h2>
    <div class="carousel-container position-relative">
      <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          $testimonials = [
            [
              'image' => './img/testimonial/1.png',
              'text' => 'Poweron has been my go-to dealer for UPS and inverter batteries. Their products are reliable, and the delivery was super quick. I no longer worry about power cuts at home!',
              'name' => 'Aman Singh',
            ],
            [
              'image' => './img/testimonial/2.png',
              'text' => 'Excellent service and top-quality batteries. The team guided me to choose the right UPS for my office, and it’s working flawlessly. Highly recommend Poweron!',
              'name' => 'Priya',
            ],
            [
              'image' => './img/testimonial/3.png',
              'text' => 'I ordered solar panels from Poweron, and the installation process was smooth. Their support team is very professional and helped me every step of the way.',
              'name' => 'Rajesh Singh',
            ],
            [
              'image' => './img/testimonial/1.png',
              'text' => 'Great experience with Poweron! Affordable prices, genuine products, and outstanding after-sales service. Truly one of the best UPS dealers in Karnataka.',
              'name' => 'Priya',
            ],
            [
              'image' => './img/testimonial/2.png',
              'text' => 'We purchased inverter batteries for our factory from Poweron. The quality is top-notch, and they delivered on time. Customer support is very responsive.',
              'name' => 'Priya',
            ]
          ];

          foreach ($testimonials as $index => $testimonial) {
            $activeClass = $index === 0 ? 'active' : '';
            echo '<div class="carousel-item ' . $activeClass . '">';
            echo '<div class="testimonial-box text-center testimonial-item text-dark p-4 rounded">';
            echo '<img src="' . htmlspecialchars($testimonial['image']) . '" alt="' . htmlspecialchars($testimonial['name']) . '" class="mx-auto d-block">';
            echo '<p class="fs-5 mt-3">' . htmlspecialchars($testimonial['text']) . '</p>';
            echo '<hr class="mx-auto w-25" style="border-color: #fff;">';
            echo '<h4 class="fw-bold mt-2 text-dark">– ' . htmlspecialchars($testimonial['name']) . '</h4>';
            echo '</div>';
            echo '</div>';
          }
          ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>

      </div>
    </div>
  </div>
</section>
<style>

.testimonial-box img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid orange;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  background-size: 1.5rem 1.5rem;
}
</style>
