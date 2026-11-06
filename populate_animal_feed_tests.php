<?php
require_once 'includes/db_connect.php';

try {
    $conn->beginTransaction();

    // 1. Ensure Product Exists
    $productSlug = 'animal-food-feed';
    $productName = 'Animal Food & Feed';
    
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$productSlug]);
    $product = $stmt->fetch();

    if (!$product) {
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, sort_order) VALUES (?, ?, ?, 5)");
        $stmt->execute([
            $productName, 
            $productSlug,
            "AKC's accredited laboratories deliver full-spectrum testing for animal food and feed materials - including pet food, livestock feed, aquafeed, compound feed, and feed packaging. Our multidisciplinary team screens for hazardous contaminants and heavy metals, evaluates feed packaging mechanical performance, characterises thermal and barrier properties, and validates biological safety and regulatory compliance across FSSAI, EU Feed Regulations, and international standards."
        ]);
        $productId = $conn->lastInsertId();
        echo "Inserted new product: $productName (ID: $productId)\n";
    } else {
        $productId = $product['id'];
        echo "Product exists: $productName (ID: $productId)\n";
        
        // Update description just in case
        $stmt = $conn->prepare("UPDATE products SET description = ? WHERE id = ?");
        $stmt->execute([
            "AKC's accredited laboratories deliver full-spectrum testing for animal food and feed materials - including pet food, livestock feed, aquafeed, compound feed, and feed packaging. Our multidisciplinary team screens for hazardous contaminants and heavy metals, evaluates feed packaging mechanical performance, characterises thermal and barrier properties, and validates biological safety and regulatory compliance across FSSAI, EU Feed Regulations, and international standards.",
            $productId
        ]);
    }

    // 2. Aggressively Delete ALL existing test mappings for this product to ensure exactly the requested counts
    $stmt = $conn->prepare("DELETE FROM product_test WHERE product_id = ?");
    $stmt->execute([$productId]);
    echo "Deleted old test mappings to enforce strict counts.\n";

    // Data Structure for Tests exactly aligning with the prompt
    $testData = [
        'chemical' => [
            ['Viscosity Measurement', 'Determination of viscosity of liquid feed preparations, supplements, and feed coating formulations used in animal food and feed materials - confirming formulation consistency and application suitability.', 'ISO 1628 / ASTM D445'],
            ['Specific Gravity and Density Measurement', 'Measurement of density of liquid feed materials, additives, and packaging polymer substrates associated with animal food and feed materials.', 'ISO 1183 / ASTM D792'],
            ['Ash Content', 'Quantitative determination of total mineral content in animal food and feed materials - confirming inorganic filler levels and providing a baseline for mineral profiling in feed formulations.', 'ISO 5984 / ASTM D5630'],
            ['Carbon Black Content', 'Quantification of carbon black levels in feed packaging films and polymer substrates associated with animal food and feed materials - verifying UV protection and barrier compounding specifications.', 'ISO 6964 / ASTM D1603'],
            ['Carbon Black Dispersion Test', 'Microscopic evaluation of carbon black distribution and uniformity in feed packaging substrates for animal food and feed materials - confirming consistent UV barrier quality.', 'ISO 18553 / ASTM D5596'],
            ['Volatile Organic Content Test', 'Measurement of VOCs in feed packaging materials, adhesives, and coatings associated with animal food and feed materials - confirming the absence of harmful emissions that could contaminate feed.', 'ISO 12219 / ASTM D3960'],
            ['Water and Moisture Content', 'Determination of moisture content in animal food and feed materials - a critical quality parameter affecting shelf life, microbial safety, energy value, and storage stability.', 'ISO 6496 / ASTM D6980'],
            ['Water Absorbance and Effect on Water', 'Evaluation of water absorption behaviour of feed pellets, extruded feed products, and packaging for animal food and feed materials - confirming moisture management and barrier performance.', 'ISO 62 / ASTM D570'],
            ['Heavy Metal Analysis', 'Screening and quantification of hazardous heavy metals including lead, cadmium, arsenic, mercury, and chromium in animal food and feed materials - per EU, FSSAI, and international feed safety regulations.', 'ICP-OES / XRF / EC 574/2011 / FSSAI'],
            ['Overall Migration Test', 'Quantification of total chemical substances migrating from packaging into animal food and feed materials - confirming food-contact safety and regulatory compliance.', 'EU 10/2011 / IS 9845'],
            ['Specific Migration Test', 'Targeted analysis of individual regulated substances migrating from packaging materials into animal food and feed materials - for EU, FDA, and international feed packaging compliance.', 'EU 10/2011 / FDA 21 CFR / REACH'],
            ['Accelerated Aging Test', 'Simulation of long-term storage conditions to predict shelf-life performance of packaging for animal food and feed materials - confirming barrier retention and chemical stability over declared shelf life.', 'ISO 4892 / ASTM D4329 / ASTM F1980'],
            ['Oven Ageing Test', 'Thermal ageing evaluation of packaging substrates and coatings for animal food and feed materials - confirming resistance to heat-induced degradation during storage and processing.', 'ISO 188 / ASTM D573'],
            ['Adhesion Test', 'Assessment of print, coating, and laminate adhesion on packaging for animal food and feed materials - confirming bond durability and preventing delamination that could contaminate feed contents.', 'ISO 2409 / ASTM D3359'],
            ['Environmental Stress Crack Resistance', 'Evaluation of the resistance of packaging for animal food and feed materials to stress cracking under mechanical and chemical exposure - critical for bag and container integrity.', 'ISO 22088 / ASTM D1693'],
            ['Residual Solvent Analysis', 'Detection of residual solvents in printed and laminated packaging for animal food and feed materials - confirming the absence of harmful chemical contamination that could transfer into feed.', 'USP 467 / ICH Q3C / ASTM E1428'],
            ['pH Value Determination', 'Measurement of pH of extracts from animal food and feed materials and their packaging - for quality control, chemical characterisation, and regulatory compliance.', 'ISO 4045 / ASTM E70'],
            ['Conductivity Measurement', 'Assessment of electrical conductivity of extracts and antistatic packaging materials associated with animal food and feed materials - for quality monitoring and packaging performance verification.', 'ISO 7888 / ASTM D1125'],
            ['Resistance to Chemical', 'Evaluation of the integrity of packaging for animal food and feed materials after exposure to feed oils, moisture, and storage chemicals - confirming chemical compatibility and barrier durability.', 'ISO 175 / ASTM D543']
        ],
        'mechanical' => [
            ['Tensile Test', 'Measurement of tensile strength and elongation of packaging films, laminates, and flexible bags for animal food and feed materials - confirming structural performance under machine filling, sealing, and distribution loads.', 'ISO 527-3 / ASTM D882 / ASTM D638'],
            ['Compressional Test', 'Determination of compressive load-bearing capacity of rigid containers, trays, and packaging components for animal food and feed materials under stacking and storage stress.', 'ISO 604 / ASTM D695 / ASTM D642'],
            ['Impact Resistance', 'Evaluation of packaging for animal food and feed materials under impact loading - simulating drop and handling stresses to confirm structural integrity under distribution conditions.', 'ISO 7765 / ASTM D1709 / ASTM D4272'],
            ['Tear Test', 'Measurement of force required to propagate a tear through packaging films and laminates for animal food and feed materials - confirming toughness and resistance to handling damage.', 'ISO 6383-1 / ASTM D1004 / ASTM D1922'],
            ['Hardness Test', 'Measurement of surface hardness of rigid packaging materials and container components for animal food and feed materials - confirming resistance to mechanical damage during transit.', 'ISO 868 / ASTM D2240 / ASTM D785'],
            ['Rub Test', 'Assessment of print and surface coating durability on packaging for animal food and feed materials under rubbing - confirming label integrity and retail presentation quality during distribution.', 'ASTM D5264 / TAPPI T830 / ISO 105-X12'],
            ['Seal Strength', 'Quantification of heat seal bond strength in packaging for animal food and feed materials - the primary mechanical test confirming packaging integrity and prevention of feed contamination.', 'ASTM F88 / ISO 11339 / ASTM F2029'],
            ['Burst Strength', 'Measurement of pressure required to rupture packaging films and laminates for animal food and feed materials - validating performance under pressurised filling, transit vibration, and stacking loads.', 'ISO 2759 / ASTM D774 / TAPPI T403'],
            ['Hot Tack', 'Measurement of immediate seal strength at sealing temperature for packaging of animal food and feed materials on high-speed filling lines - preventing premature seal failure during production.', 'ASTM F1921 / ISO 16111'],
            ['Gelbo Test', 'Flex durability evaluation of packaging for animal food and feed materials under repeated bending and twisting - simulating transit handling to assess pinhole formation and barrier loss.', 'ASTM F392 / MIL-B-131'],
            ['Torque Test', 'Measurement of opening and closing torque of resealable closures and container caps for animal food and feed materials - confirming functionality and child-resistance compliance where applicable.', 'ISO 8362 / ASTM D3474 / EN 28317'],
            ['Endurance Test', 'Evaluation of packaging for animal food and feed materials under sustained mechanical stress - confirming fatigue life and long-term integrity through the feed supply chain and storage period.', 'ISO 13003 / ASTM D7774 / ASTM F1980'],
            ['Abrasion Test', 'Quantification of surface wear resistance of packaging for animal food and feed materials - confirming print and coating durability for transit-ready and retail-display feed bags and containers.', 'ISO 4649 / ASTM D1044 / TAPPI T476'],
            ['Leak Test', 'Detection of seal defects, pinholes, and closure failures in packaging for animal food and feed materials - confirming airtight integrity for feed preservation and safety.', 'ASTM D3078 / ASTM F2096 / ISO 11607']
        ],
        'thermal' => [
            ['Thermogravimetry Analysis (TGA)', 'Measurement of weight change in animal food and feed materials as a function of temperature - characterising composition, filler content, and thermal stability.', 'ISO 11358 / ASTM E1131'],
            ['Differential Scanning Calorimetry (DSC)', 'Determination of melting point, crystallinity, and thermal transitions in animal food and feed materials.', 'ISO 11357 / ASTM E1356'],
            ['Dynamic Mechanical Analysis (DMA)', 'Evaluation of viscoelastic behaviour and glass transition temperature of animal food and feed materials under oscillatory load.', 'ISO 6721 / ASTM D5023'],
            ['Thermal Mechanical Analysis (TMA)', 'Measurement of dimensional change and coefficient of thermal expansion in animal food and feed materials with temperature.', 'ISO 11359 / ASTM E831'],
            ['Heat Deflection Temperature (HDT)', 'Determination of the temperature at which animal food and feed materials deforms under a specified load - critical for service-temperature rating.', 'ISO 75 / ASTM D648'],
            ['Melt Flow Index (MFI)', 'Measurement of melt flow rate of animal food and feed materials - indicating processability and molecular weight consistency.', 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['Opacity and Optical Density Measurement', 'Measurement of light-blocking capability of animal food and feed materials - relevant for barrier, print, and appearance specifications.', 'ASTM D589 / ISO 2469'],
            ['Absorbance / Transmittance (UV-Vis)', 'Determination of light absorbance and transmittance of animal food and feed materials across the UV-visible spectrum.', 'ASTM D1003'],
            ['Visual Colour Appearance', 'Standardised visual assessment of colour and appearance of animal food and feed materials under controlled lighting.', 'ASTM D1729'],
            ['Haze and Gloss Properties', 'Measurement of haze and surface gloss of animal food and feed materials - critical for clarity and aesthetic specifications.', 'ASTM D1003 / ASTM D523']
        ],
        'material characterisation' => [
            ['Structural Identification (FTIR)', 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of animal food and feed materials.', 'ASTM E1252 / ISO 4650'],
            ['Particle Size Analysis', 'Determination of particle size distribution in animal food and feed materials using laser diffraction methods.', 'ISO 13320'],
            ['Molecular Weight Determination (GPC)', 'Gel permeation chromatography to determine molecular weight and distribution in animal food and feed materials.', 'ASTM D5296'],
            ['Oxygen Transmission Rate (OTR)', 'Measurement of the rate of oxygen permeation through animal food and feed materials - critical for barrier and shelf-life performance.', 'ASTM D3985'],
            ['Water Vapour Transmission Rate (WVTR)', 'Measurement of the rate of water vapour permeation through animal food and feed materials - critical for moisture barrier performance.', 'ASTM F1249']
        ]
    ];

    $totalInserted = 0;

    foreach ($testData as $discName => $tests) {
        // Find discipline ID
        // Note: material characterisation should map to 'Material Characterisation'
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

            // Insert or Get Test
            $stmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
            $stmt->execute([$tName, $discId]);
            $testRow = $stmt->fetch();
            
            if ($testRow) {
                $testId = $testRow['id'];
                // Update description & standards just in case
                $stmt = $conn->prepare("UPDATE tests SET description = ?, standards = ? WHERE id = ?");
                $stmt->execute([$tDesc, $tStd, $testId]);
            } else {
                $stmt = $conn->prepare("INSERT INTO tests (name, description, standards, discipline_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tName, $tDesc, $tStd, $discId]);
                $testId = $conn->lastInsertId();
            }

            // Map Test to Animal Food Product
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
