<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style_hemachandra.css">
<style>
    /* Card Styles from Training Courses */
    .tc-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        text-align: left;
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
        text-decoration: none;
    }

    .tc-btn:hover {
        background: #004499;
    }
</style>
<!-- Hero Section -->
<section class="hero"
    style="background-image: url(''); background-color: #1a222e; min-height: 80vh; display: flex; align-items: center;">
    <div class="container hero-container" style="justify-content: space-between; width: 100%;">
        <div class="hero-content" style="max-width: 650px; padding: 40px 0;">
            <div class="section-subtitle">
                <span class="line"></span> TOTAL QUALITY ASSURANCE
            </div>
            <h1 class="hero-title">Chemical <br><span class="text-blue">Testing & Analysis</span></h1>
            <p class="hero-desc">With chemicals used in almost every product and facet of our daily lives, it is
                important to ensure they are produced efficiently and in compliance to regulatory standards. Possible
                health hazards need to be identified and assessed at every step of the global supply chain.</p>
            <div class="hero-buttons">
                <a href="#" class="btn btn-primary">Explore Services</a>
                <a href="#" class="btn btn-transparent">Request a Test</a>
            </div>
        </div>

        <div class="hero-contact-card">
            <div class="section-subtitle">
                <span class="line"></span> CONTACT US
            </div>
            <h2 class="contact-phone"><a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91
                    88604 07220</a></h2>
            <hr class="card-divider">
            <div class="contact-links">
                <a href="mailto:info@akc-ptarc.com" class="contact-email" style="text-decoration: none;">
                    <i class="bi bi-envelope"></i> Email Us: info@akc-ptarc.com
                </a>
            </div>
            <a href="mailto:info@akc-ptarc.com?subject=Inquiry from Chemicals Testing Page"
                class="btn btn-primary btn-full">Send us a request</a>
        </div>
    </div>

    <!-- Slider Controls -->
    <button class="slider-arrow prev"><i class="bi bi-chevron-left"></i></button>
    <button class="slider-arrow next"><i class="bi bi-chevron-right"></i></button>
    <div class="slider-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</section>

<section class="stats-banner">
    <div class="container stats-inner">
        <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-flask"></i></div>
            <div class="stat-text">
                <h3>40+</h3>
                <p>Expert Scientists</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-trophy"></i></div>
            <div class="stat-text">
                <h3>ISO</h3>
                <p>17025 Accredited</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-globe"></i></div>
            <div class="stat-text">
                <h3>15+</h3>
                <p>Industry Sectors</p>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
            <div class="stat-text">
                <h3>500+</h3>
                <p>Tests Conducted Monthly</p>
            </div>
        </div>
    </div>
</section>

<!-- Overview Content Section -->
<section class="py-5" style="padding: 60px 0; background: #fff;">
    <div class="container" style="display: flex; align-items: center; gap: 40px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px; text-align: left;">
            <p style="font-size: 16px; color: #555; line-height: 1.8; margin-bottom: 20px;">
                With new challenges at every corner of your business from complex manufacturing to new governing
                requirements, <strong>AKC</strong> can ensure you get to market quickly and responsibly. For all phases
                of your chemical products' life cycle, our global network of scientists, inspectors, auditors, engineers
                and regulatory experts have the expertise and experience to assist you and your organisation to comply
                with regulatory and safety requirements, manage risk, maintain product integrity, and meet
                sustainability and environmental goals. We help you test and analyse all things chemical, no matter how
                complex.
            </p>
            <p style="font-size: 16px; color: #555; line-height: 1.8;">
                Chemical manufacturing itself can also be complicated with expensive infrastructures and specialist
                equipment. At <strong>AKC</strong>, our corrosion and asset integrity experts provide testing and
                consulting on the life management of chemical manufacturing facilities and on vital equipment in
                hazardous locations to lower the risk of material and asset failure and support the overall safety of
                your operations.
            </p>
        </div>
        <div style="flex: 1; min-width: 300px;">
            <img src="assets/images/testing_qc_images/who_we_are.webp" alt="Your Total Quality Assurance Partner"
                style="width: 100%; border-radius: 12px; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services" style="background: #f8f9fa;">
    <div class="container text-center">
        <div class="section-subtitle center">
            <span class="line"></span> CHEMICAL SERVICES <span class="line"></span>
        </div>
        <h2 class="section-title">Explore Our Chemical Expertise</h2>
        <p class="section-desc">Comprehensive analysis, testing, and consulting across various chemical sectors.</p>

        <div class="services-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; text-align: left;">

            <!-- Service Card 1 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Agrochemicals & Pesticides">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> AGROCHEMICALS</div>
                    <h3 class="tc-card-title">Agrochemicals & Pesticides</h3>
                    <p class="tc-card-desc">Agrochemical and pesticide expertise supporting your business from
                        innovation to the marketplace.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 2 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Basic & Industrial Chemicals">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> INDUSTRIAL</div>
                    <h3 class="tc-card-title">Basic & Industrial Chemicals</h3>
                    <p class="tc-card-desc">Understanding chemical composition and processes, to help you meet quality,
                        environmental and safety standards.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 3 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Dyes & Detergents">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> DYES</div>
                    <h3 class="tc-card-title">Dyes & Detergents</h3>
                    <p class="tc-card-desc">Delivering a high standard of chemical safety throughout the supply chain to
                        achieve zero discharge of hazardous chemicals, detergents and dyes.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 4 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Lubricants & Greases">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> LUBRICANTS</div>
                    <h3 class="tc-card-title">Lubricants & Greases</h3>
                    <p class="tc-card-desc">Helping our customers go to market and maintain expensive machinery with
                        lubricants, fluids and additives testing plus inspection and certification.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 5 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Nanomaterials">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> NANOMATERIALS</div>
                    <h3 class="tc-card-title">Nanomaterials</h3>
                    <p class="tc-card-desc">Supporting the next generation of nanomaterial innovation.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 6 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Petrochemicals">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> PETROCHEMICALS</div>
                    <h3 class="tc-card-title">Petrochemicals</h3>
                    <p class="tc-card-desc">Supporting the global petrochemical industry with expert testing,
                        inspection, certification, outsourcing, compliance and other services.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 7 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Polymer Testing">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> POLYMERS</div>
                    <h3 class="tc-card-title">Polymer Testing</h3>
                    <p class="tc-card-desc">Integrated polymer processing, testing and development services delivered
                        through our experienced scientists and engineers.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Service Card 8 -->
            <div class="tc-card">
                <div class="tc-card-img">
                    <img src="" alt="Specialty Chemicals">
                    <div class="tc-badge">SERVICE</div>
                    <div class="tc-card-icon"><i class="bi bi-gear-fill"></i></div>
                </div>
                <div class="tc-card-content">
                    <div class="tc-card-subtitle"><span class="line"></span> SPECIALTY</div>
                    <h3 class="tc-card-title">Specialty Chemicals</h3>
                    <p class="tc-card-desc">Supporting specialty chemical innovation, bringing insight on technical and
                        performance attributes.</p>
                    <div class="tc-card-footer">
                        <div class="tc-meta">
                            <span class="tc-meta-item"><i class="bi bi-globe"></i> Global Standard</span>
                            <span class="tc-meta-item"><i class="bi bi-award"></i> ISO 17025</span>
                        </div>
                        <a href="#" class="tc-btn">Explore Service &rarr;</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>



<?php include 'includes/footer.php'; ?>