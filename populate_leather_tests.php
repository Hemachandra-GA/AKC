<?php
require_once 'includes/db_connect.php';

try {
    $conn->beginTransaction();

    $productSlug = 'leather-leatherite-products';
    $productName = 'Leather & Leatherite Products';
    
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ? OR slug = ?");
    $stmt->execute([$productSlug, 'leather-leatherite-products-testing']);
    $product = $stmt->fetch();

    $desc = "AKC's accredited laboratories deliver full-spectrum testing for genuine leather, leatherite, and coated synthetic substrates. Our team screens for restricted tanning chemicals and heavy metals, evaluates mechanical strength and flex durability, characterises thermal and coating rheology, assesses colour and surface optics, validates biological safety, and tests electrical performance of technical leather goods.";

    if (!$product) {
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, sort_order) VALUES (?, ?, ?, 3)");
        $stmt->execute([$productName, $productSlug, $desc]);
        $productId = $conn->lastInsertId();
        echo "Inserted new product: $productName (ID: $productId)\n";
    } else {
        $productId = $product['id'];
        echo "Product exists: $productName (ID: $productId)\n";
        
        $stmt = $conn->prepare("UPDATE products SET description = ? WHERE id = ?");
        $stmt->execute([$desc, $productId]);
    }

    $stmt = $conn->prepare("DELETE FROM product_test WHERE product_id = ?");
    $stmt->execute([$productId]);
    echo "Deleted old test mappings to enforce strict counts.\n";

    $testData = [
        'chemical' => [
            ['Viscosity Measurement', 'Determination of melt, solution, or intrinsic viscosity of leather and leatherite (synthetic leather) materials to characterise flow behaviour and processability.', 'ISO 1628 / ASTM D445'],
            ['Specific Gravity and Density Measurement', 'Measurement of density of leather and leatherite (synthetic leather) materials using displacement or pycnometer methods - essential for formulation verification and quality control.', 'ISO 1183 / ASTM D792'],
            ['Ash Content', 'Quantitative determination of inorganic residue remaining after complete combustion of leather and leatherite (synthetic leather) materials - confirming filler loading and purity.', 'ISO 3451 / ASTM D5630'],
            ['Carbon Black Content', 'Quantification of carbon black levels in leather and leatherite (synthetic leather) materials to verify UV protection and compounding compliance.', 'ISO 6964 / ASTM D1603'],
            ['Carbon Black Dispersion Test', 'Microscopic evaluation of carbon black distribution and uniformity within leather and leatherite (synthetic leather) materials - critical for consistent performance.', 'ISO 18553 / ASTM D5596'],
            ['Volatile Organic Content Test', 'Measurement of VOCs emitted or contained within leather and leatherite (synthetic leather) materials - supporting indoor air quality compliance and product safety certification.', 'ISO 12219 / ASTM D3960'],
            ['Water and Moisture Content', 'Determination of total water and moisture content in leather and leatherite (synthetic leather) materials - critical for processing performance and dimensional stability.', 'ISO 15512 / ASTM D6980'],
            ['Water Absorbance and Effect on Water', 'Evaluation of how leather and leatherite (synthetic leather) materials interact with water under prolonged exposure - assessing dimensional change, weight gain, and surface degradation.', 'ISO 62 / ASTM D570'],
            ['Heavy Metal Analysis', 'Screening and quantification of hazardous heavy metals including lead, cadmium, mercury, and chromium in leather and leatherite (synthetic leather) materials - per REACH and regulatory standards.', 'ICP-OES / XRF / EN 71-3'],
            ['Overall Migration Test', 'Total quantification of substances migrating from leather and leatherite (synthetic leather) materials into food simulants or contact media - per EU, BIS, and FDA safety regulations.', 'EU 10/2011 / IS 9845'],
            ['Specific Migration Test', 'Targeted analysis of individual regulated substances migrating from leather and leatherite (synthetic leather) materials - for full regulatory compliance documentation.', 'EU 10/2011 / ASTM F1308'],
            ['Accelerated Aging Test', 'Simulation of long-term environmental exposure to predict service life and durability of leather and leatherite (synthetic leather) materials under elevated temperature and humidity.', 'ISO 4892 / ASTM D4329'],
            ['Oven Ageing Test', 'Heat ageing evaluation under controlled oven conditions to assess thermal stability and change in properties of leather and leatherite (synthetic leather) materials.', 'ISO 188 / ASTM D573'],
            ['Adhesion Test', 'Cross-cut and pull-off adhesion testing of surface coatings and laminations applied to leather and leatherite (synthetic leather) materials - for quality control and compliance.', 'ISO 2409 / ASTM D3359'],
            ['Environmental Stress Crack Resistance', 'Assessment of susceptibility of leather and leatherite (synthetic leather) materials to cracking under mechanical stress in the presence of chemical agents.', 'ISO 22088 / ASTM D1693'],
            ['Residual Solvent Analysis', 'Detection and quantification of residual solvents retained in leather and leatherite (synthetic leather) materials - for safety compliance and process validation.', 'USP 467 / ICH Q3C'],
            ['pH Value Determination', 'Measurement of pH of aqueous extracts of leather and leatherite (synthetic leather) materials - confirming chemical neutrality and safety for sensitive-contact applications.', 'ISO 1924 / ASTM E70'],
            ['Conductivity Measurement', 'Assessment of electrical conductivity of extracts or solutions of leather and leatherite (synthetic leather) materials - used for quality control and contamination detection.', 'ISO 7888 / ASTM D1125'],
            ['Resistance to Chemical', 'Evaluation of the integrity and performance of leather and leatherite (synthetic leather) materials after exposure to specific chemicals, solvents, and cleaning agents.', 'ISO 175 / ASTM D543']
        ],
        'mechanical' => [
            ['Tensile Test', 'Measurement of tensile strength and elongation at break for leather and leatherite (synthetic leather) materials - confirming structural integrity and suitability for end use.', 'ISO 527 / ASTM D638'],
            ['Compressional Test', 'Determination of compressive load-bearing capacity for leather and leatherite (synthetic leather) materials under controlled axial load.', 'ISO 604 / ASTM D695'],
            ['Impact Resistance', 'Evaluation of energy absorbed during high-speed fracture using Izod and Charpy methods for leather and leatherite (synthetic leather) materials.', 'ISO 179 / ASTM D256'],
            ['Tear Test', 'Measurement of force required to propagate a tear through leather and leatherite (synthetic leather) materials - confirming toughness under real-use mechanical demands.', 'ISO 6383 / ASTM D1004'],
            ['Hardness Test', 'Measurement of resistance to permanent surface indentation in leather and leatherite (synthetic leather) materials using Shore A and Shore D scales.', 'ISO 868 / ASTM D2240'],
            ['Rub Test', 'Assessment of resistance to surface scuffing and print/finish transfer under controlled rubbing for leather and leatherite (synthetic leather) materials.', 'ASTM D5264 / ISO 105-X12'],
            ['Seal Strength', 'Quantification of heat seal bond strength in leather and leatherite (synthetic leather) materials under peel and tensile loading - confirming seal integrity.', 'ASTM F88 / ISO 11339'],
            ['Burst Strength', 'Measurement of the pressure at which leather and leatherite (synthetic leather) materials ruptures under uniform loading - validating structural performance.', 'ISO 2759 / ASTM D774'],
            ['Hot Tack', 'Measurement of immediate seal strength of leather and leatherite (synthetic leather) materials while still in the molten or setting state.', 'ASTM F1921 / ISO 16111'],
            ['Gelbo Test', 'Flex durability evaluation of leather and leatherite (synthetic leather) materials under repeated twisting and bending - assessing pinhole resistance after mechanical stress.', 'ASTM F392 / MIL-B-131'],
            ['Torque Test', 'Measurement of opening and closing torque of closures and fittings associated with leather and leatherite (synthetic leather) materials.', 'ISO 8362 / ASTM D3474'],
            ['Endurance Test', 'Evaluation of leather and leatherite (synthetic leather) materials performance under sustained or cyclic mechanical loading - determining fatigue life.', 'ISO 13003 / ASTM D7774'],
            ['Abrasion Test', 'Quantification of resistance to surface wear under controlled abrasive contact for leather and leatherite (synthetic leather) materials.', 'ISO 4649 / ASTM D1044'],
            ['Leak Test', 'Detection of seal defects, pinholes, and packaging failures in leather and leatherite (synthetic leather) materials using vacuum, pressure decay, or dye penetration methods.', 'ASTM D3078 / ISO 11607']
        ],
        'thermal' => [
            ['Thermogravimetry Analysis (TGA)', 'Measurement of weight change in leather and leatherite (synthetic leather) materials as a function of temperature - characterising composition, filler content, and thermal stability.', 'ISO 11358 / ASTM E1131'],
            ['Differential Scanning Calorimetry (DSC)', 'Determination of melting point, crystallinity, and thermal transitions in leather and leatherite (synthetic leather) materials.', 'ISO 11357 / ASTM E1356'],
            ['Dynamic Mechanical Analysis (DMA)', 'Evaluation of viscoelastic behaviour and glass transition temperature of leather and leatherite (synthetic leather) materials under oscillatory load.', 'ISO 6721 / ASTM D5023'],
            ['Thermal Mechanical Analysis (TMA)', 'Measurement of dimensional change and coefficient of thermal expansion in leather and leatherite (synthetic leather) materials with temperature.', 'ISO 11359 / ASTM E831'],
            ['Heat Deflection Temperature (HDT)', 'Determination of the temperature at which leather and leatherite (synthetic leather) materials deforms under a specified load - critical for service-temperature rating.', 'ISO 75 / ASTM D648'],
            ['Melt Flow Index (MFI)', 'Measurement of melt flow rate of leather and leatherite (synthetic leather) materials - indicating processability and molecular weight consistency.', 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['Opacity and Optical Density Measurement', 'Measurement of light-blocking capability of leather and leatherite (synthetic leather) materials - relevant for barrier, print, and appearance specifications.', 'ASTM D589 / ISO 2469'],
            ['Absorbance / Transmittance (UV-Vis)', 'Determination of light absorbance and transmittance of leather and leatherite (synthetic leather) materials across the UV-visible spectrum.', 'ASTM D1003'],
            ['Visual Colour Appearance', 'Standardised visual assessment of colour and appearance of leather and leatherite (synthetic leather) materials under controlled lighting.', 'ASTM D1729'],
            ['Haze and Gloss Properties', 'Measurement of haze and surface gloss of leather and leatherite (synthetic leather) materials - critical for clarity and aesthetic specifications.', 'ASTM D1003 / ASTM D523']
        ],
        'material characterisation' => [
            ['Structural Identification (FTIR)', 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of leather and leatherite (synthetic leather) materials.', 'ASTM E1252 / ISO 4650'],
            ['Particle Size Analysis', 'Determination of particle size distribution in leather and leatherite (synthetic leather) materials using laser diffraction methods.', 'ISO 13320'],
            ['Molecular Weight Determination (GPC)', 'Gel permeation chromatography to determine molecular weight and distribution in leather and leatherite (synthetic leather) materials.', 'ASTM D5296'],
            ['Oxygen Transmission Rate (OTR)', 'Measurement of the rate of oxygen permeation through leather and leatherite (synthetic leather) materials - critical for barrier and shelf-life performance.', 'ASTM D3985'],
            ['Water Vapour Transmission Rate (WVTR)', 'Measurement of the rate of water vapour permeation through leather and leatherite (synthetic leather) materials - critical for moisture barrier performance.', 'ASTM F1249']
        ]
    ];

    $totalInserted = 0;
    foreach ($testData as $discName => $tests) {
        $searchDisc = $discName === 'material characterisation' ? 'Material Characterization' : ucfirst($discName);
        $stmt = $conn->prepare("SELECT id FROM disciplines WHERE name = ?");
        $stmt->execute([$searchDisc]);
        $discRow = $stmt->fetch();
        
        if (!$discRow) {
            continue;
        }
        $discId = $discRow['id'];

        foreach ($tests as $t) {
            $tName = $t[0];
            $tDesc = $t[1];
            $tStd = $t[2];

            $stmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
            $stmt->execute([$tName, $discId]);
            $testRow = $stmt->fetch();
            
            if ($testRow) {
                $testId = $testRow['id'];
                $stmt = $conn->prepare("UPDATE tests SET description = ?, standards = ? WHERE id = ?");
                $stmt->execute([$tDesc, $tStd, $testId]);
            } else {
                $stmt = $conn->prepare("INSERT INTO tests (name, description, standards, discipline_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tName, $tDesc, $tStd, $discId]);
                $testId = $conn->lastInsertId();
            }

            $stmt = $conn->prepare("INSERT INTO product_test (product_id, test_id) VALUES (?, ?)");
            $stmt->execute([$productId, $testId]);
            $totalInserted++;
        }
    }

    $conn->commit();
    echo "Success! Inserted and mapped $totalInserted tests for $productName.\n";

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
?>
