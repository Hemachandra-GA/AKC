<?php
require_once 'includes/db_connect.php';

try {
    $prod_slug = 'toys-similar-products';
    $prod_name = 'Toys & Similar Products';
    $prod_desc = "AKC's accredited laboratories deliver full-spectrum testing for toys, games, children's accessories, and similar consumer products. Our multidisciplinary team screens for hazardous heavy metals and restricted substances, evaluates structural and mechanical safety, characterises thermal behaviour of materials, and validates optical, biological, and electrical properties to ensure compliance with EN 71, ASTM F963, BIS, and international safety standards.";

    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$prod_slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $prod_id = $product['id'];
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$prod_name, $prod_desc, $prod_id]);
    } else {
        $sortStmt = $conn->query("SELECT MAX(sort_order) FROM products");
        $maxSort = $sortStmt->fetchColumn();
        $sortOrder = $maxSort ? $maxSort + 1 : 1;
        
        $stmt = $conn->prepare("INSERT INTO products (name, slug, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prod_name, $prod_slug, $prod_desc, $sortOrder]);
        $prod_id = $conn->lastInsertId();
    }

    echo "Product ID: $prod_id\n";

    // tests data
    $data = [
        'chemical' => [
            ['name' => 'Viscosity Measurement', 'desc' => 'Determination of viscosity of paints, coatings, and polymer materials used on toys and similar products - confirming formulation consistency and application suitability.', 'stds' => 'ISO 1628 / ASTM D445'],
            ['name' => 'Specific Gravity and Density Measurement', 'desc' => 'Measurement of density of materials used in toys and similar products - for formulation verification, quality control, and material grading.', 'stds' => 'ISO 1183 / ASTM D792'],
            ['name' => 'Ash Content', 'desc' => 'Quantitative determination of inorganic residue remaining after complete combustion of toys and similar products - confirming filler loading and purity.', 'stds' => 'ISO 3451 / ASTM D5630'],
            ['name' => 'Carbon Black Content', 'desc' => 'Quantification of carbon black levels in toys and similar products to verify UV protection and compounding compliance.', 'stds' => 'ISO 6964 / ASTM D1603'],
            ['name' => 'Carbon Black Dispersion Test', 'desc' => 'Microscopic evaluation of carbon black distribution and uniformity within toys and similar products - critical for consistent UV protection and quality compliance.', 'stds' => 'ISO 18553 / ASTM D5596'],
            ['name' => 'Volatile Organic Content Test', 'desc' => 'Measurement of VOCs emitted or contained within toys and similar products - confirming compliance with children\'s product safety standards for indoor air quality.', 'stds' => 'ISO 12219 / ASTM D3960'],
            ['name' => 'Water and Moisture Content', 'desc' => 'Determination of total water and moisture content in toys and similar products - critical for dimensional stability and prevention of microbial growth in children\'s products.', 'stds' => 'ISO 15512 / ASTM D6980'],
            ['name' => 'Water Absorbance and Effect on Water', 'desc' => 'Evaluation of how toys and similar products interact with water under prolonged exposure - confirming safety and performance integrity during water-play and wet-use scenarios.', 'stds' => 'ISO 62 / ASTM D570'],
            ['name' => 'Heavy Metal Analysis', 'desc' => 'Screening and quantification of 8 regulated heavy metals (lead, cadmium, chromium, mercury, arsenic, barium, selenium, antimony) in toys and similar products - per EN 71-3, ASTM F963, and ISO 8124-3 migration limits.', 'stds' => 'EN 71-3 / ASTM F963 / ISO 8124-3 / ICP-OES'],
            ['name' => 'Overall Migration Test', 'desc' => 'Quantification of total extractable chemical substances from toys and similar products - confirming compliance with children\'s product safety and food-contact requirements.', 'stds' => 'EN 71-3 / IS 9845'],
            ['name' => 'Specific Migration Test', 'desc' => 'Targeted analysis of specific regulated substances migrating from toys and similar products into saliva, perspiration, or digestive simulants - per EN 71 and ASTM F963.', 'stds' => 'EN 71-3 / ASTM F963 / REACH'],
            ['name' => 'Accelerated Aging Test', 'desc' => 'Simulation of long-term use and UV exposure to predict performance retention of toys and similar products - assessing surface coatings, materials, and finishes over product service life.', 'stds' => 'ISO 4892 / ASTM D4329'],
            ['name' => 'Oven Ageing Test', 'desc' => 'Thermal ageing evaluation of toys and similar products under controlled oven conditions - confirming resistance to heat-induced degradation during storage and use.', 'stds' => 'ISO 188 / ASTM D573'],
            ['name' => 'Adhesion Test', 'desc' => 'Assessment of coating adhesion strength on the surfaces of toys and similar products - confirming that paints, prints, and decorative coatings do not flake or detach under mechanical contact.', 'stds' => 'EN 71-1 / ISO 2409 / ASTM D3359'],
            ['name' => 'Environmental Stress Crack Resistance', 'desc' => 'Evaluation of the resistance of toys and similar products to stress cracking under mechanical and chemical exposure - confirming long-term structural safety.', 'stds' => 'ISO 22088 / ASTM D1693'],
            ['name' => 'Residual Solvent Analysis', 'desc' => 'Detection of residual solvents in toys and similar products - confirming safety for children\'s skin and respiratory exposure per EN 71-9 and related standards.', 'stds' => 'USP 467 / EN 71-9'],
            ['name' => 'pH Value Determination', 'desc' => 'Measurement of pH of surface coatings and material extracts from toys and similar products - confirming skin-safety compliance for children\'s play materials.', 'stds' => 'ISO 4045 / ASTM E70'],
            ['name' => 'Conductivity Measurement', 'desc' => 'Assessment of electrical conductivity in conductive materials and components used in toys and similar products - for safety compliance in electric and battery-operated products.', 'stds' => 'ISO 7888 / ASTM D1125'],
            ['name' => 'Resistance to Chemical', 'desc' => 'Evaluation of the resistance of toys and similar products to cleaning agents, disinfectants, and children\'s body fluids - confirming chemical durability under real-use conditions.', 'stds' => 'EN 71-1 / ISO 175 / ASTM D543']
        ],
        'mechanical' => [
            ['name' => 'Tensile Test', 'desc' => 'Measurement of tensile strength and elongation of components in toys and similar products - confirming structural integrity and resistance to pull-apart failure hazards.', 'stds' => 'ISO 8124-1 / ASTM F963 / ISO 527 / ASTM D638'],
            ['name' => 'Compressional Test', 'desc' => 'Determination of compressive load capacity of toys and similar products - confirming structural safety and resistance to crushing under applied load.', 'stds' => 'ISO 604 / ASTM D695'],
            ['name' => 'Impact Resistance', 'desc' => 'Evaluation of toys and similar products under simulated drop and impact conditions - confirming resistance to breakage and prevention of hazardous sharp edge or fragment formation.', 'stds' => 'ISO 8124-1 / ASTM F963 / ISO 179 / ISO 180'],
            ['name' => 'Tear Test', 'desc' => 'Measurement of force required to tear fabric and flexible components of toys and similar products - confirming resistance to tearing and prevention of hazardous fragment generation.', 'stds' => 'ISO 13937 / ASTM D1424 / EN 71-1'],
            ['name' => 'Hardness Test', 'desc' => 'Measurement of surface hardness of materials used in toys and similar products - confirming compliance with softness requirements for products intended for young children and infants.', 'stds' => 'ISO 868 / ASTM D2240 / EN 71-1'],
            ['name' => 'Rub Test', 'desc' => 'Assessment of paint and print transfer resistance under rubbing for toys and similar products - preventing ingestion hazards from flaking surface coatings on children\'s products.', 'stds' => 'EN 71-1 / ISO 105-X12 / ASTM D5264'],
            ['name' => 'Seal Strength', 'desc' => 'Measurement of seal integrity in packaging and flexible components of toys and similar products - confirming that seals do not fail and create entrapment or suffocation hazards.', 'stds' => 'ASTM F88 / ISO 11339'],
            ['name' => 'Burst Strength', 'desc' => 'Determination of pressure required to rupture inflatable surfaces in toys and similar products - confirming safety under use conditions and compliance with inflation pressure limits.', 'stds' => 'ISO 8124-1 / ASTM F963 / ASTM D3786'],
            ['name' => 'Hot Tack', 'desc' => 'Measurement of immediate bond strength in laminate and film sealing operations for toys and similar products - for quality control in toy packaging production.', 'stds' => 'ASTM F1921 / ISO 16111'],
            ['name' => 'Gelbo Test', 'desc' => 'Flex durability evaluation of flexible packaging and laminated materials in toys and similar products under repeated bending - assessing material integrity under distribution stress.', 'stds' => 'ASTM F392 / MIL-B-131'],
            ['name' => 'Torque Test', 'desc' => 'Measurement of opening torque for cap closures and battery compartments in toys and similar products - confirming child-resistance compliance and adult-override functionality.', 'stds' => 'ISO 8317 / ASTM D3474 / EN 862'],
            ['name' => 'Endurance Test', 'desc' => 'Evaluation of toys and similar products under repeated mechanical loading simulating intensive play - confirming durability and safety through extended product service life.', 'stds' => 'ISO 8124-1 / ASTM F963 / ISO 13003'],
            ['name' => 'Abrasion Test', 'desc' => 'Quantification of surface wear resistance for coatings and material surfaces of toys and similar products - confirming that degradation does not create chemical or physical hazards.', 'stds' => 'ISO 4649 / ASTM D1044 / EN 71-1'],
            ['name' => 'Leak Test', 'desc' => 'Detection of leaks in water-filled components, inflatable toys, and sealed packaging of toys and similar products - confirming product integrity and safety under use and storage.', 'stds' => 'ASTM D3078 / ISO 8124-1 / ASTM F2096']
        ],
        'thermal' => [
            ['name' => 'Thermogravimetry Analysis (TGA)', 'desc' => 'Measurement of weight change in toys and similar products as a function of temperature - characterising composition, filler content, and thermal stability.', 'stds' => 'ISO 11358 / ASTM E1131'],
            ['name' => 'Differential Scanning Calorimetry (DSC)', 'desc' => 'Determination of melting point, crystallinity, and thermal transitions in toys and similar products.', 'stds' => 'ISO 11357 / ASTM E1356'],
            ['name' => 'Dynamic Mechanical Analysis (DMA)', 'desc' => 'Evaluation of viscoelastic behaviour and glass transition temperature of toys and similar products under oscillatory load.', 'stds' => 'ISO 6721 / ASTM D5023'],
            ['name' => 'Thermal Mechanical Analysis (TMA)', 'desc' => 'Measurement of dimensional change and coefficient of thermal expansion in toys and similar products with temperature.', 'stds' => 'ISO 11359 / ASTM E831'],
            ['name' => 'Heat Deflection Temperature (HDT)', 'desc' => 'Determination of the temperature at which toys and similar products deforms under a specified load - critical for service-temperature rating.', 'stds' => 'ISO 75 / ASTM D648'],
            ['name' => 'Melt Flow Index (MFI)', 'desc' => 'Measurement of melt flow rate of toys and similar products - indicating processability and molecular weight consistency.', 'stds' => 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['name' => 'Opacity and Optical Density Measurement', 'desc' => 'Measurement of light-blocking capability of toys and similar products - relevant for barrier, print, and appearance specifications.', 'stds' => 'ASTM D589 / ISO 2469'],
            ['name' => 'Absorbance / Transmittance (UV-Vis)', 'desc' => 'Determination of light absorbance and transmittance of toys and similar products across the UV-visible spectrum.', 'stds' => 'ASTM D1003'],
            ['name' => 'Visual Colour Appearance', 'desc' => 'Standardised visual assessment of colour and appearance of toys and similar products under controlled lighting.', 'stds' => 'ASTM D1729'],
            ['name' => 'Haze and Gloss Properties', 'desc' => 'Measurement of haze and surface gloss of toys and similar products - critical for clarity and aesthetic specifications.', 'stds' => 'ASTM D1003 / ASTM D523']
        ],
        'material-characterization' => [
            ['name' => 'Structural Identification (FTIR)', 'desc' => 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of toys and similar products.', 'stds' => 'ASTM E1252 / ISO 4650'],
            ['name' => 'Particle Size Analysis', 'desc' => 'Determination of particle size distribution in toys and similar products using laser diffraction methods.', 'stds' => 'ISO 13320'],
            ['name' => 'Molecular Weight Determination (GPC)', 'desc' => 'Gel permeation chromatography to determine molecular weight and distribution in toys and similar products.', 'stds' => 'ASTM D5296'],
            ['name' => 'Oxygen Transmission Rate (OTR)', 'desc' => 'Measurement of the rate of oxygen permeation through toys and similar products - critical for barrier and shelf-life performance.', 'stds' => 'ASTM D3985'],
            ['name' => 'Water Vapour Transmission Rate (WVTR)', 'desc' => 'Measurement of the rate of water vapour permeation through toys and similar products - critical for moisture barrier performance.', 'stds' => 'ASTM F1249']
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
            $testStmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ? AND description LIKE '%toys%'");
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

    echo "Successfully inserted and mapped Toys & Similar Products tests!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
