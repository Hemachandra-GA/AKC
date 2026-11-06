<?php
require_once 'includes/db_connect.php';

$pageTitle = "Technical Services | AKC - NABL Accredited Lab";
$metaDescription = "Real time assistance and expert support designed to ensure seamless operations, faster issue resolution, and uninterrupted service delivery.";
$metaKeywords = "technical support, lab support, quality assurance support";
include 'includes/header.php';

// Fetch all disciplines for the dropdown in custom order (strictly 4 official categories)
$disciplinesStmt = $conn->query("
    SELECT id, name, slug 
    FROM disciplines 
    WHERE id IN (1, 2, 4, 5) 
       OR slug IN ('chemical', 'mechanical', 'biological', 'electrical')
       OR LOWER(name) IN ('chemical', 'mechanical', 'biological', 'electrical')
    ORDER BY CASE WHEN LOWER(name) LIKE '%chem%' THEN 1
                  WHEN LOWER(name) LIKE '%mech%' THEN 2
                  WHEN LOWER(name) LIKE '%bio%' THEN 3
                  WHEN LOWER(name) LIKE '%elec%' THEN 4
                  ELSE 99 END ASC, name ASC
");
$disciplines = $disciplinesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all products for the dropdown
$productsStmt = $conn->query("SELECT id, name, slug FROM products ORDER BY sort_order ASC, name ASC");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Custom Stylesheet Link for this Page -->
<link rel="stylesheet" href="assets/css/technical-support.css?v=1.3">

<main>
    <!-- Hero Section -->
    <section class="ts-hero-section">
        <div class="ts-hero-container">

            <!-- Left Info Block -->
            <div class="ts-hero-info">
                <div class="ts-subtitle">
                    <span class="dot"></span>— SUPPORT SERVICES
                </div>

                <h1 class="ts-hero-title">
                    Technical<br> <span class="text-accent">Services</span>
                </h1>

                <p class="ts-hero-desc">
                    Real time assistance and expert support designed to ensure seamless operations, faster issue
                    resolution,
                    and uninterrupted service delivery across every stage.
                </p>

                <div class="ts-hero-btns">
                    <a href="#booking" class="btn-ts-primary">
                        Book a Service
                    </a>
                    <a href="mailto:info@akc-ptarc.com" class="btn-ts-outline">
                        Contact Our Experts
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="ts-stats-row">
                    <div class="ts-stat-item">
                        <i class="fa-solid fa-flask-vial"></i>
                        <div class="ts-stat-text">
                            <span class="ts-stat-value">40+</span>
                            <span class="ts-stat-label">Expert Scientists</span>
                        </div>
                    </div>
                    <div class="ts-stat-item">
                        <i class="fa-solid fa-trophy"></i>
                        <div class="ts-stat-text">
                            <span class="ts-stat-value">ISO</span>
                            <span class="ts-stat-label">17025 Accredited</span>
                        </div>
                    </div>
                    <div class="ts-stat-item">
                        <i class="fa-solid fa-globe"></i>
                        <div class="ts-stat-text">
                            <span class="ts-stat-value">15+</span>
                            <span class="ts-stat-label">Industry Sectors</span>
                        </div>
                    </div>
                    <div class="ts-stat-item">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <div class="ts-stat-text">
                            <span class="ts-stat-value">500+</span>
                            <span class="ts-stat-label">Tests Monthly</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Glassmorphic Floating Widget -->
            <div class="ts-contact-widget">
                <div class="widget-header">
                    <span class="red-bar"></span>
                    <h5>CONTACT US</h5>
                </div>

                <h3 class="widget-phone"><a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91
                        88604 07220</a></h3>

                <div class="widget-email-row">
                    <i class="fa-regular fa-envelope"></i>
                    <span>Email Us: <a href="mailto:info@akc-ptarc.com">info@akc-ptarc.com</a></span>
                </div>

                <button class="btn-widget-submit"
                    onclick="window.location.href='mailto:info@akc-ptarc.com?subject=Inquiry from Technical Services Page'">
                    Send us a request
                </button>
            </div>

        </div>
    </section>


    <!-- Our Facilities / Capabilities Section -->
    <section class="ts-facilities-section">
        <div class="ts-facilities-container">
            <div class="section-header reveal">
                <div class="ts-subtitle red">
                    <span class="line"></span>
                    OUR CAPABILITIES
                </div>
                <h2>Our Facilities</h2>
            </div>

            <div class="facilities-grid reveal-group">

                <!-- Card 1 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Inspection (Pre & Post).gif" alt="Inspection">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <h4>Inspection - Pre & Post</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Rigorous pre-production and post-production inspection ensuring dimensional accuracy and
                            compliance at every stage.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Calibration.gif" alt="Calibration">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-scale-balanced"></i>
                            <h4>Calibration</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>ISO-traceable calibration services for measurement instruments, gauges, and testing equipment
                            to international standards.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/CAD and CAM.gif" alt="CAD CAM">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-laptop-code"></i>
                            <h4>CAD / CAM / CAE</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Full-suite computer-aided design, manufacturing, and engineering simulation for product
                            development and optimization.</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Process Optimization.gif" alt="Process Optimization">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-chart-line"></i>
                            <h4>Process Optimization & Mold Trial</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Data-driven process optimization and systematic mold trials to enhance productivity and
                            reduce cycle times.</p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Import Subsitution.gif" alt="Import Substitution">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-box-open"></i>
                            <h4>Import Substitution</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Locally engineered alternatives to imported components, reducing dependency and improving
                            supply chain resilience.</p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Reverse Engineering.gif" alt="Reverse Engineering">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-gears"></i>
                            <h4>Reverse Engineering & Customization</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>3D scanning, point cloud analysis, and custom component redesign for legacy parts and bespoke
                            industrial applications.</p>
                    </div>
                </div>

                <!-- Card 7 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Troubleshooting.gif" alt="Troubleshooting">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-wrench"></i>
                            <h4>Troubleshooting & Failure Analysis</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Root cause analysis, fractography, and failure investigation to prevent recurrence and
                            improve component reliability.</p>
                    </div>
                </div>

                <!-- Card 8 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/3D Printing.gif" alt="3D Printing">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-cube"></i>
                            <h4>3D Printing - Product Design</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Rapid additive manufacturing for prototyping and functional parts using FDM, SLA, and
                            industrial-grade materials.</p>
                    </div>
                </div>

                <!-- Card 9 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Proficiency Testing.gif" alt="Prototype R&D">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-lightbulb"></i>
                            <h4>Prototype / R&D Services</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Rapid prototyping and inter-laboratory comparison services to benchmark and validate
                            measurement accuracy.</p>
                    </div>
                </div>

                <!-- Card 10 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Collaboration.gif" alt="Collaboration">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-handshake"></i>
                            <h4>Collaboration</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Joint R&D initiatives and technology transfer partnerships with academic, government bodies,
                            and global industry leaders.</p>
                    </div>
                </div>

                <!-- Card 11 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Material Development.gif" alt="New Material Development">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-vial"></i>
                            <h4>New Material Development</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Advanced formulation and synthesis of novel polymers, composites, and specialty materials
                            tailored for emerging applications.</p>
                    </div>
                </div>

                <!-- Card 12 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Quality Management.gif" alt="Quality System">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-certificate"></i>
                            <h4>Quality System & Management</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Implementation and audit support for ISO 9001, IATF 16949, and other quality management
                            system standards.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Advisory & Consulting Services Section -->
    <section class="ts-services-section">
        <div class="ts-services-container">
            <div class="section-header reveal">
                <div class="ts-subtitle red">
                    <span class="line"></span>
                    WHAT WE SERVE
                </div>
                <h2>Advisory & Consulting services</h2>
            </div>

            <div class="services-grid reveal-group" style="grid-template-columns: repeat(3, 1fr);">

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h3>01. Project Execution</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>End-to-end project management</li>
                        <li>Feasibility & techno-economic analysis</li>
                        <li>Process design and implementation</li>
                        <li>Vendor support & coordination</li>
                        <li>Monitoring, commissioning & handover</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <h3>02. Regulatory & Policy Compliance</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Regulatory requirement assessment</li>
                        <li>Compliance management</li>
                        <li>Standards, codes & statutory approvals</li>
                        <li>Policy advisory and implementation</li>
                        <li>Support for audits and filings</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3>03. Strategic Planning Business Transformation</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Strategic growth planning</li>
                        <li>Business process improvement</li>
                        <li>Digital transformation</li>
                        <li>Operational excellence</li>
                        <li>Change management & capability building</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-chess-knight"></i>
                    </div>
                    <h3>04. Competitive Intelligence</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Competitor benchmarking</li>
                        <li>Technology and product intelligence</li>
                        <li>Industry trend analysis</li>
                        <li>SWOT and market positioning</li>
                        <li>Actionable insights for decision making</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                    </div>
                    <h3>05. Market Research & Analysis</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Market trends and opportunity assessment</li>
                        <li>Customer insights and segmentation</li>
                        <li>Demand forecasting</li>
                        <li>Feasibility and viability studies</li>
                        <li>Customized research and analytical reports</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h3>06. Recruitment & Talent Acquisition</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>End-to-end recruitment solutions</li>
                        <li>Leadership and niche talent hiring</li>
                        <li>Campus and lateral hiring support</li>
                        <li>Assessment and selection services</li>
                        <li>Talent development and retention strategies</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- Compliance Section -->
    <section class="compliance-section" style="padding: 80px 20px; background: #f4f7fb;">
        <div style="max-width: 1200px; margin: 0 auto;">

            <!-- Logos and Title -->
            <div class="reveal"
                style="display: flex; justify-content: center; align-items: center; margin-bottom: 50px;">
                <div style="text-align: center; flex: 1; padding: 0 20px;">
                    <h2
                        style="color: #081D33; font-size: 32px; font-weight: 600; margin: 0 0 8px 0; text-transform: none;">
                        Regulatory,
                        Accreditation & Recognition Bodies</h2>
                    <p style="color: #64748b; font-size: 16px; margin: 0; font-weight: 400; text-transform: none;">
                        Ensuring Compliance,
                        Quality and Global Acceptance</p>
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

    <!-- Booking Section -->
    <section class="ts-booking-section" id="booking">
        <div class="ts-booking-container reveal-group">
            <!-- Left Info -->
            <div class="booking-left reveal-left">
                <div class="ts-subtitle red">
                    <span class="line"></span> Book a Test
                </div>
                <h2>Request <span style="color: var(--primary-blue);">a Test</span></h2>
                <p>Complete the form to book testing or technical services with AKC's accredited laboratory. Our team
                    will confirm
                    your booking, provide a detailed quotation, and guide you through sample submission.</p>

                <div class="booking-features">
                    <div class="bf-card">
                        <i class="fa-solid fa-bolt"></i>
                        <div class="bf-content">
                            <h4>Fast Turnaround</h4>
                            <p>Standard results in 3-5 working days. Express 24-48 hr service available on request.</p>
                        </div>
                    </div>
                    <div class="bf-card">
                        <i class="fa-solid fa-file-contract"
                            style="color: var(--primary-blue); background: #F0F9FF;"></i>
                        <div class="bf-content">
                            <h4>Accredited Test Report</h4>
                            <p>ISO 17025 accredited report with full methodology, results, and measurement uncertainty.
                            </p>
                        </div>
                    </div>
                    <div class="bf-card">
                        <i class="fa-solid fa-lock"></i>
                        <div class="bf-content">
                            <h4>Sample Confidentiality</h4>
                            <p>All samples and results handled with strict confidentiality. NDA available on request.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Form -->
            <div class="booking-right reveal-right">
                <div class="booking-form-header">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div>
                        <h3>Book a Test</h3>
                        <p>Ash Content Testing - Plastic & Resin</p>
                    </div>
                </div>

                <form action="book-test.php" method="POST" enctype="multipart/form-data" class="booking-form-body">
                    <div class="form-section-title">TEST INFORMATION</div>

                    <div class="form-group">
                        <label>Discipline Category <span>*</span></label>
                        <select class="form-control" name="discipline" id="discipline_select" required>
                            <option value="">Select the Discipline Category</option>
                            <?php foreach ($disciplines as $disc): ?>
                                <option value="<?php echo htmlspecialchars($disc['slug'] ?? $disc['id']); ?>">
                                    <?php echo htmlspecialchars($disc['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Product <span>*</span></label>
                        <select class="form-control" name="product" id="product_select" required>
                            <option value="">Select the Product Category</option>
                            <?php foreach ($products as $prod): ?>
                                <option value="<?php echo htmlspecialchars($prod['slug'] ?? $prod['id']); ?>">
                                    <?php echo htmlspecialchars($prod['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Testing type <span>*</span></label>
                        <select class="form-control" name="testing_type" id="testing_type_select" required>
                            <option value="">Select the Testing Category</option>
                        </select>
                    </div>

                    <div class="form-section-title">SAMPLE INFORMATION</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Type of Sample <span>*</span></label>
                            <input type="text" name="sample_type" class="form-control"
                                placeholder="Select material type..." required>
                        </div>
                        <div class="form-group">
                            <label>Number of Samples <span>*</span></label>
                            <input type="number" name="num_samples" class="form-control" placeholder="e.g. 3" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Sample Description <span>*</span></label>
                        <input type="text" name="sample_desc" class="form-control"
                            placeholder="e.g. Filled PP pellets, 50g, natural colour" required>
                    </div>

                    <div class="form-group">
                        <label>Upload Sample Photograph <span>*</span></label>
                        <div class="file-upload-area" style="position: relative;">
                            <input type="file" name="sample_photo" accept=".png, .jpg, .jpeg, .pdf"
                                style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;"
                                required>
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Click to upload or drag & drop</p>
                            <span style="font-size: 0.8rem; color: #94A3B8;">JPG, PNG or PDF - Max 10MB</span>
                        </div>
                    </div>

                    <div class="form-section-title">CUSTOMER DETAILS</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name <span>*</span></label>
                            <input type="text" name="fullname" class="form-control" placeholder="Your full name"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Company <span>*</span></label>
                            <input type="text" name="company" class="form-control" placeholder="Company or organization"
                                required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address <span>*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number <span>*</span></label>
                            <input type="hidden" name="country_code" value="+91">
                            <input type="tel" name="phone" class="form-control" placeholder="10 digit number"
                                pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Additional Requirements</label>
                        <textarea name="requirements" class="form-control" rows="3"
                            placeholder="Express turnaround, NDA required, specific standard, etc."></textarea>
                    </div>

                    <button type="submit" class="btn-book-submit">
                        <i class="fa-regular fa-calendar-check"></i> Book Test
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="ts-faq-section">
        <div class="ts-faq-container reveal-group">
            <!-- Left Support Card -->
            <div class="faq-left reveal-left">
                <div class="ts-subtitle red">
                    <span class="line"></span> SUPPORT
                </div>
                <h2>Frequently Asked<br>Questions</h2>

                <div class="faq-support-card">
                    <i class="fa-solid fa-comment-dots"></i>
                    <h3>Need Expert <span>Guidance?</span></h3>
                    <p>Our qualified team of polymer scientists and compliance specialists are available to discuss your
                        testing requirements and recommend the right programme.</p>

                    <div class="faq-contact">
                        <span>Direct contact:</span>
                        <a href="mailto:info@akc-ptarc.com">info@akc-ptarc.com</a>
                    </div>

                    <button class="btn-faq-contact" onclick="window.location.href='mailto:info@akc-ptarc.com'">
                        Contact Our Team <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                    </button>
                </div>
            </div>

            <!-- Right Accordion -->
            <div class="faq-right reveal-right">
                <div class="faq-item">
                    <div class="faq-header-toggle">
                        What types of testing services does AKC provide?
                        <i class="fa-solid fa-chevron-up" style="background: var(--primary-blue); color: white;"></i>
                    </div>
                    <div class="faq-body" style="display: block;">
                        AKC provides a full spectrum of testing services including chemical composition analysis,
                        restricted substance testing, polymer and material testing, contaminant screening, heavy metal
                        analysis, and regulatory compliance verification across multiple industry sectors.
                        <div class="faq-feedback">
                            <span>Was This Content Helpful ?</span>
                            <div class="faq-feedback-icons">
                                <i class="fa-regular fa-thumbs-up"></i>
                                <i class="fa-regular fa-thumbs-down"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header-toggle">
                        How long does testing take?
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-body">
                        Turnaround time varies by test type and sample complexity. Standard tests are typically
                        completed within 3-5 working days. Express processing is available on request.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header-toggle">
                        Do AKC laboratories follow international standards?
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-body">
                        Yes, our testing procedures and methodologies are in strict compliance with ISO, ASTM, DIN, and
                        other global standards ensuring accuracy and reliability.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header-toggle">
                        Can AKC provide certification reports?
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-body">
                        Yes, we provide ISO 17025 accredited reports which are recognized globally for compliance and
                        quality assurance audits.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header-toggle">
                        What sample submission requirements apply?
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-body">
                        Samples must be clearly labeled and submitted securely. Required quantities depend on the type
                        of test being conducted. Our team will guide you during the booking process.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="ts-final-cta reveal">
        <div class="ts-final-cta-container">
            <div class="cta-text">
                <div class="ts-subtitle">
                    <span class="line"></span> READY TO BEGIN?
                </div>
                <h2>Need Services?</h2>
                <p>Our expert team is ready to discuss your requirements and deliver precise, accredited results.</p>
            </div>
            <div class="cta-buttons">
                <a href="mailto:info@akc-ptarc.com" class="btn-light">
                    <i class="fa-regular fa-file-lines"></i> Request a Test
                </a>
                <a href="mailto:info@akc-ptarc.com" class="btn-outline">
                    <i class="fa-solid fa-headset"></i> Contact Our Experts
                </a>
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // FAQ Accordion script
        const toggles = document.querySelectorAll('.faq-header-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const body = this.nextElementSibling;
                const icon = this.querySelector('i');
                const parent = this.parentElement;

                if (body.style.display === 'none' || body.style.display === '') {
                    body.style.display = 'block';
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                    icon.style.backgroundColor = 'var(--primary-blue)';
                    icon.style.color = 'white';
                    parent.style.borderColor = 'var(--primary-blue)';
                } else {
                    body.style.display = 'none';
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                    icon.style.backgroundColor = '#F1F5F9';
                    icon.style.color = '#64748B';
                    parent.style.borderColor = '#E2E8F0';
                }
            });
        });

        // Cascading dropdowns logic
        const disciplineSelect = document.getElementById('discipline_select');
        const productSelect = document.getElementById('product_select');
        const testingTypeSelect = document.getElementById('testing_type_select');

        if (disciplineSelect && productSelect && testingTypeSelect) {

            function fetchTests() {
                const discSlug = disciplineSelect.value;
                const prodSlug = productSelect.value;

                // Default option
                testingTypeSelect.innerHTML = '<option value="">Select the Testing Category</option>';

                if (discSlug && prodSlug) {
                    fetch(`api_get_options.php?action=get_tests&discipline=${encodeURIComponent(discSlug)}&product=${encodeURIComponent(prodSlug)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                const option = document.createElement('option');
                                option.value = 'custom';
                                option.textContent = 'No tests available - Request Custom Test';
                                testingTypeSelect.appendChild(option);
                            } else {
                                data.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.slug || item.id;
                                    option.textContent = item.name;
                                    testingTypeSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(err => console.error('Error fetching tests:', err));
                }
            }

            disciplineSelect.addEventListener('change', fetchTests);
            productSelect.addEventListener('change', fetchTests);
        }

        // File upload JS
        const fileInput = document.querySelector('input[name="sample_photo"]');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const label = this.nextElementSibling.nextElementSibling;
                if (this.files && this.files.length > 0) {
                    label.textContent = this.files[0].name;
                } else {
                    label.textContent = 'Click to upload or drag & drop';
                }
            });
        }

        // Loader on submit
        const formSubmit = document.querySelector('.booking-form-body');
        if (formSubmit) {
            formSubmit.addEventListener('submit', function () {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                    btn.style.opacity = '0.8';
                    btn.style.cursor = 'not-allowed';
                    setTimeout(() => { btn.disabled = true; }, 10);
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>