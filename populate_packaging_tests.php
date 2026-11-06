<?php
require_once 'includes/db_connect.php';

try {
    $prod_slug = 'packaging-specific-products';
    $prod_name = 'Packaging Specific Products';
    $prod_desc = "AKC's accredited laboratories deliver full-spectrum testing for food-grade and industrial packaging materials - including flexible films, laminates, pouches, rigid containers, and closures. Our multidisciplinary team evaluates migration compliance, restricted substance content, seal and burst performance, thermal and barrier properties, optical clarity, biological safety, and electrical characteristics on a single accredited platform.";

    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$prod_slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $prod_id = $product['id'];
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$prod_name, $prod_desc, $prod_id]);

        $del = $conn->prepare("DELETE FROM product_test WHERE product_id = ?");
        $del->execute([$prod_id]);
    } else {
        $sortStmt = $conn->query("SELECT MAX(sort_order) FROM products");
        $maxSort = $sortStmt->fetchColumn();
        $sortOrder = $maxSort ? $maxSort + 1 : 1;
        
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prod_name, $prod_slug, $prod_desc, $sortOrder]);
        $prod_id = $conn->lastInsertId();
    }

    $data = [
        'chemical' => [
            ['name' => 'Viscosity Measurement', 'desc' => 'Determination of viscosity of inks, adhesives, coatings, and lacquers used in the production of packaging specific products - confirming formulation consistency and application performance.', 'stds' => 'ISO 1628 / ASTM D445'],
            ['name' => 'Specific Gravity and Density Measurement', 'desc' => 'Measurement of density of substrate materials and coating preparations used in packaging specific products - for formulation verification and quality control.', 'stds' => 'ISO 1183 / ASTM D792'],
            ['name' => 'Ash Content', 'desc' => 'Quantitative determination of inorganic residue remaining after complete combustion of packaging specific products - confirming filler loading, mineral content, and substrate purity.', 'stds' => 'ISO 3451 / ASTM D5630'],
            ['name' => 'Carbon Black Content', 'desc' => 'Quantification of carbon black levels in packaging specific products to verify UV protection and light-barrier compounding specifications.', 'stds' => 'ISO 6964 / ASTM D1603'],
            ['name' => 'Carbon Black Dispersion Test', 'desc' => 'Microscopic evaluation of carbon black distribution and uniformity within packaging specific products - critical for uniform UV protection and light-barrier performance.', 'stds' => 'ISO 18553 / ASTM D5596'],
            ['name' => 'Volatile Organic Content Test', 'desc' => 'Measurement of VOCs and residual solvents in inks, adhesives, and coatings used in packaging specific products - a critical chemical safety and food-contact compliance parameter.', 'stds' => 'ISO 12219 / ASTM D3960'],
            ['name' => 'Water and Moisture Content', 'desc' => 'Determination of moisture content in packaging specific products - for processing performance, seal quality, and dimensional stability.', 'stds' => 'ISO 15512 / ASTM D6980'],
            ['name' => 'Water Absorbance and Effect on Water', 'desc' => 'Evaluation of moisture uptake and its effect on the performance of packaging specific products - critical for barrier integrity, dimensional stability, and shelf-life.', 'stds' => 'ISO 62 / ASTM D570'],
            ['name' => 'Heavy Metal Analysis', 'desc' => 'Screening and quantification of regulated heavy metals in inks, pigments, coatings, and substrate materials of packaging specific products - per EU Packaging Directive and REACH requirements.', 'stds' => 'ICP-OES / XRF / EU 94/62/EC / REACH SVHC'],
            ['name' => 'Overall Migration Test', 'desc' => 'Total quantification of substances migrating from packaging specific products into food simulants - a mandatory compliance test under EU 10/2011, IS 9845, and FDA regulations.', 'stds' => 'EU 10/2011 / IS 9845 / FDA 21 CFR'],
            ['name' => 'Specific Migration Test', 'desc' => 'Targeted analysis of individual regulated substances migrating from packaging specific products into food simulants - for full regulatory compliance documentation.', 'stds' => 'EU 10/2011 / FDA 21 CFR / REACH'],
            ['name' => 'Accelerated Aging Test', 'desc' => 'Simulation of long-term storage and distribution exposure to predict shelf-life performance and barrier retention of packaging specific products under accelerated conditions.', 'stds' => 'ISO 4892 / ASTM D4329 / ASTM F1980'],
            ['name' => 'Oven Ageing Test', 'desc' => 'Thermal ageing evaluation of packaging specific products under controlled oven conditions - confirming resistance to heat-induced chemical change during hot-fill, retort, and sterilisation.', 'stds' => 'ISO 188 / ASTM D573'],
            ['name' => 'Adhesion Test', 'desc' => 'Assessment of ink, coating, and laminate adhesion on packaging specific products - confirming bond durability and resistance to delamination under handling and distribution stresses.', 'stds' => 'ISO 2409 / ASTM D3359 / PSTC-101'],
            ['name' => 'Environmental Stress Crack Resistance', 'desc' => 'Evaluation of the resistance of packaging specific products to stress cracking under combined mechanical and chemical exposure - critical for container and bottle integrity.', 'stds' => 'ISO 22088 / ASTM D1693'],
            ['name' => 'Residual Solvent Analysis', 'desc' => 'Detection and quantification of residual solvents in printed, laminated, and coated packaging specific products - a key food-safety and regulatory compliance test.', 'stds' => 'USP 467 / ICH Q3C / ASTM E1428'],
            ['name' => 'pH Value Determination', 'desc' => 'Measurement of pH of extracts and surface coatings of packaging specific products - confirming chemical neutrality and food-contact safety compliance.', 'stds' => 'ISO 1924 / ASTM E70'],
            ['name' => 'Conductivity Measurement', 'desc' => 'Assessment of electrical conductivity of antistatic and conductive materials used in packaging specific products - for ESD-sensitive product packaging performance verification.', 'stds' => 'ISO 7888 / ASTM D1125'],
            ['name' => 'Resistance to Chemical', 'desc' => 'Evaluation of the integrity and performance of packaging specific products after exposure to packaged product constituents, cleaning agents, and storage chemicals.', 'stds' => 'ISO 175 / ASTM D543']
        ],
        'mechanical' => [
            ['name' => 'Tensile Test', 'desc' => 'Measurement of tensile strength and elongation of packaging specific products - confirming structural performance under machine-direction and cross-direction loads during production and distribution.', 'stds' => 'ISO 527-3 / ASTM D882 / ASTM D638'],
            ['name' => 'Compressional Test', 'desc' => 'Determination of compressive load-bearing capacity of rigid and semi-rigid packaging specific products under vertical stacking stress in storage and transit.', 'stds' => 'ISO 604 / ASTM D695 / ASTM D642'],
            ['name' => 'Impact Resistance', 'desc' => 'Evaluation of packaging specific products under impact loading - simulating drop, puncture, and handling stresses to confirm structural integrity during distribution.', 'stds' => 'ISO 7765 / ASTM D1709 / ASTM D4272'],
            ['name' => 'Tear Test', 'desc' => 'Measurement of force required to propagate a tear through packaging specific products - confirming toughness under distribution and handling conditions.', 'stds' => 'ISO 6383-1 / ASTM D1004 / ASTM D1922'],
            ['name' => 'Hardness Test', 'desc' => 'Measurement of surface hardness of rigid packaging specific products - confirming resistance to surface indentation and mechanical damage during handling and transit.', 'stds' => 'ISO 868 / ASTM D2240 / ASTM D785'],
            ['name' => 'Rub Test', 'desc' => 'Assessment of print and surface coating durability under dry and wet rubbing for packaging specific products - critical for retail presentation quality and brand integrity.', 'stds' => 'ASTM D5264 / TAPPI T830 / ISO 105-X12'],
            ['name' => 'Seal Strength', 'desc' => 'Quantification of heat seal bond strength in packaging specific products under peel and tensile loading - the primary mechanical test confirming packaging integrity and product protection.', 'stds' => 'ASTM F88 / ISO 11339 / ASTM F2029'],
            ['name' => 'Burst Strength', 'desc' => 'Measurement of hydraulic or pneumatic pressure required to rupture packaging specific products under uniform loading - validating performance under pressurised storage and distribution.', 'stds' => 'ISO 2759 / ASTM D774 / TAPPI T403'],
            ['name' => 'Hot Tack', 'desc' => 'Measurement of immediate seal strength in packaging specific products while still at sealing temperature - critical for high-speed filling lines to prevent premature seal failure.', 'stds' => 'ASTM F1921 / ISO 16111'],
            ['name' => 'Gelbo Test', 'desc' => 'Flex durability evaluation of packaging specific products under repeated combined twisting and bending - simulating distribution handling to assess pinhole formation and barrier retention.', 'stds' => 'ASTM F392 / MIL-B-131'],
            ['name' => 'Torque Test', 'desc' => 'Measurement of opening and closing torque of closures, caps, and lidding systems on packaging specific products - confirming child-resistance, tamper evidence, and consumer usability.', 'stds' => 'ISO 8362 / ASTM D3474 / EN 28317'],
            ['name' => 'Endurance Test', 'desc' => 'Evaluation of packaging specific products under sustained or cyclic mechanical stress - confirming fatigue life and long-term integrity through the product supply chain.', 'stds' => 'ISO 13003 / ASTM D7774 / ASTM F1980'],
            ['name' => 'Abrasion Test', 'desc' => 'Quantification of surface wear resistance of packaging specific products under controlled friction - confirming print and coating durability for transit-ready and retail-display packaging.', 'stds' => 'ISO 4649 / ASTM D1044 / TAPPI T476'],
            ['name' => 'Leak Test', 'desc' => 'Detection of seal defects, pinholes, and closure failures in packaging specific products using vacuum immersion, pressure decay, or dye penetration methods - the definitive packaging integrity test.', 'stds' => 'ASTM D3078 / ASTM F2096 / ISO 11607']
        ],
        'thermal' => [
            ['name' => 'Thermogravimetry Analysis (TGA)', 'desc' => 'Measurement of weight change in packaging specific products as a function of temperature - characterising composition, filler content, and thermal stability.', 'stds' => 'ISO 11358 / ASTM E1131'],
            ['name' => 'Differential Scanning Calorimetry (DSC)', 'desc' => 'Determination of melting point, crystallinity, and thermal transitions in packaging specific products.', 'stds' => 'ISO 11357 / ASTM E1356'],
            ['name' => 'Dynamic Mechanical Analysis (DMA)', 'desc' => 'Evaluation of viscoelastic behaviour and glass transition temperature of packaging specific products under oscillatory load.', 'stds' => 'ISO 6721 / ASTM D5023'],
            ['name' => 'Thermal Mechanical Analysis (TMA)', 'desc' => 'Measurement of dimensional change and coefficient of thermal expansion in packaging specific products with temperature.', 'stds' => 'ISO 11359 / ASTM E831'],
            ['name' => 'Heat Deflection Temperature (HDT)', 'desc' => 'Determination of the temperature at which packaging specific products deforms under a specified load - critical for service-temperature rating.', 'stds' => 'ISO 75 / ASTM D648'],
            ['name' => 'Melt Flow Index (MFI)', 'desc' => 'Measurement of melt flow rate of packaging specific products - indicating processability and molecular weight consistency.', 'stds' => 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['name' => 'Opacity and Optical Density Measurement', 'desc' => 'Measurement of light-blocking capability of packaging specific products - relevant for barrier, print, and appearance specifications.', 'stds' => 'ASTM D589 / ISO 2469'],
            ['name' => 'Absorbance / Transmittance (UV-Vis)', 'desc' => 'Determination of light absorbance and transmittance of packaging specific products across the UV-visible spectrum.', 'stds' => 'ASTM D1003'],
            ['name' => 'Visual Colour Appearance', 'desc' => 'Standardised visual assessment of colour and appearance of packaging specific products under controlled lighting.', 'stds' => 'ASTM D1729'],
            ['name' => 'Haze and Gloss Properties', 'desc' => 'Measurement of haze and surface gloss of packaging specific products - critical for clarity and aesthetic specifications.', 'stds' => 'ASTM D1003 / ASTM D523']
        ],
        'material-characterization' => [
            ['name' => 'Structural Identification (FTIR)', 'desc' => 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of packaging specific products.', 'stds' => 'ASTM E1252 / ISO 4650'],
            ['name' => 'Particle Size Analysis', 'desc' => 'Determination of particle size distribution in packaging specific products using laser diffraction methods.', 'stds' => 'ISO 13320'],
            ['name' => 'Molecular Weight Determination (GPC)', 'desc' => 'Gel permeation chromatography to determine molecular weight and distribution in packaging specific products.', 'stds' => 'ASTM D5296'],
            ['name' => 'Oxygen Transmission Rate (OTR)', 'desc' => 'Measurement of the rate of oxygen permeation through packaging specific products - critical for barrier and shelf-life performance.', 'stds' => 'ASTM D3985'],
            ['name' => 'Water Vapour Transmission Rate (WVTR)', 'desc' => 'Measurement of the rate of water vapour permeation through packaging specific products - critical for moisture barrier performance.', 'stds' => 'ASTM F1249']
        ]
    ];

    foreach ($data as $disc_slug => $tests) {
        $stmt = $conn->prepare("SELECT id FROM disciplines WHERE slug = ?");
        $stmt->execute([$disc_slug]);
        $discipline = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$discipline) {
            $name = ucwords(str_replace('-', ' ', $disc_slug));
            $ins = $conn->prepare("INSERT INTO disciplines (name, slug) VALUES (?, ?)");
            $ins->execute([$name, $disc_slug]);
            $disc_id = $conn->lastInsertId();
        } else {
            $disc_id = $discipline['id'];
        }

        foreach ($tests as $t) {
            $testStmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ? AND description LIKE '%packaging%'");
            $testStmt->execute([$t['name'], $disc_id]);
            $testRow = $testStmt->fetch(PDO::FETCH_ASSOC);

            if ($testRow) {
                $test_id = $testRow['id'];
                $upd = $conn->prepare("UPDATE tests SET description = ?, standards = ? WHERE id = ?");
                $upd->execute([$t['desc'], $t['stds'], $test_id]);
            } else {
                $ins = $conn->prepare("INSERT INTO tests (name, description, standards, discipline_id) VALUES (?, ?, ?, ?)");
                $ins->execute([$t['name'], $t['desc'], $t['stds'], $disc_id]);
                $test_id = $conn->lastInsertId();
            }

            $mapCheck = $conn->prepare("SELECT * FROM product_test WHERE product_id = ? AND test_id = ?");
            $mapCheck->execute([$prod_id, $test_id]);
            if ($mapCheck->rowCount() == 0) {
                $mapIns = $conn->prepare("INSERT INTO product_test (product_id, test_id) VALUES (?, ?)");
                $mapIns->execute([$prod_id, $test_id]);
            }
        }
    }

    echo "Successfully inserted and mapped Packaging Specific Products tests!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
