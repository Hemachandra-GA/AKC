<?php
require_once 'includes/db_connect.php';

$pageTitle = "Advisory Services | AKC-PTARC - NABL Accredited Lab";
$metaDescription = "Real time assistance and expert support designed to ensure seamless operations, faster issue resolution, and uninterrupted service delivery.";
$metaKeywords = "advisory services, technical support, lab support, quality assurance support";
include 'includes/header.php';

// Fetch all disciplines dynamically from database for the dropdown
$disciplinesStmt = $conn->query("
    SELECT id, name, slug 
    FROM disciplines 
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
                    Advisory<br> <span class="text-accent">Services</span>
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

                <h3 class="widget-phone"><a href="tel:+911234567890" style="color: inherit; text-decoration: none;">+91 12345 67890</a></h3>

                <div class="widget-email-row">
                    <i class="fa-regular fa-envelope"></i>
                    <span>Email Us: <a href="mailto:info@akc-ptarc.com">info@akc-ptarc.com</a></span>
                </div>

                <button class="btn-widget-submit"
                    onclick="window.location.href='mailto:info@akc-ptarc.com?subject=Enquiry from Technical Services Page'">
                    Send us a request
                </button>
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
                    <h3>02. Regulatory Compliance &amp; Legal Obligations</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Standards &amp; Guidelines related to Design, Manufacturing, Use &amp; Disposal of Plastics Products</li>
                        <li>Govt. Policy Advocacy &amp; Implementation</li>
                        <li>Food Contact: Environmental Standards</li>
                        <li>Extended Producer Responsibility (EPR): Registration &amp; Audit</li>
                        <li>Standards, Codes &amp; Statutory Approvals</li>
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
                        <li>Change management &amp; capability building</li>
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
                    <h3>05. Market Research &amp; Analysis</h3>
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
                    <h3>06. Recruitment &amp; Talent Acquisition</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>End-to-end recruitment solutions</li>
                        <li>Leadership and niche talent hiring</li>
                        <li>Campus and lateral hiring support</li>
                        <li>Assessment and selection services</li>
                        <li>Talent development and retention strategies</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-recycle"></i>
                    </div>
                    <h3>07. Recycling &amp; Sustainability</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>3R, Circular Economy &amp; Carbon Neutralizing</li>
                        <li>Recycling Content Requirements</li>
                        <li>End of Life Management (BOL, EOL, LCA &amp; P.W.M. Rules)</li>
                        <li>Enforcement of SUP ban &amp; Restrictions</li>
                        <li>Labeling &amp; Marking (QR Code)</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-atom"></i>
                    </div>
                    <h3>08. Advanced Material Development</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Material Innovation and Formulation Optimization</li>
                        <li>Industry Specific Applications: Packaging, Automotive, Aerospace, Medical Devices etc.</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <h3>09. Technical &amp; Financial Feasibility</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Plant Setup, Material &amp; Machinery Selection: Financial viability</li>
                        <li>Laboratory/Institute Set up &amp; Management</li>
                        <li>Technology Adoption &amp; Implementation: AI &amp; Machine Learning</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <h3>10. Plastics &amp; Environment</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Plastic Pollution &amp; Littering</li>
                        <li>Compostable / Biodegradable Packaging</li>
                        <li>Physical, Mechanical, Chemical &amp; Organic Recycling</li>
                        <li>E-waste / Plastics Waste Management</li>
                        <li>Solid Waste Management: TAD / MAD</li>
                        <li>Micro &amp; Nano Plastics</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-cube"></i>
                    </div>
                    <h3>11. Product Design &amp; 3D Printing</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Import Substitution: Fossil &amp; Non-Fossil Products</li>
                        <li>Tailored Material Architectures</li>
                        <li>SLA, FDM &amp; SLS (Prototyping)</li>
                        <li>Multifunctional Material Systems</li>
                        <li>Rheology-Driven Printability</li>
                        <li>Design for Additive Manufacturing</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-circle-nodes"></i>
                    </div>
                    <h3>12. Selection of Plastics Materials</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Advanced Filled Plastics: Additives &amp; Masterbatch</li>
                        <li>Composites &amp; Nanocomposites</li>
                        <li>Dispersants &amp; Coupling Agents</li>
                        <li>Impact Modification of Plastics</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-gears"></i>
                    </div>
                    <h3>13. Polymer Manufacturing, Process &amp; Engineering</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Melt Flow &amp; Viscosity Control</li>
                        <li>Process Optimization &amp; Troubleshooting</li>
                        <li>Advanced Blending &amp; Compounding</li>
                        <li>Mathematical Modelling &amp; Simulation</li>
                        <li>Innovative Molding &amp; Extrusion</li>
                    </ul>
                </div>

                <div class="service-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-vial-circle-check"></i>
                    </div>
                    <h3>14. Analysis of Polymers &amp; Plastics</h3>
                    <ul
                        style="padding-left: 20px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                        <li>Mechanical &amp; Chemical Testing</li>
                        <li>Thermal &amp; Rheological Testing</li>
                        <li>Product Testing</li>
                        <li>Failure Analysis / Investigation</li>
                        <li>Automotive Polymers Testing</li>
                        <li>Composite Testing</li>
                        <li>Microbial Testing</li>
                        <li>NDT (Non-Destructive Testing)</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- CAD/CAM/CAE Capabilities Section -->
    <section class="ts-services-section" style="background: #ffffff; padding: 75px 20px; border-top: 1px solid #e2e8f0;">
        <div class="ts-services-container">
            <div class="section-header reveal">
                <div class="ts-subtitle red">
                    <span class="line"></span>
                    ENGINEERING &amp; SIMULATION
                </div>
                <h2>CAD/CAM/CAE</h2>
            </div>

            <div class="cad-logos-grid reveal-group">
                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website0.png" alt="PTC Creo Parametric" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website2.png" alt="CATIA" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website4.png" alt="Siemens NX" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website5.png" alt="SolidWorks" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website6.png" alt="ANSYS" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website7.png" alt="Altair HyperWorks" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website8.png" alt="Abaqus" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website9.png" alt="ESA Software" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website10.png" alt="Fibersim NX" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website67.png" alt="Autodesk Moldflow" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website68.png" alt="Moldex3D" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website69.png" alt="STAR-CCM+" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website70.png" alt="Mastercam" loading="lazy">
                    </div>
                </div>

                <div class="cad-logo-card reveal-child">
                    <div class="cad-logo-img-wrap">
                        <img src="assets/images/Logos for website/Logos for website71.png" alt="LS-DYNA" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inspection Services Section -->
    <section class="ts-services-section" style="background: #f8fafc; padding: 75px 20px; border-top: 1px solid #e2e8f0;">
        <div class="ts-services-container">
            <div class="section-header reveal">
                <div class="ts-subtitle red">
                    <span class="line"></span>
                    QUALITY AUDIT &amp; CONFORMITY
                </div>
                <h2>Inspection Services</h2>
            </div>

            <div class="services-grid reveal-group" style="grid-template-columns: repeat(3, 1fr);">

                <div class="service-card inspection-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3>01. General Inspection &amp; Assessment</h3>
                    <ul class="inspection-list">
                        <li><i class="fa-solid fa-circle-check"></i><span>Zero Defect Zero Effect (ZED) &mdash; BRONZE, SILVER, GOLD</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>FSSAI Third Party Assessment</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Hygiene Rating Audit</span></li>
                    </ul>
                </div>

                <div class="service-card inspection-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <h3>02. Rubber &amp; Plastic Product Industry</h3>
                    <ul class="inspection-list">
                        <li><i class="fa-solid fa-circle-check"></i><span>Pressure Pipes / Screen &amp; Casing Pipe</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>SWR Pipe / HDPE Pressure Pipes</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>PE Water Storage Tank</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Plastics Piping Systems for Water Supply / Polyethylene Pipes</span></li>
                    </ul>
                </div>

                <div class="service-card inspection-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </div>
                    <h3>03. Metal Product Industry</h3>
                    <ul class="inspection-list">
                        <li><i class="fa-solid fa-circle-check"></i><span>Steel Pipes / Ductile Iron Pipes &amp; Fittings</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Overhead Transmission Line Tower Fabrication</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>General Construction Steel Structure</span></li>
                    </ul>
                </div>

                <div class="service-card inspection-card card-span-2 reveal-child">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div class="sc-icon" style="margin-bottom: 0;">
                                <i class="fa-solid fa-wheat-awn"></i>
                            </div>
                            <h3 style="margin-bottom: 0;">04. Food Product Industry</h3>
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; color: #0a58ca; background: #eff6ff; padding: 4px 12px; border-radius: 20px; border: 1px solid #dbeafe;">7 Sub-Sectors Covered</span>
                    </div>
                    <div class="food-two-col-grid">
                        <ul class="inspection-list">
                            <li><i class="fa-solid fa-circle-check"></i><span>Food Processing</span></li>
                            <li><i class="fa-solid fa-circle-check"></i><span>Milk and Milk Products (Dairy Unit)</span></li>
                            <li><i class="fa-solid fa-circle-check"></i><span>Meat and Meat Products / Egg and Egg Products</span></li>
                            <li><i class="fa-solid fa-circle-check"></i><span>Fish and Fish Products / General Food Processing</span></li>
                        </ul>
                        <ul class="inspection-list">
                            <li><i class="fa-solid fa-circle-check"></i><span>Fruits and Vegetables, Seaweeds, and Nuts and Seeds</span></li>
                            <li><i class="fa-solid fa-circle-check"></i><span>Confectionery / Bakery and Confectionery Products</span></li>
                            <li><i class="fa-solid fa-circle-check"></i><span>Salts, Spices, Soups, Sauces, Salads and Protein Products</span></li>
                        </ul>
                    </div>
                </div>

                <div class="service-card inspection-card reveal-child">
                    <div class="sc-icon">
                        <i class="fa-solid fa-gas-pump"></i>
                    </div>
                    <h3>05. Oil &amp; Gas Industry</h3>
                    <ul class="inspection-list">
                        <li><i class="fa-solid fa-circle-check"></i><span>Gas Supply</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>Petroleum Product Storage &amp; Supply</span></li>
                        <li><i class="fa-solid fa-circle-check"></i><span>ERDMP (Emergency Response and Disaster Management Plan)</span></li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- Management System Audit, Training & Certification Services Section -->
    <section class="industries-section" style="background: #f8fafc; padding: 95px 20px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
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
                    <div class="universal-section-label" style="display: inline-block; padding: 6px 16px; background: rgba(13, 71, 161, 0.08); color: #0d47a1; border-radius: 20px; font-size: 13px; font-weight: 700; letter-spacing: 1px; margin-bottom: 12px;">
                        MANAGEMENT SYSTEMS &amp; ACCREDITATIONS
                    </div>
                    <h2 class="universal-section-heading" style="margin-top: 0; margin-bottom: 8px; font-size: 32px; font-weight: 600; color: #081D33;">
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

    <!-- Enquiry Section -->
    <section class="ts-booking-section" id="enquiry">
        <div class="ts-booking-container reveal-group">
            <!-- Left Info -->
            <div class="booking-left reveal-left">
                <div class="ts-subtitle red">
                    <span class="line"></span> ENQUIRY
                </div>
                <h2>Send Us <span style="color: var(--primary-blue);">an Enquiry</span></h2>
                <p>Have questions regarding our advisory services, technical consultancy, management system audits, or institutional accreditation support? Complete the form and our advisory specialists will contact you within 24 hours.</p>

                <div class="booking-features">
                    <div class="bf-card">
                        <i class="fa-solid fa-user-tie"></i>
                        <div class="bf-content">
                            <h4>Senior Technical Advisory</h4>
                            <p>Direct consultation with experienced polymer scientists, quality auditors, and industry veterans.</p>
                        </div>
                    </div>
                    <div class="bf-card">
                        <i class="fa-solid fa-compass-drafting"
                            style="color: var(--primary-blue); background: #F0F9FF;"></i>
                        <div class="bf-content">
                            <h4>Tailored Solutions</h4>
                            <p>Customized guidance aligned with ISO standards, NAAC/NBA criteria, and specific industry needs.</p>
                        </div>
                    </div>
                    <div class="bf-card">
                        <i class="fa-solid fa-shield-halved"></i>
                        <div class="bf-content">
                            <h4>Strict Confidentiality</h4>
                            <p>All discussions, proprietary processes, and organizational information are protected under NDA.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div class="booking-right reveal-right">
                <div class="booking-form-header">
                    <i class="fa-solid fa-envelope-open-text"></i>
                    <div>
                        <h3>Advisory Enquiry Form</h3>
                        <p>Connect with our technical advisory team</p>
                    </div>
                </div>

                <form action="contact-us.php#enquiry" method="POST" class="booking-form-body">
                    <div class="form-section-title">YOUR DETAILS</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name <span>*</span></label>
                            <input type="text" name="fullname" class="form-control" placeholder="Your full name" required>
                        </div>
                        <div class="form-group">
                            <label>Company / Organization <span>*</span></label>
                            <input type="text" name="company" class="form-control" placeholder="Company or institution name" required>
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
                            <input type="tel" name="phone" class="form-control" placeholder="10 digit number" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>

                    <div class="form-section-title">ENQUIRY DETAILS</div>

                    <div class="form-group">
                        <label>Service Area of Interest <span>*</span></label>
                        <select class="form-control" name="service" required>
                            <option value="">Select Service Area</option>
                            <option value="Management System Audit & Certification">Management System Audit &amp; Certification (ISO 9001, 14001, 45001, 21001, etc.)</option>
                            <option value="Academic Accreditation Support">Academic Accreditation Support (NAAC, NBA, AICTE, NIRF)</option>
                            <option value="Technical Consultation & Advisory">Technical Consultation &amp; Advisory Services</option>
                            <option value="Process Optimization & Troubleshooting">Process Optimization &amp; Troubleshooting</option>
                            <option value="Material Selection & Development">Material Selection &amp; Development</option>
                            <option value="Regulatory Compliance & Product Audits">Regulatory Compliance &amp; Product Audits</option>
                            <option value="Other Advisory Enquiries">Other Advisory Enquiries</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message / Requirements <span>*</span></label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Please describe your organization, project scope, standards of interest, or specific advisory requirements..." required></textarea>
                    </div>

                    <button type="submit" class="btn-book-submit">
                        <i class="fa-solid fa-paper-plane"></i> Submit Enquiry
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
                        What types of testing services does AKC-PTARC provide?
                        <i class="fa-solid fa-chevron-up" style="background: var(--primary-blue); color: white;"></i>
                    </div>
                    <div class="faq-body" style="display: block;">
                        AKC-PTARC provides a full spectrum of testing services including chemical composition analysis,
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
                        Do AKC-PTARC laboratories follow international standards?
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-body">
                        Yes, our testing procedures and methodologies are in strict compliance with ISO, ASTM, DIN, and
                        other global standards ensuring accuracy and reliability.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-header-toggle">
                        Can AKC-PTARC provide certification reports?
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
                <h2>Need Advisory Support?</h2>
                <p>Our expert team is ready to discuss your requirements and deliver tailored, accredited advisory solutions.</p>
            </div>
            <div class="cta-buttons">
                <a href="#enquiry" class="btn-light">
                    <i class="fa-regular fa-paper-plane"></i> Submit an Enquiry
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

        // Loader on submit
        const formSubmit = document.querySelector('.booking-form-body');
        if (formSubmit) {
            formSubmit.addEventListener('submit', function () {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
                    btn.style.opacity = '0.8';
                    btn.style.cursor = 'not-allowed';
                    setTimeout(() => { btn.disabled = true; }, 10);
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>