<?php 
$pageTitle = "Our Facilities | AKC";
$metaDescription = "Explore AKC's state-of-the-art testing and analysis facilities across all material categories.";
$metaKeywords = "akc facility, testing laboratory, polymer testing, material analysis";
include 'includes/header.php'; 
?>

<main style="padding-top: 60px; padding-bottom: 60px;">
    <!-- Categories Section -->
    <section class="categories-section">
        <div class="categories-container">

            <div class="section-header reveal" style="text-align: center; margin-bottom: 40px;">
                <div class="browse-label"
                    style="justify-content: center; color: #0d47a1; font-weight: bold; letter-spacing: 1px; margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                    <span class="label-line"
                        style="width: 30px; height: 2px; background: #ff3b3b; display: inline-block;"></span>
                    OUR CAPABILITIES
                </div>
                <h2 style="font-size: 32px; font-weight: 600; color: #081D33; margin-top: 0; margin-bottom: 8px;">Testing & Analysis Across Every Material Category</h2>
                <p style="font-size: 16px; color: #555; max-width: 800px; margin: 0 auto; line-height: 1.7;">From polymers and packaging
                    to textiles, rubber, and medical devices — our certified labs are equipped for precision testing
                    across 8 scientific disciplines.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs-nav reveal"
                style="display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 40px;">
                <button class="tab-btn active" onclick="switchTab(event, 'chemical')"><i
                        class="fa-solid fa-flask-vial"></i> Chemical</button>
                <button class="tab-btn" onclick="switchTab(event, 'mechanical')"><i class="fa-solid fa-cogs"></i>
                    Mechanical</button>
                <button class="tab-btn" onclick="switchTab(event, 'biological')"><i class="fa-solid fa-microbes"></i>
                    Biological</button>
                <button class="tab-btn" onclick="switchTab(event, 'electrical')"><i class="fa-solid fa-bolt"></i>
                    Electrical</button>
                <button class="tab-btn" onclick="switchTab(event, 'material')"><i class="fa-solid fa-microscope"></i>
                    Material Characterisation</button>
                <button class="tab-btn" onclick="switchTab(event, 'thermal')"><i class="fa-solid fa-fire"></i>
                    Thermal</button>
                <button class="tab-btn" onclick="switchTab(event, 'rheological')"><i class="fa-solid fa-water"></i>
                    Rheological</button>
                <button class="tab-btn" onclick="switchTab(event, 'optical')"><i class="fa-solid fa-eye"></i>
                    Optical</button>
            </div>

            <!-- Tab Contents -->

            <!-- Plastic & Resin (4 items) -->
            <div id="chemical" class="tab-content active reveal-group" style="display: grid;">
                <div class="category-card reveal-child" onclick="window.location.href='chemical/polymer-polymeric-products-testing'" style="cursor: pointer;">
                    <div class="card-image"><span class="card-tag">Packaging</span><img
                            src="assets/images/testing-qc.jpg" alt="PET Bottles"></div>
                    <div class="card-footer">
                        <h4>PET Bottles</h4>
                        <div class="arrow-btn"><i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </div>
                <div class="category-card reveal-child" onclick="window.location.href='chemical'" style="cursor: pointer;">
                    <div class="card-image"><span class="card-tag">Film</span><img
                            src="assets/images/support-services.jpg" alt="Packaging Film"></div>
                    <div class="card-footer">
                        <h4>Packaging Film / Laminates</h4>
                        <div class="arrow-btn"><i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </div>
                <div class="category-card reveal-child" onclick="window.location.href='chemical'" style="cursor: pointer;">
                    <div class="card-image"><span class="card-tag">Industrial</span><img
                            src="assets/images/education-training.jpg" alt="Plumbing Equipment"></div>
                    <div class="card-footer">
                        <h4>Plumbing Equipment</h4>
                        <div class="arrow-btn"><i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </div>
                <div class="category-card reveal-child" onclick="window.location.href='chemical'" style="cursor: pointer;">
                    <div class="card-image"><span class="card-tag">Consumer</span><img
                            src="assets/images/banner-lab.png" alt="Toys"></div>
                    <div class="card-footer">
                        <h4>Toys</h4>
                        <div class="arrow-btn"><i class="fa-solid fa-arrow-right"></i></div>
                    </div>
                </div>
            </div>

            <!-- Mechanical -->
            <div id="mechanical" class="tab-content" style="display: none;">
                <!-- Add similar mechanical cards here... currently placeholder -->
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our mechanical testing facilities.</p>
            </div>

            <div id="biological" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our biological testing facilities.</p>
            </div>

            <div id="electrical" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our electrical testing facilities.</p>
            </div>
            
            <div id="material" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our material characterisation testing facilities.</p>
            </div>

            <div id="thermal" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our thermal testing facilities.</p>
            </div>
            
            <div id="rheological" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our rheological testing facilities.</p>
            </div>

            <div id="optical" class="tab-content" style="display: none;">
                 <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #5e6e82;">Explore our optical testing facilities.</p>
            </div>

        </div>
    </section>
</main>

<script>
function switchTab(event, tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Show current tab content & add active class to button
    event.currentTarget.classList.add('active');
    const selectedTab = document.getElementById(tabId);
    if(selectedTab) {
        selectedTab.classList.add('active');
        if(selectedTab.classList.contains('category-card') === false) {
             selectedTab.style.display = 'grid'; // Maintain grid layout
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
