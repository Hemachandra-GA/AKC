<?php
$pageTitle = "Virtual Tour | AKC-PTARC Laboratories & Facilities";
$metaDescription = "Take an immersive virtual tour of AKC-PTARC's NABL-accredited testing laboratories, advanced instrumentation, and multidisciplinary research facilities.";
$metaKeywords = "virtual tour, lab tour, AKC facilities, testing laboratories, polymer research center";
include 'includes/header.php';
?>

<!-- Custom Stylesheet Link -->
<link rel="stylesheet" href="assets/css/technical-support.css?v=1.3">

<style>
    .vt-hero {
        position: relative;
        background: url('assets/images/about_hero_bg_v2.png') no-repeat center center/cover;
        padding: 110px 20px 80px;
        color: #ffffff;
        text-align: center;
    }
    .vt-hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(8, 29, 51, 0.92) 0%, rgba(10, 88, 202, 0.85) 100%);
    }
    .vt-hero-content {
        position: relative;
        z-index: 2;
        max-width: 850px;
        margin: 0 auto;
    }
    .vt-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #93c5fd;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }
    .vt-badge span {
        width: 24px;
        height: 2px;
        background: #93c5fd;
        display: inline-block;
    }
    .vt-hero h1 {
        font-size: 42px;
        font-weight: 700;
        margin: 0 0 16px 0;
        line-height: 1.25;
        color: #ffffff;
    }
    .vt-hero p {
        font-size: 17px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.9);
        margin: 0 auto 25px;
    }
    .vt-video-section {
        padding: 70px 20px;
        background: #ffffff;
    }
    .vt-video-wrapper {
        max-width: 1100px;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(8, 29, 51, 0.15);
        border: 1px solid #e2e8f0;
        position: relative;
        background: #000;
    }
    .vt-video-wrapper video {
        width: 100%;
        height: auto;
        max-height: 560px;
        display: block;
        object-fit: cover;
    }
    .vt-zones-section {
        padding: 80px 20px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    .vt-zones-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        max-width: 1200px;
        margin: 35px auto 0;
    }
    .vt-zone-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #edf2f7;
    }
    .vt-zone-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 30px rgba(10, 88, 202, 0.12);
    }
    .vt-zone-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }
    .vt-zone-card .vt-zone-body {
        padding: 24px 20px;
    }
    .vt-zone-card h3 {
        font-size: 18px;
        font-weight: 700;
        color: #081D33;
        margin: 0 0 8px 0;
    }
    .vt-zone-card p {
        font-size: 14px;
        line-height: 1.6;
        color: #64748b;
        margin: 0;
    }
    @media (max-width: 900px) {
        .vt-zones-grid {
            grid-template-columns: 1fr;
            max-width: 480px;
        }
        .vt-hero h1 {
            font-size: 32px;
        }
    }
</style>

<main>
    <!-- Virtual Tour Hero -->
    <section class="vt-hero">
        <div class="vt-hero-overlay"></div>
        <div class="vt-hero-content reveal">
            <div class="vt-badge">
                <span></span> VIRTUAL TOUR <span></span>
            </div>
            <h1>Experience AKC-PTARC Facilities</h1>
            <p>Step inside India's premier NABL-accredited polymer technology and applied research centre. Explore our testing laboratories, precision analytical equipment, and industrial innovation hubs virtually.</p>
        </div>
    </section>

    <!-- Virtual Video Tour -->
    <section class="vt-video-section">
        <div class="container text-center">
            <div class="section-header center reveal" style="text-align: center; margin-bottom: 30px;">
                <div class="ts-subtitle red" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span class="line"></span> 360° LAB WALKTHROUGH
                </div>
                <h2 style="font-size: 32px; font-weight: 700; color: #081D33; margin-top: 8px;">Explore Our Advanced Laboratories</h2>
                <p style="color: #64748b; font-size: 16px; max-width: 750px; margin: 10px auto 0;">Take a walkthrough of our world-class research infrastructure, showcasing state-of-the-art testing stations, climate-controlled testing bays, and precision analytical instruments.</p>
            </div>

            <div class="vt-video-wrapper reveal-scale">
                <video autoplay loop muted playsinline controls poster="assets/images/laboratory_facility_1.png">
                    <source src="assets/videos/home-page-banner-fast.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </section>

    <!-- Laboratory Zones Showcase -->
    <section class="vt-zones-section">
        <div class="container">
            <div class="section-header center reveal" style="text-align: center;">
                <div class="ts-subtitle red" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span class="line"></span> INFRASTRUCTURE HIGHLIGHTS
                </div>
                <h2 style="font-size: 32px; font-weight: 700; color: #081D33; margin-top: 8px;">Key Testing &amp; Research Zones</h2>
            </div>

            <div class="vt-zones-grid reveal-group">
                <div class="vt-zone-card reveal-child">
                    <img src="assets/images/laboratory_facility_1.png" alt="Spectroscopy & Polymer Characterization">
                    <div class="vt-zone-body">
                        <h3>Polymer Characterization Bay</h3>
                        <p>High-resolution FTIR, DSC, TGA, and chromatography equipment for advanced polymer synthesis and raw material validation.</p>
                    </div>
                </div>

                <div class="vt-zone-card reveal-child">
                    <img src="assets/images/laboratory_facility_2.png" alt="Mechanical & Structural Testing">
                    <div class="vt-zone-body">
                        <h3>Mechanical Testing Suite</h3>
                        <p>Universal Testing Machines (UTM), impact pendulums, hardness gauges, and fatigue rigs under precise ASTM/ISO conditions.</p>
                    </div>
                </div>

                <div class="vt-zone-card reveal-child">
                    <img src="assets/images/laboratory_facility_3.png" alt="Packaging & Environmental Chambers">
                    <div class="vt-zone-body">
                        <h3>Environmental Simulation Lab</h3>
                        <p>Accelerated weathering (QUV/Xenon), humidity cycling chambers, and burst/drop testing stations for transport packaging.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Facilities / Capabilities Section (Shifted from Advisory Services) -->
    <section class="ts-facilities-section" style="padding: 90px 20px; background: #ffffff;">
        <div class="ts-facilities-container">
            <div class="section-header reveal" style="text-align: center; margin-bottom: 30px;">
                <div class="ts-subtitle red" style="display: inline-flex; align-items: center; gap: 8px;">
                    <span class="line"></span> OUR CAPABILITIES
                </div>
                <h2 style="font-size: 32px; font-weight: 700; color: #081D33; margin-top: 8px;">Our Facilities</h2>
                <p style="color: #64748b; font-size: 16px; max-width: 750px; margin: 10px auto 0;">Comprehensively equipped facilities spanning inspection, calibration, prototyping, reverse engineering, and advanced materials development.</p>
            </div>

            <div class="facilities-grid reveal-group">

                <!-- Card 1 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Inspection (Pre & Post).gif" alt="Inspection">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <h4>Inspection - Pre &amp; Post</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Rigorous pre-production and post-production inspection ensuring dimensional accuracy and compliance at every stage.</p>
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
                        <p>ISO-traceable calibration services for measurement instruments, gauges, and testing equipment to international standards.</p>
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
                        <p>Full-suite computer-aided design, manufacturing, and engineering simulation for product development and optimization.</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Process Optimization.gif" alt="Process Optimization">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-chart-line"></i>
                            <h4>Process Optimization &amp; Mold Trial</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Data-driven process optimization and systematic mold trials to enhance productivity and reduce cycle times.</p>
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
                        <p>Locally engineered alternatives to imported components, reducing dependency and improving supply chain resilience.</p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Reverse Engineering.gif" alt="Reverse Engineering">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-gears"></i>
                            <h4>Reverse Engineering &amp; Customization</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>3D scanning, point cloud analysis, and custom component redesign for legacy parts and bespoke industrial applications.</p>
                    </div>
                </div>

                <!-- Card 7 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Troubleshooting.gif" alt="Troubleshooting">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-wrench"></i>
                            <h4>Troubleshooting &amp; Failure Analysis</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Root cause analysis, fractography, and failure investigation to prevent recurrence and improve component reliability.</p>
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
                        <p>Rapid additive manufacturing for prototyping and functional parts using FDM, SLA, and industrial-grade materials.</p>
                    </div>
                </div>

                <!-- Card 9 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Proficiency Testing.gif" alt="Prototype R&D">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-lightbulb"></i>
                            <h4>Prototype / R&amp;D Services</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Rapid prototyping and inter-laboratory comparison services to benchmark and validate measurement accuracy.</p>
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
                        <p>Joint R&D initiatives and technology transfer partnerships with academic, government bodies, and global industry leaders.</p>
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
                        <p>Advanced formulation and synthesis of novel polymers, composites, and specialty materials tailored for emerging applications.</p>
                    </div>
                </div>

                <!-- Card 12 -->
                <div class="facility-card reveal-child">
                    <div class="fc-image-container">
                        <img src="assets/images/Quality Management.gif" alt="Quality System">
                        <div class="fc-overlay-title">
                            <i class="fa-solid fa-certificate"></i>
                            <h4>Quality System &amp; Management</h4>
                        </div>
                    </div>
                    <div class="fc-content">
                        <p>Implementation and audit support for ISO 9001, IATF 16949, and other quality management system standards.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Visit / Enquiry CTA Section -->
    <section class="ts-final-cta reveal">
        <div class="ts-final-cta-container">
            <div class="cta-text">
                <div class="ts-subtitle">
                    <span class="line"></span> SCHEDULE A VISIT
                </div>
                <h2>Want to Visit Our Laboratories in Person?</h2>
                <p>We welcome industry partners, researchers, and academic delegations for on-site facility tours and technical consultations.</p>
            </div>
            <div class="cta-buttons">
                <a href="contact-us.php" class="btn-light">
                    <i class="fa-regular fa-calendar-check"></i> Plan Lab Visit
                </a>
                <a href="advisory-service.php#enquiry" class="btn-outline">
                    <i class="fa-solid fa-headset"></i> Advisory Enquiry
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

