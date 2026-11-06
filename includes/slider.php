<section class="hero-slider">
    <div class="slides-container" id="slidesContainer">
        
        <!-- Slide 1 -->
        <div class="slide">
            <div class="slide-bg">
                <img src="assets/images/testing-qc.jpg" alt="Testing QC Analysis">
            </div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-label"><span class="label-line"></span> TOTAL QUALITY ASSURANCE</div>
                <h2>Testing • QC • <span>Analysis</span></h2>
                <p>Advanced laboratory testing and quality assurance for modern industries. Precision-driven. Globally certified. Scientifically proven.</p>
                <a href="#" class="btn-know-more">Know More</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide">
            <div class="slide-bg">
                <img src="assets/images/support-services.jpg" alt="Support Services">
            </div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-label"><span class="label-line"></span> TOTAL QUALITY ASSURANCE</div>
                <h2>Innovation • Design • <span>Solutions</span></h2>
                <p>Developing cutting-edge solutions for complex engineering challenges. Our team of experts is dedicated to pushing the boundaries of what is possible in modern laboratory research.</p>
                <a href="#" class="btn-know-more">Know More</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slide">
            <div class="slide-bg">
                <img src="assets/images/education-training.jpg" alt="Education Training">
            </div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-label"><span class="label-line"></span> TOTAL QUALITY ASSURANCE</div>
                <h2>Sustainable • Research • <span>Growth</span></h2>
                <p>Committed to a sustainable future through rigorous scientific research and development. We empower our partners to achieve long-term growth through excellence and integrity.</p>
                <a href="#" class="btn-know-more">Know More</a>
            </div>
        </div>

    </div>

    <!-- Controls -->
    <div class="slider-arrow arrow-prev" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></div>
    <div class="slider-arrow arrow-next" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></div>

    <div class="slider-dots">
        <div class="dot active" onclick="setSlide(0)"></div>
        <div class="dot" onclick="setSlide(1)"></div>
        <div class="dot" onclick="setSlide(2)"></div>
    </div>
</section>

<script>
    let currentSlide = 0;
    const slidesContainer = document.getElementById('slidesContainer');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = 3;

    function moveSlide(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        updateSlider();
    }

    function setSlide(index) {
        currentSlide = index;
        updateSlider();
    }

    function updateSlider() {
        slidesContainer.style.transform = `translateX(-${currentSlide * (100 / totalSlides)}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            if (index === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Auto slide
    setInterval(() => {
        moveSlide(1);
    }, 5000);
</script>
