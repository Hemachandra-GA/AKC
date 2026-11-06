<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // 1. Get the protocol (http:// or https://)
    $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $is_https ? "https://" : "http://";
    // 2. Get the domain name
    $domain = $_SERVER['HTTP_HOST'];
    // 3. Get the base directory
    $script_dirname = dirname($_SERVER['SCRIPT_NAME']);
    $base_dir = ($script_dirname === '\\' || $script_dirname === '/') ? '' : $script_dirname;
    // 4. Create the dynamic BASE URL
    $base_url = $protocol . $domain . $base_dir;
    if (!defined('BASE_URL')) {
        define('BASE_URL', $base_url);
    }
    ?>
    <!-- IMPORTANT: Inform the browser of the dynamic base url -->
    <base href="<?php echo BASE_URL; ?>/">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AKC - Packaging Technology & Research Centre</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/FINAL Logo-Round (2).png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/FINAL Logo-Round (2).png">
    <link rel="apple-touch-icon" href="assets/images/FINAL Logo-Round (2).png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.5">
    <link rel="stylesheet" href="assets/css/scroll-reveal.css?v=1.0">
    <link rel="stylesheet" href="assets/css/header.css?v=1.3">
    <link rel="stylesheet" href="assets/css/banner.css?v=1.6">
    <link rel="stylesheet" href="assets/css/services.css?v=1.2">
    <link rel="stylesheet" href="assets/css/about.css?v=1.4">
    <link rel="stylesheet" href="assets/css/slider.css?v=1.2">
    <link rel="stylesheet" href="assets/css/categories.css?v=1.2">
    <link rel="stylesheet" href="assets/css/support-banner.css?v=1.2">
    <link rel="stylesheet" href="assets/css/team.css?v=1.2">
    <link rel="stylesheet" href="assets/css/training-banner.css?v=1.2">
    <link rel="stylesheet" href="assets/css/accreditation.css">
    <link rel="stylesheet" href="assets/css/book-test.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/course-hero.css">
    <link rel="stylesheet" href="assets/css/course-details.css">
    <link rel="stylesheet" href="assets/css/course-curriculum.css">
    <link rel="stylesheet" href="assets/css/packaging-essentials-hero.css">
    <link rel="stylesheet" href="assets/css/pe-overview.css">
    <link rel="stylesheet" href="assets/css/pe-info-cards.css">
    <link rel="stylesheet" href="assets/css/pe-pricing.css">
    <style>
        /* MAIN MENU */
        .main-menu {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 32px !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .main-menu>li {
            position: relative !important;
        }

        .main-menu>li>a {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 24px 0 !important;
            text-decoration: none !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            transition: color 0.2s ease !important;
        }

        .main-menu>li>a:hover {
            color: #2563eb !important;
        }

        /* DESKTOP SUBMENU */
        .main-menu .submenu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            min-width: 250px !important;
            background: #ffffff !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 10px 0 !important;
            border-radius: 10px !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15) !important;
            border: 1px solid #e2e8f0 !important;
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateY(12px) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
            z-index: 999 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .main-menu .submenu.products-submenu {
            width: 440px !important;
            max-height: 75vh !important;
            overflow-y: auto !important;
        }

        .main-menu .submenu li {
            width: 100% !important;
            display: block !important;
        }

        .main-menu .submenu li a {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 11px 20px !important;
            color: #334155 !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            white-space: normal !important;
            line-height: 1.4 !important;
        }

        .main-menu .submenu li a:hover {
            background: #f8fafc !important;
            color: #2563eb !important;
            padding-left: 24px !important;
        }

        .main-menu .submenu li a i {
            width: 18px !important;
            text-align: center !important;
            color: #2563eb !important;
            font-size: 14px !important;
            flex-shrink: 0 !important;
        }

        /* Show dropdown on hover */
        .main-menu .dropdown:hover>.submenu {
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        /* MOBILE NAVIGATION */
        @media (max-width: 991px) {
            .main-menu {
                display: none !important;
            }

            .mobile-nav-links {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .mobile-nav-links li {
                width: 100%;
            }

            .mobile-nav-links li a {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 14px 20px;
                text-decoration: none;
                color: #1e293b;
                font-weight: 600;
            }

            .mobile-nav-links .submenu {
                position: static !important;
                width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                visibility: visible !important;
                opacity: 1 !important;
                transform: none !important;
                display: none !important;
                background: #f8fafc !important;
                border: none !important;
            }

            .mobile-nav-links .dropdown.active>.submenu {
                display: block !important;
            }

            .mobile-nav-links .submenu li a {
                padding: 10px 20px 10px 40px !important;
                font-size: 13.5px !important;
                font-weight: 500 !important;
                color: #334155 !important;
            }

            .mobile-nav-links .dropdown.active>a i {
                transform: rotate(180deg);
                transition: transform 0.3s ease;
            }
        }
    </style>
</head>

<body>

    <header>
        <!-- Top Notification Bar -->
        <div class="top-bar">
            <div class="container">
                One-Stop Solution for Polymer Products
            </div>
        </div>

        <!-- Main Navigation Bar -->
        <nav class="main-header">
            <div class="header-container">
                <!-- Logo Area -->
                <div class="logo">
                    <a href="index.php">
                        <!-- Replace with actual logo image -->
                        <img src="assets/images/FINAL Logo.png" alt="AKC Logo">
                    </a>
                </div>

                <!-- Navigation Menu -->
                <div class="nav-menu">
                    <ul class="main-menu">
                        <li><a href="index.php">Home</a></li>
                        <li class="dropdown">
                            <a href="about.php">
                                About Us <i class="fa-solid fa-chevron-down"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="about.php"><i class="fa-solid fa-building"></i> About AKC-PTARC</a></li>
                                <li><a href="our-team.php"><i class="fa-solid fa-users"></i> Our team</a></li>
                                <li><a href="#"><i class="fa-solid fa-vr-cardboard"></i> Virtual tour</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="testing-and-certification.php">
                                Testing &amp; Certification <i class="fa-solid fa-chevron-down"></i>
                            </a>
                            <ul class="submenu products-submenu">
                                <li><a href="dynamic-product.php?slug=polymer-and-polymeric-products"><i
                                            class="fa-solid fa-shapes"></i> Polymer & Polymeric Products</a></li>
                                <li><a href="dynamic-product.php?slug=rubber-and-rubber-products"><i
                                            class="fa-solid fa-compact-disc"></i> Rubber and Rubber Products</a></li>
                                <li><a href="dynamic-product.php?slug=textiles-and-textile-products"><i
                                            class="fa-solid fa-shirt"></i> Textiles and Textile Products</a></li>
                                <li><a href="dynamic-product.php?slug=leather-and-leather-products"><i
                                            class="fa-solid fa-vest"></i> Leather and Leather Products</a></li>
                                <li><a href="dynamic-product.php?slug=toys-and-sports-goods"><i
                                            class="fa-solid fa-puzzle-piece"></i> Toys and Sports Goods</a></li>
                                <li><a href="dynamic-product.php?slug=recyclate-and-recycled-products-food-grade"><i
                                            class="fa-solid fa-recycle"></i> Recyclate and Recycled Products (Food
                                        Grade)</a></li>
                                <li><a href="dynamic-product.php?slug=pharmaceuticals-products"><i
                                            class="fa-solid fa-pills"></i> Pharmaceuticals Products</a></li>
                                <li><a href="dynamic-product.php?slug=medical-devices-and-healthcare-products"><i
                                            class="fa-solid fa-heart-pulse"></i> Medical Devices and Healthcare
                                        Products</a></li>
                                <li><a
                                        href="dynamic-product.php?slug=dyes-paints-pigments-adhesives-and-surface-coatings"><i
                                            class="fa-solid fa-fill-drip"></i> Dyes, Paints, Pigments, Adhesives and
                                        Surface Coatings</a></li>
                                <li><a href="dynamic-product.php?slug=food-and-agricultural-products"><i
                                            class="fa-solid fa-wheat-awn"></i> Food and Agricultural Products</a></li>
                                <li><a href="dynamic-product.php?slug=water-and-packaged-drinking-water"><i
                                            class="fa-solid fa-bottle-water"></i> Water and Packaged Drinking Water</a>
                                </li>
                                <li><a href="dynamic-product.php?slug=electrical-and-electronics"><i
                                            class="fa-solid fa-bolt"></i> Electrical and Electronics</a></li>
                            </ul>
                        </li>

                        <li><a href="technical-services.php">Technical Services</a></li>
                        <li><a href="training-courses.php">Up-skilling Courses</a></li>
                        <!-- <li><a href="sustainability.php">Sustainability</a></li> (Hidden for now) -->
                        <li><a href="contact-us.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Action Button -->
                <div class="header-actions">
                    <a href="admin_dashboard.php" class="member-login">
                        <i class="fa-solid fa-lock"></i> Member Login
                    </a>
                    <button class="mobile-menu-toggle" id="menuToggleBtn" aria-label="Toggle Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Drawer -->
    <div class="mobile-menu-drawer" id="mobileMenu">
        <div class="drawer-header">
            <div class="drawer-logo">
                <a href="index.php">
                    <img src="assets/images/FINAL Logo.png" alt="AKC Logo">
                </a>
            </div>
            <button class="drawer-close" id="menuCloseBtn" aria-label="Close Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="drawer-content">
            <ul class="mobile-nav-links">
                <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a href="#">
                        About Us <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="about.php">About AKC-PTARC</a></li>
                        <li><a href="our-team.php">Our team</a></li>
                        <li><a href="#">Virtual tour</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">
                        Testing &amp; Certification <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="dynamic-product.php?slug=polymer-and-polymeric-products">Polymer & Polymeric
                                Products</a></li>
                        <li><a href="dynamic-product.php?slug=rubber-and-rubber-products">Rubber and Rubber Products</a>
                        </li>
                        <li><a href="dynamic-product.php?slug=textiles-and-textile-products">Textiles and Textile
                                Products</a></li>
                        <li><a href="dynamic-product.php?slug=leather-and-leather-products">Leather and Leather
                                Products</a></li>
                        <li><a href="dynamic-product.php?slug=toys-and-sports-goods">Toys and Sports Goods</a></li>
                        <li><a href="dynamic-product.php?slug=recyclate-and-recycled-products-food-grade">Recyclate and
                                Recycled Products (Food Grade)</a></li>
                        <li><a href="dynamic-product.php?slug=pharmaceuticals-products">Pharmaceuticals Products</a>
                        </li>
                        <li><a href="dynamic-product.php?slug=medical-devices-and-healthcare-products">Medical Devices
                                and Healthcare Products</a></li>
                        <li><a href="dynamic-product.php?slug=dyes-paints-pigments-adhesives-and-surface-coatings">Dyes,
                                Paints, Pigments, Adhesives and Surface Coatings</a></li>
                        <li><a href="dynamic-product.php?slug=food-and-agricultural-products">Food and Agricultural
                                Products</a></li>
                        <li><a href="dynamic-product.php?slug=water-and-packaged-drinking-water">Water and Packaged
                                Drinking Water</a></li>
                        <li><a href="dynamic-product.php?slug=electrical-and-electronics">Electrical and Electronics</a>
                        </li>
                    </ul>
                </li>

                <li><a href="technical-services.php">Technical Services</a></li>
                <li><a href="training-courses.php">Up-skilling Courses</a></li>
                <!-- <li><a href="sustainability.php">Sustainability</a></li> (Hidden for now) -->
                <li><a href="contact-us.php">Contact Us</a></li>
            </ul>
            <div class="drawer-actions">
                <a href="admin_dashboard.php" class="member-login">
                    <i class="fa-solid fa-lock"></i> Member Login
                </a>
            </div>
        </div>
    </div>
    <div class="mobile-menu-overlay" id="menuOverlay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('menuToggleBtn');
            const closeBtn = document.getElementById('menuCloseBtn');
            const drawer = document.getElementById('mobileMenu');
            const overlay = document.getElementById('menuOverlay');

            function openMenu() {
                drawer.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                drawer.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', openMenu);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeMenu);
            }

            if (overlay) {
                overlay.addEventListener('click', closeMenu);
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            document.querySelectorAll(".mobile-nav-links .dropdown > a").forEach(function (link) {

                link.addEventListener("click", function (e) {

                    if (window.innerWidth <= 991) {

                        e.preventDefault();

                        this.parentElement.classList.toggle("active");

                    }

                });

            });

        });
    </script>