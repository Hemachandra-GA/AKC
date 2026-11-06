<?php
require_once 'includes/db_connect.php';

try {
    $conn->beginTransaction();

    $productSlug = 'food-agricultural-products';
    $productName = 'Food & Agricultural Products';
    
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$productSlug]);
    $product = $stmt->fetch();

    $desc = "AKC's accredited laboratories deliver full-spectrum testing for food and agricultural products - including raw agricultural commodities, processed food materials, food-contact surfaces, and food packaging. Our multidisciplinary team screens for hazardous contaminants and restricted substances, evaluates packaging mechanical performance, characterises thermal and barrier properties, validates optical quality, and assesses biological safety to ensure compliance with FSSAI, CODEX Alimentarius, EU food regulations, and international safety standards.";

    if (!$product) {
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, sort_order) VALUES (?, ?, ?, 6)");
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
            ['Viscosity Measurement', 'Determination of viscosity of liquid food preparations, sauces, oils, and food-contact coating formulations used with food and agricultural products - confirming formulation consistency and processing suitability.', 'ISO 1628 / ASTM D445'],
            ['Specific Gravity and Density Measurement', 'Measurement of density of food liquids, emulsions, agricultural oils, and packaging substrate materials associated with food and agricultural products - for quality control and specification compliance.', 'ISO 1183 / ASTM D792'],
            ['Ash Content', 'Quantitative determination of total mineral content in food and agricultural products after combustion - confirming inorganic mineral levels and providing a baseline for nutritional and quality profiling.', 'ISO 2171 / AOAC 923.03'],
            ['Carbon Black Content', 'Quantification of carbon black levels in packaging films and polymer substrates used with food and agricultural products - verifying UV protection and light-barrier compounding specifications.', 'ISO 6964 / ASTM D1603'],
            ['Carbon Black Dispersion Test', 'Microscopic evaluation of carbon black distribution and uniformity in packaging substrates for food and agricultural products - confirming consistent UV barrier quality and material performance.', 'ISO 18553 / ASTM D5596'],
            ['Volatile Organic Content Test', 'Measurement of VOCs in packaging materials, adhesives, and coatings used with food and agricultural products - confirming the absence of harmful emissions that could migrate into food.', 'ISO 12219 / ASTM D3960'],
            ['Water and Moisture Content', 'Determination of moisture content in food and agricultural products - a critical quality and safety parameter affecting shelf life, microbial stability, texture, and regulatory compliance.', 'ISO 712 / AOAC 925.10 / ASTM D6980'],
            ['Water Absorbance and Effect on Water', 'Evaluation of water absorption behaviour of food packaging materials and agricultural product coatings - confirming moisture management, barrier performance, and dimensional stability.', 'ISO 62 / ASTM D570'],
            ['Heavy Metal Analysis', 'Screening and quantification of hazardous heavy metals including lead, cadmium, arsenic, mercury, and chromium in food and agricultural products - per FSSAI, CODEX Alimentarius, EU 1881/2006, and international food safety limits.', 'ICP-OES / ICP-MS / FSSAI / CODEX CAC/GL 5 / EU 1881/2006'],
            ['Overall Migration Test', 'Total quantification of substances migrating from food-contact packaging into food and agricultural products - a mandatory compliance test under EU 10/2011, IS 9845, and FSSAI regulations.', 'EU 10/2011 / IS 9845 / FDA 21 CFR'],
            ['Specific Migration Test', 'Targeted analysis of individual regulated substances migrating from packaging materials into food and agricultural products - for full regulatory compliance documentation and certification.', 'EU 10/2011 / FDA 21 CFR / REACH'],
            ['Accelerated Aging Test', 'Simulation of long-term storage and distribution conditions to predict shelf-life performance of packaging for food and agricultural products - confirming barrier retention and chemical stability.', 'ISO 4892 / ASTM D4329 / ASTM F1980'],
            ['Oven Ageing Test', 'Thermal ageing evaluation of packaging substrates and food-contact coatings for food and agricultural products - confirming resistance to heat-induced degradation during pasteurisation, retort, and hot-fill processing.', 'ISO 188 / ASTM D573'],
            ['Adhesion Test', 'Assessment of coating, laminate, and label adhesion on packaging for food and agricultural products - confirming bond durability and resistance to delamination under handling and storage conditions.', 'ISO 2409 / ASTM D3359 / PSTC-101'],
            ['Environmental Stress Crack Resistance', 'Evaluation of the resistance of packaging materials for food and agricultural products to stress cracking under combined mechanical and chemical exposure - critical for container and bottle integrity.', 'ISO 22088 / ASTM D1693'],
            ['Residual Solvent Analysis', 'Detection and quantification of residual solvents in printed, laminated, and coated packaging for food and agricultural products - a critical food-safety compliance test confirming solvent absence in food-contact layers.', 'USP 467 / ICH Q3C / ASTM E1428'],
            ['pH Value Determination', 'Measurement of pH of food and agricultural products and their packaging extracts - confirming acidity, alkalinity, and safety compliance for processing, preservation, and food-contact applications.', 'ISO 1842 / AOAC 943.02 / ASTM E70'],
            ['Conductivity Measurement', 'Assessment of electrical conductivity of food product extracts, agricultural solutions, and antistatic packaging materials - for quality monitoring and process control in food production.', 'ISO 7888 / ASTM D1125'],
            ['Resistance to Chemical', 'Evaluation of the integrity of packaging materials for food and agricultural products after exposure to food constituents, cleaning agents, sanitisers, and storage chemicals - confirming chemical compatibility and safety.', 'ISO 175 / ASTM D543']
        ],
        'mechanical' => [
            ['Tensile Test', 'Measurement of tensile strength and elongation of packaging films, laminates, and flexible bags for food and agricultural products - confirming structural performance under machine filling, sealing, and distribution loads.', 'ISO 527-3 / ASTM D882 / ASTM D638'],
            ['Compressional Test', 'Determination of compressive load-bearing capacity of rigid containers, trays, crates, and packaging components for food and agricultural products under stacking and storage stress.', 'ISO 604 / ASTM D695 / ASTM D642'],
            ['Impact Resistance', 'Evaluation of packaging for food and agricultural products under impact loading - simulating drop and handling stresses to confirm structural integrity during distribution and retail handling.', 'ISO 7765 / ASTM D1709 / ASTM D4272'],
            ['Tear Test', 'Measurement of force required to propagate a tear through packaging films and laminates for food and agricultural products - confirming toughness and resistance to handling damage in the supply chain.', 'ISO 6383-1 / ASTM D1004 / ASTM D1922'],
            ['Hardness Test', 'Measurement of surface hardness of rigid packaging materials and container components for food and agricultural products - confirming resistance to mechanical damage during transit and warehousing.', 'ISO 868 / ASTM D2240 / ASTM D785'],
            ['Rub Test', 'Assessment of print and surface coating durability on packaging for food and agricultural products under rubbing - confirming label integrity, brand presentation quality, and resistance to scuffing during distribution.', 'ASTM D5264 / TAPPI T830 / ISO 105-X12'],
            ['Seal Strength', 'Quantification of heat seal bond strength in packaging for food and agricultural products - the primary mechanical test confirming packaging integrity and prevention of contamination or spoilage.', 'ASTM F88 / ISO 11339 / ASTM F2029'],
            ['Burst Strength', 'Measurement of pressure required to rupture packaging films, pouches, and laminates for food and agricultural products - validating performance under pressurised filling, hot-fill, and distribution stresses.', 'ISO 2759 / ASTM D774 / TAPPI T403'],
            ['Hot Tack', 'Measurement of immediate seal strength at sealing temperature for packaging of food and agricultural products on high-speed filling lines - preventing premature seal failure and food contamination.', 'ASTM F1921 / ISO 16111'],
            ['Gelbo Test', 'Flex durability evaluation of packaging for food and agricultural products under repeated bending and twisting - simulating transit and distribution handling to assess pinhole formation and barrier loss.', 'ASTM F392 / MIL-B-131'],
            ['Torque Test', 'Measurement of opening and closing torque of caps, closures, and resealable packaging systems for food and agricultural products - confirming usability, tamper evidence, and consumer-opening force compliance.', 'ISO 8362 / ASTM D3474 / EN 28317'],
            ['Endurance Test', 'Evaluation of packaging for food and agricultural products under sustained or cyclic mechanical stress - confirming fatigue life and long-term integrity through the food supply chain and storage period.', 'ISO 13003 / ASTM D7774 / ASTM F1980'],
            ['Abrasion Test', 'Quantification of surface wear resistance of packaging for food and agricultural products - confirming print and coating durability for transit-ready, retail-display, and consumer-use packaging.', 'ISO 4649 / ASTM D1044 / TAPPI T476'],
            ['Leak Test', 'Detection of seal defects, pinholes, and closure failures in packaging for food and agricultural products - confirming hermetic integrity for food safety and prevention of spoilage.', 'ASTM D3078 / ASTM F2096 / ISO 11607']
        ],
        'thermal' => [
            ['Thermogravimetry Analysis (TGA)', 'Measurement of weight change in food and agricultural products as a function of temperature - characterising composition, filler content, and thermal stability.', 'ISO 11358 / ASTM E1131'],
            ['Differential Scanning Calorimetry (DSC)', 'Determination of melting point, crystallinity, and thermal transitions in food and agricultural products.', 'ISO 11357 / ASTM E1356'],
            ['Dynamic Mechanical Analysis (DMA)', 'Evaluation of viscoelastic behaviour and glass transition temperature of food and agricultural products under oscillatory load.', 'ISO 6721 / ASTM D5023'],
            ['Thermal Mechanical Analysis (TMA)', 'Measurement of dimensional change and coefficient of thermal expansion in food and agricultural products with temperature.', 'ISO 11359 / ASTM E831'],
            ['Heat Deflection Temperature (HDT)', 'Determination of the temperature at which food and agricultural products deforms under a specified load - critical for service-temperature rating.', 'ISO 75 / ASTM D648'],
            ['Melt Flow Index (MFI)', 'Measurement of melt flow rate of food and agricultural products - indicating processability and molecular weight consistency.', 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['Opacity and Optical Density Measurement', 'Measurement of light-blocking capability of food and agricultural products - relevant for barrier, print, and appearance specifications.', 'ASTM D589 / ISO 2469'],
            ['Absorbance / Transmittance (UV-Vis)', 'Determination of light absorbance and transmittance of food and agricultural products across the UV-visible spectrum.', 'ASTM D1003'],
            ['Visual Colour Appearance', 'Standardised visual assessment of colour and appearance of food and agricultural products under controlled lighting.', 'ASTM D1729'],
            ['Haze and Gloss Properties', 'Measurement of haze and surface gloss of food and agricultural products - critical for clarity and aesthetic specifications.', 'ASTM D1003 / ASTM D523']
        ],
        'material characterisation' => [
            ['Structural Identification (FTIR)', 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of food and agricultural products.', 'ASTM E1252 / ISO 4650'],
            ['Particle Size Analysis', 'Determination of particle size distribution in food and agricultural products using laser diffraction methods.', 'ISO 13320'],
            ['Molecular Weight Determination (GPC)', 'Gel permeation chromatography to determine molecular weight and distribution in food and agricultural products.', 'ASTM D5296'],
            ['Oxygen Transmission Rate (OTR)', 'Measurement of the rate of oxygen permeation through food and agricultural products - critical for barrier and shelf-life performance.', 'ASTM D3985'],
            ['Water Vapour Transmission Rate (WVTR)', 'Measurement of the rate of water vapour permeation through food and agricultural products - critical for moisture barrier performance.', 'ASTM F1249']
        ]
    ];

    $totalInserted = 0;
    foreach ($testData as $discName => $tests) {
        $searchDisc = $discName === 'material characterisation' ? 'Material Characterization' : ucfirst($discName);
        $stmt = $conn->prepare("SELECT id FROM disciplines WHERE name = ?");
        $stmt->execute([$searchDisc]);
        $discRow = $stmt->fetch();
        
        if (!$discRow) {
            echo "Warning: Discipline '$searchDisc' not found. Skipping.\n";
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
