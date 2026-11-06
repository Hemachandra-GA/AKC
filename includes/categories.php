<section class="categories-section">
    <div class="categories-container">
        
        <div class="section-header">
            <div class="browse-label"><span class="label-line"></span> CORE TESTING PRODUCTS</div>
            <h2>Accredited <span>Products & Testing Categories</span></h2>
        </div>

        <?php
        $homeProds = [];
        if (isset($conn)) {
            $hpStmt = $conn->query("SELECT id, name, slug, description FROM products ORDER BY sort_order ASC, name ASC");
            if ($hpStmt) {
                $homeProds = $hpStmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        ?>

        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin-top: 30px;">
            <?php foreach ($homeProds as $index => $hp): ?>
                <a href="<?php echo htmlspecialchars($hp['slug']); ?>" class="product-card-link" style="text-decoration: none; color: inherit;">
                    <div class="category-card" style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column; justify-content: space-between;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -5px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.05)'">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <span style="background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 20px;">Product #<?php echo ($index + 1); ?></span>
                                <i class="fa-solid fa-arrow-right" style="color: #2563eb;"></i>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 10px; line-height: 1.3;"><?php echo htmlspecialchars($hp['name']); ?></h3>
                            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin: 0;"><?php echo htmlspecialchars($hp['description']); ?></p>
                        </div>
                        <div style="margin-top: 16px; font-size: 13px; font-weight: 600; color: #2563eb; display: flex; align-items: center; gap: 6px;">
                            Browse Testing Categories & Tests <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>
