<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style_hemachandra.css">
<!-- Bootstrap Icons CDN link -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Google Fonts CDN links for modern typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

<style>
    .service-card {
        color: #000 !important;
    }
</style>

<!-- ==============================
     HERO SECTION
============================== -->
<section class="pr-hero"
    style="background: url('assets/images/support_services/support_service_banner.webp') center/cover no-repeat; position: relative; z-index: 1;">
    <div class="container pr-hero-inner"
        style="flex-direction: column; align-items: flex-start; padding-top: 120px; padding-bottom: 60px;">
        <div class="pr-hero-content" style="max-width: 800px;">
            <!-- Breadcrumb -->
            <nav class="pr-breadcrumb">
                <a href="#">Home</a>
                <span class="pr-bc-sep">›</span>
                <a href="#">Services</a>
                <span class="pr-bc-sep">›</span>
                <a href="#">Industries</a>
                <span class="pr-bc-sep">›</span>
                <a href="#">Support Services</a>
            </nav>
            <div class="pr-hero-label"><span class="pr-label-line"></span> SUPPORT</div>
            <h1 class="pr-hero-title"><strong>Support</strong> <br><span class="pr-blue">Services</span></h1>
            <p class="pr-hero-desc">Ash Provides comprehensive support services tailored to your needs, ensuring high
                quality, safety, and regulatory compliance across multiple industry sectors.</p>
            <div class="pr-hero-buttons">
                <a href="#" class="pr-btn pr-btn-primary">Request a Test</a>
                <a href="#" class="pr-btn pr-btn-outline-white">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Banner placed right under the hero like the image -->
<div class="stats-banner" style="border-top: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
                <div class="stat-text">
                    <h3>40+</h3>
                    <p>Projects Done</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="bi bi-award"></i></div>
                <div class="stat-text">
                    <h3>ISO</h3>
                    <p>Accredited</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-text">
                    <h3>15+</h3>
                    <p>Years Experience</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                <div class="stat-text">
                    <h3>500+</h3>
                    <p>Testing Standards</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================
     THE SERVICES WE OFFER
============================== -->
<section class="services" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container" style="position: relative;">
        <div class="reveal">
            <div class="pr-section-label"><span class="pr-label-line"></span> OUR SERVICES</div>
            <h2 class="pr-section-title">The <span class="pr-blue">Services</span> we offer</h2>
        </div>

        <div class="services-grid reveal-group" style="margin-top: 40px; position: relative;">

            <!-- Left Arrow -->
            <div class="slider-arrow prev"
                style="background: var(--primary-blue); border:none; left: -60px; color:#fff;"><i
                    class="bi bi-chevron-left"></i></div>

            <div class="service-card reveal-child" style="background: #fff;">
                <div class="service-icon"><i class="bi bi-shield-check"
                        style="font-size:2rem; color: var(--primary-blue);"></i></div>
                <h3>Assurance</h3>
                <p>We provide comprehensive assurance services to guarantee your products meet the highest standards of
                    quality and safety before they reach the market.</p>
                <a href="#" class="service-link">Read More →</a>
            </div>

            <div class="service-card reveal-child" style="background: #fff;">
                <div class="service-icon"><i class="bi bi-clipboard-data"
                        style="font-size:2rem; color: var(--primary-blue);"></i></div>
                <h3>Testing</h3>
                <p>Our advanced testing laboratories perform rigorous analysis across various parameters to ensure
                    performance, reliability, and regulatory compliance.</p>
                <a href="#" class="service-link">Read More →</a>
            </div>

            <div class="service-card reveal-child" style="background: #fff;">
                <div class="service-icon"><i class="bi bi-search"
                        style="font-size:2rem; color: var(--primary-blue);"></i></div>
                <h3>Inspection</h3>
                <p>Expert inspection services at every stage of production to identify defects early, minimise risks,
                    and maintain consistent output quality.</p>
                <a href="#" class="service-link">Read More →</a>
            </div>

            <!-- Right Arrow -->
            <div class="slider-arrow next"
                style="background: var(--primary-blue); border:none; right: -60px; color:#fff;"><i
                    class="bi bi-chevron-right"></i></div>

        </div>

        <!-- Slider Dots -->
        <div style="text-align: center; margin-top: 30px; display: flex; justify-content: center; gap: 8px;">
            <span
                style="width: 30px; height: 8px; background: var(--primary-blue); border-radius: 4px; display: inline-block;"></span>
            <span style="width: 8px; height: 8px; background: #ccc; border-radius: 50%; display: inline-block;"></span>
            <span style="width: 8px; height: 8px; background: #ccc; border-radius: 50%; display: inline-block;"></span>
        </div>
    </div>
</section>

<!-- ==============================
     INDUSTRIES WE SERVE
============================== -->
<section class="industries">
    <div class="container">
        <div class="reveal" style="text-align: center;">
            <div class="pr-section-label" style="justify-content: center;"><span class="pr-label-line"></span>
                INDUSTRIES</div>
            <h2 class="pr-section-title text-center"><span class="pr-blue">Industries</span> We Serve</h2>
        </div>

        <div class="industries-grid reveal-group" style="margin-top: 40px;">
            <!-- Card 1 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-cup-straw" style="font-size: 2rem; color: #f59e0b;"></i>
                </div>
                <h3>Food and Beverage</h3>
                <p>Comprehensive testing and compliance solutions for the global food and beverage industry.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 2 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-capsule" style="font-size: 2rem; color: #3b82f6;"></i></div>
                <h3>Pharmaceutical</h3>
                <p>Rigorous analytical testing for pharmaceutical products, ensuring safety and efficacy.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 3 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-box-seam" style="font-size: 2rem; color: #8b5cf6;"></i></div>
                <h3>Packaging and Printing</h3>
                <p>Testing services for all types of packaging materials to verify durability and compliance.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 4 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-car-front" style="font-size: 2rem; color: #ef4444;"></i>
                </div>
                <h3>Automotive</h3>
                <p>Advanced material testing for automotive components, enhancing performance and safety.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 5 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-layers" style="font-size: 2rem; color: #ec4899;"></i></div>
                <h3>Textile</h3>
                <p>Quality assurance and compliance testing for textiles, apparel, and footwear products.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 6 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-gear-wide-connected"
                        style="font-size: 2rem; color: #64748b;"></i></div>
                <h3>Industrial automation</h3>
                <p>Certification and testing for industrial machinery and automated systems.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
            <!-- Card 7 -->
            <div class="industry-card reveal-child" style="border: 1px solid #eee;">
                <div class="industry-icon"><i class="bi bi-tools" style="font-size: 2rem; color: #f97316;"></i></div>
                <h3>Heavy engineering</h3>
                <p>Robust testing solutions for heavy engineering components and structural materials.</p>
                <a href="#" class="pr-blue" style="font-size: 13px; font-weight: 600;">Read More →</a>
            </div>
        </div>
    </div>
</section>

<!-- ==============================
     TRUSTED BY LOGOS
============================== -->
<section class="reveal"
    style="padding: 60px 0; background: #fff; text-align: center; border-bottom: 1px solid #f0f0f0;">
    <div class="container">
        <p
            style="font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 1px;">
            Trusted by 50+</p>
        <div
            style="display: flex; justify-content: center; gap: 60px; align-items: center; flex-wrap: wrap; opacity: 0.6; filter: grayscale(100%);">
            <!-- Placeholders for Logos. User will add images later. Using text/icons for now -->
            <span style="font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:8px;"><i
                    class="bi bi-at"></i> Atlassian</span>
            <span style="font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:8px;"><i
                    class="bi bi-slack"></i> slack</span>
            <span style="font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:8px;"><i
                    class="bi bi-dropbox"></i> Dropbox</span>
            <span style="font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:8px;"><i
                    class="bi bi-bag"></i> shopify</span>
            <span style="font-size: 24px; font-weight: 800; display:flex; align-items:center; gap:8px;"><i
                    class="bi bi-google"></i> Google</span>
        </div>

        <!-- Certificates Placeholder -->
        <div style="margin-top: 60px; display: flex; justify-content: center; gap: 30px;">
            <img src="assets/images/support_services/certificate_image.webp" alt="Certificate"
                style="max-height: 140px; width: auto; object-fit: contain;">
            <img src="assets/images/support_services/certificate_image.webp" alt="Certificate"
                style="max-height: 140px; width: auto; object-fit: contain;">
            <img src="assets/images/support_services/certificate_image.webp" alt="Certificate"
                style="max-height: 140px; width: auto; object-fit: contain;">
        </div>
    </div>
</section>

<!-- ==============================
     REQUEST THIS TEST / BOOKING FORM
============================== -->
<section style="padding: 100px 0; background: #f8f9fa;">
    <div class="container">
        <div class="reveal-group"
            style="display: grid; grid-template-columns: 1fr 500px; gap: 60px; align-items: start;">

            <!-- Left Side: Features -->
            <div class="reveal-left">
                <div class="pr-section-label"><span class="pr-label-line"></span> Book a Test</div>
                <h2 class="pr-section-title">Request <span class="pr-blue">a Test</span></h2>
                <p style="font-size: 16px; color: #555; margin-top: 0; margin-bottom: 40px; line-height: 1.7;">Ash
                    Provides comprehensive support services tailored to your needs, ensuring high quality, safety, and
                    regulatory compliance across multiple industry sectors.</p>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #f59e0b; font-size: 1.2rem;">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Fast Turnaround</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">Quick results delivery without
                                compromising on testing quality.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #3b82f6; font-size: 1.2rem;">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Accredited Facilities</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">ISO 17025 accredited labs ensuring
                                international standards compliance.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #10b981; font-size: 1.2rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Reliable Accuracy</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">State-of-the-art equipment providing
                                precise and dependable data.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #ef4444; font-size: 1.2rem;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Expert Personnel</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">Highly qualified scientists and
                                engineers managing every project.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #8b5cf6; font-size: 1.2rem;">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Customised Solutions</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">Tailor-made testing protocols to meet
                                specific client requirements.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div
                            style="width: 45px; height: 45px; background: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: #ec4899; font-size: 1.2rem;">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; margin-bottom: 5px;">Comprehensive Reporting</h4>
                            <p style="font-size: 13.5px; color: #666; margin: 0;">Detailed, easy-to-understand test
                                reports and certification documentation.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form Container -->
            <div class="reveal-right"
                style="background: #fff; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.08); overflow: hidden;">
                <div style="background: var(--primary-blue); padding: 25px; color: #fff;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <i class="bi bi-pencil-square" style="font-size: 1.5rem;"></i>
                        <div>
                            <h3 style="margin: 0; font-size: 20px;">Book a Test</h3>
                            <p style="margin: 0; font-size: 12px; opacity: 0.8; margin-top: 4px;">Fill the form below to
                                get started</p>
                        </div>
                    </div>
                </div>

                <form style="padding: 30px;">
                    <h4 style="font-size: 14px; margin-bottom: 20px; color: #111;">Contact Information</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">First
                                name <span class="pr-red">*</span></label>
                            <input type="text" placeholder="Your first name"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa;">
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Last
                                name <span class="pr-red">*</span></label>
                            <input type="text" placeholder="Your last name"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label
                            style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Email
                            address <span class="pr-red">*</span></label>
                        <input type="email" placeholder="you@company.com"
                            style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa;">
                    </div>

                    <div style="margin-bottom: 30px;">
                        <label
                            style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Select
                            Required Service <span class="pr-red">*</span></label>
                        <select
                            style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa; color: #555; appearance: auto;">
                            <option>Select a service from the list</option>
                            <option>Assurance</option>
                            <option>Testing</option>
                            <option>Inspection</option>
                        </select>
                    </div>

                    <!-- File Upload Box -->
                    <div
                        style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 30px; text-align: center; margin-bottom: 30px; background: #f8f9fa; cursor: pointer;">
                        <i class="bi bi-cloud-arrow-up"
                            style="font-size: 2rem; color: #94a3b8; margin-bottom: 10px; display: block;"></i>
                        <p style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 5px;">Click to
                            upload or drag and drop</p>
                        <p style="font-size: 12px; color: #94a3b8; margin: 0;">SVG, PNG, JPG or PDF (max. 10MB)</p>
                    </div>

                    <h4 style="font-size: 14px; margin-bottom: 20px; color: #111;">Company Details</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label
                                style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Company
                                name <span class="pr-red">*</span></label>
                            <input type="text" placeholder="Your company name"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa;">
                        </div>
                        <div>
                            <label
                                style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Phone
                                number <span class="pr-red">*</span></label>
                            <input type="tel" placeholder="+1 (555) 000-0000"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa;">
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label
                            style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #555;">Additional
                            Information</label>
                        <textarea placeholder="Tell us about your project requirements..." rows="4"
                            style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #f8f9fa; resize: vertical;"></textarea>
                    </div>

                    <button type="button" class="pr-btn pr-btn-primary pr-btn-full"
                        style="padding: 16px; font-size: 16px;">Book a Test</button>
                </form>
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
                <p class="pr-expert-desc">Our qualified team of specialists are available to discuss your testing
                    requirements and recommend the right programme.</p>
                <p class="pr-expert-contact-label">Direct contact:</p>
                <p class="pr-expert-email"><i class="bi bi-envelope"></i> info@akc.com</p>
                <a href="#" class="pr-btn pr-btn-outline-white pr-btn-full">Contact Our Team →</a>
            </div>
        </div>

        <!-- Right: FAQ Accordion -->
        <div class="pr-faq-right">
            <!-- FAQ 1 (Open) -->
            <div class="pr-faq-item pr-faq-open">
                <button class="pr-faq-q">
                    What types of services does AKC provide?
                    <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                </button>
                <div class="pr-faq-a">
                    <p>AKC provides a full spectrum of testing, inspection, and certification services across multiple
                        industries to ensure product quality and regulatory compliance.</p>
                    <div class="pr-faq-helpful">
                        <span>Was This Content Helpful ?</span>
                        <button class="pr-helpful-btn"><i class="bi bi-hand-thumbs-up"></i></button>
                        <button class="pr-helpful-btn"><i class="bi bi-hand-thumbs-down"></i></button>
                    </div>
                </div>
            </div>
            <!-- FAQ 2 -->
            <div class="pr-faq-item">
                <button class="pr-faq-q">
                    How long does standard testing take?
                    <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="pr-faq-a" style="display:none;">
                    <p>Turnaround times vary by test type, typically ranging from 5–15 business days for standard tests.
                        Expedited services are available upon request.</p>
                </div>
            </div>
            <!-- FAQ 3 -->
            <div class="pr-faq-item">
                <button class="pr-faq-q">
                    Do AKC laboratories follow international standards?
                    <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="pr-faq-a" style="display:none;">
                    <p>Yes, AKC laboratories are ISO/IEC 17025 accredited and follow all relevant ASTM, ISO, EN, and FDA
                        standards for each test method.</p>
                </div>
            </div>
            <!-- FAQ 4 -->
            <div class="pr-faq-item">
                <button class="pr-faq-q">
                    Can AKC provide certification reports?
                    <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="pr-faq-a" style="display:none;">
                    <p>Yes, AKC issues formal test reports and certificates of conformance accepted by regulators and
                        retailers globally.</p>
                </div>
            </div>
            <!-- FAQ 5 -->
            <div class="pr-faq-item">
                <button class="pr-faq-q">
                    What are the payment options and terms?
                    <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="pr-faq-a" style="display:none;">
                    <p>We offer flexible payment options including bank transfer and online payments. Standard terms are
                        30 days net upon invoice receipt.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ==============================
     BOTTOM CTA SECTION
============================== -->
<!-- Note: Removed the background image property so the user can add it later, just using a blue background. -->
<section class="pr-cta" style="background: var(--primary-blue); padding: 80px 0;">
    <div class="container pr-cta-inner">
        <div class="pr-cta-content">
            <div class="pr-section-label" style="color: #fff;"><span class="pr-label-line"
                    style="background: #fff;"></span> GET STARTED TODAY</div>
            <h2 class="pr-cta-title">Need Services?</h2>
            <p class="pr-cta-desc">Our expert team is ready to deliver precise, globally recognised results for your
                products and projects.</p>
        </div>
        <div class="pr-cta-buttons">
            <a href="#" class="pr-btn pr-btn-white" style="color: var(--primary-blue);"><i
                    class="bi bi-clipboard-check"></i> Request a Test</a>
            <a href="#" class="pr-btn pr-btn-outline-white"><i class="bi bi-envelope"></i> Contact Our Team</a>
        </div>
    </div>
</section>

<!-- FAQ Accordion JS -->
<script>
    document.querySelectorAll('.pr-faq-q').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.pr-faq-item');
            var answer = item.querySelector('.pr-faq-a');
            var icon = item.querySelector('.pr-faq-icon i');
            var isOpen = item.classList.contains('pr-faq-open');

            // Close all
            document.querySelectorAll('.pr-faq-item').forEach(function (i) {
                i.classList.remove('pr-faq-open');
                i.querySelector('.pr-faq-a').style.display = 'none';
                var ic = i.querySelector('.pr-faq-icon i');
                ic.className = 'bi bi-chevron-down';
                i.querySelector('.pr-faq-icon').classList.remove('active');
            });

            // Open clicked if it was closed
            if (!isOpen) {
                item.classList.add('pr-faq-open');
                answer.style.display = 'block';
                icon.className = 'bi bi-chevron-up';
                item.querySelector('.pr-faq-icon').classList.add('active');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>