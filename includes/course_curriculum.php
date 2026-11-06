<section class="course-curriculum-section">
    <div class="course-curriculum-container">
        <div class="curriculum-card">
            <!-- Header -->
            <div class="curriculum-header">
                <div class="curriculum-title-area">
                    <div class="section-header">
                        <h3 class="section-title">CURRICULUM</h3>
                        <div class="title-line"></div>
                    </div>
                    <h2 class="curriculum-main-title">Course Topics</h2>
                </div>
                <div class="curriculum-badge">
                    <i class="fa-solid fa-table-list"></i> <strong>8</strong> Topics
                </div>
            </div>

            <!-- Content -->
            <div class="curriculum-content">
                <!-- Module A -->
                <div class="curriculum-module">
                    <h4 class="module-title">MODULE A — MATERIALS & PROPERTIES</h4>
                    <div class="accordion-list">
                        <!-- Accordion Item 1 (Open) -->
                        <div class="accordion-item open">
                            <div class="accordion-header">
                                <span class="accordion-num">01</span>
                                <h5 class="accordion-title">Polymer Properties and Selection Criteria</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-xmark"></i></span>
                            </div>
                            <div class="accordion-body">
                                <ul>
                                    <li>Molecular weight distribution and its effect on mechanical performance</li>
                                    <li>Thermal, barrier, and optical properties of common packaging polymers</li>
                                    <li>Criteria for selecting resins: cost, processability, regulatory compliance</li>
                                    <li>Comparison of commodity vs. engineering polymers</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Accordion Item 2 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">02</span>
                                <h5 class="accordion-title">Plastic Packaging and the Circular Economy</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Detailed breakdown of plastic lifecycle, recycling technologies, and designing for the circular economy.</p>
                            </div>
                        </div>

                        <!-- Accordion Item 3 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">03</span>
                                <h5 class="accordion-title">Property Comparisons</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Comprehensive analysis of different material properties to choose the right option for your specific packaging needs.</p>
                            </div>
                        </div>

                        <!-- Accordion Item 4 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">04</span>
                                <h5 class="accordion-title">Sheet and Film Extrusion</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Exploration of extrusion processes, machinery, and techniques used to create uniform sheets and films.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Module B -->
                <div class="curriculum-module">
                    <h4 class="module-title">MODULE B — CONVERSION PROCESSES</h4>
                    <div class="accordion-list">
                        <!-- Accordion Item 5 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">05</span>
                                <h5 class="accordion-title">Injection Moulding Plastic</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Deep dive into injection moulding processes, typical defects, and mould design fundamentals.</p>
                            </div>
                        </div>
                        
                        <!-- Accordion Item 6 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">06</span>
                                <h5 class="accordion-title">Thermoforming</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Understanding vacuum and pressure forming techniques, material selection, and cycle times.</p>
                            </div>
                        </div>

                        <!-- Accordion Item 7 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">07</span>
                                <h5 class="accordion-title">Flexible Packaging and Laminates</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Design and structure of multi-layer laminates, barrier properties, and sealant technologies.</p>
                            </div>
                        </div>

                        <!-- Accordion Item 8 -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <span class="accordion-num">08</span>
                                <h5 class="accordion-title">Blow Moulding and Bottle Design</h5>
                                <span class="accordion-icon"><i class="fa-solid fa-plus"></i></span>
                            </div>
                            <div class="accordion-body">
                                <p>Extrusion and stretch blow moulding methods, lightweighting, and structural design of plastic bottles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionHeaders = document.querySelectorAll('.course-curriculum-section .accordion-header');
        
        accordionHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const item = this.parentElement;
                const isOpen = item.classList.contains('open');
                
                // Toggle the clicked item
                if (isOpen) {
                    item.classList.remove('open');
                    this.querySelector('.accordion-icon').innerHTML = '<i class="fa-solid fa-plus"></i>';
                } else {
                    item.classList.add('open');
                    this.querySelector('.accordion-icon').innerHTML = '<i class="fa-solid fa-xmark"></i>';
                }
            });
        });
    });
</script>
