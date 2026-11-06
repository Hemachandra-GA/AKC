<?php
require_once 'includes/db_connect.php';

try {
    $prod_slug = 'polymer-polymeric-products';
    
    $stmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$prod_slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found. Please run the chemical tests insertion first.");
    }
    $prod_id = $product['id'];

    $data = [
        'mechanical' => [
            ['name' => 'Tensile Test', 'desc' => 'Measurement of tensile strength and elongation at break for polymer materials and polymeric products - confirming structural integrity and suitability for end use.', 'stds' => 'ISO 527 / ASTM D638'],
            ['name' => 'Compressional Test', 'desc' => 'Determination of compressive load-bearing capacity for polymer materials and polymeric products under controlled axial load.', 'stds' => 'ISO 604 / ASTM D695'],
            ['name' => 'Impact Resistance', 'desc' => 'Evaluation of energy absorbed during high-speed fracture using Izod and Charpy methods for polymer materials and polymeric products.', 'stds' => 'ISO 179 / ASTM D256'],
            ['name' => 'Tear Test', 'desc' => 'Measurement of force required to propagate a tear through polymer materials and polymeric products - confirming toughness under real-use mechanical demands.', 'stds' => 'ISO 6383 / ASTM D1004'],
            ['name' => 'Hardness Test', 'desc' => 'Measurement of resistance to permanent surface indentation in polymer materials and polymeric products using Shore A and Shore D scales.', 'stds' => 'ISO 868 / ASTM D2240'],
            ['name' => 'Rub Test', 'desc' => 'Assessment of resistance to surface scuffing and print/finish transfer under controlled rubbing for polymer materials and polymeric products.', 'stds' => 'ASTM D5264 / ISO 105-X12'],
            ['name' => 'Seal Strength', 'desc' => 'Quantification of heat seal bond strength in polymer materials and polymeric products under peel and tensile loading - confirming seal integrity.', 'stds' => 'ASTM F88 / ISO 11339'],
            ['name' => 'Burst Strength', 'desc' => 'Measurement of the pressure at which polymer materials and polymeric products ruptures under uniform loading - validating structural performance.', 'stds' => 'ISO 2759 / ASTM D774'],
            ['name' => 'Hot Tack', 'desc' => 'Measurement of immediate seal strength of polymer materials and polymeric products while still in the molten or setting state.', 'stds' => 'ASTM F1921 / ISO 16111'],
            ['name' => 'Gelbo Test', 'desc' => 'Flex durability evaluation of polymer materials and polymeric products under repeated twisting and bending - assessing pinhole resistance after mechanical stress.', 'stds' => 'ASTM F392 / MIL-B-131'],
            ['name' => 'Torque Test', 'desc' => 'Measurement of opening and closing torque of closures and fittings associated with polymer materials and polymeric products.', 'stds' => 'ISO 8362 / ASTM D3474'],
            ['name' => 'Endurance Test', 'desc' => 'Evaluation of polymer materials and polymeric products performance under sustained or cyclic mechanical loading - determining fatigue life.', 'stds' => 'ISO 13003 / ASTM D7774'],
            ['name' => 'Abrasion Test', 'desc' => 'Quantification of resistance to surface wear under controlled abrasive contact for polymer materials and polymeric products.', 'stds' => 'ISO 4649 / ASTM D1044'],
            ['name' => 'Leak Test', 'desc' => 'Detection of seal defects, pinholes, and packaging failures in polymer materials and polymeric products using vacuum, pressure decay, or dye penetration methods.', 'stds' => 'ASTM D3078 / ISO 11607']
        ],
        'thermal' => [
            ['name' => 'Thermogravimetry Analysis (TGA)', 'desc' => 'Measurement of weight change in polymer materials and polymeric products as a function of temperature - characterising composition, filler content, and thermal stability.', 'stds' => 'ISO 11358 / ASTM E1131'],
            ['name' => 'Differential Scanning Calorimetry (DSC)', 'desc' => 'Determination of melting point, crystallinity, and thermal transitions in polymer materials and polymeric products.', 'stds' => 'ISO 11357 / ASTM E1356'],
            ['name' => 'Dynamic Mechanical Analysis (DMA)', 'desc' => 'Evaluation of viscoelastic behaviour and glass transition temperature of polymer materials and polymeric products under oscillatory load.', 'stds' => 'ISO 6721 / ASTM D5023'],
            ['name' => 'Thermal Mechanical Analysis (TMA)', 'desc' => 'Measurement of dimensional change and coefficient of thermal expansion in polymer materials and polymeric products with temperature.', 'stds' => 'ISO 11359 / ASTM E831'],
            ['name' => 'Heat Deflection Temperature (HDT)', 'desc' => 'Determination of the temperature at which polymer materials and polymeric products deforms under a specified load - critical for service-temperature rating.', 'stds' => 'ISO 75 / ASTM D648'],
            ['name' => 'Melt Flow Index (MFI)', 'desc' => 'Measurement of melt flow rate of polymer materials and polymeric products - indicating processability and molecular weight consistency.', 'stds' => 'ISO 1133 / ASTM D1238']
        ],
        'optical' => [
            ['name' => 'Opacity and Optical Density Measurement', 'desc' => 'Measurement of light-blocking capability of polymer materials and polymeric products - relevant for barrier, print, and appearance specifications.', 'stds' => 'ASTM D589 / ISO 2469'],
            ['name' => 'Absorbance / Transmittance (UV-Vis)', 'desc' => 'Determination of light absorbance and transmittance of polymer materials and polymeric products across the UV-visible spectrum.', 'stds' => 'ASTM D1003'],
            ['name' => 'Visual Colour Appearance', 'desc' => 'Standardised visual assessment of colour and appearance of polymer materials and polymeric products under controlled lighting.', 'stds' => 'ASTM D1729'],
            ['name' => 'Haze and Gloss Properties', 'desc' => 'Measurement of haze and surface gloss of polymer materials and polymeric products - critical for clarity and aesthetic specifications.', 'stds' => 'ASTM D1003 / ASTM D523']
        ],
        'material-characterization' => [
            ['name' => 'Structural Identification (FTIR)', 'desc' => 'Fourier-transform infrared spectroscopy to identify the chemical structure and composition of polymer materials and polymeric products.', 'stds' => 'ASTM E1252 / ISO 4650'],
            ['name' => 'Particle Size Analysis', 'desc' => 'Determination of particle size distribution in polymer materials and polymeric products using laser diffraction methods.', 'stds' => 'ISO 13320'],
            ['name' => 'Molecular Weight Determination (GPC)', 'desc' => 'Gel permeation chromatography to determine molecular weight and distribution in polymer materials and polymeric products.', 'stds' => 'ASTM D5296'],
            ['name' => 'Oxygen Transmission Rate (OTR)', 'desc' => 'Measurement of the rate of oxygen permeation through polymer materials and polymeric products - critical for barrier and shelf-life performance.', 'stds' => 'ASTM D3985'],
            ['name' => 'Water Vapour Transmission Rate (WVTR)', 'desc' => 'Measurement of the rate of water vapour permeation through polymer materials and polymeric products - critical for moisture barrier performance.', 'stds' => 'ASTM F1249']
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
            $testStmt = $conn->prepare("SELECT id FROM tests WHERE name = ? AND discipline_id = ?");
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

    echo "Successfully inserted and mapped extra tests!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
