<?php 
$pageTitle = "Our Team | AKC PTARC";
$metaDescription = "Meet the multidisciplinary team and scientific leadership at AKC PTARC.";
$metaKeywords = "AKC PTARC team, scientific leadership, polymer scientists, packaging experts";
include 'includes/header.php'; 
?>
<main>
    <!-- PAGE HEADER -->
    <div style="background: url('assets/images/support-services.jpg') no-repeat center center/cover; padding: 100px 0; text-align: center; position: relative;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 22, 40, 0.85);"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <h1 style="color: white; font-size: 36px; font-weight: 600; margin-bottom: 10px;">Our Team</h1>
            <p style="color: #90caf9; font-size: 1.2rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">Scientific Leadership & Expertise</p>
        </div>
    </div>

    <!-- Leadership & Team Section -->
    <section id="leadership" style="padding: 90px 5%; background: #f8fafc;">
        <style>
            .leader-row {
                display: flex;
                align-items: flex-start;
                gap: 40px;
                max-width: 1400px;
                margin: 0 auto 60px;
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            }
            .leader-row.reverse {
                flex-direction: row-reverse;
            }
            .leader-photo {
                flex: 0 0 300px;
                height: 380px;
                border-radius: 20px;
                overflow: hidden;
                border: none;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            }
            .leader-photo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top;
            }
            .leader-content {
                flex: 1;
            }
            .leader-content h3 {
                color: #111;
                font-size: 26px;
                font-weight: 800;
                margin-bottom: 5px;
            }
            .leader-content .designation {
                color: var(--primary-blue);
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .leader-content h4 {
                font-size: 22px;
                color: #333;
                margin-bottom: 20px;
                font-weight: 700;
            }
            .leader-content p {
                font-size: 16px;
                color: #555;
                line-height: 1.6;
                margin-bottom: 15px;
            }
            .quote-end {
                font-weight: 700;
                color: var(--primary-blue);
                font-size: 18px;
                font-style: italic;
                margin-top: 25px;
                padding-left: 15px;
                border-left: 4px solid var(--primary-blue);
            }

            @media (max-width: 900px) {
                .leader-row, .leader-row.reverse {
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    padding: 35px 25px;
                    gap: 30px;
                }
                .leader-photo {
                    flex: 0 0 260px;
                    height: 330px;
                }
                .quote-end {
                    border-left: none;
                    padding-left: 0;
                }
            }

            /* Team Grid Styles */
            .team-section {
                max-width: 1200px;
                margin: 100px auto 0;
            }
            .team-category {
                margin-bottom: 60px;
            }
            .team-category h3 {
                text-align: center;
                font-size: 26px;
                font-weight: 800;
                color: #111;
                margin-bottom: 40px;
                padding-bottom: 15px;
                position: relative;
            }
            .team-category h3::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 4px;
                background: var(--primary-blue);
                border-radius: 2px;
            }
            .team-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 30px;
            }
            .team-card {
                background: white;
                padding: 30px 25px;
                border-radius: 16px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.06);
                text-align: center;
                border-top: 5px solid var(--primary-blue);
                transition: transform 0.3s, box-shadow 0.3s;
            }
            .team-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            }
            .team-card h4 {
                color: #111;
                font-size: 20px;
                font-weight: 800;
                margin-bottom: 8px;
            }
            .team-card p.designation {
                color: var(--primary-blue);
                font-size: 15px;
                font-weight: 700;
                margin-bottom: 12px;
            }
            .team-card p.qual {
                color: #666;
                font-size: 14px;
                margin-bottom: 8px;
                font-weight: 500;
            }
            .team-card p.email {
                color: #888;
                font-size: 14px;
                word-break: break-all;
            }
            
            .multi-team {
                text-align: center;
                max-width: 900px;
                margin: 0 auto 60px;
                padding: 40px;
                background: rgba(13, 71, 161, 0.04);
                border-radius: 16px;
                border: 1px solid rgba(13, 71, 161, 0.1);
            }
            .multi-team h3 {
                font-size: 24px;
                font-weight: 600;
                margin-top: 0;
                margin-bottom: 8px;
                color: #081D33;
            }
            .multi-team p {
                color: #555;
                line-height: 1.7;
                font-size: 16px;
                margin-top: 0;
            }
        </style>

        <div class="reveal" style="text-align: center; margin-bottom: 70px;">
            <div class="section-subtitle center" style="justify-content: center; display: flex; align-items: center; color: var(--accent-red); font-weight: 700; letter-spacing: 1px; margin-top: 0; margin-bottom: 8px;">
                <span class="line" style="display: inline-block; width: 30px; height: 2px; background-color: var(--accent-red); margin: 0 10px;"></span> 
                LEADERSHIP 
                <span class="line" style="display: inline-block; width: 30px; height: 2px; background-color: var(--accent-red); margin: 0 10px;"></span>
            </div>
            <h2 style="font-size: 32px; font-weight: 600; color: #081D33; margin-top: 0; margin-bottom: 8px;">Our Guiding Vision</h2>
        </div>

        <!-- 1. Founder Sir (Photo Left, Text Right) -->
        <div class="leader-row reveal-group">
            <div class="leader-photo reveal-left">
                <img src="assets/images/ashok-chaturvedi.jpeg?v=2" onerror="this.src='https://ui-avatars.com/api/?name=Ashok+kumar+Chaturvedi&background=0A58CA&color=fff&size=400'" alt="Mr. Ashok kumar chaturvedi">
            </div>
            <div class="leader-content reveal-right">
                <h4>Founder's Vision</h4>
                <p>Every industry advances on one enduring foundation: trust in science.</p>
                <p>At AKC PTARC, our vision is to build an institution that transforms scientific knowledge into industrial progress. We believe testing & evaluating is the starting point for innovation, stronger products, resilient infrastructure, informed regulation, and sustainable growth. Every confident decision begins with credible evidence.</p>
                <p>AKC PTARC brings together scientific testing, technical consultancy, applied research, innovation, and knowledge creation within a single ecosystem. Across chemical, mechanical, electrical, and biological sciences, we support organizations throughout the product lifecycle, helping them improve quality, strengthen compliance, and compete confidently in global markets.</p>
                <p>Our commitment also extends to developing future talent. In alignment with the vision of NEP 2020 and with the approval of the Sector Skill Council (SSC), we aim to foster meaningful academia–industry collaboration through academic up-skilling, research, partnerships, and technical engagement that bridge knowledge with real-world application. This vision will be implemented phase wise through a learning ecosystem comprising online, hybrid, and full-time programs, enabling learners to build industry-relevant skills through accessible, flexible, and immersive pathways.</p>
                <p>As an NABL accredited, GLP compliant, and BIS certified institution, we uphold the highest standards of scientific integrity, technical excellence, and impartiality. Our purpose is clear: to create an institution where science empowers industry, innovation drives progress, and knowledge creates lasting impact.</p>
                <div class="quote-end">Progress begins where science earns trust.</div>
                <div style="margin-top: 25px;">
                    <h3>Mr. Ashok kumar chaturvedi</h3>
                    <div class="designation">Founder, AKC-PTARC</div>
                </div>
            </div>
        </div>

        <!-- 2. Pillai Sir (Text Left, Photo Right) -->
        <div class="leader-row reverse reveal-group">
            <div class="leader-photo reveal-right">
                <img src="assets/images/jeevaraj.jpeg" onerror="this.src='https://ui-avatars.com/api/?name=Jeevaraj+Pillai&background=10B981&color=fff&size=400'" alt="Mr. Jeevaraj Pillai">
            </div>
            <div class="leader-content reveal-left">
                <h4>Our Commitment to Excellence</h4>
                <p>The strength of an institution is measured by the confidence it inspires.</p>
                <p>At AKC PTARC, excellence is reflected not only in the accuracy of our work but in the integrity of our decisions, the consistency of our quality systems, and the value we create for every stakeholder. These principles shape our culture and define every engagement.</p>
                <p>As an NABL accredited, GLP compliant, BIS and ISO certified institution, we provide comprehensive testing, technical consultancy, conformity assessment, and scientific support that help organizations strengthen product performance, meet regulatory expectations, and accelerate innovation. Our multidisciplinary expertise enables us to deliver practical solutions with precision, responsiveness, and technical depth.</p>
                <p>We believe enduring partnerships are built on transparency, accountability, and trust. By combining scientific capability with industry insight, we help businesses navigate complex technical challenges while maintaining the highest standards of quality and compliance.</p>
                <div class="quote-end">Excellence is sustained when integrity becomes culture.</div>
                <div style="margin-top: 25px;">
                    <h3>Mr. Jeevaraj Pillai</h3>
                    <div class="designation">Patron, AKC PTARC</div>
                </div>
            </div>
        </div>

        <!-- 3. Nayak Sir (Photo Left, Text Right) -->
        <div class="leader-row reveal-group">
            <div class="leader-photo reveal-left">
                <img src="assets/images/mr_nayak.jpeg" onerror="this.src='https://ui-avatars.com/api/?name=S.K.+Nayak&background=0D47A1&color=fff&size=400'" alt="Dr. S.K.Nayak">
            </div>
            <div class="leader-content reveal-right">
                <h4>Scientific Leadership</h4>
                <p>Scientific leadership is defined by the knowledge we create and the impact that knowledge delivers.</p>
                <p>At AKC PTARC, advanced testing is complemented by applied research, technical consultancy, innovation, and multidisciplinary scientific expertise. Every methodology, analysis, and recommendation reflects our commitment to accuracy, reliability, and internationally accepted standards.</p>
                <p>We continuously strengthen our capabilities through modern technologies, research driven thinking, and scientific collaboration that address emerging industrial challenges. In parallel, we advance the industry academia interface envisioned by NEP 2020 through academic upskilling, collaborative research, technical training, and knowledge exchange that prepare future professionals for a rapidly evolving world.</p>
                <p>As an NABL accredited, GLP compliant, and BIS certified institution, AKC PTARC stands at the intersection of science, industry, and innovation. Our responsibility is to generate knowledge, enable informed decisions, and deliver technical leadership that creates measurable value for industry and society.</p>
                <div class="quote-end">Innovation creates value only when it is grounded in evidence.</div>
                <div style="margin-top: 25px;">
                    <h3>Dr. S.K.Nayak</h3>
                    <div class="designation">Chief Scientific Advisor, AKC PTARC</div>
                    <div style="margin-top: 10px;">
                        <a href="https://drsknayak.in/" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; color: #0056b3; font-weight: 500; text-decoration: none; font-size: 0.95rem;">
                            <i class="fa-solid fa-link"></i> Visit (drsknayak.in)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members Grid -->
        <div class="team-section" id="team-members">
            
            <div class="multi-team reveal">
                <h3>A Multidisciplinary Team</h3>
                <p>Our team represents a blend of advanced scientific qualifications, specialised technical training, and practical industry experience. Together, we bring the scientific depth and technical capability required to uphold the highest standards of quality, accuracy, and professional service.</p>
            </div>

            <!-- Scientific & Quality Leadership -->
            <div class="team-category">
                <h3 class="reveal">Scientific & Quality Leadership</h3>
                <div class="team-grid reveal-group">
                    <div class="team-card reveal-child">
                        <h4>Dr. Rahul Dubey</h4>
                        <p class="designation">Laboratory-in-Chief</p>
                        <p class="qual">Ph.D. in Polymer Chemistry</p>
                        <p class="email"><a href="mailto:labochief@akc-ptarc.com" style="color: inherit;">labochief@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Dr. Meenakshi Tanwar</h4>
                        <p class="designation">Senior Scientist & Quality Manager (QM)</p>
                        <p class="qual">Ph.D. in Chemistry</p>
                        <p class="email"><a href="mailto:meenakshi.srsqm@akc-ptarc.com" style="color: inherit;">meenakshi.srsqm@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Dr. Asish K. Sahu</h4>
                        <p class="designation">Senior Scientist & Technical Manager (TM)</p>
                        <p class="qual">Ph.D. in Chemistry</p>
                        <p class="email"><a href="mailto:asish.srstm@akc-ptarc.com" style="color: inherit;">asish.srstm@akc-ptarc.com</a></p>
                    </div>
                </div>
            </div>

            <!-- Scientific & Technical Team -->
            <div class="team-category">
                <h3 class="reveal">Scientific & Technical Team</h3>
                <div class="team-grid reveal-group">
                    <div class="team-card reveal-child">
                        <h4>Dr. Tushar Kanta Nayak</h4>
                        <p class="designation">Scientist</p>
                        <p class="qual">Ph.D. in Chemistry</p>
                        <p class="email"><a href="mailto:tushar.sci@akc-ptarc.com" style="color: inherit;">tushar.sci@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Vishakha Gaur</h4>
                        <p class="designation">Technical Officer</p>
                        <p class="qual">M.Tech. in Chemical Engineering</p>
                        <p class="email"><a href="mailto:vishakha.to@akc-ptarc.com" style="color: inherit;">vishakha.to@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Shubham Pandey</h4>
                        <p class="designation">Assistant Technical Officer</p>
                        <p class="qual">Diploma in Plastic Engineering</p>
                        <p class="email"><a href="mailto:shubham.ato@akc-ptarc.com" style="color: inherit;">shubham.ato@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Syed Rizwan Ali</h4>
                        <p class="designation">Assistant Technical Officer</p>
                        <p class="qual">M.Sc. in Chemistry</p>
                        <p class="email"><a href="mailto:rizwan.ato@akc-ptarc.com" style="color: inherit;">rizwan.ato@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Balkrishna Tripathi</h4>
                        <p class="designation">Assistant Technical Officer</p>
                        <p class="qual">M.Sc. Tech.</p>
                        <p class="email"><a href="mailto:balkrishna.ato@akc-ptarc.com" style="color: inherit;">balkrishna.ato@akc-ptarc.com</a></p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Anshul Sharma</h4>
                        <p class="designation">Junior Technical Officer</p>
                        <p class="qual">B.Sc. in Biotechnology</p>
                        <p class="email"><a href="mailto:anshul.jto@akc-ptarc.com" style="color: inherit;">anshul.jto@akc-ptarc.com</a></p>
                    </div>
                </div>
            </div>

            <!-- Laboratory & Client Support -->
            <div class="team-category">
                <h3 class="reveal">Laboratory & Client Support</h3>
                <div class="team-grid reveal-group" style="grid-template-columns: repeat(auto-fit, minmax(280px, 400px)); justify-content: center;">
                    <div class="team-card reveal-child">
                        <h4>Shivam Kumar</h4>
                        <p class="designation">Laboratory Analyst</p>
                        <p class="qual">B.Sc. (PCM)</p>
                        <p class="email">+91 72478 12566</p>
                    </div>
                    <div class="team-card reveal-child">
                        <h4>Puspanjali</h4>
                        <p class="designation">Front Desk Executive</p>
                        <p class="qual">Bachelor's Degree</p>
                        <p class="email"><a href="mailto:info@akc-ptarc.com" style="color: inherit;">info@akc-ptarc.com</a></p>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>
<?php include 'includes/footer.php'; ?>