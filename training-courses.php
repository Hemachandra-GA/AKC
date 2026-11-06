<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style_hemachandra.css">
<!-- Bootstrap Icons CDN link -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Google Fonts CDN links for modern typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* Custom Styles for Training & Courses Page */
    .tc-hero {
        background: linear-gradient(120deg, #0D1B2A 0%, #1565C0 55%, #1976D2 100%);
        padding: 80px 0 0;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .tc-hero-inner {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: center;
        padding-bottom: 60px;
    }

    .tc-breadcrumb {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 20px;
    }

    .tc-breadcrumb a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
    }

    .tc-breadcrumb a:hover {
        color: #fff;
    }

    .tc-subtitle {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        color: #f9c74f;
        margin-bottom: 15px;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tc-subtitle .line {
        display: inline-block;
        width: 30px;
        height: 2px;
        background: #f9c74f;
    }

    .tc-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 42px;
        font-weight: 600 !important;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .tc-title span {
        color: #5cb3ff;
        font-weight: 600 !important;
    }

    .tc-desc {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.7;
        margin-bottom: 40px;
        max-width: 500px;
    }

    .tc-stats-row {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .tc-stat h3 {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 5px;
        line-height: 1;
    }

    .tc-stat p {
        font-size: 11px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Image Grid in Hero */
    .tc-image-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 15px;
        height: 400px;
    }

    .tc-img-main {
        grid-row: 1 / 3;
        grid-column: 1 / 2;
        background: #1a222e;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }

    .tc-img-side-top {
        grid-row: 1 / 2;
        grid-column: 2 / 3;
        background: #1a222e;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }

    .tc-img-side-bottom {
        grid-row: 2 / 3;
        grid-column: 2 / 3;
        background: #1a222e;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }

    .tc-img-placeholder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tc-img-label {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 4px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* Feature Bar */
    .tc-feature-bar {
        background: #151b26;
        padding: 25px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .tc-feature-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .tc-feature-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .tc-feature-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
    }

    .tc-feature-text h4 {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .tc-feature-text p {
        color: rgba(255, 255, 255, 0.5);
        font-size: 11px;
    }

    /* Courses Section */
    .tc-courses {
        padding: 80px 0;
        background: #f8f9fa;
    }

    .tc-courses-header {
        margin-bottom: 50px;
    }

    .tc-courses-header .pr-section-label {
        margin-top: 0;
        margin-bottom: 8px;
    }

    .tc-courses-header h2 {
        font-size: 32px;
        font-weight: 600;
        color: #081D33;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .tc-courses-header p {
        color: #555;
        max-width: 600px;
        font-size: 16px;
        line-height: 1.6;
        margin-top: 0;
    }

    .tc-grid-top {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }

    .tc-grid-bottom {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        max-width: 800px;
        margin: 0 auto;
    }

    .tc-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .tc-card:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        transform: translateY(-5px);
    }

    .tc-card-img {
        height: 180px;
        background: #e2e8f0;
        position: relative;
    }

    .tc-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tc-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .tc-card-icon {
        position: absolute;
        bottom: 15px;
        left: 15px;
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
    }

    .tc-card-content {
        padding: 30px 20px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .tc-card-subtitle {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary-blue);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tc-card-subtitle .line {
        display: inline-block;
        width: 15px;
        height: 2px;
        background: var(--primary-blue);
    }

    .tc-card-title {
        font-size: 20px;
        font-weight: 600;
        color: #111;
        margin-bottom: 12px;
    }

    .tc-card-desc {
        font-size: 13.5px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
    }

    .tc-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .tc-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .tc-meta-item {
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tc-meta-item i {
        color: #94a3b8;
    }

    .tc-btn {
        background: var(--primary-blue);
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .tc-btn:hover {
        background: #004499;
    }

    /* FAQ Styles */
    .tc-faq-q {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 15px;
        font-weight: 600;
        color: #111;
        cursor: pointer;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .tc-faq-q:hover {
        border-color: #cbd5e1;
    }

    .tc-faq-icon-left {
        width: 32px;
        height: 32px;
        background: #f0f6ff;
        color: var(--primary-blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .tc-faq-icon-right {
        width: 24px;
        height: 24px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .tc-faq-item.active .tc-faq-icon-right {
        background: var(--primary-blue);
        color: #fff;
    }

    .tc-faq-a {
        padding: 0 24px 20px 70px;
        font-size: 14px;
        color: #555;
        line-height: 1.6;
        display: none;
    }

    .tc-faq-item.active .tc-faq-a {
        display: block;
    }

    /* Bottom CTA */
    .tc-bottom-cta {
        background: url('assets/images/training_courses/CTA%20BAND.webp') center/cover no-repeat;
        background-color: #002b5e;
        /* Fallback */
        padding: 100px 0;
        color: #fff;
        text-align: left;
        position: relative;
    }

    .tc-bottom-cta-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1000px;
        margin: 0 auto;
    }

    .tc-bottom-cta-title h2 {
        font-size: 32px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 8px;
    }

    .tc-bottom-cta-title p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
        max-width: 500px;
        line-height: 1.7;
        margin-top: 0;
    }

    .tc-btn-outline {
        background: transparent;
        border: 2px solid #fff;
        color: #fff;
        padding: 14px 28px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .tc-btn-outline:hover {
        background: #fff;
        color: #002b5e;
    }

    @media (max-width: 992px) {
        .tc-hero-inner {
            grid-template-columns: 1fr;
        }

        .tc-grid-top,
        .tc-grid-bottom {
            grid-template-columns: 1fr;
        }

        .tc-bottom-cta-inner {
            flex-direction: column;
            text-align: center;
            gap: 30px;
        }

        .tc-feature-inner {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<!-- ==============================
     HERO SECTION
============================== -->
<section class="tc-hero">
    <div class="container tc-hero-inner">
        <!-- Left: Content -->
        <div>
            <div class="tc-breadcrumb">
                <a href="#">Home</a> / <span>Courses</span>
            </div>
            <div class="tc-subtitle">
                <span class="line"></span> PROFESSIONAL DEVELOPMENT
            </div>
            <h1 class="tc-title" style="font-weight: 600 !important;">Training & <br><span style="font-weight: 600 !important;">Courses</span></h1>
            <p class="tc-desc">At the onset of 2027, we will be launching various basic training classes &amp; certification courses</p>
        </div>

        <!-- Right: Image Grid -->
        <div class="tc-image-grid">
            <div class="tc-img-main tc-img-placeholder">
                <img src="assets/images/training_courses/Lab%20research.webp" alt="Research Lab">
                <div class="tc-img-label">RESEARCH LAB</div>
            </div>
            <div class="tc-img-side-top tc-img-placeholder">
                <img src="assets/images/training_courses/MATERIAL.webp" alt="Material">
                <div class="tc-img-label">MATERIAL</div>
            </div>
            <div class="tc-img-side-bottom tc-img-placeholder">
                <img src="assets/images/training_courses/Training.webp" alt="Training">
                <div class="tc-img-label">TRAINING</div>
            </div>
        </div>
    </div>

    <!-- Feature Bar -->
    <div class="tc-feature-bar">
        <div class="container tc-feature-inner">
            <div class="tc-feature-item">
                <div class="tc-feature-icon" style="color: #3b82f6;"><i class="bi bi-journal-bookmark-fill"></i></div>
                <div class="tc-feature-text">
                    <h4>Polymer Technology</h4>
                    <p>Science & Processing</p>
                </div>
            </div>
            <div class="tc-feature-item">
                <div class="tc-feature-icon" style="color: #60a5fa;"><i class="bi bi-box-seam"></i></div>
                <div class="tc-feature-text">
                    <h4>Packaging Technology</h4>
                    <p>Design & Standards</p>
                </div>
            </div>
            <div class="tc-feature-item">
               <div class="tc-feature-icon" style="color: #10b981;"><i class="bi bi-recycle"></i></div>

                <div class="tc-feature-text">
                    <h4>Sustainable Packaging</h4>
                    <p>Recycling & Circular</p>
                </div>
            </div>
            <div class="tc-feature-item">
                <div class="tc-feature-icon" style="color: #a855f7;"><i class="bi bi-file-earmark-text"></i></div>
                <div class="tc-feature-text">
                    <h4>Regulatory Affairs</h4>
                    <p>Compliance & Safety</p>
                </div>
            </div>
            <div class="tc-feature-item">
                <div class="tc-feature-icon" style="color: #ef4444;"><i class="bi bi-layers"></i></div>
                <div class="tc-feature-text">
                    <h4>Material Technology</h4>
                    <p>Innovation & Testing</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==============================
     COURSES SECTION
============================== -->
<section class="tc-courses">
    <div class="container">

        <div class="tc-courses-header">
            <div class="pr-section-label"><span class="pr-label-line"></span> OUR PROGRAMMES</div>
            <h2>Explore Course Categories</h2>
            <p>Five specialised disciplines covering the full spectrum of packaging science, regulatory compliance, and
                sustainable solutions for today's industry.</p>
        </div>

        <!-- Top Row (3 cards) -->
        <div class="tc-grid-top">
            <!-- Course 1 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="assets/images/training_courses/RESEARCH%20LAB.webp" alt="Polymer Technology">
                    <div class="tc-badge">CORE SCIENCE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> POLYMER TECHNOLOGY</div>
                    <h3 class="tc-card-title">Polymer Technology</h3>
                    <p class="tc-card-desc">Explore the science and structure of polymers used in modern packaging —
                        from synthesis and...</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <div class="tc-meta-item"><i class="bi bi-clock"></i> 3-5 Days</div>
                            <div class="tc-meta-item"><i class="bi bi-bar-chart-fill"></i> All Levels</div>
                        </div>
                        <a href="contact-us.php" class="tc-btn">Contact Us →</a>
                    </div>
                </div>
            </div>

            <!-- Course 2 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="assets/images/training_courses/Packaging%20Technology.webp" alt="Packaging Technology">
                    <div class="tc-badge">FUNDAMENTALS</div>
                    <div class="tc-card-icon"><i class="bi bi-box-seam-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> PACKAGING TECHNOLOGY</div>
                    <h3 class="tc-card-title">Packaging Technology</h3>
                    <p class="tc-card-desc">A comprehensive programme covering packaging design principles, material
                        selection, testing...</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <div class="tc-meta-item"><i class="bi bi-clock"></i> 3-5 Days</div>
                            <div class="tc-meta-item"><i class="bi bi-bar-chart-fill"></i> All Levels</div>
                        </div>
                        <a href="contact-us.php" class="tc-btn">Contact Us →</a>
                    </div>
                </div>
            </div>

            <!-- Course 3 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="assets/images/training_courses/Sustainable%20Packaging.webp" alt="Sustainable Packaging">
                    <div class="tc-badge">SUSTAINABILITY</div>
                    <div class="tc-card-icon"><i class="bi bi-recycle"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> SUSTAINABLE PACKAGING</div>
                    <h3 class="tc-card-title">Sustainable Packaging</h3>
                    <p class="tc-card-desc">Understand eco-design strategies, biodegradable materials, circular economy
                        frameworks, and...</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <div class="tc-meta-item"><i class="bi bi-clock"></i> 3-5 Days</div>
                            <div class="tc-meta-item"><i class="bi bi-bar-chart-fill"></i> All Levels</div>
                        </div>
                        <a href="contact-us.php" class="tc-btn">Contact Us →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row (2 cards) -->
        <div class="tc-grid-bottom">
            <!-- Course 4 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="assets/images/training_courses/Regulatory%20Affairs.webp" alt="Regulatory Affairs">
                    <div class="tc-badge">COMPLIANCE</div>
                    <div class="tc-card-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> REGULATORY AFFAIRS</div>
                    <h3 class="tc-card-title">Regulatory Affairs – Packaging</h3>
                    <p class="tc-card-desc">Navigate global packaging regulations, food contact compliance, safety
                        assessments, and labelling...</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <div class="tc-meta-item"><i class="bi bi-clock"></i> 3-5 Days</div>
                            <div class="tc-meta-item"><i class="bi bi-bar-chart-fill"></i> Intermediate</div>
                        </div>
                        <a href="contact-us.php" class="tc-btn">Contact Us →</a>
                    </div>
                </div>
            </div>

            <!-- Course 5 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="assets/images/training_courses/Material%20Technology.webp" alt="Material Technology">
                    <div class="tc-badge">ADVANCED</div>
                    <div class="tc-card-icon"><i class="bi bi-layers-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> MATERIAL TECHNOLOGY</div>
                    <h3 class="tc-card-title">Material Technology</h3>
                    <p class="tc-card-desc">Deep dive into substrate characterisation, barrier properties, adhesives,
                        coatings, and emerging smart...</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <div class="tc-meta-item"><i class="bi bi-clock"></i> 3-5 Days</div>
                            <div class="tc-meta-item"><i class="bi bi-bar-chart-fill"></i> Advanced</div>
                        </div>
                        <a href="contact-us.php" class="tc-btn">Contact Us →</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ==============================
     FAQ SECTION
============================== -->
<section class="pr-faq-section">
    <div class="container pr-faq-inner">

        <!-- Left: Expert Guidance -->
        <div class="pr-faq-left">
            <div class="pr-section-label"><span class="pr-label-line"></span> SUPPORT</div>
            <h2 class="pr-faq-title"><em>Frequently Asked<br>Questions</em></h2>

            <div class="pr-expert-card" style="margin-top: 30px;">
                <div class="pr-expert-chat-icon"><i class="bi bi-chat-dots"></i></div>
                <h3 class="pr-expert-title">Need Expert <span class="pr-blue">Guidance?</span></h3>
                <p class="pr-expert-desc">Our qualified team of polymer scientists and compliance specialists are
                    available to discuss your training requirements and recommend the right programme.</p>
                <p class="pr-expert-contact-label">Direct contact:</p>
                <p class="pr-expert-email"><i class="bi bi-envelope"></i> info@akc-ptarc.com</p>
                <a href="#" class="pr-btn pr-btn-white pr-btn-full" style="color: #1a2d4a;">Contact Our Team →</a>
            </div>
        </div>

        <!-- Right: FAQ Accordion -->
        <div class="pr-faq-right">

            <div class="tc-faq-item active">
                <div class="tc-faq-q">
                    <div style="display:flex; align-items:center;">
                        <div class="tc-faq-icon-left"><i class="bi bi-question-lg"></i></div>
                        What is the duration of each course?
                    </div>
                    <div class="tc-faq-icon-right"><i class="bi bi-x-lg"></i></div>
                </div>
                <div class="tc-faq-a">
                    Course durations vary by programme. Most courses run between 3 to 5 days, combining theoretical
                    instruction with hands-on practical sessions. The exact schedule is provided upon registration. Some
                    advanced modules may be offered as multi-week programmes with flexible scheduling options.
                </div>
            </div>

            <div class="tc-faq-item">
                <div class="tc-faq-q">
                    <div style="display:flex; align-items:center;">
                        <div class="tc-faq-icon-left"><i class="bi bi-laptop"></i></div>
                        Are these courses available online or offline?
                    </div>
                    <div class="tc-faq-icon-right"><i class="bi bi-plus-lg"></i></div>
                </div>
                <div class="tc-faq-a">
                    We offer both online virtual classrooms and in-person practical training at our accredited
                    facilities.
                </div>
            </div>

            <div class="tc-faq-item">
                <div class="tc-faq-q">
                    <div style="display:flex; align-items:center;">
                        <div class="tc-faq-icon-left"><i class="bi bi-award"></i></div>
                        Will I receive a certification after completing the course?
                    </div>
                    <div class="tc-faq-icon-right"><i class="bi bi-plus-lg"></i></div>
                </div>
                <div class="tc-faq-a">
                    Yes, all participants who successfully complete the programme will receive a globally recognised AKC
                    Certificate of Completion.
                </div>
            </div>

            <div class="tc-faq-item">
                <div class="tc-faq-q">
                    <div style="display:flex; align-items:center;">
                        <div class="tc-faq-icon-left"><i class="bi bi-person-check"></i></div>
                        Who can enrol in these courses?
                    </div>
                    <div class="tc-faq-icon-right"><i class="bi bi-plus-lg"></i></div>
                </div>
                <div class="tc-faq-a">
                    Our courses are designed for industry professionals, quality assurance teams, product developers,
                    and students looking to advance their knowledge.
                </div>
            </div>

            <div class="tc-faq-item">
                <div class="tc-faq-q">
                    <div style="display:flex; align-items:center;">
                        <div class="tc-faq-icon-left"><i class="bi bi-tools"></i></div>
                        Do the courses include practical or industry training?
                    </div>
                    <div class="tc-faq-icon-right"><i class="bi bi-plus-lg"></i></div>
                </div>
                <div class="tc-faq-a">
                    Yes, many of our programmes feature hands-on laboratory sessions and practical demonstrations using
                    state-of-the-art testing equipment.
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ==============================
     BOTTOM CTA SECTION
============================== -->
<section class="tc-bottom-cta">
    <div class="container tc-bottom-cta-inner">
        <div class="tc-bottom-cta-title">
            <div class="pr-section-label" style="color: #fff;"><span class="pr-label-line"
                    style="background: #fff;"></span> READY TO BEGIN?</div>
            <h2>Start Your Learning <br>Journey Today</h2>
            <p>Enrol in a course and gain industry-recognised expertise backed by AKC's world-class research and testing
                facilities.</p>
        </div>
        <div>
            <a href="#" class="tc-btn-outline">Download Brochure</a>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.tc-faq-q').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.tc-faq-item');
            var isActive = item.classList.contains('active');

            // Close all
            document.querySelectorAll('.tc-faq-item').forEach(function (i) {
                i.classList.remove('active');
                i.querySelector('.tc-faq-icon-right i').className = 'bi bi-plus-lg';
            });

            // Open clicked if it was not active
            if (!isActive) {
                item.classList.add('active');
                item.querySelector('.tc-faq-icon-right i').className = 'bi bi-x-lg';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>