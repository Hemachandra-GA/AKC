<?php
require_once 'includes/db_connect.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : 'chemical';

$stmt = $conn->prepare("SELECT * FROM disciplines WHERE slug = ?");
$stmt->execute([$slug]);
$discipline = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discipline) {
    $prodCheck = $conn->prepare("SELECT id FROM products WHERE slug = ? OR LOWER(name) = LOWER(?)");
    $prodCheck->execute([$slug, $slug]);
    if ($prodCheck->fetch()) {
        include __DIR__ . '/dynamic-product.php';
        exit;
    }
    die("Discipline not found.");
}

// Fetch related products (All products are displayed across all disciplines unconditionally)
$prodStmt = $conn->prepare("
    SELECT * FROM products 
    ORDER BY sort_order ASC, name ASC
");
$prodStmt->execute();
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);

$discName = htmlspecialchars($discipline['name']);

if (strtolower($slug) === 'chemical') {
    $pageTitle = "Chemical Testing & Certification Services | AKC – NABL Accredited Lab";
    $metaDescription = "AKC offers NABL-accredited chemical testing and certification services for polymers, textiles, leather, rubber, packaging, food-grade materials, and more. Request a Test today.";
    $metaKeywords = "chemical testing lab India, NABL chemical testing, polymer chemical analysis, REACH RoHS testing, restricted substance testing, chemical composition analysis, packaging compliance testing";
} elseif (strtolower($slug) === 'mechanical') {
    $pageTitle = "Mechanical Testing Services | Structural & Material Performance Testing – AKC";
    $metaDescription = "AKC provides NABL-accredited mechanical testing for polymers, packaging, textiles, leather, rubber, and toys — including tensile, impact, hardness, burst, seal strength, and leak testing. Request a Test today.";
    $metaKeywords = "mechanical testing lab India, tensile strength testing, polymer mechanical testing, NABL mechanical testing, seal strength testing, burst strength testing, packaging mechanical testing, impact resistance testing India";
} elseif (strtolower($slug) === 'rheological') {
    $pageTitle = "Rheological Testing Services | Viscosity, Flow & Viscoelastic Analysis – AKC";
    $metaDescription = "AKC provides NABL-accredited rheological testing for polymers, coatings, adhesives, and industrial fluids — including rotational viscometry, capillary rheometry, oscillatory rheology, and yield stress measurement.";
    $metaKeywords = "rheological testing India, viscosity testing lab, polymer rheology India, NABL rheological testing, oscillatory rheometry India, capillary rheometry, yield stress measurement India";
} elseif (strtolower($slug) === 'thermal') {
    $pageTitle = "Thermal Testing Services | DSC, TGA, DMA & Thermal Analysis – AKC";
    $metaDescription = "AKC provides NABL-accredited thermal testing for polymers, packaging, rubber, and industrial materials — including TGA, DSC, DMA, TMA, HDT, and MFI. Request a thermal analysis today.";
    $metaKeywords = "thermal testing lab India, DSC testing India, TGA polymer testing, NABL thermal analysis, heat deflection temperature testing, melt flow index testing, DMA testing India";
} else {
    $pageTitle = $discName . " Testing & Certification Services | AKC - NABL Accredited Lab";
    $metaDescription = "AKC offers NABL-accredited testing for {$discName}.";
    $metaKeywords = "testing lab India, NABL testing, {$discName} testing";
}

include 'includes/header.php';
?>

<!-- Custom Stylesheet Link for this Page (Slash Removed Here) -->
<link rel="stylesheet" href="assets/css/chemical-testing.css">

<main>
    <!-- Chemical Testing Hero Section -->
    <section class="chemical-hero-section">
        <div class="chemical-hero-container">

            <!-- Left Info Block -->
            <div class="chemical-hero-info">
                <div class="chemical-subtitle">
                    <span class="dot"></span>
                    <?php
                    if (strtolower($slug) === 'mechanical') {
                        echo '— DISCIPLINE CATEGORY – MECHANICAL';
                    } elseif (strtolower($slug) === 'rheological') {
                        echo '— DISCIPLINE CATEGORY – RHEOLOGICAL';
                    } elseif (strtolower($slug) === 'thermal') {
                        echo '— DISCIPLINE CATEGORY – THERMAL';
                    } else {
                        echo strtoupper($discName) . ' TESTING & CERTIFICATION';
                    }
                    ?>
                </div>

                <h1 class="chemical-hero-title">
                    <?php if (strtolower($slug) === 'mechanical'): ?>
                        Mechanical & <span class="text-accent">Structural Testing</span>
                    <?php elseif (strtolower($slug) === 'rheological'): ?>
                        Rheological <span class="text-accent">Testing</span>
                    <?php elseif (strtolower($slug) === 'thermal'): ?>
                        Thermal <span class="text-accent">Testing</span>
                    <?php else: ?>
                        Accredited <?php echo $discName; ?> Testing for Every Material. <span class="text-accent">Every
                            Industry.</span>
                    <?php endif; ?>
                </h1>

                <p class="chemical-hero-desc">
                    <?php if (strtolower($slug) === 'mechanical'): ?>
                        AKC provides precision mechanical and structural testing services to verify the strength,
                        performance, and durability of polymers, packaging, textiles, leather, rubber, toys, and industrial
                        materials — backed by NABL-accredited methods and internationally recognised standards.
                    <?php elseif (strtolower($slug) === 'chemical'): ?>
                        From restricted substance screening to advanced polymer composition analysis — AKC's NABL-accredited
                        chemical laboratories deliver test results you can rely on for regulatory compliance, product
                        certification, and quality assurance.
                    <?php elseif (strtolower($slug) === 'rheological'): ?>
                        AKC provides comprehensive rheological testing and flow analysis services for polymer melts,
                        coatings, adhesives, sealants, food products, and industrial fluids — characterising viscosity,
                        viscoelastic behaviour, flow curves, yield stress, and processing properties using NABL-accredited
                        methodologies and state-of-the-art rheometric instrumentation.
                    <?php elseif (strtolower($slug) === 'thermal'): ?>
                        AKC provides comprehensive thermal testing and analysis services for polymers, packaging materials,
                        rubber, textiles, and industrial compounds — characterising thermal stability, phase transitions,
                        viscoelastic behaviour, and melt flow properties using state-of-the-art analytical instrumentation
                        and NABL-accredited methodologies.
                    <?php else: ?>
                        Advanced <?php echo strtolower($discName); ?> testing - AKC's NABL-accredited laboratories deliver
                        test results you can rely on for regulatory compliance, product certification, and quality
                        assurance.
                    <?php endif; ?>
                </p>

                <div class="chemical-hero-btns">
                    <a href="#categories"
                        style="display: inline-block; padding: 14px 32px; background: #0A58CA; color: white; font-weight: 700; font-size: 1rem; border-radius: 6px; text-decoration: none; border: none; letter-spacing: 0.5px;"
                        onmouseover="this.style.background='#0d6efd'" onmouseout="this.style.background='#0A58CA'"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        <?php echo (strtolower($slug) === 'mechanical' || strtolower($slug) === 'rheological' || strtolower($slug) === 'thermal') ? 'Request Testing' : 'Explore Testing Categories'; ?>
                        <i class="fa-solid fa-flask" style="margin-left: 8px;"></i>
                    </a>

                </div>

                <div class="trust-bar"
                    style="margin-top: 30px; font-weight: bold; color: rgba(255,255,255,0.9); font-size: 14px; display: flex; flex-wrap: wrap; align-items: center;">
                    <span>NABL Accredited</span>
                    <span class="divider" style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span>ISO 17025</span>
                    <span class="divider" style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span>REACH & RoHS Compliant</span>
                    <span class="divider" style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span>OECD GLP</span>
                    <span class="divider" style="margin: 0 10px; opacity: 0.5;">|</span>
                    <span>International Standards</span>
                </div>
            </div>

            <!-- Right Glassmorphic Floating Widget -->
            <div class="chemical-contact-widget">
                <div class="widget-header">
                    <span class="red-bar"></span>
                    <h5>CONTACT US</h5>
                </div>

                <h3 class="widget-phone"><a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91
                        88604 07220</a></h3>

                <div class="widget-email-row">
                    <i class="fa-regular fa-envelope"></i>
                    <span><a href="mailto:info@akc-ptarc.com" style="color: white; text-decoration: none;">Email
                            Us<?php echo (strtolower($slug) !== 'mechanical' && strtolower($slug) !== 'rheological' && strtolower($slug) !== 'thermal') ? ': info@akc-ptarc.com' : ''; ?></a></span>
                </div>

                <button class="btn-widget-submit"
                    onclick="window.location.href='mailto:info@akc-ptarc.com?subject=Inquiry from <?php echo $discName; ?> Testing Page'">
                    <?php echo (strtolower($slug) === 'mechanical' || strtolower($slug) === 'rheological' || strtolower($slug) === 'thermal') ? 'Send us a Request' : 'Send as a request'; ?>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>

        </div>
    </section>

    <!-- Testing Scope Categories Grid Section -->
    <section id="categories" class="chemical-categories-section">
        <div class="chemical-categories-container">

            <div class="categories-section-header">
                <div
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #0A58CA; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 15px;">
                    <span style="width: 24px; height: 2px; background-color: #0A58CA; display: inline-block;"></span>
                    <?php echo (strtolower($slug) === 'mechanical' || strtolower($slug) === 'rheological' || strtolower($slug) === 'thermal') ? 'OVERVIEW' : 'WHAT WE TEST'; ?>
                    <span style="width: 24px; height: 2px; background-color: #0A58CA; display: inline-block;"></span>
                </div>
                <h2><?php
                if (strtolower($slug) === 'mechanical') {
                    echo 'Mechanical Testing Services';
                } elseif (strtolower($slug) === 'rheological') {
                    echo 'Rheological Testing Services';
                } elseif (strtolower($slug) === 'thermal') {
                    echo 'Thermal Testing Services';
                } else {
                    echo 'Comprehensive ' . $discName . ' Analysis Across All Material Categories';
                }
                ?></h2>

                <?php if (strtolower($slug) === 'mechanical'): ?>
                    <p style="max-width: 850px; line-height: 1.7; margin-bottom: 15px;">AKC's mechanical testing laboratory
                        delivers comprehensive structural and performance analysis across polymers, packaging films,
                        textiles, leather, rubber compounds, toys, and industrial components. Our accredited engineers apply
                        advanced testing methods to measure tensile strength, compressive load, impact resistance, hardness,
                        seal integrity, burst performance, and leak-tightness — enabling manufacturers to validate product
                        quality, meet regulatory requirements, and confirm end-use performance.</p>
                    <p style="max-width: 850px; line-height: 1.7; margin-bottom: 25px;">Whether you need tensile testing for
                        packaging laminates, burst strength evaluation for flexible pouches, seal integrity assessment,
                        torque and endurance testing for closures, or abrasion and rub resistance for coated surfaces — AKC
                        provides detailed, audit-ready reports backed by ISO/IEC 17025 accreditation.</p>

                    <div
                        style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 15px; margin-top: 10px; font-weight: 600; color: #64748B; font-size: 0.95rem;">
                        <span style="color: #0A58CA; margin-right: 5px;">Related Services:</span>
                        <span>Tensile Testing</span> <span style="color: #cbd5e1;">|</span>
                        <span>Seal Strength</span> <span style="color: #cbd5e1;">|</span>
                        <span>Burst Strength</span> <span style="color: #cbd5e1;">|</span>
                        <span>Impact Resistance</span> <span style="color: #cbd5e1;">|</span>
                        <span>Hardness Testing</span> <span style="color: #cbd5e1;">|</span>
                        <span>Leak Testing</span>
                    </div>
                <?php elseif (strtolower($slug) === 'thermal'): ?>
                    <p style="max-width: 850px; line-height: 1.7; margin-bottom: 15px;">Thermal analysis is fundamental to
                        understanding material performance under temperature-dependent conditions. AKC's thermal testing
                        division is equipped with Thermogravimetric Analysers (TGA), Differential Scanning Calorimeters
                        (DSC), Dynamic Mechanical Analysers (DMA), Thermomechanical Analysers (TMA), and Melt Flow Indexers
                        — enabling precise characterisation of how materials respond to heat, cold, and thermal cycling
                        across the full range of processing and service temperatures.</p>
                    <p style="max-width: 850px; line-height: 1.7; margin-bottom: 25px;">Whether you are determining the
                        composition of a polymer compound by TGA, measuring the glass transition temperature of a film by
                        DSC, evaluating viscoelastic behaviour of a rubber compound by DMA, or assessing processability by
                        MFI — AKC delivers accurate, reproducible thermal data in NABL-formatted reports aligned with ISO,
                        ASTM, and international standards.</p>

                    <div
                        style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 15px; margin-top: 10px; font-weight: 600; color: #64748B; font-size: 0.95rem;">
                        <span style="color: #0A58CA; margin-right: 5px;">Related Services:</span>
                        <span>TGA Analysis</span> <span style="color: #cbd5e1;">|</span>
                        <span>DSC Testing</span> <span style="color: #cbd5e1;">|</span>
                        <span>DMA Testing</span> <span style="color: #cbd5e1;">|</span>
                        <span>Heat Deflection Temperature</span> <span style="color: #cbd5e1;">|</span>
                        <span>Melt Flow Index</span> <span style="color: #cbd5e1;">|</span>
                        <span>Thermal Stability</span>
                    </div>
                <?php else: ?>
                    <p style="max-width: 850px; line-height: 1.7; margin-bottom: 15px;"><?php echo $discName; ?> testing is
                        at the core of quality assurance, product safety, and regulatory compliance for manufacturers across
                        every sector. At AKC, our <?php echo strtolower($discName); ?> testing division is equipped with
                        state-of-the-art analytical instruments and staffed by certified experts.</p>
                    <p style="max-width: 850px; line-height: 1.7;">We perform numerous standard
                        <?php echo strtolower($discName); ?> test protocols across multiple material and product categories.
                        Every result is delivered in NABL-formatted reports suitable for audit, certification, and
                        regulatory submission.
                    </p>
                <?php endif; ?>
            </div>

            <div class="chemical-disciplines-grid">

                <?php
                $count = 1;
                foreach ($products as $prod):
                    $numStr = str_pad($count, 2, '0', STR_PAD_LEFT);
                    ?>
                    <!-- Slashes Removed from the Onclick and Href links below -->
                    <div class="discipline-card"
                        onclick="window.location.href='<?php echo urlencode($slug); ?>/<?php echo urlencode($prod['slug']); ?>';"
                        style="cursor: pointer;">
                        <div class="card-meta">
                            <span class="card-num"><?php echo $numStr; ?></span>
                            <div class="card-icon-wrapper"><i class="fa-solid fa-atom"></i></div>
                        </div>
                        <h3><?php echo htmlspecialchars($prod['name']); ?></h3>
                        <p><?php echo htmlspecialchars($prod['description'] ? $prod['description'] : 'Advanced testing and analysis for ' . $prod['name']); ?>
                        </p>
                        <a href="<?php echo urlencode($slug); ?>/<?php echo urlencode($prod['slug']); ?>"
                            class="card-link">Explore Category <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <?php
                    $count++;
                endforeach;

                if (empty($products)):
                    ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: #64748B;">No products mapped to this
                        discipline yet.</p>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- Brand CTA Strip -->
    <section class="chemical-cta-section">
        <div class="chemical-cta-container">
            <h2 class="cta-title">"Precision in every analysis. Confidence in every result."</h2>
            <p class="cta-subtext">
                <?php if (strtolower($slug) === 'rheological'): ?>
                    Our rheological testing laboratories operate to the highest international standards - delivering results
                    that manufacturers, certification bodies, and formulators trust.
                <?php else: ?>
                    Our <?php echo strtolower($discName); ?> testing laboratories operate to the highest international
                    standards - delivering results that manufacturers, certification bodies, and regulators trust.
                <?php endif; ?>
            </p>
        </div>
    </section>

    <?php if (strtolower($slug) === 'rheological' || strtolower($slug) === 'thermal' || strtolower($slug) === 'mechanical'): ?>
        <!-- Compliance Frameworks Section -->
        <section class="compliance-section" style="padding: 80px 5%; background-color: #f8fafc;">
            <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center;">
                <div
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #E11D48; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 0; margin-bottom: 8px;">
                    <span style="width: 24px; height: 2px; background-color: #E11D48; display: inline-block;"></span>
                    <span>COMPLIANCE FRAMEWORKS</span>
                    <span style="width: 24px; height: 2px; background-color: #E11D48; display: inline-block;"></span>
                </div>
                <h2 style="font-size: 32px; font-weight: 600; color: #081D33; margin-top: 0; margin-bottom: 8px;">Testing
                    Standards</h2>
                <p style="max-width: 800px; margin: 0 auto 40px; color: #64748B; font-size: 16px; line-height: 1.7;">
                    All <?php echo strtolower($slug); ?> testing at AKC is performed in accordance with the following
                    national and international standards:
                </p>

                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; text-align: left;">
                    <?php if (strtolower($slug) === 'rheological'): ?>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 3219 /
                                ASTM D2196</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Rotational viscometry
                                standards for determination of viscosity of polymer solutions and dispersions at defined shear
                                rates.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 11443 /
                                ASTM D3835</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Capillary rheometry —
                                measuring viscosity of polymer melts at elevated temperatures and high shear rates
                                representative of processing conditions.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 6721-10 /
                                ASTM D4440</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Dynamic oscillatory
                                rheometry — measuring storage modulus, loss modulus, and tan delta of polymer melts and
                                viscoelastic materials.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 20965</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Determination of
                                extensional viscosity of polymer solutions — relevant to film blowing and fibre spinning
                                applications.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ASTM D4287
                            </h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Cone-and-plate
                                viscometry for precise low-sample-volume viscosity measurement at controlled shear.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">NABL</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">National Accreditation
                                Board for Testing and Calibration Laboratories — the accreditation framework under which all AKC
                                rheological test results are issued.</p>
                        </div>
                    <?php elseif (strtolower($slug) === 'thermal'): ?>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 11358</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Thermogravimetry of
                                polymers — general principles for TGA testing methodology and data reporting.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 11357</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Differential scanning
                                calorimetry for plastics — covering glass transition, melting, and crystallisation analysis.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 6721</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Determination of dynamic
                                mechanical properties of plastics — used in DMA testing for viscoelastic characterisation.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 11359</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Thermomechanical
                                analysis of plastics — covering dimensional change, expansion, and probe force measurements.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 75</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Determination of
                                temperature of deflection under load (HDT) — for service temperature classification of polymer
                                materials.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO 1133 /
                                ASTM D1238</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Melt flow rate of
                                thermoplastics — governing MFI measurement for processability and molecular weight consistency.
                            </p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ASTM E1131 /
                                E1356 / D5023</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">ASTM standards for TGA,
                                DSC, and DMA testing of polymeric and composite materials.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">NABL</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">National Accreditation
                                Board for Testing and Calibration Laboratories — the accreditation framework under which all AKC
                                thermal test results are issued.</p>
                        </div>
                    <?php elseif (strtolower($slug) === 'mechanical'): ?>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ASTM</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">American Society for
                                Testing and Materials — widely adopted standards for tensile, impact, seal strength, burst, and
                                hardness testing of plastics, packaging, rubber, and textiles.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">ISO</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">International
                                Organisation for Standardisation — globally recognised standards for mechanical property
                                evaluation of industrial and polymeric materials.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">NABL</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">National Accreditation
                                Board for Testing and Calibration Laboratories — India's accreditation body under which AKC's
                                mechanical labs operate.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">BIS</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Bureau of Indian
                                Standards — applicable Indian standards for material performance, packaging, toys, and consumer
                                product mechanical compliance.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">EN / DIN</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">European and German
                                engineering standards — for rubber, textile, and structural mechanical testing to international
                                export compliance requirements.</p>
                        </div>
                        <div
                            style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <h4 style="color: #0A58CA; font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">TAPPI</h4>
                            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0;">Technical Association of
                                the Pulp and Paper Industry — standards for mechanical testing of paper, board, and packaging
                                substrates.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>







    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="faq-container">

            <!-- Left Column: Header & Support Card -->
            <div class="faq-left-column">
                <div class="faq-section-header">
                    <div class="faq-subtitle"
                        style="display: inline-flex; align-items: center; gap: 8px; color: #E11D48; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 15px;">
                        <span class="line"
                            style="width: 24px; height: 2px; background-color: #E11D48; display: inline-block;"></span>
                        SUPPORT
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #0A58CA; margin: 0; line-height: 1.2;">
                        Frequently Asked<br>Questions</h2>
                </div>

                <div class="faq-support-card">
                    <div class="support-icon-wrapper">
                        <i class="fa-solid fa-comment-dots"></i>
                    </div>
                    <h3 style="white-space: nowrap;">Need Expert <span
                            style="color: #38BDF8; font-style: italic;">Guidance?</span></h3>
                    <p>Our qualified team of polymer scientists and compliance specialists are available to discuss your
                        testing requirements and recommend the right programme.</p>

                    <div class="faq-contact-info">
                        <span>Direct contact:</span>
                        <a href="mailto:info@akc-ptarc.com">info@akc-ptarc.com</a>
                    </div>

                    <button class="btn-faq-contact" onclick="window.location.href='mailto:info@akc-ptarc.com'">
                        Contact Our Team <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Right Column: Accordion -->
            <div class="faq-accordion-container">

                <?php
                if (strtolower($slug) === 'mechanical') {
                    $faqs = [
                        [
                            'q' => 'What mechanical testing services does AKC provide?',
                            'a' => 'AKC provides a comprehensive range of mechanical testing services including tensile, compressional, impact resistance, tear, hardness, rub, seal strength, burst strength, hot tack, gelbo, torque, endurance, abrasion, and leak testing — across polymers, packaging, textiles, leather, rubber, and toys.'
                        ],
                        [
                            'q' => 'Which product categories does AKC\'s mechanical testing lab cover?',
                            'a' => 'Our mechanical testing services cover polymer and polymeric products, textile and textile products, leather and leatherite products, rubber and rubberic products, toys and similar products, and packaging specific products.'
                        ],
                        [
                            'q' => 'Are mechanical test reports accepted for export and regulatory compliance?',
                            'a' => 'Yes. All mechanical test reports issued by AKC are NABL-formatted and ISO/IEC 17025 compliant — accepted by international regulatory bodies, certification agencies, and importing authorities.'
                        ],
                        [
                            'q' => 'How long does mechanical testing take?',
                            'a' => 'Standard mechanical tests are typically completed within 5–7 working days. Multi-parameter programmes may require 10–15 days. Expedited turnaround is available on request.'
                        ],
                        [
                            'q' => 'Can AKC test both raw materials and finished products?',
                            'a' => 'Yes. Our laboratory is equipped to test raw polymer granules, films, laminates, moulded parts, rubber compounds, coated textiles, leather goods, toy components, and fully assembled packaging — at any stage of the production cycle.'
                        ]
                    ];
                } elseif (strtolower($slug) === 'rheological') {
                    $faqs = [
                        [
                            'q' => 'What rheological testing services does AKC provide?',
                            'a' => 'AKC provides a comprehensive suite of accredited rheological testing services for polymers, packaging, textiles, rubber, leather, toys, food-contact materials, and industrial products - all delivered in NABL-formatted reports with internationally recognised standards.'
                        ],
                        [
                            'q' => 'How long does testing take?',
                            'a' => 'Standard tests are typically completed within 5-10 working days depending on complexity. Multi-parameter programmes may require 10-15 days. Expedited processing is available on request.'
                        ],
                        [
                            'q' => 'Are test reports accepted internationally?',
                            'a' => 'Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for regulatory submissions, certification, and compliance purposes in India and internationally.'
                        ]
                    ];
                } elseif (strtolower($slug) === 'thermal') {
                    $faqs = [
                        [
                            'q' => 'What thermal testing services does AKC provide?',
                            'a' => 'AKC provides a comprehensive suite of accredited thermal testing services for polymers, packaging, textiles, rubber, leather, toys, food-contact materials, and industrial products - all delivered in NABL-formatted reports with internationally recognised standards.'
                        ],
                        [
                            'q' => 'How long does testing take?',
                            'a' => 'Standard tests are typically completed within 5-10 working days depending on complexity. Multi-parameter programmes may require 10-15 days. Expedited processing is available on request.'
                        ],
                        [
                            'q' => 'Are test reports accepted internationally?',
                            'a' => 'Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for regulatory submissions, certification, and compliance purposes in India and internationally.'
                        ]
                    ];
                } else {
                    $faqs = [
                        [
                            'q' => 'What chemical testing services does AKC provide?',
                            'a' => 'AKC provides a full spectrum of chemical testing including composition analysis, restricted substance screening (REACH, RoHS, Prop 65), polymer and material testing, contamination screening, heavy metal analysis, formulation testing, and regulatory compliance verification - across 10+ product categories.'
                        ],
                        [
                            'q' => 'How long does chemical testing take?',
                            'a' => 'Turnaround time varies by test type and sample complexity. Standard tests are typically completed within 5-10 working days. Expedited processing is available on request. Our team will advise on timelines at the time of enquiry.'
                        ],
                        [
                            'q' => 'Are your chemical testing reports accepted by international regulators?',
                            'a' => 'Yes. Our reports are issued in NABL-accredited format, compliant with ISO/IEC 17025 standards, and are accepted by regulatory authorities, certification bodies, and importers across India and internationally.'
                        ],
                        [
                            'q' => 'Can AKC provide certification reports?',
                            'a' => 'Yes. We issue NABL-formatted certification reports for all accredited tests, suitable for product certification submissions, regulatory filings, and client quality audits.'
                        ],
                        [
                            'q' => 'What sample submission requirements apply?',
                            'a' => 'Sample requirements vary by test protocol. Generally, we require labelled samples in appropriate quantities, accompanied by a completed test request form. Our team will provide specific guidance based on your test type and material.'
                        ]
                    ];
                }

                $faqCount = 0;
                foreach ($faqs as $faq):
                    $faqCount++;
                    $isOpen = ($faqCount === 1);
                    ?>
                    <div class="faq-item"
                        style="background: white; margin-bottom: 15px; border-radius: 8px; border: 1px solid <?php echo $isOpen ? '#0A58CA' : '#E2E8F0'; ?>; overflow: hidden; transition: all 0.3s ease;">
                        <div class="faq-header-toggle"
                            style="padding: 20px 25px; font-size: 0.95rem; font-weight: 600; color: <?php echo $isOpen ? '#0A58CA' : '#081D33'; ?>; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                            <?php echo htmlspecialchars($faq['q']); ?>
                            <i class="fa-solid <?php echo $isOpen ? 'fa-chevron-up' : 'fa-chevron-down'; ?>"
                                style="color: <?php echo $isOpen ? '#ffffff' : '#64748B'; ?>; background: <?php echo $isOpen ? '#0A58CA' : '#F1F5F9'; ?>; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;"></i>
                        </div>
                        <div class="faq-body"
                            style="padding: 0 25px 25px 25px; color: #64748B; line-height: 1.7; display: <?php echo $isOpen ? 'block' : 'none'; ?>;">
                            <div style="padding-bottom: 15px;">
                                <?php echo htmlspecialchars($faq['a']); ?>
                            </div>
                            <div class="feedback-actions"
                                style="margin-top: 20px; padding: 15px 20px; background: #F8FAFC; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 600; color: #0A58CA;">Was This Content Helpful ?</span>
                                <div style="display: flex; gap: 15px;">
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 6px; background: #F0F9FF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                                        <i class="fa-regular fa-thumbs-up" style="color: #0A58CA;"></i>
                                    </div>
                                    <div
                                        style="width: 36px; height: 36px; border-radius: 6px; background: #F0F9FF; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                                        <i class="fa-regular fa-thumbs-down" style="color: #0A58CA;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- Final Call to Action Section -->
    <section class="chemical-cta-section"
        style="padding: 80px 20px; text-align: center; background: #0d47a1; color: white;">
        <div class="cta-container" style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto;">
            <?php if (strtolower($slug) === 'rheological'): ?>
                <div
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #FFD166; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 0; margin-bottom: 8px;">
                    <span>GET STARTED</span>
                </div>
                <h2 style="font-size: 32px; font-weight: 600; color: #ffffff; margin-top: 0; margin-bottom: 8px;">Need
                    Rheological Testing Services?</h2>
                <p
                    style="font-size: 16px; margin-top: 0; margin-bottom: 30px; color: rgba(255,255,255,0.9); line-height: 1.7;">
                    Our rheological testing team is ready to discuss your viscosity, flow, and viscoelastic testing
                    requirements — from simple viscosity QC to full rheological characterisation programmes.</p>

                <div class="cta-actions" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <a href="#categories" class="btn-outline"
                        style="border: 2px solid white; color: white; padding: 14px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; text-decoration: none; transition: background 0.3s;"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        Book a Test
                    </a>
                </div>
            <?php elseif (strtolower($slug) === 'thermal'): ?>
                <div
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #FFD166; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 0; margin-bottom: 8px;">
                    <span>GET STARTED</span>
                </div>
                <h2 style="font-size: 32px; font-weight: 600; color: #ffffff; margin-top: 0; margin-bottom: 8px;">Need
                    Thermal Testing Services?</h2>
                <p
                    style="font-size: 16px; margin-top: 0; margin-bottom: 30px; color: rgba(255,255,255,0.9); line-height: 1.7;">
                    Our accredited thermal analysis team is ready to characterise your materials across TGA, DSC, DMA, TMA,
                    HDT, MFI, and specialist thermal disciplines — delivering precise, reproducible results.</p>

                <div class="cta-actions" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <a href="#categories" class="btn-outline"
                        style="border: 2px solid white; color: white; padding: 14px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; text-decoration: none; transition: background 0.3s;"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        Book a Test
                    </a>
                </div>
            <?php elseif (strtolower($slug) === 'mechanical'): ?>
                <div
                    style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; color: #FFD166; font-weight: 700; font-size: 0.85rem; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 0; margin-bottom: 8px;">
                    <span>GET STARTED</span>
                </div>
                <h2 style="font-size: 32px; font-weight: 600; color: #ffffff; margin-top: 0; margin-bottom: 8px;">Need
                    Mechanical Testing Services?</h2>
                <p
                    style="font-size: 16px; margin-top: 0; margin-bottom: 30px; color: rgba(255,255,255,0.9); line-height: 1.7;">
                    Our accredited laboratory team is ready to evaluate your materials and deliver precise, globally
                    recognised test results for your products and components.</p>

                <div class="cta-actions" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <a href="#categories" class="btn-outline"
                        style="border: 2px solid white; color: white; padding: 14px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; text-decoration: none; transition: background 0.3s;"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        Book a Test
                    </a>
                </div>
            <?php else: ?>
                <h2 style="font-size: 32px; font-weight: 600; color: #ffffff; margin-top: 0; margin-bottom: 8px;">Ready to
                    Get Started?</h2>
                <p
                    style="font-size: 16px; margin-top: 0; margin-bottom: 30px; color: rgba(255,255,255,0.9); line-height: 1.7;">
                    Submit your sample today or
                    speak with our <?php echo strtolower($discName); ?> testing experts to discuss your compliance and
                    quality requirements.</p>

                <div class="cta-actions" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <a href="#categories" class="btn-outline"
                        style="border: 2px solid white; color: white; padding: 14px 40px; font-size: 16px; font-weight: bold; border-radius: 6px; text-decoration: none; transition: background 0.3s;"
                        onclick="event.preventDefault(); const target = document.getElementById('categories'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });">
                        Book a Test
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // FAQ Accordion script
        const toggles = document.querySelectorAll('.faq-header-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const body = this.nextElementSibling;
                const icon = this.querySelector('i');
                const parent = this.parentElement;

                if (body.style.display === 'none' || body.style.display === '') {
                    body.style.display = 'block';
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                    icon.style.backgroundColor = '#0A58CA';
                    icon.style.color = '#ffffff';
                    this.style.color = '#0A58CA';
                    parent.style.borderColor = '#0A58CA';
                } else {
                    body.style.display = 'none';
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                    icon.style.backgroundColor = '#F1F5F9';
                    icon.style.color = '#64748B';
                    this.style.color = '#081D33';
                    parent.style.borderColor = '#E2E8F0';
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>