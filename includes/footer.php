<footer class="site-footer">
    <div class="footer-container">
        <!-- Footer Top Bar -->
        <div class="footer-top">
            <p>Bringing quality, safety & sustainability to life.</p>
            <div class="cert-badges">
                <div class="badge"><span></span> ISO 17025</div>
                <div class="badge"><span></span> ILAC-MRA</div>
                <div class="badge"><span></span> GMP CERTIFIED</div>
                <div class="badge"><span></span> OECD GLP</div>
            </div>
        </div>

        <!-- Footer Main Grid -->
        <div class="footer-grid">
            <!-- Column 1: Brand & Newsletter -->
            <div class="footer-info">
                <div class="footer-logo">
                    <img src="assets/images/FINAL Logo.png" alt="AKC Logo">
                </div>
                <p>AKC is a total quality assurance provider delivering world-class testing, inspection, certification
                    and training services to industry.</p>

                <form class="newsletter-form">
                    <input type="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>

                <div class="social-icons">
                    <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://youtube.com" target="_blank" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Column 2: Company -->
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About AKC</a></li>
                    <li><a href="#services">Our Services</a></li>
                    <li><a href="#industries">Industries</a></li>
                    <li><a href="training-courses.php">Training Courses</a></li>
                  
                </ul>
            </div>

            <!-- Column 3: Services -->
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="testing-and-certification.php">Testing & Analysis</a></li>
                    <li><a href="support-services.php">Support Services</a></li>
                    <li><a href="training-courses.php">Education & Training</a></li>
                </ul>
            </div>

            <!-- Column 4: Contact -->
            <div class="footer-col">
                <h4>Contact Us</h4>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="contact-text">
                        <h5>Phone</h5>
                        <p><a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91 88604 07220</a></p>
                        <p>Mon–Sat, 9:00 AM – 6:30 PM</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-text">
                        <h5>Email</h5>
                        <p>info@akc-ptarc.com</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="contact-text">
                        <h5>Location</h5>
                        <p>Plot 108, Phase 2, Sector 155,</p>
                        <p>Noida, Uttar Pradesh 201304</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AKC Packaging Technology & Research Centre | All rights reserved.</p>
            <p>Designed and Developed by <b>Two35 Playtform</b></p>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button id="scrollToTopBtn" title="Go to top" style="display: none; position: fixed; bottom: 30px; right: 30px; z-index: 999; font-size: 20px; border: none; outline: none; background-color: #0A58CA; color: white; cursor: pointer; border-radius: 50%; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); width: 50px; height: 50px; justify-content: center; align-items: center; transition: background-color 0.3s, opacity 0.3s; opacity: 0; pointer-events: none;" onmouseover="this.style.backgroundColor='#0d6efd'" onmouseout="this.style.backgroundColor='#0A58CA'">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<script>
    // Get the button
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");

    // When the user scrolls down 300px from the top of the document, show the button
    window.addEventListener('scroll', function() {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            scrollToTopBtn.style.display = "flex";
            setTimeout(() => {
                scrollToTopBtn.style.opacity = "1";
                scrollToTopBtn.style.pointerEvents = "auto";
            }, 10);
        } else {
            scrollToTopBtn.style.opacity = "0";
            scrollToTopBtn.style.pointerEvents = "none";
            setTimeout(() => {
                // Check again to avoid hiding if user scrolled back down quickly
                if (document.body.scrollTop <= 300 && document.documentElement.scrollTop <= 300) {
                    scrollToTopBtn.style.display = "none";
                }
            }, 300);
        }
    });

    // When the user clicks on the button, scroll to the top of the document
    scrollToTopBtn.onclick = function() {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }
</script>

<script src="assets/js/scroll-reveal.js"></script>

</body>

</html>