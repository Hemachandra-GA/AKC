<?php
$pageTitle = "AKC | Polymer & Packaging Testing, Research & Certification - UFlex";
$metaDescription = "AKC is India's premier polymer and packaging research centre. NABL-accredited labs, advanced material characterisation, R&D, and industry training - backed by UFlex. Request a Test today.";
$metaKeywords = "polymer testing lab India, packaging material testing, NABL-accredited testing centre, chemical testing and certification, material characterisation, UFlex research centre";
include 'includes/header.php';
?>

<!-- Preload banner video for instantaneous playback with zero glitch -->
<link rel="preload" href="assets/videos/home-page-banner-fast.mp4" as="video" type="video/mp4">

<main>

    <!-- Hero Banner Section -->
    <section class="hero-banner">
        <!-- Right side video with overlay -->
        <div class="banner-image-area" style="background: #0d47a1;">
            <video autoplay loop muted playsinline webkit-playsinline="true" preload="auto" disablepictureinpicture disableremoteplayback style="opacity: 0; transition: opacity 0.35s ease-in; width: 100%; height: 100%; object-fit: cover;">
                <!-- Direct CDN MP4 stream: plays instantly on Vercel CDN & local -->
                <source src="assets/videos/home-page-banner-fast.mp4" type="video/mp4">
            </video>
            <script>
                // Instant video playback with zero static-image glitch
                (function() {
                    var v = document.querySelector('.banner-image-area video');
                    if (v) {
                        v.muted = true;
                        function reveal() {
                            v.style.opacity = '1';
                        }
                        function playAndReveal() {
                            reveal();
                            var p = v.play();
                            if (p !== undefined) p.catch(function() {});
                        }
                        v.addEventListener('playing', reveal, { once: true });
                        v.addEventListener('timeupdate', reveal, { once: true });
                        v.addEventListener('canplay', playAndReveal, { once: true });
                        v.addEventListener('loadeddata', playAndReveal, { once: true });
                        
                        // Immediate play kickoff
                        var promise = v.play();
                        if (promise !== undefined) {
                            promise.then(reveal).catch(function() {});
                        }
                        document.addEventListener('touchstart', playAndReveal, { once: true });
                        document.addEventListener('click', playAndReveal, { once: true });
                    }
                })();
            </script>
            <div class="banner-image-overlay"></div>
        </div>

        <!-- Content area -->
        <div class="banner-container">
            <div class="banner-content">
                <h1 style="font-size: 42px; font-weight: 600;">Where Precision Meets<br>Polymer Technology</h1>
                <p>India's most advanced packaging and polymer research centre - delivering NABL-accredited testing,
                    material characterisation, and industry-driven R&D solutions trusted by manufacturers across 15+
                    sectors.</p>
                <div class="hero-cta-wrapper" style="width: 295px; max-width: 100%; display: block;">
                    <div class="hero-actions" style="margin-bottom: 22px; width: 100%; display: block;">
                        <a href="testing-and-certification.php" class="btn-primary"
                            style="display: block !important; width: 100% !important; text-align: center !important; box-sizing: border-box !important; padding: 14px 15px !important; white-space: nowrap !important;">Explore
                            Our Testing Services</a>
                    </div>

                    <div class="trust-bar"
                        style="display: flex !important; justify-content: space-between !important; align-items: center !important; width: 100% !important; margin-top: 0 !important; gap: 0 !important; flex-wrap: nowrap !important; box-sizing: border-box !important;">
                        <img src="assets/images/iso-9001.png" alt="ISO 9001"
                            style="height: 52px !important; width: 52px !important; object-fit: contain !important; background: white !important; border-radius: 6px !important; padding: 4px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important; flex-shrink: 0 !important;">
                        <img src="assets/images/iso-14001.png" alt="ISO 14001"
                            style="height: 52px !important; width: 52px !important; object-fit: contain !important; background: white !important; border-radius: 6px !important; padding: 4px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important; flex-shrink: 0 !important;">
                        <img src="assets/images/iso-45001.png" alt="ISO 45001"
                            style="height: 52px !important; width: 52px !important; object-fit: contain !important; background: white !important; border-radius: 6px !important; padding: 4px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important; flex-shrink: 0 !important;">
                        <img src="assets/images/iso-21001.png" alt="ISO 21001"
                            style="height: 52px !important; width: 52px !important; object-fit: contain !important; background: white !important; border-radius: 6px !important; padding: 4px !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important; flex-shrink: 0 !important;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Universal Section Label & Quick Navigation Cards Styles -->
    <style>
        /* Exact Button & ISO Icons Vertical Alignment */
        .hero-cta-wrapper {
            width: 295px !important;
            max-width: 100% !important;
            display: block !important;
        }

        .hero-cta-wrapper .hero-actions {
            width: 100% !important;
            margin-bottom: 22px !important;
            display: block !important;
        }

        .hero-cta-wrapper .btn-primary {
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            box-sizing: border-box !important;
            padding: 14px 15px !important;
            white-space: nowrap !important;
        }

        .hero-cta-wrapper .trust-bar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
            margin-top: 0 !important;
            gap: 0 !important;
            flex-wrap: nowrap !important;
            box-sizing: border-box !important;
        }

        .hero-cta-wrapper .trust-bar img {
            height: 52px !important;
            width: 52px !important;
            object-fit: contain !important;
            background: white !important;
            border-radius: 6px !important;
            padding: 4px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
            flex-shrink: 0 !important;
        }

        @media (max-width: 480px) {
            .hero-cta-wrapper {
                width: 100% !important;
            }

            .hero-cta-wrapper .trust-bar img {
                height: 44px !important;
                width: 44px !important;
            }
        }

        /* Universal Section Label Style */
        .universal-section-label {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            letter-spacing: 1.5px !important;
            text-transform: uppercase !important;
            color: rgba(0, 0, 0, 0.6) !important;
            margin-top: 0 !important;
            margin-bottom: 8px !important;
            line-height: 1.2 !important;
        }

        .universal-section-label::before {
            content: "" !important;
            display: inline-block !important;
            width: 12px !important;
            height: 0 !important;
            border-top: 2px dashed #ff3b3b !important;
            flex-shrink: 0 !important;
        }

        .universal-section-label.dark,
        .universal-section-label.on-dark {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .universal-section-label.dark::before,
        .universal-section-label.on-dark::before {
            border-top: 2px dashed #ff3b3b !important;
        }

        /* Universal Section Heading Style */
        .universal-section-heading {
            font-size: 32px !important;
            font-weight: 600 !important;
            color: #081D33;
            line-height: 1.25 !important;
            letter-spacing: normal !important;
            text-transform: none !important;
            margin-top: 0 !important;
            margin-bottom: 8px !important;
        }

        .universal-section-heading.on-dark {
            color: #ffffff !important;
        }

        .universal-section-heading+p,
        .universal-section-heading+div,
        .universal-section-heading+.section-desc {
            margin-top: 0 !important;
        }


        @media (max-width: 991px) {
            .about-line-break {
                display: none !important;
            }
        }

        @media (max-width: 900px) {
            .service-card .card-hover-content {
                display: none !important;
            }
        }

        .mou-header-box {
            border-radius: 12px;
            padding: 15px 25px;
            width: 100%;
            flex-shrink: 0;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 15px;
            color: white;
            margin: 0 auto;
        }

        .mou-header-box.blue {
            background: #1e40af;
        }

        .mou-header-box.green {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
        }

        .mou-header-box i {
            font-size: 32px;
        }

        .mou-header-box h3 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            text-align: left;
            line-height: 1.2;
        }

        @media (min-width: 768px) {
            .mou-header-box {
                max-width: 250px;
                flex-direction: column;
                padding: 30px;
            }

            .mou-header-box i {
                font-size: 40px;
                margin-bottom: 15px;
            }

            .mou-header-box h3 {
                text-align: center;
            }
        }
    </style>
    <section class="services-section" id="services">
        <div class="services-container reveal-group">
            <div class="service-card reveal-child" onclick="window.location.href='testing-and-certification.php';"
                style="cursor: pointer;">
                <div class="card-bg"><img src="assets/images/testing-qc.jpg" alt="Testing QC Analysis"></div>
                <div class="card-overlay"></div>
                <div class="card-content">
                    <div class="card-icon"><i class="fa-solid fa-microscope"></i></div>
                    <h3 style="font-size: 24px; font-weight: 500;">Testing & Certification</h3>
                    <div class="card-hover-content">
                        <p>Comprehensive chemical, mechanical, and physical testing for polymers, packaging, and
                            industrial materials - delivered with precision and NABL-backed accuracy.</p>
                        <a href="testing-and-certification.php">View Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="service-card reveal-child" onclick="window.location.href='technical-services.php';"
                style="cursor: pointer;">
                <div class="card-bg"><img src="assets/images/support-services.jpg" alt="Support Services"></div>
                <div class="card-overlay"></div>
                <div class="card-content">
                    <div class="card-icon"><i class="fa-solid fa-phone-volume"></i></div>
                    <h3 style="font-size: 24px; font-weight: 500;">Testing, Quality Control and Certifications</h3>
                    <div class="card-hover-content">
                        <p>End-to-end technical support including consultancy, regulatory compliance guidance, and
                            industrial problem-solving for manufacturers and R&D teams.</p>
                        <a href="technical-services.php">View Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="service-card reveal-child" onclick="window.location.href='training-courses.php';"
                style="cursor: pointer;">
                <div class="card-bg"><img src="assets/images/education-training.jpg" alt="Education Training"></div>
                <div class="card-overlay"></div>
                <div class="card-content">
                    <div class="card-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3 style="font-size: 24px; font-weight: 500;">Up-skilling, Academic & Applied Research</h3>
                    <div class="card-hover-content">
                        <p>Industry-relevant training programmes in polymer science, quality control, and advanced
                            testing methodologies - designed for professionals and academic institutions.</p>
                        <a href="training-courses.php">View Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About AKC Section -->
    <section class="about-section" style="padding: 95px 20px; background: white;">
        <div class="about-container reveal" style="text-align: center; max-width: 1080px; margin: 0 auto;">
            <div class="universal-section-label">WHO WE ARE</div>
            <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">Built by Industry, Designed
                for Industry</h2>
            <div class="about-content" style="font-size: 16px; font-weight: 400; line-height: 1.7; color: #555;">
                <p
                    style="font-size: 16px !important; font-weight: 400; line-height: 1.7; color: #555; margin-bottom: 22px;">
                    AKC is established to bridge the gap between industrial demands and advanced scientific research.<br
                        class="about-line-break"> We are India's first integrated centre combining polymer testing,
                    quality assurance, R&D, and skill development under one roof.</p>
                <p
                    style="font-size: 16px !important; font-weight: 400; line-height: 1.7; color: #555; margin-bottom: 35px; max-width: 900px; margin-left: auto; margin-right: auto;">
                    PTARC aims to evolve from an advanced R&D and testing laboratory into an internationally recognized
                    Institute and global centre of excellence in polymer, packaging, and material science.</p>
                <a href="about.php" class="btn-primary"
                    style="background: #0d47a1; color: white; padding: 16px 40px; border-radius: 6px; text-decoration: none; display: inline-block; font-weight: 500; font-size: 16px;">Learn
                    More About Us</a>
            </div>
        </div>
    </section>

    <!-- Statistics Strip -->
    <section class="statistics-strip" style="background: #f4f7fb; padding: 95px 20px;">
        <div class="reveal" style="text-align: center; margin-bottom: 55px;">
            <div class="universal-section-label">QUALITY CONTROL &amp; TESTING</div>
            <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">NABL Scope</h2>
            <p
                style="font-size: 16px; font-weight: 400; color: #64748b; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                Comprehensive testing and evaluation capabilities across chemical, mechanical, biological and electrical
                domains.</p>
        </div>
        <div class="stats-container reveal-group"
            style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; text-align: center;">

            <div class="reveal-child"
                style="background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 40px 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-bottom: 4px solid var(--primary-blue); transition: transform 0.3s ease;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div
                    style="width: 65px; height: 65px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; box-shadow: 0 4px 10px rgba(0,86,179,0.3);">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
                <h3 style="font-size: 36px; color: var(--primary-blue); margin-bottom: 5px; font-weight: 600;"><span
                        class="stat-number" data-target="1400">1400</span></h3>
                <p
                    style="font-size: 12px; color: var(--text-gray); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                    Tests Conducted For</p>
                <h4 style="font-size: 16px; color: var(--text-dark); font-weight: 500; line-height: 1.4; margin: 0;">
                    Chemical
                    Testing & Characterisation</h4>
            </div>

            <div class="reveal-child"
                style="background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 40px 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-bottom: 4px solid var(--primary-blue); transition: transform 0.3s ease;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div
                    style="width: 65px; height: 65px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; box-shadow: 0 4px 10px rgba(0,86,179,0.3);">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <h3 style="font-size: 36px; color: var(--primary-blue); margin-bottom: 5px; font-weight: 600;"><span
                        class="stat-number" data-target="6234">6234</span></h3>
                <p
                    style="font-size: 12px; color: var(--text-gray); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                    Tests Conducted For</p>
                <h4 style="font-size: 16px; color: var(--text-dark); font-weight: 500; line-height: 1.4; margin: 0;">
                    Mechanical
                    Testing & Performance Evaluation</h4>
            </div>

            <div class="reveal-child"
                style="background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 40px 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-bottom: 4px solid var(--primary-blue); transition: transform 0.3s ease;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div
                    style="width: 65px; height: 65px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; box-shadow: 0 4px 10px rgba(0,86,179,0.3);">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h3 style="font-size: 36px; color: var(--primary-blue); margin-bottom: 5px; font-weight: 600;"><span
                        class="stat-number" data-target="9026">9026</span></h3>
                <p
                    style="font-size: 12px; color: var(--text-gray); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                    Tests Conducted For</p>
                <h4 style="font-size: 16px; color: var(--text-dark); font-weight: 500; line-height: 1.4; margin: 0;">
                    Biological &
                    Biodegradation Assessment</h4>
            </div>

            <div class="reveal-child"
                style="background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 40px 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-bottom: 4px solid var(--primary-blue); transition: transform 0.3s ease;"
                onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div
                    style="width: 65px; height: 65px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 20px; box-shadow: 0 4px 10px rgba(0,86,179,0.3);">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 style="font-size: 36px; color: var(--primary-blue); margin-bottom: 5px; font-weight: 600;"><span
                        class="stat-number" data-target="1200">1200</span></h3>
                <p
                    style="font-size: 12px; color: var(--text-gray); font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                    Tests Conducted For</p>
                <h4 style="font-size: 16px; color: var(--text-dark); font-weight: 500; line-height: 1.4; margin: 0;">
                    Electrical
                    Testing<br><span
                        style="font-size: 13px; font-weight: 500; opacity: 0.9; color: var(--text-gray);">(Under
                        process)</span>
                </h4>
            </div>

        </div>
    </section>

    <!-- Categories / Products We Cater To Section -->
    <section class="categories-section" style="padding: 95px 20px 85px; background: #ffffff;">
        <div class="categories-container" style="max-width: 1200px; margin: 0 auto;">

            <div class="section-header reveal" style="text-align: center; margin-bottom: 45px;">
                <div class="universal-section-label">PRODUCTS WE CATER TO</div>
                <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">Testing & Analysis
                    Across Every Material Category</h2>
                <p
                    style="font-size: 16px; font-weight: 400; color: #555; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                    From polymers and packaging to textiles, rubber, and medical devices - our certified labs are
                    equipped for precision testing across all scientific disciplines.</p>
            </div>

            <!-- Products List / Badges -->
            <div class="tabs-nav reveal"
                style="display: flex; justify-content: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
                <a href="dynamic-product.php?slug=plastics-and-plastic-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-flask"></i> Plastics and Plastic Products</a>
                <a href="dynamic-product.php?slug=rubber-and-rubber-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-ring"></i> Rubber and Rubber Products</a>
                <a href="dynamic-product.php?slug=textiles-and-textile-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-shirt"></i> Textiles and Textile Products</a>
                <a href="dynamic-product.php?slug=leather-and-leather-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-shoe-prints"></i> Leather and Leather
                    Products</a>
                <a href="dynamic-product.php?slug=toys-and-sports-goods" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-gamepad"></i> Toys and Sports Goods</a>
                <a href="dynamic-product.php?slug=recyclate-and-recycled-products-food-grade" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-recycle"></i> Recyclate and Recycled Products
                    (Food Grade)</a>
                <a href="dynamic-product.php?slug=pharmaceuticals-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-pills"></i> Pharmaceuticals Products</a>
                <a href="dynamic-product.php?slug=medical-devices-and-healthcare-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-heart-pulse"></i> Medical Devices and
                    Healthcare Products</a>
                <a href="dynamic-product.php?slug=dyes-paints-pigments-adhesives-and-surface-coatings" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-fill-drip"></i> Dyes, Paints, Pigments,
                    Adhesives and Surface Coatings</a>
                <a href="dynamic-product.php?slug=food-and-agricultural-products" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-wheat-awn"></i> Food and Agricultural
                    Products</a>
                <a href="dynamic-product.php?slug=water-and-packaged-drinking-water" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-bottle-water"></i> Water and Packaged Drinking
                    Water</a>
                <a href="dynamic-product.php?slug=electrical-and-electronics" class="tab-btn"
                    style="text-decoration: none;"><i class="fa-solid fa-bolt"></i> Electrical and Electronics</a>
            </div>
        </div>
    </section>

    <!-- We are certified by Section -->
    <style>
        .cert-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .cert-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12) !important;
        }

        .cert-card:hover img {
            transform: scale(1.02);
        }
    </style>
    <section class="certified-by-section"
        style="padding: 95px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; text-align: center;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <div class="reveal" style="margin-bottom: 50px;">
                <div class="universal-section-label">
                    ACCREDITATIONS &amp; CERTIFICATIONS
                </div>
                <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">We Are Certified By
                </h2>
                <p
                    style="font-size: 16px; font-weight: 400; color: #64748b; max-width: 750px; margin: 0 auto; line-height: 1.6;">
                    Following ISO certifications are acquired by AKC-PTARC for their respective areas</p>
            </div>

            <div class="certificate-grid reveal-group"
                style="display: flex; justify-content: center; align-items: stretch; gap: 28px; flex-wrap: wrap;">
                <!-- ISO 9001 -->
                <div class="cert-card reveal-child"
                    onclick="openCertModal('assets/images/certificates/AKC PTARC_ISO 9001_page-0001.jpg', 'ISO 9001:2015 Certificate - Quality Management System')"
                    style="background: #ffffff; padding: 15px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; width: 255px; cursor: pointer;">
                    <div
                        style="width: 100%; height: 330px; overflow: hidden; border-radius: 8px; border: 1px solid #eef2f6; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <img src="assets/images/certificates/AKC PTARC_ISO 9001_page-0001.jpg"
                            alt="ISO 9001:2015 Certificate"
                            style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.3s ease;">
                    </div>
                    <h4 style="font-size: 17px; font-weight: 500; color: #0f172a; margin: 0 0 5px 0;">ISO 9001:2015</h4>
                    <span style="font-size: 14px; color: #64748b; font-weight: 400; margin-bottom: 10px;">Quality
                        Management</span>
                    <span
                        style="font-size: 14px; color: var(--primary-blue); font-weight: 500; display: flex; align-items: center; gap: 5px;"><i
                            class="fa-solid fa-magnifying-glass-plus"></i> View Certificate</span>
                </div>

                <!-- ISO 14001 -->
                <div class="cert-card reveal-child"
                    onclick="openCertModal('assets/images/certificates/AKC PTARC_ISO 14001_page-0001.jpg', 'ISO 14001:2015 Certificate - Environmental Management System')"
                    style="background: #ffffff; padding: 15px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; width: 255px; cursor: pointer;">
                    <div
                        style="width: 100%; height: 330px; overflow: hidden; border-radius: 8px; border: 1px solid #eef2f6; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <img src="assets/images/certificates/AKC PTARC_ISO 14001_page-0001.jpg"
                            alt="ISO 14001:2015 Certificate"
                            style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.3s ease;">
                    </div>
                    <h4 style="font-size: 17px; font-weight: 500; color: #0f172a; margin: 0 0 5px 0;">ISO 14001:2015
                    </h4>
                    <span style="font-size: 14px; color: #64748b; font-weight: 400; margin-bottom: 10px;">Environmental
                        Management</span>
                    <span
                        style="font-size: 14px; color: var(--primary-blue); font-weight: 500; display: flex; align-items: center; gap: 5px;"><i
                            class="fa-solid fa-magnifying-glass-plus"></i> View Certificate</span>
                </div>

                <!-- ISO 45001 -->
                <div class="cert-card reveal-child"
                    onclick="openCertModal('assets/images/certificates/AKC PTARC_ISO 45001_page-0001.jpg', 'ISO 45001:2018 Certificate - Occupational Health & Safety Management')"
                    style="background: #ffffff; padding: 15px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; width: 255px; cursor: pointer;">
                    <div
                        style="width: 100%; height: 330px; overflow: hidden; border-radius: 8px; border: 1px solid #eef2f6; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <img src="assets/images/certificates/AKC PTARC_ISO 45001_page-0001.jpg"
                            alt="ISO 45001:2018 Certificate"
                            style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.3s ease;">
                    </div>
                    <h4 style="font-size: 17px; font-weight: 500; color: #0f172a; margin: 0 0 5px 0;">ISO 45001:2018
                    </h4>
                    <span style="font-size: 14px; color: #64748b; font-weight: 400; margin-bottom: 10px;">Occupational
                        Health &amp; Safety</span>
                    <span
                        style="font-size: 14px; color: var(--primary-blue); font-weight: 500; display: flex; align-items: center; gap: 5px;"><i
                            class="fa-solid fa-magnifying-glass-plus"></i> View Certificate</span>
                </div>

                <!-- ISO 21001 -->
                <div class="cert-card reveal-child"
                    onclick="openCertModal('assets/images/certificates/AKC PTARC_ISO 21001_page-0001.jpg', 'ISO 21001:2018 Certificate - Educational Organizations Management')"
                    style="background: #ffffff; padding: 15px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; width: 255px; cursor: pointer;">
                    <div
                        style="width: 100%; height: 330px; overflow: hidden; border-radius: 8px; border: 1px solid #eef2f6; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <img src="assets/images/certificates/AKC PTARC_ISO 21001_page-0001.jpg"
                            alt="ISO 21001:2018 Certificate"
                            style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.3s ease;">
                    </div>
                    <h4 style="font-size: 17px; font-weight: 500; color: #0f172a; margin: 0 0 5px 0;">ISO 21001:2018
                    </h4>
                    <span style="font-size: 14px; color: #64748b; font-weight: 400; margin-bottom: 10px;">Educational
                        Organizations</span>
                    <span
                        style="font-size: 14px; color: var(--primary-blue); font-weight: 500; display: flex; align-items: center; gap: 5px;"><i
                            class="fa-solid fa-magnifying-glass-plus"></i> View Certificate</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Certificate Lightbox Modal -->
    <div id="certLightboxModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 99999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(5px);"
        onclick="closeCertModal(event)">
        <div style="background: white; border-radius: 12px; max-width: 850px; width: 100%; max-height: 95vh; display: flex; flex-direction: column; position: relative; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.4);"
            onclick="event.stopPropagation()">
            <div
                style="padding: 15px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="certModalTitle" style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">
                    Certificate</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <a id="certModalOpenBtn" href="#" target="_blank"
                        style="padding: 6px 14px; background: var(--primary-blue); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;"><i
                            class="fa-solid fa-arrow-up-right-from-square"></i> Open Full Image</a>
                    <button onclick="closeCertModal()"
                        style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; line-height: 1;"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            <div style="padding: 20px; overflow-y: auto; text-align: center; background: #f8fafc;">
                <img id="certModalImg" src="" alt="Certificate Full Preview"
                    style="max-width: 100%; height: auto; max-height: 75vh; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>

    <script>
        function openCertModal(imgSrc, title) {
            document.getElementById('certModalImg').src = imgSrc;
            document.getElementById('certModalTitle').innerText = title;
            document.getElementById('certModalOpenBtn').href = imgSrc;
            const modal = document.getElementById('certLightboxModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeCertModal(e) {
            if (!e || e.target.id === 'certLightboxModal' || e.target.closest('button')) {
                document.getElementById('certLightboxModal').style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>

    <!-- Accredited Library & Sectors We Support Section -->
    <section class="industries-section" style="background: #f4f7fb; padding: 95px 20px;">
        <div class="industries-container" style="max-width: 1200px; margin: 0 auto; text-align: center;">
            <?php
            $managementStandards = [
                ['icon' => 'fa-solid fa-gear', 'code' => 'ISO 9001:2015'],
                ['icon' => 'fa-solid fa-leaf', 'code' => 'ISO 14001:2015'],
                ['icon' => 'fa-solid fa-helmet-safety', 'code' => 'ISO 45001:2018'],
                ['icon' => 'fa-solid fa-utensils', 'code' => 'ISO 22000:2018'],
                ['icon' => 'fa-solid fa-lock', 'code' => 'ISO/IEC 27001:2022'],
                ['icon' => 'fa-solid fa-desktop', 'code' => 'ISO 20000-1:2018'],
                ['icon' => 'fa-solid fa-plus-square', 'code' => 'ISO/IEC 13485:2016'],
                ['icon' => 'fa-solid fa-bolt', 'code' => 'ISO 50001:2018'],
                ['icon' => 'fa-solid fa-brain', 'code' => 'ISO 42001:2023'],
                ['icon' => 'fa-solid fa-user-shield', 'code' => 'ISO 27701:2019'],
                ['icon' => 'fa-solid fa-handshake-slash', 'code' => 'ISO 37001:2016'],
                ['icon' => 'fa-solid fa-network-wired', 'code' => 'ISO/IEC 22301:2019'],
                ['icon' => 'fa-solid fa-building', 'code' => 'ISO 41001:2018'],
                ['icon' => 'fa-solid fa-graduation-cap', 'code' => 'ISO 21001:2018'],
                ['icon' => 'fa-solid fa-database', 'code' => 'ISO 55001:2014'],
                ['icon' => 'fa-solid fa-chart-line', 'code' => 'ISO 31001:2018'],
                ['icon' => 'fa-solid fa-car', 'code' => 'IATF 16949:2016'],
                ['icon' => 'fa-solid fa-wheat-awn', 'code' => 'FSSC 22000'],
                ['icon' => 'fa-solid fa-bottle-droplet', 'code' => 'ISO 22716'],
                ['icon' => 'fa-solid fa-server', 'code' => 'ISO 16363:2025'],
                ['icon' => 'fa-solid fa-plane', 'code' => 'AS 9100'],
            ];

            // Assign uniform theme colors
            foreach ($managementStandards as &$std) {
                $std['color'] = '#0d47a1'; // Primary dark blue
                $std['bg'] = '#f1f5f9'; // Light slate background
            }
            unset($std);

            $academicAccreditations = [
                ['code' => 'NAAC', 'name' => 'National Assessment and Accreditation Council', 'icon' => 'fa-solid fa-award'],
                ['code' => 'NBA', 'name' => 'National Board of Accreditation', 'icon' => 'fa-solid fa-certificate'],
                ['code' => 'AICTE', 'name' => 'All India Council for Technical Education', 'icon' => 'fa-solid fa-cogs'],
                ['code' => 'NIRF', 'name' => 'National Institutional Ranking Framework', 'icon' => 'fa-solid fa-ranking-star'],
                ['code' => 'IQAC', 'name' => 'Internal Quality Assurance Cell', 'icon' => 'fa-solid fa-user-graduate'],
                ['code' => 'EQAC', 'name' => 'External Quality Assurance Cell', 'icon' => 'fa-solid fa-file-signature'],
                ['code' => 'ACBSP', 'name' => 'Accreditation Council for Business Schools and Programs', 'icon' => 'fa-solid fa-briefcase'],
                ['code' => 'ASHE', 'name' => 'Association for the Study of Higher Education', 'icon' => 'fa-solid fa-book-open'],
                ['code' => 'ACSC', 'name' => 'Accreditation Council for Skills and Competencies', 'icon' => 'fa-solid fa-globe'],
                ['code' => 'NEP 2020', 'name' => 'National Education Policy 2020', 'icon' => 'fa-solid fa-book-open-reader'],
            ];

            foreach ($academicAccreditations as &$acc) {
                $acc['color'] = '#0284c7'; // Academic accreditation accent color
            }
            unset($acc);
            ?>
            <div class="akc-standards-section" style="margin-top: 0; text-align: left;">
                <!-- Main Section Header -->
                <div class="reveal" style="text-align: center; margin-bottom: 50px;">
                    <div class="universal-section-label">
                        MANAGEMENT SYSTEMS &amp; ACCREDITATIONS
                    </div>
                    <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">
                        Management System Audit, Training &amp; Certification Services
                    </h2>

                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 35px; align-items: start;">

                    <!-- Left Column: Management System Standards -->
                    <div class="akc-mgt-card reveal-left"
                        style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="background: #0d47a1; color: white; padding: 12px 20px; text-align: center;">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: white;">Our Management
                                System Standards</h3>
                        </div>

                        <div style="padding: 15px 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px 12px;">
                                <?php foreach ($managementStandards as $std): ?>
                                    <div style="position: relative; width: 100%; height: 36px; transition: transform 0.2s ease; cursor: default;"
                                        onmouseover="this.style.transform='translateX(3px)';"
                                        onmouseout="this.style.transform='none';">
                                        <!-- Icon Section -->
                                        <div
                                            style="position: absolute; left: 0; top: 0; width: 45px; height: 100%; background: <?php echo $std['color']; ?>; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 50%, calc(100% - 10px) 100%, 0 100%); border-radius: 16px 0 0 16px; z-index: 2;">
                                            <i class="<?php echo $std['icon']; ?>" style="margin-right: 4px;"></i>
                                        </div>
                                        <!-- Text Section -->
                                        <div
                                            style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: <?php echo $std['bg']; ?>; display: flex; align-items: center; padding-left: 52px; clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 50%, calc(100% - 12px) 100%, 0 100%); border-radius: 16px 0 0 16px; font-weight: 500; color: #1e293b; font-size: 0.85rem; z-index: 1;">
                                            <?php echo $std['code']; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Academic Accreditation -->
                    <div class="akc-acad-card reveal-right"
                        style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div class="card-header"
                            style="background: #0284c7; color: #ffffff; padding: 12px 20px; text-align: center;">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #ffffff;">Academic
                                Accreditation, Certification &amp; Implementation</h3>
                        </div>
                        <div class="card-sub-banner"
                            style="background: #f0f9ff; color: #0369a1; text-align: center; padding: 10px 20px; font-size: 12px; font-weight: 500; border-bottom: 1px solid #bae6fd;">
                            Supporting institutions for quality education, research and academic excellence
                        </div>

                        <div style="padding: 20px;">
                            <div class="akc-acad-grid"
                                style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <?php foreach ($academicAccreditations as $acc): ?>
                                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 15px; display: flex; align-items: center; gap: 12px; background: white; transition: all 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.02);"
                                        onmouseover="this.style.boxShadow='0 4px 10px rgba(0,0,0,0.06)'; this.style.borderColor='#cbd5e1';"
                                        onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.02)'; this.style.borderColor='#e2e8f0';">
                                        <div class="icon"
                                            style="display: flex; justify-content: center; align-items: center; color: <?php echo $acc['color']; ?>; font-size: 1.5rem; flex-shrink: 0; width: 35px;">
                                            <i class="<?php echo $acc['icon']; ?>"></i>
                                        </div>
                                        <div>
                                            <div
                                                style="font-weight: 600; color: #0f172a; font-size: 0.95rem; line-height: 1.2; text-align: left;">
                                                <?php echo $acc['code']; ?>
                                            </div>
                                            <div
                                                style="font-size: 0.65rem; color: #64748b; margin-top: 3px; line-height: 1.3; text-align: left;">
                                                <?php echo $acc['name']; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Add responsive styles -->
            <style>
                /* Academic Accreditation, Certification & Implementation Card */
                .akc-acad-card .card-header {
                    background: #0284c7 !important;
                    color: #ffffff !important;
                }

                .akc-acad-card .card-sub-banner {
                    background: #f0f9ff !important;
                    color: #0369a1 !important;
                    border-bottom: 1px solid #bae6fd !important;
                }

                .akc-acad-card .icon,
                .akc-acad-card .icon i {
                    color: #0284c7 !important;
                }

                @media (max-width: 992px) {
                    .akc-standards-section>div:nth-child(2) {
                        grid-template-columns: 1fr !important;
                    }
                }

                @media (max-width: 576px) {
                    .akc-acad-grid {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>
        </div>
    </section>

    <!-- Support / CTA Full-Width Section -->
    <section style="position: relative; min-height: 520px; overflow: hidden; display: flex; align-items: center;">

        <!-- Full-Width Background Image -->
        <img src="assets/images/support-services.jpg" alt="Technical Support"
            style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">

        <!-- Dark Gradient Overlay (stronger on left) -->
        <div
            style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(8,29,51,0.92) 0%, rgba(8,29,51,0.75) 45%, rgba(8,29,51,0.15) 100%); z-index: 1;">
        </div>

        <!-- Text Content (Left Side) -->
        <div class="reveal"
            style="position: relative; z-index: 2; max-width: 1200px; width: 100%; margin: 0 auto; padding: 95px 5%;">
            <div style="text-align: center;">
                <div class="universal-section-label on-dark">
                    SUPPORT SERVICES
                </div>
                <h2 class="universal-section-heading on-dark"
                    style="margin-top: 0; margin-bottom: 8px; color: #ffffff !important;">
                    Technical Support
                </h2>
                <p
                    style="font-size: 16px; font-weight: 400; line-height: 1.6; color: rgba(255,255,255,0.85); margin-bottom: 35px; max-width: 800px; margin-left: auto; margin-right: auto;">
                    Our support teams go beyond issuing reports. We provide in-depth technical consultation, help you
                    navigate regulatory compliance, and work alongside your teams to resolve complex material and
                    process challenges.
                </p>
                <a href="technical-services.php"
                    style="display: inline-block; padding: 14px 32px; background: #0A58CA; color: white; font-weight: 500; font-size: 16px; border-radius: 6px; text-decoration: none;"
                    onmouseover="this.style.background='#0d6efd'" onmouseout="this.style.background='#0A58CA'">
                    View Services
                </a>
            </div>
        </div>

    </section>

    <!-- Our Facility Section -->
    <section class="facility-section" style="padding: 95px 20px; background: white;">
        <div class="facility-container reveal" style="max-width: 1000px; margin: 0 auto; text-align: center;">
            <div class="universal-section-label">STATE-OF-THE-ART INFRASTRUCTURE</div>
            <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">Labs Built for Accuracy.
                Equipped for Tomorrow.</h2>
            <p style="font-size: 16px; font-weight: 400; color: #555; line-height: 1.7;">AKC operates one of India's
                most comprehensively
                equipped research and testing facilities, featuring advanced instrumentation across chemical,
                mechanical, thermal, electrical, and optical testing domains. Our laboratories are
                <strong>NABL-accredited</strong> and <strong>ISO 17025 compliant</strong>, ensuring accurate, reliable,
                and internationally recognized results across diverse industry sectors.
            </p>
        </div>
    </section>

    <!-- Education & Training Section -->
    <section class="training-banner" style="padding: 95px 20px; text-align: center;">
        <div class="training-container reveal" style="max-width: 900px; margin: 0 auto;">
            <div class="training-content" style="text-align: center; margin: 0 auto;">
                <div class="universal-section-label on-dark">
                    BUILD CAPABILITY
                </div>
                <h2 class="universal-section-heading on-dark" style="margin-top: 0; margin-bottom: 8px;">Training
                    Programmes That Create
                    Industry-Ready Experts</h2>
                <p
                    style="font-size: 16px; font-weight: 400; line-height: 1.65; margin-bottom: 35px; color: rgba(255,255,255,0.9);">
                    Our
                    professional training courses are designed in collaboration with industry experts and academic
                    partners. From foundational polymer science to advanced quality assurance, our programmes prepare
                    engineers, technicians, and researchers to perform at the highest level.</p>
                <a href="training-courses.php" class="btn-view-courses"
                    style="display: inline-block; padding: 14px 35px; background: #0d47a1; color: white; font-weight: 500; font-size: 16px; border-radius: 6px; text-decoration: none;">View
                    Training Courses</a>
            </div>
        </div>
    </section>

    <!-- Vision Section -->
    <section class="vision-section" style="padding: 95px 20px; background: #fff; border-bottom: 1px solid #eee;">
        <div style="max-width: 1200px; margin: 0 auto;">

            <!-- Logos and Title -->
            <div class="reveal" style="text-align: center; margin-bottom: 40px;">
                <div class="universal-section-label">VISION &amp; PURPOSE</div>
                <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">Vision</h2>
            </div>

            <!-- Vision Quote Box -->
            <div class="reveal-scale"
                style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); border-radius: 20px; padding: 55px 40px; position: relative; overflow: hidden; margin-bottom: 50px; box-shadow: 0 10px 30px rgba(0,86,179,0.08); text-align: center; border: 1px solid rgba(0,86,179,0.1);">
                <i class="fa-solid fa-quote-left"
                    style="position: absolute; top: 20px; left: 30px; font-size: 60px; color: rgba(0,86,179,0.15);"></i>

                <!-- Watermark Text -->
                <!--<div
                    style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%) rotate(-90deg); opacity: 0.05; font-size: 50px; font-weight: 900; color: var(--primary-blue); white-space: nowrap; pointer-events: none;">
                    INNOVATION &nbsp; TESTING &nbsp; SKILLS &nbsp; SUSTAINABILITY
                </div>-->

                <p
                    style="font-size: 18px; color: var(--primary-blue); font-weight: 500; line-height: 1.6; max-width: 900px; margin: 0 auto; position: relative; z-index: 1;">
                    To be a <strong style="font-weight: 600; font-size: 18px;">globally recognized centre of
                        excellence</strong> in polymer, packaging, and material science, driving innovation,
                    sustainability and industrial growth through cutting-edge research, testing, training and
                    collaborative solutions.
                </p>

                <i class="fa-solid fa-quote-right"
                    style="position: absolute; bottom: 20px; right: 30px; font-size: 60px; color: rgba(0,86,179,0.15);"></i>
            </div>

            <!-- 4 Pillars Cards -->
            <div class="reveal-group"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px;">

                <!-- Card 1 -->
                <div class="reveal-child"
                    style="background: linear-gradient(145deg, var(--primary-blue) 0%, #003a8c 100%); color: white; border-radius: 12px; padding: 30px 20px; text-align: center; box-shadow: 0 8px 20px rgba(0,86,179,0.25); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; align-items: center;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 25px rgba(0,86,179,0.35)';"
                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 20px rgba(0,86,179,0.25)';">
                    <div
                        style="background: white; color: var(--primary-blue); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3
                        style="font-size: 16px; font-weight: 500; color: #ffffff; min-height: 44px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; line-height: 1.3;">
                        Quality Control, Testing &amp; Certification</h3>
                    <p
                        style="font-size: 14px; margin: 0; color: rgba(255, 255, 255, 0.88) !important; line-height: 1.5; font-weight: 400;">
                        Reliable
                        testing. Trusted quality. Global compliance.</p>
                </div>

                <!-- Card 2 -->
                <div class="reveal-child"
                    style="background: linear-gradient(145deg, var(--primary-blue) 0%, #003a8c 100%); color: white; border-radius: 12px; padding: 30px 20px; text-align: center; box-shadow: 0 8px 20px rgba(0,86,179,0.25); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; align-items: center;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 25px rgba(0,86,179,0.35)';"
                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 20px rgba(0,86,179,0.25)';">
                    <div
                        style="background: white; color: var(--primary-blue); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-microscope"></i>
                    </div>
                    <h3
                        style="font-size: 16px; font-weight: 500; color: #ffffff; min-height: 44px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; line-height: 1.3;">
                        Applied Research</h3>
                    <p
                        style="font-size: 14px; margin: 0; color: rgba(255, 255, 255, 0.88) !important; line-height: 1.5; font-weight: 400;">
                        Innovative
                        materials. Real-world solutions.</p>
                </div>

                <!-- Card 3 -->
                <div class="reveal-child"
                    style="background: linear-gradient(145deg, var(--primary-blue) 0%, #003a8c 100%); color: white; border-radius: 12px; padding: 30px 20px; text-align: center; box-shadow: 0 8px 20px rgba(0,86,179,0.25); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; align-items: center;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 25px rgba(0,86,179,0.35)';"
                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 20px rgba(0,86,179,0.25)';">
                    <div
                        style="background: white; color: var(--primary-blue); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3
                        style="font-size: 16px; font-weight: 500; color: #ffffff; min-height: 44px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; line-height: 1.3;">
                        Academic &amp; Upskilling</h3>
                    <p
                        style="font-size: 14px; margin: 0; color: rgba(255, 255, 255, 0.88) !important; line-height: 1.5; font-weight: 400;">
                        Knowledge
                        today. Skilled tomorrow.</p>
                </div>

                <!-- Card 4 -->
                <div class="reveal-child"
                    style="background: linear-gradient(145deg, var(--primary-blue) 0%, #003a8c 100%); color: white; border-radius: 12px; padding: 30px 20px; text-align: center; box-shadow: 0 8px 20px rgba(0,86,179,0.25); transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; align-items: center;"
                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 25px rgba(0,86,179,0.35)';"
                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 20px rgba(0,86,179,0.25)';">
                    <div
                        style="background: white; color: var(--primary-blue); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 20px; flex-shrink: 0;">
                        <i class="fa-solid fa-cogs"></i>
                    </div>
                    <h3
                        style="font-size: 16px; font-weight: 500; color: #ffffff; min-height: 44px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; line-height: 1.3;">
                        Technical Consultancy &amp; Services</h3>
                    <p
                        style="font-size: 14px; margin: 0; color: rgba(255, 255, 255, 0.88) !important; line-height: 1.5; font-weight: 400;">
                        Expertise
                        for a stronger industry.</p>
                </div>

            </div>
        </div>
    </section>


    <!-- Compliance Section -->
    <section class="compliance-section" style="padding: 95px 20px; background: #f4f7fb;">
        <div style="max-width: 1200px; margin: 0 auto;">

            <!-- Logos and Title -->
            <div class="reveal"
                style="display: flex; justify-content: center; align-items: center; margin-bottom: 55px;">
                <div style="text-align: center; flex: 1; padding: 0 20px;">
                    <div class="universal-section-label">REGULATORY &amp; ACCREDITATION</div>
                    <h2 class="universal-section-heading" style="margin: 0 0 8px 0;">Regulatory,
                        Accreditation &amp; Recognition Bodies</h2>
                    <p
                        style="color: #64748b; font-size: 16px; margin: 0; font-weight: 400; text-transform: none; letter-spacing: normal;">
                        Ensuring Compliance, Quality and Global Acceptance</p>
                </div>
            </div>

            <!-- Block 1: Regulatory Frameworks -->
            <div class="reveal"
                style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 45px; border: 1px solid #e2e8f0;">
                <div
                    style="background: linear-gradient(90deg, var(--primary-blue) 0%, #003a8c 100%); padding: 15px 20px; display: flex; align-items: center; gap: 15px; color: white;">
                    <div
                        style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 50%; flex: 0 0 45px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-gavel"></i>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 500; line-height: 1.2;">Regulatory
                            Frameworks</h3>
                        <span style="font-size: 13px; font-weight: 400; opacity: 0.9; line-height: 1.3;">Guiding
                            Standards for Safety, Quality and Market Compliance</span>
                    </div>
                </div>
                <div class="scroll-logos-container"
                    style="padding: 35px 15px; display: flex; flex-wrap: nowrap; justify-content: space-between; gap: 15px; align-items: center; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/fssai.jpg" alt="FSSAI"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">FSSAI<br>Food Safety and Standards
                            Authority</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/rites.png" alt="RITES"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">RITES<br>Rail India Technical and
                            Economic Service</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/cpcb.png" alt="CPCB"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">CPCB<br>Central Pollution Control
                            Board</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/fci.png" alt="FCI"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">FCI<br>Food Corporation of India
                        </div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/rdso.jpg" alt="RDSO"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">RDSO<br>Research Designs and
                            Standards Organisation</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/usda.jpg" alt="USDA"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">USDA<br>United States Department of
                            Agriculture</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/bis.png" alt="BIS"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">BIS<br>Bureau of Indian Standards
                        </div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/gem.jpg" alt="GeM"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">GeM<br>Government e-Marketplace
                        </div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/sedex.png" alt="Sedex"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">Sedex<br>Supplier Ethical Data
                            Exchange</div>
                    </div>
                </div>
            </div>

            <!-- Block 2: Test Methods -->
            <div class="reveal"
                style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <div
                    style="background: linear-gradient(90deg, var(--primary-blue) 0%, #003a8c 100%); padding: 15px 20px; display: flex; align-items: center; gap: 15px; color: white;">
                    <div
                        style="background: rgba(255,255,255,0.2); width: 45px; height: 45px; border-radius: 50%; flex: 0 0 45px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 500; line-height: 1.2;">Test Methods
                        </h3>
                        <span style="font-size: 13px; font-weight: 400; opacity: 0.9; line-height: 1.3;">Globally
                            Accepted Standards for Reliable Testing</span>
                    </div>
                </div>
                <div class="scroll-logos-container"
                    style="padding: 35px 15px; display: flex; flex-wrap: nowrap; justify-content: space-between; gap: 15px; align-items: center; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/astm.png" alt="ASTM"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">ASTM<br>International</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/fssai.jpg" alt="FSSAI Methods"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">FSSAI<br>Methods</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/is.jpg" alt="IS"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">IS<br>(Indian Standards)</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/esma.jpg" alt="ESSMA"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">ESSMA<br>(European Sales &
                            Marketing)</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/jis.jpg" alt="JIS"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">JIS<br>(Japanese Industrial
                            Standards)</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/saso.jpg" alt="SASO"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">SASO<br>(Saudi Standards)</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/din.png" alt="DIN"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">DIN<br>(Deutsches Institut)</div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/sa.jpg" alt="AS/NZS"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">AS/NZS<br>(Australian/New Zealand)
                        </div>
                    </div>
                    <div
                        style="text-align: center; width: 110px; flex: 0 0 110px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end;">
                        <img src="assets/images/icons/fcc.png" alt="FCC"
                            style="max-height: 50px; max-width: 100%; height: auto; width: auto; object-fit: contain; margin-bottom: 10px;"
                            onerror="this.src='assets/images/testing-qc.jpg'">
                        <div style="font-size: 10px; color: #555; line-height: 1.2;">FCC<br>(Federal Communications)
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FAQ Section -->
    <!-- <section class="faq-section" style="padding: 40px 20px; background: #f9f9f9;">
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: 2rem; font-weight: 800; color: #081D33; line-height: 1.15; margin-bottom: 15px;">
                    Have questions?<br>
                    <span style="color: #0A58CA; font-style: italic; font-weight: 800;">Our experts are ready to
                        help.</span>
                </h2>
            </div>

            <div class="faq-item"
                style="background: white; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid #0d47a1; text-align: left;">
                <h4 style="font-size: 20px; margin-bottom: 12px; color: #333;">What types of testing does AKC offer?
                </h4>
                <p style="color: #666; line-height: 1.5; font-size: 16px;">We offer accredited testing across chemical,
                    mechanical, thermal, biological, electrical, rheological, and optical domains - covering polymers,
                    packaging, textiles, rubber, leather, paper, medical devices, and more.</p>
            </div>
            <div class="faq-item"
                style="background: white; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid #0d47a1; text-align: left;">
                <h4 style="font-size: 20px; margin-bottom: 12px; color: #333;">Is AKC NABL-accredited?</h4>
                <p style="color: #666; line-height: 1.5; font-size: 16px;">Yes. Our laboratories are NABL-accredited and
                    ISO 17025 compliant, meeting the highest national and international quality standards for testing
                    and calibration.</p>
            </div>
            <div class="faq-item"
                style="background: white; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid #0d47a1; text-align: left;">
                <h4 style="font-size: 20px; margin-bottom: 12px; color: #333;">How do I submit a sample for testing?
                </h4>
                <p style="color: #666; line-height: 1.5; font-size: 16px;">Fill out our online enquiry form, call our
                    lab directly, or email us to discuss your testing requirement. Our team will guide you through the
                    sample submission process.</p>
            </div>
            <div class="faq-item"
                style="background: white; padding: 25px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 4px solid #0d47a1; text-align: left;">
                <h4 style="font-size: 20px; margin-bottom: 12px; color: #333;">Do you offer training for industry
                    professionals?</h4>
                <p style="color: #666; line-height: 1.5; font-size: 16px;">Absolutely. We run structured training
                    programmes in polymer science, quality control, packaging technology, and laboratory practices -
                    for both individual professionals and corporate teams.</p>
            </div>
        </div>
    </section>-->

    <!-- Key MoUs Under Process Section -->
    <style>
        .mou-section-container {
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
            padding: 0 15px;
        }

        .mou-group {
            margin-bottom: 55px;
        }

        .mou-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            border-radius: 50px;
            color: white;
            font-weight: 500 !important;
            font-size: 18px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .mou-badge span {
            font-weight: 500 !important;
        }

        .mou-badge.blue {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
        }

        /* Gradient background for Key R&D Badge */
        .mou-badge.green {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
        }

        .mou-badge i {
            font-size: 22px;
        }

        .mou-scroller {
            width: 100%;
            overflow: hidden;
            position: relative;
            /* Fade edges */
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }

        .mou-scroller-inner {
            display: flex;
            gap: 20px;
            width: max-content;
            animation: scroll-mou 30s linear infinite;
        }

        .mou-scroller:hover .mou-scroller-inner {
            animation-play-state: paused;
        }

        @keyframes scroll-mou {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(calc(-50% - 10px));
            }
        }

        .mou-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            height: 170px;
            width: 160px;
            flex: 0 0 160px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .mou-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }
    </style>
    <section style="background: #f8fafc; padding: 95px 0 100px 0; border-top: 1px solid #eef0f4; overflow: hidden;">

        <div class="reveal" style="text-align: center; margin-bottom: 60px; padding: 0 20px;">
            <div class="universal-section-label">PARTNERSHIPS &amp; COLLABORATIONS</div>
            <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px;">
                Key MoUs Under Process</h2>
            <p style="font-size: 16px; color: #64748b; max-width: 700px; margin: 0 auto; font-weight: 400;">
                Collaborating with leading institutions for research, innovation and skill development.</p>
        </div>

        <div class="mou-section-container">
            <!-- IITs & Universities -->
            <div class="mou-group reveal">
                <div style="text-align: center;">
                    <div class="mou-badge blue">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>IITs & Universities</span>
                    </div>
                </div>

                <div class="mou-scroller">
                    <div class="mou-scroller-inner">
                        <?php
                        $unis = [
                            ['name' => 'IIT Kanpur', 'image' => 'kanpur.png'],
                            ['name' => 'IIT Delhi', 'image' => 'delhi.png'],
                            ['name' => 'IIT Bhubaneswar', 'image' => 'bhubaneswar.jpg'],
                            ['name' => 'IIT Kharagpur', 'image' => 'kharagpur.png'],
                            ['name' => 'IIT Hyderabad', 'image' => 'hyderabad.png'],
                            ['name' => 'IIT Madras', 'image' => 'madras.png?v=3'],
                            ['name' => 'Noida International University', 'image' => 'niu.jpg'],
                            ['name' => 'DPSRU, Delhi', 'image' => 'dpsru.jpg']
                        ];
                        for ($i = 0; $i < 2; $i++) {
                            foreach ($unis as $uni) {
                                echo '<div class="mou-card">
                                    <img src="assets/images/icons/' . $uni['image'] . '" alt="' . $uni['name'] . '" style="max-height: 60px; max-width: 90%; margin-bottom: 15px; object-fit: contain;">
                                    <span style="font-size: 12px; font-weight: 500; color: #334155; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word;">' . $uni['name'] . '</span>
                                  </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Key R&D / Government Partners -->
            <div class="mou-group reveal" style="margin-bottom: 0;">
                <div style="text-align: center;">
                    <div class="mou-badge green">
                        <i class="fa-solid fa-building-flag"></i>
                        <span>Key R&D / Government Partners</span>
                    </div>
                </div>

                <div class="mou-scroller">
                    <div class="mou-scroller-inner" style="animation-direction: reverse; animation-duration: 35s;">
                        <?php
                        $govts = [
                            ['name' => 'CSIR', 'image' => 'csir.jpg'],
                            ['name' => 'Dept of Science & Technology (DST)', 'image' => 'dst.png?v=3'],
                            ['name' => 'Dept of Biotechnology (DBT)', 'image' => 'dbt.jpg'],
                            ['name' => 'DSIR', 'image' => 'dsir.jpg'],
                            ['name' => 'Indian Institute of Packaging (IIP)', 'image' => 'iip_new.jpg'],
                            ['name' => 'CIPET', 'image' => 'cipet.jpg'],
                            ['name' => 'CSIR-NCL', 'image' => 'csir-ncl.png'],
                            ['name' => 'CFTRI', 'image' => 'cftri.jpg'],
                            ['name' => 'CLRI', 'image' => 'clri.jpg'],
                            ['name' => 'NIFTEM', 'image' => 'nieftem.jpg'],
                            ['name' => 'ICMR-NIN', 'image' => 'icmr-nin.jpg'],
                            ['name' => 'ICAR', 'image' => 'icar.png'],
                            ['name' => 'AIRA', 'image' => 'aira.jpg']
                        ];
                        for ($i = 0; $i < 2; $i++) {
                            foreach ($govts as $govt) {
                                $imgStyle = 'max-height: 60px; max-width: 90%; margin-bottom: 15px; object-fit: contain;';
                                echo '<div class="mou-card">
                                    <img src="assets/images/icons/' . $govt['image'] . '" alt="' . $govt['name'] . '" style="' . $imgStyle . '">
                                    <span style="font-size: 12px; font-weight: 500; color: #334155; line-height: 1.3;">' . $govt['name'] . '</span>
                                  </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </section>




</main>

<script>
    function switchTab(evt, tabId) {
        // Hide all tab contents
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.classList.remove('active'));

        // Remove active class from all buttons
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        // Show the current tab, and add an "active" class to the button that opened the tab
        const targetTab = document.getElementById(tabId);
        if (targetTab) {
            targetTab.classList.add('active');
        }
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add('active');
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statNumbers = document.querySelectorAll('.stat-number');
        if (statNumbers.length === 0) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    if (!isNaN(target)) {
                        animateValue(entry.target, 0, target, 3500); // Set to 3.5 seconds
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        statNumbers.forEach(stat => {
            stat.innerText = '0';
            observer.observe(stat);
        });

        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                obj.innerHTML = Math.floor(easeProgress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    obj.innerHTML = end;
                }
            };
            window.requestAnimationFrame(step);
        }
    });
</script>
<?php include 'includes/footer.php'; ?>