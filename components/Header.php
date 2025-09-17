<?php
// C:\xampp\htdocs\aroha\components\Header.php

// Ensure db.php is included to get the database connection ($con)
// Adjust the path if your db.php is located elsewhere relative to Header.php
require_once('./config/db.php');

// Fetch Categories from the database
$dynamicCategories = [];
if (isset($con)) { // Check if $con is set (connection successful)
    $categoriesResult = $con->query("SELECT id, name FROM categories ORDER BY name ASC");
    if ($categoriesResult) {
        while ($cat = $categoriesResult->fetch_assoc()) {
            $dynamicCategories[] = $cat;
        }
        $categoriesResult->free(); // Free result set
    } else {
        // Handle database query error if necessary
        error_log("Error fetching categories: " . $con->error);
    }
} else {
    error_log("Database connection not established in Header.php");
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Bag <span id="cart-item-count">0</span> items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <ul id="cart-items" class="list-group"></ul>

        <div class="bg-light p-3 mt-3 rounded">
            <h6 class="text-danger"><i class="bi bi-percent"></i> Coupons</h6>
            <p class="text-danger small m-0">Apply now and save extra!</p>
        </div>

        <div class="mt-3">
            <h6>Price Details</h6>
            <div class="d-flex justify-content-between">
                <span>Bag MRP (<span id="cart-item-count-total">0</span> items)</span>
                <span>₹<span id="cart-mrp">0</span></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Bag Discount</span>
                <span class="text-success">-₹<span id="cart-discount">0</span></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Shipping</span>
                <span class="text-success">FREE</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>You Pay</span>
                <span>₹<span id="cart-total">0</span></span>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-danger w-100" onclick="clearCart()">Clear Cart</button>
            <a href="./checkout.php">
                <button class="btn btn-primary w-100 mt-2">Proceed</button>
            </a>
        </div>
    </div>
</div>

<header id="header" class="header d-flex align-items-center sticky-top bg-white shadow-sm">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
        <a href="/" class="logo d-flex align-items-center me-auto">
            <img src="/assets/img/logo.webp" alt="Logo" style="width: 90px;" />
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="/">Home</a></li>
                <li class="dropdown"><a href="#"><span>Company Profile</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="about.php">About</a></li>
                        <li><a href="certifications.php">Certifications</a></li>
                        <li><a href="testimonial.php">Testimonials</a></li>
                    </ul>
                </li>
                <li class="dropdown"><a href="#"><span>Categories</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <?php
                        if (!empty($dynamicCategories)) {
                            foreach ($dynamicCategories as $category) {
                        ?>
                                <li>
                                    <a href="category.php?category_id=<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                </li>
                            <?php
                            }
                        } else {
                            ?>
                            <li><a href="#">No Categories Found</a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="dropdown"><a href="#"><span>Events</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="fairs_and_exhibition.php">Fairs & Exhibition</a></li>
                    </ul>
                </li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </nav>

        <a class="btn-getstarted ms-3" style="text-decoration: none;" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
            Enquire Now <span id="cart-count" class="badge rounded-pill bg-danger ms-1" style="display: none;">0</span>
        </a>

        <i class="mobile-nav-toggle d-xl-none bi bi-list fs-2 ms-3"></i>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ADD TO CART FUNCTION
        document.querySelectorAll(".add-to-cart").forEach(button => {
            button.addEventListener("click", function() {
                const product = {
                    id: this.dataset.id,
                    name: this.dataset.name,
                    price: this.dataset.price,
                    image: this.dataset.image,
                    quantity: 1
                };

                let cart = JSON.parse(localStorage.getItem("cart")) || [];

                const index = cart.findIndex(item => item.id === product.id);
                if (index !== -1) {
                    cart[index].quantity += 1;
                } else {
                    cart.push(product);
                }

                localStorage.setItem("cart", JSON.stringify(cart));
                updateCartUI();
                alert(`${product.name} added to cart!`);
            });
        });

        // CART UI UPDATE FUNCTION
        window.updateCartUI = function() {
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            let cartList = document.getElementById("cart-items");
            let itemCount = document.getElementById("cart-item-count");
            let itemCountTotal = document.getElementById("cart-item-count-total");
            let cartMRP = document.getElementById("cart-mrp");
            let cartDiscount = document.getElementById("cart-discount");
            let cartTotal = document.getElementById("cart-total");

            cartList.innerHTML = '';
            let totalItems = 0;
            let totalMRP = 0;
            let discount = 0;

            cart.forEach(item => {
                totalItems += item.quantity;
                totalMRP += item.price * item.quantity;

                let li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center";
                li.innerHTML = `
                <div>
                    <div><strong>${item.name}</strong></div>
                    <div>₹${item.price} x ${item.quantity}</div>
                </div>
                <img src="${item.image}" width="50" height="50" style="object-fit:cover;border-radius:5px;">
            `;
                cartList.appendChild(li);
            });

            discount = totalMRP * 0.1; // example 10% discount
            let finalTotal = totalMRP - discount;

            itemCount.innerText = totalItems;
            itemCountTotal.innerText = totalItems;
            cartMRP.innerText = totalMRP.toFixed(2);
            cartDiscount.innerText = discount.toFixed(2);
            cartTotal.innerText = finalTotal.toFixed(2);

            // Show badge on cart button
            const cartCountBadge = document.getElementById("cart-count");
            if (totalItems > 0) {
                cartCountBadge.innerText = totalItems;
                cartCountBadge.style.display = "inline-block";
            } else {
                cartCountBadge.style.display = "none";
            }
        };

        // CLEAR CART FUNCTION
        window.clearCart = function() {
            localStorage.removeItem("cart");
            updateCartUI();
        };

        // Initialize cart UI on load
        updateCartUI();
    });
</script>


<style>
    /* Base styles for the nav menu (desktop) */
    .navmenu {
        display: flex;
        flex-direction: row;
        gap: 20px;
        padding: 0;
        /* Ensure no default padding affecting alignment */
        margin: 0;
        /* Ensure no default margin affecting alignment */
    }

    .navmenu ul {
        list-style: none;
        /* Remove bullet points */
        margin: 0;
        padding: 0;
        display: flex;
        /* For desktop, keep horizontal flow */
        gap: 20px;
        /* Space between main menu items */
    }

    .navmenu ul li a {
        text-decoration: none;
        display: block;
        /* Make links block-level for padding/margins */
        padding: 10px 0;
        /* Example padding */
        color: #333;
        /* Default link color */
    }

    .navmenu ul li a.active {
        color: blue;
        /* Highlight active link */
    }

    .navmenu .dropdown {
        position: relative;
        /* For desktop dropdown positioning */
    }

    .navmenu .dropdown ul {
        display: none;
        /* Hidden by default on desktop */
        position: absolute;
        top: 100%;
        left: 0;
        background-color: white;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        min-width: 160px;
        z-index: 1000;
        flex-direction: column;
        /* Ensure vertical stack for dropdown items */
        gap: 0;
        /* No gap between sub-menu items */
    }

    .navmenu .dropdown:hover>ul {
        display: flex;
        /* Show on hover for desktop */
    }

    .navmenu .dropdown ul li {
        padding: 0;
        /* Remove padding from list items for sub-menu */
    }

    .navmenu .dropdown ul li a {
        padding: 10px 15px;
        /* Padding for sub-menu items */
        white-space: nowrap;
        /* Prevent breaking of long text */
    }

    .navmenu .toggle-dropdown {
        margin-left: 5px;
        transition: transform 0.2s ease;
    }


    /* Responsive nav menu for mobile */
    @media (max-width: 991px) {
        .navmenu {
            position: fixed;
            /* Use fixed for full viewport coverage */
            top: 0;
            /* Start from the very top */
            right: 0;
            width: 280px;
            /* Adjust width as needed */
            height: 100vh;
            /* Full viewport height */
            flex-direction: column;
            background-color: white;
            padding: 1rem;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.2);
            /* Shadow from left */
            z-index: 1050;
            /* Higher than Bootstrap offcanvas if needed, but offcanvas is 1045 */
            transform: translateX(100%);
            /* Start off-screen to the right */
            transition: transform 0.3s ease-out;
            visibility: hidden;
            overflow-y: auto;
            /* Enable scrolling for long menus */
            justify-content: flex-start;
            /* Align content to the top */
        }

        .navmenu.navmenu-active {
            transform: translateX(0);
            /* Slide into view */
            visibility: visible;
        }

        .navmenu ul {
            flex-direction: column;
            /* Stack menu items vertically */
            gap: 0;
            /* Remove gap between main menu items in mobile */
            padding-top: 50px;
            /* Space from top of offcanvas to first item */
            width: 100%;
            /* Take full width of offcanvas */
        }

        .navmenu ul li {
            margin-bottom: 0;
            /* Adjust vertical spacing as needed */
            border-bottom: 1px solid #eee;
            /* Visual separator between main items */
        }

        .navmenu ul li:last-child {
            border-bottom: none;
            /* No border for the last item */
        }

        .navmenu ul li a {
            padding: 12px 15px;
            /* Comfortable padding for touch */
            color: #333;
            /* Default link color */
            width: 100%;
            /* Make link fill the list item */
            box-sizing: border-box;
            /* Include padding in width */
        }

        .navmenu ul li a.active {
            color: blue;
            /* Highlight active link */
        }

        /* Mobile Dropdown Specific Styles */
        .navmenu .dropdown ul {
            position: static;
            /* Allows sub-menu to flow naturally below parent */
            visibility: visible;
            /* Make visible when parent is active */
            opacity: 1;
            box-shadow: none;
            /* Remove shadow for nested list */
            background-color: #f8f8f8;
            /* Slightly different background for sub-menu */
            padding-left: 25px;
            /* Indent sub-items more */
            display: none;
            /* Hidden by default, toggled by JS 'active' class */
            border-top: 1px solid #eee;
            /* Separator for sub-menu */
        }

        .navmenu .dropdown.active>ul {
            display: flex;
            /* Show sub-menu when parent has 'active' class */
        }

        .navmenu .dropdown ul li {
            border-bottom: none;
            /* No border between sub-menu items */
        }

        .navmenu .dropdown ul li a {
            padding: 10px 15px;
            /* Padding for sub-menu items */
        }

        .navmenu .dropdown .toggle-dropdown {
            display: inline-block;
            /* Ensure arrow is visible */
            position: absolute;
            /* Position relative to parent link */
            right: 15px;
            /* Align to the right of the menu item */
            top: 50%;
            transform: translateY(-50%) rotate(0deg);
            /* Initial rotation */
            cursor: pointer;
            font-size: 0.9em;
            /* Smaller arrow */
            color: #666;
            /* Subtler color */
        }

        .navmenu .dropdown.active .toggle-dropdown {
            transform: translateY(-50%) rotate(180deg);
            /* Rotate when active */
        }

        /* Hide desktop toggle for mobile */
        .d-xl-none {
            display: block !important;
            /* Ensure mobile toggle is always block on mobile */
        }
    }

    /* Ensure desktop styles are correct */
    @media (min-width: 992px) {
        .mobile-nav-toggle {
            display: none !important;
            /* Hide mobile toggle on desktop */
        }

        .navmenu {
            display: flex;
            /* Keep horizontal flex on desktop */
            flex-direction: row;
            /* Other desktop specific styles if any */
        }

        .navmenu .dropdown ul {
            display: none;
            /* Hidden by default on desktop */
        }

        .navmenu .dropdown:hover>ul {
            display: flex;
            /* Show on hover for desktop */
        }
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navToggle = document.querySelector(".mobile-nav-toggle");
        const navMenu = document.querySelector(".navmenu");

        if (navToggle && navMenu) {
            navToggle.addEventListener("click", function () {
                navMenu.classList.toggle("navmenu-active");
            });

            // Optional: Close menu when clicking a link (for better UX)
            navMenu.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    navMenu.classList.remove("navmenu-active");
                });
            });
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>