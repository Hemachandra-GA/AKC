<?php
require_once 'includes/db_connect.php';
$pageTitle = "Testing & QC Analysis | NABL-Accredited Laboratory Testing Services – AKC";
$metaDescription = "AKC offers precision laboratory testing and quality assurance services across plastics, rubber, textiles, leather, toys, recyclate, pharmaceuticals, medical devices, dyes/coatings, food/agri, water, and electrical product categories. Globally certified. Scientifically proven. Request a Test today";
$metaKeywords = "laboratory testing services India, QC testing and analysis, NABL-accredited testing lab, quality assurance lab, industrial testing certification";
include 'includes/header.php';
?>

<!-- Custom Stylesheet Link for this Page -->
<link rel="stylesheet" href="assets/css/qc-analysis.css">

<main>
    <!-- Hero Section -->
    <section class="qc-hero-section">
        <div class="qc-hero-container">

            <!-- Left Info Block -->
            <div class="qc-hero-info">
                <div class="qc-subtitle">
                    <span class="dot"></span>— TOTAL QUALITY ASSURANCE
                </div>

                <h1 class="qc-hero-title">
                    Testing &<br> <span class="text-accent">Certification</span>
                </h1>

                <p class="qc-hero-desc">
                    Advanced laboratory testing and quality assurance for modern industries. Precision-driven. Globally
                    certified. Scientifically proven.
                </p>

                <div class="qc-hero-btns">
                    <a href="#categories" class="btn-qc-primary"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        Explore Testing
                    </a>
                    <a href="mailto:info@akc.com" class="btn-qc-outline">
                        Request a Test
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="qc-stats-row">
                    <div class="qc-stat-item">
                        <i class="fa-solid fa-flask-vial"></i>
                        <div class="qc-stat-text">
                            <span class="qc-stat-value">40+</span>
                            <span class="qc-stat-label">Expert Scientists</span>
                        </div>
                    </div>
                    <div class="qc-stat-item">
                        <i class="fa-solid fa-trophy"></i>
                        <div class="qc-stat-text">
                            <span class="qc-stat-value">ISO</span>
                            <span class="qc-stat-label">17025 Accredited</span>
                        </div>
                    </div>
                    <div class="qc-stat-item">
                        <i class="fa-solid fa-globe"></i>
                        <div class="qc-stat-text">
                            <span class="qc-stat-value">15+</span>
                            <span class="qc-stat-label">Industry Sectors</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Glassmorphic Floating Widget -->
            <div class="qc-contact-widget">
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
                    onclick="window.location.href='mailto:info@akc-ptarc.com?subject=Inquiry from QC Analysis Page'">
                    Send us a request
                </button>
            </div>

        </div>
    </section>

    <!-- Testing Scope Categories Grid Section -->
    <section id="categories" class="qc-categories-section">
        <div class="qc-categories-container">

            <div class="categories-section-header">
                <div class="qc-subtitle red">
                    <span class="line"></span>
                    SECTORS WE SERVE
                    <span class="line"></span>
                </div>
                <h2>Product Categories</h2>
            </div>

            <div class="qc-disciplines-grid">
                <?php
                if (isset($conn)) {
                    $prodStmt = $conn->query("SELECT id, name, slug, description FROM products ORDER BY sort_order ASC, name ASC");
                    if ($prodStmt) {
                        $icons = [
                            'fa-vial',
                            'fa-gear',
                            'fa-scroll',
                            'fa-briefcase',
                            'fa-gamepad',
                            'fa-recycle',
                            'fa-pills',
                            'fa-stethoscope',
                            'fa-spray-can',
                            'fa-apple-whole',
                            'fa-bottle-water',
                            'fa-microchip'
                        ];
                        $idx = 0;
                        while ($p = $prodStmt->fetch(PDO::FETCH_ASSOC)) {
                            $iconClass = $icons[$idx % count($icons)];
                            $idx++;
                            echo '<a href="dynamic-product.php?slug=' . htmlspecialchars($p['slug']) . '" class="discipline-link">';
                            echo '  <div class="discipline-card">';
                            echo '      <div class="card-icon-wrapper"><i class="fa-solid ' . $iconClass . '"></i></div>';
                            echo '      <h3>' . htmlspecialchars($p['name']) . '</h3>';
                            echo '      <p>' . htmlspecialchars($p['description']) . '</p>';
                            echo '  </div>';
                            echo '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Who We Are Section -->
    <section class="qc-who-we-are">
        <div class="qc-who-we-are-container">
            <div class="who-we-are-image">
                <img src="assets/images/testing-qc.jpg" alt="Lab Scientists working">
                <div class="experience-badge">
                    <span class="badge-num">15+</span>
                    <span class="badge-text">Years of<br>Excellence</span>
                </div>
            </div>
            <div class="who-we-are-content">
                <div class="qc-subtitle red">— WHO WE ARE</div>
                <h2>Your Total Quality Assurance Partner</h2>
                <p>AKC – Packaging Technology & Research Centre provides comprehensive testing, inspection,
                    certification, and quality assurance solutions that help industries maintain the highest standards
                    of safety, reliability, and sustainability. Our accredited laboratories and experienced team deliver
                    the scientific rigour that modern industry demands.</p>
                <p>Whether you are a manufacturer seeking product certification, an R&D team requiring advanced material
                    analysis, or a procurement team validating supplier compliance — AKC is your single-source quality
                    assurance partner.</p>

                <div class="who-we-are-stats">
                    <div class="stat-col">
                        <h4>40+</h4>
                        <p>Expert Scientists &<br>Engineers</p>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-col">
                        <h4>Global</h4>
                        <p>Industry Standards<br>Applied</p>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-col">
                        <h4>3</h4>
                        <p>Advanced Testing<br>Labs</p>
                    </div>
                </div>

                <a href="about.php#team-members" class="btn-qc-primary">Learn More About AKC</a>
            </div>
        </div>
    </section>

    <!-- Facility Section -->
    <section class="qc-facility-section">
        <div class="qc-facility-container">
            <div class="qc-subtitle red">— CHECK OUR FACILITIES</div>
            <h2>Facility</h2>
            <p>AKC's testing and QC facility spans four specialised laboratory disciplines, each equipped with
                precision instruments maintained to international calibration standards:</p>

            <div class="facility-tabs">
                <button class="tab-btn active"><i class="fa-solid fa-flask"></i> Chemical</button>
                <button class="tab-btn"><i class="fa-solid fa-gear"></i> Mechanical</button>
                <button class="tab-btn"><i class="fa-solid fa-dna"></i> Biological</button>
                <button class="tab-btn"><i class="fa-solid fa-bolt"></i> Electrical</button>
            </div>

            <div class="facility-images">
                <img src="assets/images/facility_lab_work.png" alt="Facility Image 1">
                <img src="assets/images/facility_lab_work.png" alt="Facility Image 2">
                <img src="assets/images/facility_lab_work.png" alt="Facility Image 3">
            </div>
        </div>
    </section>

    <!-- Final Call to Action Section -->
    <section class="qc-footer-cta">
        <div class="qc-footer-cta-container">
            <div class="cta-left">
                <div class="qc-subtitle">— READY TO BEGIN?</div>
                <h2>Need Testing or Certification Services?</h2>
                <p>Our expert team is ready to discuss your requirements and deliver precise, accredited results across
                    all testing disciplines.</p>
            </div>
            <div class="cta-right">
                <a href="mailto:info@akc.com" class="btn-cta-light">
                    Request a Test
                </a>
                <a href="#" class="btn-cta-outline">
                    Contact Our Experts
                </a>
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const facilityTabs = document.querySelectorAll('.facility-tabs .tab-btn');
        const facilityImages = document.querySelectorAll('.facility-images img');

        // Image mapping for different categories using available lab images
        const imageSets = {
            'Chemical': [
                'assets/images/facility_lab_work.png',
                'assets/images/facility_lab_work.png',
                'assets/images/facility_lab_work.png'
            ],
            'Mechanical': [
                'assets/images/laboratory_facility_1.png',
                'assets/images/laboratory_facility_2.png',
                'assets/images/laboratory_facility_3.png'
            ],
            'Biological': [
                'assets/images/testing-qc.jpg',
                'assets/images/laboratory_facility_1.png',
                'assets/images/facility_lab_work.png'
            ],
            'Electrical': [
                'assets/images/laboratory_facility_3.png',
                'assets/images/testing-qc.jpg',
                'assets/images/laboratory_facility_2.png'
            ]
        };

        facilityTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active class from all tabs
                facilityTabs.forEach(t => t.classList.remove('active'));
                // Add active class to the clicked tab
                this.classList.add('active');

                // Get category name from tab text
                const category = this.textContent.trim();

                // Get mapped images, fallback to Chemical if not found
                const newImages = imageSets[category] || imageSets['Chemical'];

                // Update images with a smooth fade effect
                facilityImages.forEach((img, index) => {
                    img.style.opacity = '0';
                    setTimeout(() => {
                        if (newImages[index]) {
                            img.src = newImages[index];
                        }
                        img.style.transition = 'opacity 0.4s ease';
                        img.style.opacity = '1';
                    }, 300);
                });
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>