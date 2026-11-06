<?php
require_once 'includes/db_connect.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : 'polymer-and-polymeric-products';

$stmt = $conn->prepare("SELECT * FROM products WHERE slug = ?");
$stmt->execute([$slug]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $fallbackStmt = $conn->query("SELECT * FROM products ORDER BY sort_order ASC LIMIT 1");
    $product = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
}

if (!$product) {
    die("Product not found.");
}

$prodName = htmlspecialchars($product['name']);
$prodDesc = htmlspecialchars($product['description'] ? $product['description'] : "AKC provides comprehensive chemical, mechanical, thermal, and regulatory testing services for " . $prodName . ".");

$productItemsMap = [
    'polymer-and-polymeric-products' => ['Plastic Films', 'Plastic Sheets', 'Plastic Laminates', 'Plastic Bottles', 'Plastic Containers', 'Plastic Pipes & Fittings', 'Plastic Rods', 'Plastic Granules/Pellets', 'Plastic Compounds', 'Masterbatches', 'Plastic Caps & Closures', 'Plastic Crates', 'Plastic Bags', 'Plastic Packaging Materials', 'Geomembranes', 'Thermoformed Products', 'Injection Molded Components', 'Blow Molded Products', 'Extruded Profiles', 'Engineering Plastics'],
    'polymer-polymeric-products' => ['Plastic Films', 'Plastic Sheets', 'Plastic Laminates', 'Plastic Bottles', 'Plastic Containers', 'Plastic Pipes & Fittings', 'Plastic Rods', 'Plastic Granules/Pellets', 'Plastic Compounds', 'Masterbatches', 'Plastic Caps & Closures', 'Plastic Crates', 'Plastic Bags', 'Plastic Packaging Materials', 'Geomembranes', 'Thermoformed Products', 'Injection Molded Components', 'Blow Molded Products', 'Extruded Profiles', 'Engineering Plastics'],
    'polymer-polymeric-products-testing' => ['Plastic Films', 'Plastic Sheets', 'Plastic Laminates', 'Plastic Bottles', 'Plastic Containers', 'Plastic Pipes & Fittings', 'Plastic Rods', 'Plastic Granules/Pellets', 'Plastic Compounds', 'Masterbatches', 'Plastic Caps & Closures', 'Plastic Crates', 'Plastic Bags', 'Plastic Packaging Materials', 'Geomembranes', 'Thermoformed Products', 'Injection Molded Components', 'Blow Molded Products', 'Extruded Profiles', 'Engineering Plastics'],
    'rubber-and-rubber-products' => ['Natural Rubber', 'Synthetic Rubber', 'Rubber Sheets', 'Rubber Hoses', 'Rubber Tubes', 'Rubber Gaskets', 'O-rings', 'Rubber Seals', 'Rubber Belts', 'Rubber Mats', 'Rubber Footwear Components', 'Rubber Rollers', 'Rubber Cables', 'Latex Products', 'Automotive Rubber Parts', 'Rubber Compounds'],
    'rubber-rubberic-products' => ['Natural Rubber', 'Synthetic Rubber', 'Rubber Sheets', 'Rubber Hoses', 'Rubber Tubes', 'Rubber Gaskets', 'O-rings', 'Rubber Seals', 'Rubber Belts', 'Rubber Mats', 'Rubber Footwear Components', 'Rubber Rollers', 'Rubber Cables', 'Latex Products', 'Automotive Rubber Parts', 'Rubber Compounds'],
    'rubber-rubberic-products-testing' => ['Natural Rubber', 'Synthetic Rubber', 'Rubber Sheets', 'Rubber Hoses', 'Rubber Tubes', 'Rubber Gaskets', 'O-rings', 'Rubber Seals', 'Rubber Belts', 'Rubber Mats', 'Rubber Footwear Components', 'Rubber Rollers', 'Rubber Cables', 'Latex Products', 'Automotive Rubber Parts', 'Rubber Compounds'],
    'textiles-and-textile-products' => ['Woven Fabrics', 'Nonwoven Fabrics', 'Knitted Fabrics', 'Technical Textiles', 'Garments', 'Fibers', 'Yarns', 'Threads', 'Ropes', 'Geotextiles', 'Medical Textiles', 'Packaging Fabrics', 'Upholstery Fabrics', 'Protective Clothing', 'Industrial Fabrics'],
    'textile-textile-products' => ['Woven Fabrics', 'Nonwoven Fabrics', 'Knitted Fabrics', 'Technical Textiles', 'Garments', 'Fibers', 'Yarns', 'Threads', 'Ropes', 'Geotextiles', 'Medical Textiles', 'Packaging Fabrics', 'Upholstery Fabrics', 'Protective Clothing', 'Industrial Fabrics'],
    'textile-textile-products-testing' => ['Woven Fabrics', 'Nonwoven Fabrics', 'Knitted Fabrics', 'Technical Textiles', 'Garments', 'Fibers', 'Yarns', 'Threads', 'Ropes', 'Geotextiles', 'Medical Textiles', 'Packaging Fabrics', 'Upholstery Fabrics', 'Protective Clothing', 'Industrial Fabrics'],
    'leather-and-leather-products' => ['Finished Leather', 'Synthetic Leather', 'PU Leather', 'PVC Leather', 'Footwear', 'Handbags', 'Wallets', 'Belts', 'Gloves', 'Jackets', 'Upholstery Leather', 'Leather Accessories', 'Safety Shoes'],
    'leather-leatherite-products' => ['Finished Leather', 'Synthetic Leather', 'PU Leather', 'PVC Leather', 'Footwear', 'Handbags', 'Wallets', 'Belts', 'Gloves', 'Jackets', 'Upholstery Leather', 'Leather Accessories', 'Safety Shoes'],
    'leather-leatherite-products-testing' => ['Finished Leather', 'Synthetic Leather', 'PU Leather', 'PVC Leather', 'Footwear', 'Handbags', 'Wallets', 'Belts', 'Gloves', 'Jackets', 'Upholstery Leather', 'Leather Accessories', 'Safety Shoes'],
    'toys-and-sports-goods' => ['Plastic Toys', 'Wooden Toys', 'Soft Toys', 'Educational Toys', "Children's Articles", 'Sports Balls', 'Cricket Equipment', 'Badminton Equipment', 'Tennis Equipment', 'Football Equipment', 'Gym Equipment', 'Helmets', 'Protective Sports Gear', 'Fitness Accessories'],
    'toys-similar-products' => ['Plastic Toys', 'Wooden Toys', 'Soft Toys', 'Educational Toys', "Children's Articles", 'Sports Balls', 'Cricket Equipment', 'Badminton Equipment', 'Tennis Equipment', 'Football Equipment', 'Gym Equipment', 'Helmets', 'Protective Sports Gear', 'Fitness Accessories'],
    'toys-similar-products-testing' => ['Plastic Toys', 'Wooden Toys', 'Soft Toys', 'Educational Toys', "Children's Articles", 'Sports Balls', 'Cricket Equipment', 'Badminton Equipment', 'Tennis Equipment', 'Football Equipment', 'Gym Equipment', 'Helmets', 'Protective Sports Gear', 'Fitness Accessories'],
    'recyclate-and-recycled-products-food-grade' => ['Food Grade rPET Flakes', 'rPET Pellets', 'rHDPE', 'rPP', 'Recycled PE', 'Recycled Films', 'Recycled Bottles', 'Recycled Packaging Materials', 'Recycled Plastic Sheets', 'Food Contact Recycled Plastics', 'PCR (Post-Consumer Recycled) Materials'],
    'pharmaceuticals-products' => ['Tablets', 'Capsules', 'Syrups', 'Injectables', 'Ointments', 'Creams', 'Suspensions', 'Powders', 'API (Active Pharmaceutical Ingredients)', 'Excipients', 'Blister Packs', 'Pharmaceutical Packaging', 'IV Fluids', 'Medical Gases'],
    'medical-devices-and-healthcare-products' => ['Syringes', 'Needles', 'Catheters', 'IV Sets', 'Surgical Gloves', 'Surgical Masks', 'PPE Kits', 'Bandages', 'Cotton', 'Gauze', 'Medical Tubing', 'Blood Bags', 'Diagnostic Kits', 'Medical Packaging', 'Implant Packaging', 'Sterilization Pouches'],
    'dyes-paints-pigments-adhesives-and-surface-coatings' => ['Decorative Paints', 'Industrial Paints', 'Powder Coatings', 'Primers', 'Varnishes', 'Enamels', 'Pigments', 'Printing Inks', 'Textile Dyes', 'Solvent-based Adhesives', 'Water-based Adhesives', 'Hot Melt Adhesives', 'Epoxy Adhesives', 'PU Adhesives', 'Surface Coatings', 'Protective Coatings'],
    'food-and-agricultural-products' => ['Cereals & Grains', 'Pulses', 'Flour', 'Rice', 'Wheat', 'Edible Oils', 'Milk & Dairy Products', 'Meat & Poultry', 'Fish & Seafood', 'Fruits', 'Vegetables', 'Spices', 'Tea', 'Coffee', 'Sugar', 'Honey', 'Bakery Products', 'Confectionery', 'Snacks', 'Ready-to-Eat Foods', 'Beverages', 'Packaged Foods', 'Animal Feed', 'Fertilizers', 'Seeds', 'Pesticides', 'Organic Foods'],
    'food-agricultural-products' => ['Cereals & Grains', 'Pulses', 'Flour', 'Rice', 'Wheat', 'Edible Oils', 'Milk & Dairy Products', 'Meat & Poultry', 'Fish & Seafood', 'Fruits', 'Vegetables', 'Spices', 'Tea', 'Coffee', 'Sugar', 'Honey', 'Bakery Products', 'Confectionery', 'Snacks', 'Ready-to-Eat Foods', 'Beverages', 'Packaged Foods', 'Animal Feed', 'Fertilizers', 'Seeds', 'Pesticides', 'Organic Foods'],
    'food-agricultural-products-testing' => ['Cereals & Grains', 'Pulses', 'Flour', 'Rice', 'Wheat', 'Edible Oils', 'Milk & Dairy Products', 'Meat & Poultry', 'Fish & Seafood', 'Fruits', 'Vegetables', 'Spices', 'Tea', 'Coffee', 'Sugar', 'Honey', 'Bakery Products', 'Confectionery', 'Snacks', 'Ready-to-Eat Foods', 'Beverages', 'Packaged Foods', 'Animal Feed', 'Fertilizers', 'Seeds', 'Pesticides', 'Organic Foods'],
    'water-and-packaged-drinking-water' => ['Raw Water', 'Ground Water', 'Surface Water', 'Drinking Water', 'Packaged Drinking Water', 'Mineral Water', 'Purified Water', 'RO Water', 'Process Water', 'Boiler Water', 'Cooling Water', 'Waste Water', 'Industrial Water', 'Bottled Water'],
    'electrical-and-electronics' => ['Electric Wires', 'Cables', 'Switches', 'Sockets', 'Plugs', 'MCBs', 'RCCBs', 'Distribution Boards', 'Transformers', 'LED Lamps', 'Electrical Insulation Materials', 'PVC Insulated Cables', 'XLPE Cables', 'Flexible Cables', 'Batteries', 'Electronic Components', 'Printed Circuit Boards (PCB)', 'Chargers', 'Power Supplies', 'Consumer Electronics', 'Electrical Appliances']
];

$catSlug = isset($_GET['category']) ? trim($_GET['category']) : (isset($_GET['discipline']) ? trim($_GET['discipline']) : null);

// Define the 4 official testing disciplines
$officialDisciplines = [
    'Chemical'   => ['id' => 1, 'slug' => 'chemical'],
    'Mechanical' => ['id' => 2, 'slug' => 'mechanical'],
    'Biological' => ['id' => 4, 'slug' => 'biological'],
    'Electrical' => ['id' => 5, 'slug' => 'electrical']
];

// Comprehensive standard test library for all 4 disciplines
$defaultDisciplineTests = [
    'Chemical' => [
        ['name' => 'Ash Content', 'description' => 'Ash content determines the total inorganic residue remaining after complete combustion of the sample at a specified temperature. The test indicates the amount of mineral fillers and inorganic impurities present in the material.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Sulphated Ash Content', 'description' => 'Sulphated ash is determined by treating the sample with sulphuric acid followed by ignition to convert metallic constituents into stable sulphates. The test evaluates inorganic residue and additives present.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Carbon Black Content', 'description' => 'This test measures the percentage of carbon black incorporated into polymer materials by thermal decomposition under controlled conditions for UV protection and mechanical reinforcement.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Carbon Black Dispersion', 'description' => 'Carbon black dispersion evaluates the uniformity of carbon black distribution within polymer products using microscopic examination.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'VCM (Vinyl Chloride Monomer) Content', 'description' => 'Determines residual vinyl chloride monomer present in PVC materials using gas chromatographic techniques for food contact and safety compliance.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Overall Migration', 'description' => 'Overall migration determines the total quantity of non-volatile substances transferred from food contact materials into food simulants under specified conditions.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Water Absorption', 'description' => 'Water absorption determines the amount of water absorbed by a material after immersion under specified conditions, evaluating dimensional stability and performance in wet environments.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Heavy Metal Extraction', 'description' => 'Heavy metal extraction measures the migration of toxic metals from materials into specified extraction media to ensure compliance with safety standards.', 'standards' => 'ISO / ASTM / IS']
    ],
    'Mechanical' => [
        ['name' => 'Tensile Strength', 'description' => 'Tensile strength determines the maximum stress a material can withstand before failure under a uniaxial tensile load. The test evaluates mechanical performance and structural integrity.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Elongation at Break', 'description' => 'Elongation at break measures the percentage increase in specimen length before rupture during tensile testing, indicating ductility and flexibility.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Yield Strength', 'description' => 'Yield strength is the stress at which a material begins to deform permanently under tensile loading, identifying the elastic limit before plastic deformation occurs.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Tensile Modulus', 'description' => 'Tensile modulus measures stiffness by determining the ratio of stress to strain within the elastic region, indicating resistance to elastic deformation under load.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Impact Test', 'description' => 'Impact testing determines the ability of a material to absorb energy under sudden loading without fracturing, evaluating toughness and shock resistance.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Shore A / Shore D Hardness', 'description' => 'Shore hardness testing measures the resistance of plastics and elastomers to indentation using a standardized durometer.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Flexural Strength', 'description' => 'Flexural testing determines the behaviour of a material under bending loads by measuring flexural strength, modulus, and strain.', 'standards' => 'ISO / ASTM / IS'],
        ['name' => 'Tear Strength', 'description' => 'Tear strength measures the force required to propagate an existing tear in flexible materials during handling, processing, and service.', 'standards' => 'ISO / ASTM / IS']
    ],
    'Biological' => [
        ['name' => 'Aerobic Plate Count (APC) / Total Plate Count (TPC)', 'description' => 'Determines the total number of viable aerobic microorganisms present in samples, evaluating overall microbiological quality, hygiene, and regulatory compliance.', 'standards' => 'ISO / IS / WHO'],
        ['name' => 'Yeast & Mold Count (YMC)', 'description' => 'Determines the number of viable yeasts and molds present in the sample, assessing microbial spoilage and product stability.', 'standards' => 'ISO / IS / WHO'],
        ['name' => 'Escherichia coli (E. coli) Count', 'description' => 'Detects and enumerates Escherichia coli as an indicator of fecal contamination to verify microbiological safety.', 'standards' => 'ISO / IS / WHO'],
        ['name' => 'Total Coliform Count', 'description' => 'Detects and enumerates coliform bacteria as indicators of sanitary quality and hygienic processing.', 'standards' => 'ISO / IS / WHO']
    ],
    'Electrical' => [
        ['name' => 'Fine Mist Test', 'description' => 'Evaluates resistance to exposure from fine water droplets or mist. The test verifies protection against moisture penetration and corrosion.', 'standards' => 'IEC / ISO / IS'],
        ['name' => 'Immersion Test', 'description' => 'Determines the ability of products to resist water ingress during immersion for specified durations and depths. The test verifies sealing effectiveness.', 'standards' => 'IEC / ISO / IS'],
        ['name' => 'Low Temperature Test', 'description' => 'Evaluates product performance after exposure to low-temperature conditions, assessing functionality, brittleness, and dimensional stability.', 'standards' => 'IEC / ISO / IS'],
        ['name' => 'IK Impact Test (IK01–IK11)', 'description' => 'Determines the resistance of electrical enclosures and equipment to external mechanical impacts per the specified IK rating.', 'standards' => 'IEC / ISO / IS'],
        ['name' => 'Ingress Protection (IP) Test', 'description' => 'Verifies the degree of protection provided by enclosures against solid particles and water ingress according to international standards.', 'standards' => 'IEC / ISO / IS']
    ]
];

// Helper to categorize any test into one of the 4 official disciplines
function resolveDisciplineName($test) {
    $dId = (int)($test['discipline_id'] ?? 0);
    $dName = (string)($test['discipline_name'] ?? '');
    $dSlug = (string)($test['discipline_slug'] ?? '');
    $tName = (string)($test['name'] ?? '');

    if ($dId === 1 || stripos($dName, 'chem') !== false || stripos($dSlug, 'chem') !== false) return 'Chemical';
    if ($dId === 2 || stripos($dName, 'mech') !== false || stripos($dSlug, 'mech') !== false) return 'Mechanical';
    if ($dId === 4 || stripos($dName, 'bio') !== false || stripos($dSlug, 'bio') !== false) return 'Biological';
    if ($dId === 5 || stripos($dName, 'elec') !== false || stripos($dSlug, 'elec') !== false) return 'Electrical';
    return null;
}

// 1. Fetch tests mapped specifically to this product
$mappedByDiscipline = [];
try {
    $mappedStmt = $conn->prepare("
        SELECT t.*, COALESCE(d.name, '') as discipline_name, COALESCE(d.slug, '') as discipline_slug
        FROM tests t
        JOIN product_test pt ON t.id = pt.test_id
        LEFT JOIN disciplines d ON t.discipline_id = d.id
        WHERE pt.product_id = ?
        ORDER BY t.id ASC
    ");
    $mappedStmt->execute([$product['id']]);
    $mappedTests = $mappedStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($mappedTests as $t) {
        $cat = resolveDisciplineName($t);
        if ($cat) {
            $mappedByDiscipline[$cat][] = $t;
        }
    }
} catch (Throwable $e) {}

// 2. Fetch all tests by discipline from database
$generalByDiscipline = [];
try {
    $allTestsStmt = $conn->query("
        SELECT t.*, COALESCE(d.name, '') as discipline_name, COALESCE(d.slug, '') as discipline_slug
        FROM tests t
        LEFT JOIN disciplines d ON t.discipline_id = d.id
        ORDER BY t.id ASC
    ");
    $allTests = $allTestsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allTests as $t) {
        $cat = resolveDisciplineName($t);
        if ($cat) {
            $generalByDiscipline[$cat][] = $t;
        }
    }
} catch (Throwable $e) {}

// 3. Build testsByDiscipline ensuring all 4 categories ALWAYS have complete tests
$testsByDiscipline = [];
foreach ($officialDisciplines as $dName => $dMeta) {
    $tests = [];
    if (!empty($mappedByDiscipline[$dName])) {
        $tests = $mappedByDiscipline[$dName];
    } elseif (!empty($generalByDiscipline[$dName])) {
        $tests = $generalByDiscipline[$dName];
    }

    // Ensure all 4 categories ALWAYS have rich, complete test cards populated
    if (empty($tests) || count($tests) < 3) {
        $existingNames = array_map(function($t) { return strtolower($t['name'] ?? ''); }, $tests);
        foreach (($defaultDisciplineTests[$dName] ?? []) as $dt) {
            if (!in_array(strtolower($dt['name']), $existingNames)) {
                $tests[] = $dt;
            }
        }
    }
    $testsByDiscipline[$dName] = $tests;
}

// Determine which discipline tab should be active
$activeDisciplineName = null;
if ($catSlug) {
    foreach ($officialDisciplines as $dName => $dMeta) {
        if (
            strcasecmp($catSlug, $dName) === 0 ||
            strcasecmp($catSlug, $dMeta['slug']) === 0 ||
            stripos($catSlug, $dMeta['slug']) !== false ||
            stripos($catSlug, $dName) !== false
        ) {
            $activeDisciplineName = $dName;
            break;
        }
    }
}
if (!$activeDisciplineName) {
    $activeDisciplineName = 'Chemical';
}

// Fetch official 12 Products for the sidebar navigation
$official12Slugs = [
    'polymer-and-polymeric-products',
    'rubber-and-rubber-products',
    'textiles-and-textile-products',
    'leather-and-leather-products',
    'toys-and-sports-goods',
    'recyclate-and-recycled-products-food-grade',
    'pharmaceuticals-products',
    'medical-devices-and-healthcare-products',
    'dyes-paints-pigments-adhesives-and-surface-coatings',
    'food-and-agricultural-products',
    'water-and-packaged-drinking-water',
    'electrical-and-electronics'
];

$inClause = implode(',', array_fill(0, count($official12Slugs), '?'));
$allProductsStmt = $conn->prepare("SELECT id, name, slug FROM products WHERE slug IN ($inClause)");
$allProductsStmt->execute($official12Slugs);
$fetchedProducts = $allProductsStmt->fetchAll(PDO::FETCH_ASSOC);

$prodBySlug = [];
foreach ($fetchedProducts as $fp) {
    $prodBySlug[$fp['slug']] = $fp;
}

$allProductsList = [];
foreach ($official12Slugs as $os) {
    if (isset($prodBySlug[$os])) {
        $allProductsList[] = $prodBySlug[$os];
    }
}

$allCategoriesStmt = $conn->query("SELECT id, name, slug FROM disciplines ORDER BY id ASC");
$allCategoriesList = $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$discSlug = isset($catSlug) ? $catSlug : null;

$sidebarData = [];
foreach ($allProductsList as $p) {
    $stmt = $conn->prepare("
        SELECT d.id, d.name, d.slug 
        FROM disciplines d
        JOIN product_discipline pd ON d.id = pd.discipline_id
        WHERE pd.product_id = ?
        ORDER BY d.id ASC
    ");
    $stmt->execute([$p['id']]);
    $discList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($discList)) {
        $discList = $allCategoriesList;
    }
    // Keep ONLY the 4 official categories: Chemical, Mechanical, Biological, Electrical
    $allowedCategories = ['chemical', 'mechanical', 'biological', 'electrical'];
    $filteredDiscList = [];
    foreach ($allowedCategories as $ac) {
        foreach ($discList as $d) {
            $dLower = strtolower($d['slug'] ?? $d['name'] ?? '');
            if (str_contains($dLower, $ac)) {
                $filteredDiscList[] = $d;
                break;
            }
        }
    }
    // If not all 4 were found, add standard 4
    if (count($filteredDiscList) < 4) {
        $filteredDiscList = [
            ['id' => 1, 'name' => 'Chemical', 'slug' => 'chemical'],
            ['id' => 2, 'name' => 'Mechanical', 'slug' => 'mechanical'],
            ['id' => 4, 'name' => 'Biological', 'slug' => 'biological'],
            ['id' => 5, 'name' => 'Electrical', 'slug' => 'electrical']
        ];
    }
    $sidebarData[] = [
        'product' => $p,
        'disciplines' => $filteredDiscList
    ];
}


if ($slug === 'polymer-polymeric-products' || $slug === 'polymer-polymeric-products-testing') {
    $pageTitle = "Polymer & Polymeric Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for polymers and polymeric products.";
    $metaKeywords = "polymer testing lab India, NABL polymer testing, polymer compliance testing, polymer chemical testing, polymer mechanical testing India";
} elseif ($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing') {
    $pageTitle = "Textile & Textile Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for woven and non-woven textiles.";
    $metaKeywords = "textile testing lab India, NABL textile testing, textile compliance testing, textile chemical testing, textile mechanical testing India";
} elseif ($slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing') {
    $pageTitle = "Leather & Leatherite Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for leather and leatherite products.";
    $metaKeywords = "leather testing lab India, NABL leather testing, leather compliance testing, leather chemical testing, leather mechanical testing India";
} elseif ($slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing') {
    $pageTitle = "Rubber & Rubberic Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for rubber compounds and rubber products.";
    $metaKeywords = "rubber testing lab India, NABL rubber testing, rubber compliance testing, rubber chemical testing, rubber mechanical testing India";
} elseif ($slug === 'toys-similar-products' || $slug === 'toys-similar-products-testing') {
    $pageTitle = "Toys & Similar Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for toys and similar consumer products.";
    $metaKeywords = "toy testing lab India, NABL toy testing, EN71 testing India, toy compliance testing, toy chemical testing, toy mechanical testing India";
} elseif ($slug === 'packaging-specific-products' || $slug === 'packaging-specific-products-testing') {
    $pageTitle = "Packaging Specific Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for food-grade and industrial packaging materials.";
    $metaKeywords = "packaging testing lab India, NABL packaging testing, food contact material testing, packaging compliance testing, migration testing India, seal strength testing, packaging mechanical testing";
} elseif ($slug === 'animal-food-feed' || $slug === 'animal-food-feed-testing') {
    $pageTitle = "Animal Food & Feed Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for animal food and feed materials and packaging.";
    $metaKeywords = "animal feed testing lab India, NABL animal feed testing, feed contaminant testing, heavy metal feed testing, feed packaging testing, animal food compliance testing India";
} elseif ($slug === 'food-agricultural-products' || $slug === 'food-agricultural-products-testing') {
    $pageTitle = "Food & Agricultural Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for food and agricultural products and their packaging.";
    $metaKeywords = "food testing lab India, agricultural product testing, NABL food testing, food contact compliance India, food safety testing, FSSAI testing lab, food packaging testing India";
} elseif ($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food') {
    $pageTitle = "Residue & Contaminants in Food Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing for residues and contaminants in food - covering pesticide residues, heavy metals, mycotoxins, adulterants, and chemical contaminants per FSSAI, CODEX, EU, and international regulatory standards.";
    $metaKeywords = "food contaminant testing India, pesticide residue testing, NABL food residue testing, mycotoxin testing India, heavy metal food testing, FSSAI contaminant testing, food adulteration testing India";
} elseif ($slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing') {
    $pageTitle = "Atmospheric Pollution Testing | Multi-Discipline Environmental Analysis - AKC";
    $metaDescription = "AKC provides NABL-accredited atmospheric pollution testing - covering ambient air quality, stack emissions, VOCs, particulate matter, heavy metals in air, and industrial pollutant monitoring per CPCB, MoEFCC, and international environmental standards.";
    $metaKeywords = "atmospheric pollution testing India, ambient air quality testing, stack emission testing NABL, VOC air testing India, particulate matter testing, CPCB compliance testing, industrial emission monitoring India";
} elseif ($slug === 'polymer-polymeric-products' || $slug === 'polymer-polymeric-products-testing') {
    $pageTitle = "Polymer & Polymeric Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for polymers and polymeric products.";
    $metaKeywords = "polymer testing lab India, NABL polymer testing, polymer compliance testing, polymer chemical testing, polymer mechanical testing India";
} elseif ($slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing') {
    $pageTitle = "Textile & Textile Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for woven and non-woven textiles.";
    $metaKeywords = "textile testing lab India, NABL textile testing, textile compliance testing, textile chemical testing, textile mechanical testing India";
} elseif ($slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing') {
    $pageTitle = "Leather & Leatherite Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for leather and leatherite products.";
    $metaKeywords = "leather testing lab India, NABL leather testing, leather compliance testing, leather chemical testing, leather mechanical testing India";
} elseif ($slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing') {
    $pageTitle = "Rubber & Rubberic Products Testing | Multi-Discipline Compliance Testing - AKC";
    $metaDescription = "AKC provides NABL-accredited testing across chemical, mechanical, biological, and electrical disciplines for rubber compounds and rubber products.";
    $metaKeywords = "rubber testing lab India, NABL rubber testing, rubber compliance testing, rubber chemical testing, rubber mechanical testing India";
} else {
    $pageTitle = $prodName . " Testing | Chemical Analysis & Compliance - AKC";
    $metaDescription = "AKC offers NABL-accredited testing for " . $prodName;
    $metaKeywords = "testing lab India, NABL testing, " . $prodName;
}
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/style_hemachandra.css">
<link rel="stylesheet" href="assets/css/qc-analysis.css">

<!-- Bootstrap Icons CDN link -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Google Fonts CDN links for modern typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">

<!-- ==============================
     HERO / BANNER SECTION
============================== -->
<section class="pr-hero">
    <div class="container pr-hero-inner">
        <!-- Left: Content -->
        <div class="pr-hero-content">
            <!-- Breadcrumb -->
            <nav class="pr-breadcrumb">
                <a href="index.php">Home</a>
                <span class="pr-bc-sep">></span>
                <a href="#">Products</a>
                <span class="pr-bc-sep">></span>
                <span class="pr-bc-current"><?php echo $prodName; ?></span>
            </nav>
            <div class="pr-hero-label">- Product View</div>
            <h1 class="pr-hero-title"><strong><?php echo str_replace(' Testing', '', $prodName); ?></strong> <span
                    class="pr-blue">Testing</span></h1>
            <p class="pr-hero-desc"><?php echo $prodDesc; ?></p>
            <div class="pr-hero-buttons">
                <a href="javascript:void(0);"
                    onclick="openBookTestPopup('<?php echo htmlspecialchars(addslashes($activeDisciplineName ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($prodName)); ?>', '', '');"
                    class="pr-btn pr-btn-white"><i class="bi bi-clipboard-check"></i> Request Testing</a>
                <a href="contact-us.php" class="pr-btn pr-btn-outline-white">Contact Experts</a>
            </div>
        </div>

        <!-- Right: Contact Card -->
        <div class="pr-hero-contact-card">
            <div class="pr-section-label">
                <span class="pr-label-line"></span> Contact Us
            </div>
            <h2 class="pr-contact-phone"><a href="tel:+918860407220" style="color: inherit; text-decoration: none;">+91
                    88604 07220</a></h2>
            <hr class="pr-card-divider">
            <div class="pr-contact-links">
                <a href="mailto:info@akc-ptarc.com" class="pr-contact-email" style="text-decoration: none;">
                    <i class="bi bi-envelope"></i> Email Us: info@akc-ptarc.com
                </a>
            </div>
            <a href="mailto:info@akc-ptarc.com?subject=Inquiry from <?php echo htmlspecialchars(addslashes($prodName ?? 'Product Page')); ?>"
                class="pr-btn pr-btn-primary pr-btn-full">Send us a request</a>
        </div>
    </div>
</section>

<!-- ==============================
     OVERVIEW + SIDEBAR SECTION
============================== -->
<section class="pr-overview-section">
    <div class="container pr-overview-inner">

        <!-- Left: Overview Content -->
        <div class="pr-overview-content">
            <div class="pr-section-label">- Overview</div>
            <h2 class="pr-section-title"><?php echo $prodName; ?> <br><span class="pr-blue-italic">We Deal In</span>
            </h2>

            <?php
            $currentProductItems = isset($productItemsMap[$slug]) ? $productItemsMap[$slug] : null;
            if (!$currentProductItems) {
                foreach ($productItemsMap as $mapKey => $mapItems) {
                    if (str_contains($slug, $mapKey) || str_contains($mapKey, $slug)) {
                        $currentProductItems = $mapItems;
                        break;
                    }
                }
            }
            if ($currentProductItems):
                ?>
                <p><?php echo implode(', ', $currentProductItems) . '.'; ?></p>
            <?php else: ?>
                <p><?php echo $prodDesc; ?></p>
            <?php endif; ?>



            <?php
            // Standards of representative products
            $representativeStandardsMap = [
                'polymer-and-polymeric-products' => [
                    '3D Printing Filaments',
                    'Blow Moulded Products',
                    'Bulk Containers',
                    'Composite Products',
                    'Engineering Plastics',
                    'Extruded Profiles',
                    'FIBC',
                    'Foam Products',
                    'Geosynthetics',
                    'Injection Moulded Products',
                    'Masterbatches',
                    'Plastic Bags',
                    'Plastic Bottles',
                    'Plastic Caps & Closures',
                    'Plastic Compounds',
                    'Plastic Containers',
                    'Plastic Crates',
                    'Plastic Films',
                    'Plastic Furniture',
                    'Plastic Housewares',
                    'Plastic Laminates',
                    'Plastic Pallets',
                    'Plastic Pipes & Fittings',
                    'Plastic Raw Material',
                    'Plastic Rods',
                    'Plastic Sheets',
                    'Plastic Tanks',
                    'Rotomoulded Products',
                    'Thermoformed Products',
                    'Woven Sacks'
                ],
                'rubber-and-rubber-products' => [
                    'Anti-vibration Mounts',
                    'Automotive Components',
                    'Bridge Bearings',
                    'Bushes',
                    'Conveyor Belts',
                    'Ebonite Products',
                    'Expansion Joints',
                    'FKM Products',
                    'Footwear',
                    'Gaskets',
                    'Inflatable Products',
                    'Latex Products',
                    'Other',
                    'Power Transmission Belts',
                    'Raw Rubber',
                    'Reclaimed Rubber',
                    'Rubber Cables',
                    'Rubber Cord',
                    'Rubber Flooring',
                    'Rubber Footwear Components',
                    'Rubber Gloves',
                    'Rubber Hose',
                    'Rubber Linings',
                    'Rubber Mats',
                    'Rubber Profiles',
                    'Rubber Rollers',
                    'Rubber Sheets',
                    'Rubber-Coated Fabric',
                    'Seals & O-Rings',
                    'Silicone Products',
                    'Sponge Rubber',
                    'Tubes',
                    'Tyres'
                ],
                'leather-and-leather-products' => [
                    'Automotive Leather',
                    'Fashion Accessories',
                    'Finished Leather',
                    'Footwear Leather',
                    'Industrial Leather',
                    'Leather Garments',
                    'Leather Goods',
                    'Protective PPE',
                    'Raw Leather',
                    'Saddlery',
                    'Safety Shoes',
                    'Sports Goods',
                    'Sustainable Leather',
                    'Synthetic Leather',
                    'Upholstery Leather'
                ],
                'medical-devices-and-healthcare-products' => [
                    'Bandages',
                    'Blood Bags',
                    'Catheters',
                    'Cotton',
                    'Diagnostic Kits',
                    'Gauze',
                    'Implant Packaging',
                    'IV Sets',
                    'Medical Packaging',
                    'Medical Tubing',
                    'Needles',
                    'PPE Kits',
                    'Sterilization Pouches',
                    'Surgical Gloves',
                    'Surgical Masks',
                    'Syringes'
                ],
                'dyes-paints-pigments-adhesives-and-surface-coatings' => [
                    'Decorative Paints',
                    'Enamels',
                    'Epoxy Adhesives',
                    'Hot Melt Adhesives',
                    'Industrial Paints',
                    'Pigments',
                    'Powder Coatings',
                    'Primers',
                    'Printing Inks',
                    'Protective Coatings',
                    'PU Adhesives',
                    'Solvent-Based Adhesives',
                    'Surface Coatings',
                    'Textile Dyes',
                    'Varnishes',
                    'Water-Based Adhesives'
                ],
                'toys-and-sports-goods' => [
                    'Arts & Crafts',
                    'Badminton Equipment',
                    'Children_s Articles',
                    'Cricket Equipment',
                    'Educational Toys',
                    'Electronic Toys',
                    'Fitness Accessories',
                    'Football Equipment',
                    'Gym Equipment',
                    'Helmets',
                    'Outdoor Toys',
                    'Plastic Toys',
                    'Protective Sports Gear',
                    'Ride-on Toys',
                    'Seasonal Toys',
                    'Soft Toys',
                    'Sports Balls',
                    'Sports Toys',
                    'Tennis Equipment',
                    'Wooden Toys'
                ],
                'textiles-and-textile-products' => [
                    'Agrotextiles',
                    'Buildtech',
                    'Clothtech',
                    'Fibres',
                    'Garments',
                    'Geotextiles',
                    'Home Textiles',
                    'Hometech',
                    'Industrial Fabrics',
                    'Knitted Fabric',
                    'Medical Textiles',
                    'Mobiltech',
                    'Nonwoven',
                    'Oekotech',
                    'Packaging Textiles',
                    'Protective Clothing',
                    'Ropes',
                    'Smart Textiles',
                    'Sportech',
                    'Textile Composites',
                    'Threads',
                    'Woven Fabric',
                    'Yarn'
                ],
                'recyclate-and-recycled-products-food-grade' => [
                    'Crumb Rubber',
                    'Food Contact Recycled PET',
                    'PCR Plastics',
                    'Reclaimed Rubber',
                    'Recovered Materials',
                    'Recycled Aluminium',
                    'Recycled Bottles',
                    'Recycled Construction Materials',
                    'Recycled Cotton Fibre',
                    'Recycled Glass',
                    'Recycled Paper',
                    'Recycled Paperboard',
                    'Recycled Plastic Containers',
                    'Recycled Plastic Granules',
                    'Recycled Plastic Packaging',
                    'Recycled Plastic Products',
                    'Recycled Plastic Sheets',
                    'Recycled Polyester Fibre',
                    'Recycled Steel',
                    'Recycled Wood Products',
                    'rPET Flakes'
                ]
            ];

            $currentRepStandards = isset($representativeStandardsMap[$slug]) ? $representativeStandardsMap[$slug] : [];

            if (!empty($currentRepStandards)):
                ?>
                <div class="pr-representative-standards">
                    <div class="pr-rep-header">
                        <h3>Standards of Representative Products</h3>
                        <p>Click on the products below to view details of their respective standards.</p>
                    </div>
                    <div class="pr-rep-grid">
                        <?php
                        $slugToCategoryName = [
                            'polymer-and-polymeric-products' => 'Polymer & Polymeric Products',
                            'rubber-and-rubber-products' => 'Rubber and Rubber products',
                            'leather-and-leather-products' => 'Leather and Leather products',
                            'medical-devices-and-healthcare-products' => 'Medical devices',
                            'dyes-paints-pigments-adhesives-and-surface-coatings' => 'Dyes and Pigments',
                            'toys-and-sports-goods' => 'Toys and Sports',
                            'textiles-and-textile-products' => 'Textile and Textile Products',
                            'recyclate-and-recycled-products-food-grade' => 'Recycle and Recyclate products'
                        ];
                        $productCategoryName = isset($slugToCategoryName[$slug]) ? $slugToCategoryName[$slug] : 'Standards';

                        foreach ($currentRepStandards as $repProduct):
                            $baseName = $productCategoryName . ' - ' . $repProduct;
                            $pdfPath = 'assets/standards/' . rawurlencode($productCategoryName) . '/' . rawurlencode($baseName . '.pdf');
                            $docxPath = 'assets/standards/' . rawurlencode($productCategoryName) . '/' . rawurlencode($baseName . '.docx');

                            $finalPath = $pdfPath;
                            $isDocx = false;
                            if (
                                !file_exists(__DIR__ . '/assets/standards/' . $productCategoryName . '/' . $baseName . '.pdf') &&
                                file_exists(__DIR__ . '/assets/standards/' . $productCategoryName . '/' . $baseName . '.docx')
                            ) {
                                $finalPath = $docxPath;
                                $isDocx = true;
                            }

                            $href = htmlspecialchars($finalPath);
                            if ($isDocx) {
                                $host = $_SERVER['HTTP_HOST'];
                                // Use live vercel URL for localhost so Google Docs Viewer can still access it during local testing
                                if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                                    $publicUrl = "https://akc-beta.vercel.app/" . $finalPath;
                                } else {
                                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                                    $publicUrl = $protocol . "://" . $host . "/" . $finalPath;
                                }
                                $href = "https://docs.google.com/viewer?url=" . urlencode($publicUrl);
                            }
                            ?>
                            <a href="<?php echo $href; ?>" target="_blank" class="pr-rep-item">
                                <?php if (!$isDocx): ?>
                                    <i class="bi bi-file-earmark-pdf" style="color: #ff0000;"></i>
                                <?php else: ?>
                                    <i class="bi bi-file-earmark-word" style="color: #0ea5e9;"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($repProduct); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <style>
                    .pr-representative-standards {
                        margin-top: 40px;
                        background: #ffffff;
                        border-radius: 12px;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                        overflow: hidden;
                    }

                    .pr-rep-header {
                        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                        color: #ffffff;
                        padding: 24px 30px;
                        text-align: center;
                    }

                    .pr-rep-header h3 {
                        margin: 0 0 8px 0;
                        font-size: 22px;
                        font-weight: 700;
                        letter-spacing: -0.02em;
                    }

                    .pr-rep-header p {
                        margin: 0;
                        font-size: 15px;
                        color: #cbd5e1;
                    }

                    .pr-rep-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                        gap: 16px;
                        padding: 30px;
                        background: #f8fafc;
                    }

                    .pr-rep-item {
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        color: #334155;
                        text-decoration: none;
                        font-size: 14px;
                        font-weight: 600;
                        padding: 12px 16px;
                        background: #ffffff;
                        border: 1px solid #cbd5e1;
                        border-radius: 8px;
                        transition: all 0.2s ease-in-out;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
                    }

                    .pr-rep-item i {
                        font-size: 18px;
                        color: #ef4444;
                        /* PDF icon color */
                    }

                    .pr-rep-item:hover {
                        background: #f1f5f9;
                        border-color: #94a3b8;
                        color: #0f172a;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
                    }
                </style>
            <?php endif; ?>

        </div>

        <!-- Right: Sticky Sidebar -->
        <style>
            .pr-sidebar {
                background: #ffffff;
                border-radius: 12px;
                border: 1px solid #cbd5e1;
                box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
                overflow: hidden;
            }

            .pr-sidebar-header {
                background: #0f172a;
                color: #ffffff;
                padding: 20px 22px;
            }

            .pr-sidebar-label {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 1px;
                color: #94a3b8;
                margin-bottom: 4px;
                text-transform: uppercase;
            }

            .pr-sidebar-title {
                font-size: 18px;
                font-weight: 700;
                color: #ffffff;
                margin: 0;
            }

            .pr-sidebar-category {
                padding: 13px 20px !important;
                font-size: 15px !important;
                font-weight: 500 !important;
                color: #0f172a !important;
                background: #f8fafc !important;
                border-bottom: 1px solid #e2e8f0 !important;
                transition: all 0.2s ease !important;
            }

            .pr-sidebar-category:hover {
                background: #f1f5f9 !important;
                color: #2563eb !important;
            }

            .pr-sidebar-category.active {
                background: #eff6ff !important;
                color: #1d4ed8 !important;
                border-left: 4px solid #2563eb !important;
            }

            .pr-sidebar-list {
                list-style: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #ffffff !important;
            }

            .pr-sidebar-item {
                padding: 0 !important;
                border-bottom: 1px solid #f1f5f9 !important;
                transition: all 0.2s ease !important;
                background: #ffffff !important;
            }

            .pr-sidebar-item a {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                width: 100% !important;
                padding: 10px 20px 10px 28px !important;
                font-size: 13.5px !important;
                font-weight: 600 !important;
                color: #1e293b !important;
                /* Sharp, high-contrast dark text */
                text-decoration: none !important;
                transition: all 0.2s ease !important;
            }

            .pr-sidebar-item a:hover {
                background: #f8fafc !important;
                color: #2563eb !important;
            }

            .pr-sidebar-item i {
                color: #2563eb !important;
                /* Vivid primary blue icon */
                font-size: 14px !important;
                margin-right: 8px !important;
                width: 18px !important;
                text-align: center !important;
                opacity: 1 !important;
            }

            .pr-sidebar-item.active {
                background: #eff6ff !important;
            }

            .pr-sidebar-item.active a {
                color: #1d4ed8 !important;
                font-weight: 700 !important;
                background: #eff6ff !important;
            }

            .pr-sidebar-item.active i {
                color: #1d4ed8 !important;
            }

            .pr-sidebar-num {
                font-size: 11px !important;
                font-weight: 700 !important;
                color: #475569 !important;
                background: #e2e8f0 !important;
                padding: 2px 7px !important;
                border-radius: 10px !important;
            }

            .pr-sidebar-item.active .pr-sidebar-num {
                background: #bfdbfe !important;
                color: #1e40af !important;
            }
        </style>

        <div class="pr-sidebar">
            <div class="pr-sidebar-header">
                <p class="pr-sidebar-label">BROWSE BY PRODUCT</p>
                <p class="pr-sidebar-title">Products & Categories</p>
            </div>

            <?php
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim(str_replace('\\', '/', $scriptDir), '/');

            foreach ($sidebarData as $item):
                $p = $item['product'];
                $discList = $item['disciplines'];
                $isProductActive = ($p['slug'] === $product['slug']);

                // Assign distinct icon for each of the 12 products
                $ps = $p['slug'];
                if ($ps === 'polymer-and-polymeric-products')
                    $prodIcon = 'fa-solid fa-shapes';
                elseif ($ps === 'rubber-and-rubber-products')
                    $prodIcon = 'fa-solid fa-compact-disc';
                elseif ($ps === 'textiles-and-textile-products')
                    $prodIcon = 'fa-solid fa-shirt';
                elseif ($ps === 'leather-and-leather-products')
                    $prodIcon = 'fa-solid fa-briefcase';
                elseif ($ps === 'toys-and-sports-goods')
                    $prodIcon = 'fa-solid fa-gamepad';
                elseif ($ps === 'recyclate-and-recycled-products-food-grade')
                    $prodIcon = 'fa-solid fa-recycle';
                elseif ($ps === 'pharmaceuticals-products')
                    $prodIcon = 'fa-solid fa-pills';
                elseif ($ps === 'medical-devices-and-healthcare-products')
                    $prodIcon = 'fa-solid fa-stethoscope';
                elseif ($ps === 'dyes-paints-pigments-adhesives-and-surface-coatings')
                    $prodIcon = 'fa-solid fa-spray-can';
                elseif ($ps === 'food-and-agricultural-products')
                    $prodIcon = 'fa-solid fa-apple-whole';
                elseif ($ps === 'water-and-packaged-drinking-water')
                    $prodIcon = 'fa-solid fa-bottle-water';
                elseif ($ps === 'electrical-and-electronics')
                    $prodIcon = 'fa-solid fa-bolt';
                else
                    $prodIcon = 'fa-solid fa-box-open';
                ?>
                <div class="pr-sidebar-category <?php echo $isProductActive ? 'active' : ''; ?>"
                    style="cursor:pointer; display: flex; justify-content: space-between; align-items: center;"
                    onclick="toggleSidebarList(this)">
                    <div style="font-size: 15px; font-weight: 500; color: inherit; text-decoration: none;">
                        <i class="<?php echo $prodIcon; ?>"
                            style="margin-right: 10px; font-size: 15px; width: 20px; display: inline-block; text-align: center; color: #2563eb;"></i>
                        <?php echo htmlspecialchars($p['name']); ?>
                    </div>
                    <span><i class="bi <?php echo $isProductActive ? 'bi-chevron-down' : 'bi-chevron-right'; ?>"
                            style="font-size: 0.9rem;"></i></span>
                </div>
                <ul class="pr-sidebar-list" style="display: <?php echo $isProductActive ? 'block' : 'none'; ?>;">
                    <?php
                    $count = 1;
                    foreach ($discList as $d):
                        $isCatActive = ($catSlug && (strtolower($catSlug) === strtolower($d['slug']) || strtolower($catSlug) === strtolower($d['name'])));
                        $catUrl = 'dynamic-product.php?slug=' . urlencode($p['slug']) . '&category=' . urlencode($d['slug']) . '#available-tests';

                        // Assign distinct icon for each discipline
                        $dName = strtolower($d['name']);
                        if (str_contains($dName, 'chem'))
                            $discIcon = 'fa-solid fa-flask';
                        elseif (str_contains($dName, 'mechan'))
                            $discIcon = 'fa-solid fa-gears';
                        elseif (str_contains($dName, 'therm'))
                            $discIcon = 'fa-solid fa-temperature-high';
                        elseif (str_contains($dName, 'rheol'))
                            $discIcon = 'fa-solid fa-droplet';
                        elseif (str_contains($dName, 'optic'))
                            $discIcon = 'fa-solid fa-eye';
                        elseif (str_contains($dName, 'mater'))
                            $discIcon = 'fa-solid fa-microscope';
                        elseif (str_contains($dName, 'bio'))
                            $discIcon = 'fa-solid fa-virus';
                        elseif (str_contains($dName, 'elec'))
                            $discIcon = 'fa-solid fa-bolt';
                        else
                            $discIcon = 'fa-solid fa-flask';
                        ?>
                        <li class="pr-sidebar-item <?php echo $isCatActive && $isProductActive ? 'active' : ''; ?>">
                            <a href="<?php echo $catUrl; ?>">
                                <span>
                                    <i class="<?php echo $discIcon; ?>"></i>
                                    <?php echo htmlspecialchars($d['name']); ?>
                                </span>
                                <span class="pr-sidebar-num"><?php echo str_pad($count, 2, '0', STR_PAD_LEFT); ?></span>
                            </a>
                        </li>
                        <?php
                        $count++;
                    endforeach; ?>
                </ul>
            <?php endforeach; ?>

            <div class="pr-sidebar-cta-text" style="margin-top: 20px;">Need help choosing a service?</div>
            <a href="contact-us.php" class="pr-btn pr-btn-primary pr-btn-full">Talk to an Expert</a>
        </div>

    </div>
</section>

<!-- ==============================
     AVAILABLE TESTS SECTION
============================== -->
<section id="available-tests" class="pr-tests-section">
    <div class="container">
        <div class="pr-section-label"><span class="pr-label-line"></span>- Laboratory Tests</div>
        <h2 class="pr-section-title">Available <span class="pr-blue-italic">Tests</span></h2>
        <p class="pr-tests-desc" style="max-width: 800px;">Our accredited test portfolio covers varying parameters for
            <?php echo $prodName; ?>.
        </p>

        <div class="pr-discipline-tabs"
            style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
            <?php
            foreach ($testsByDiscipline as $disciplineName => $disciplinetests):
                $tabId = strtolower(str_replace([' ', '&'], ['-', ''], $disciplineName));
                $isTabActive = ($disciplineName === $activeDisciplineName);
                ?>
                <button class="pr-tab-btn <?php echo $isTabActive ? 'active' : ''; ?>"
                    onclick="openDisciplineTab(event, 'tab-<?php echo $tabId; ?>')"
                    style="padding: 10px 22px; border: 1px solid <?php echo $isTabActive ? '#0A58CA' : '#cbd5e1'; ?>; background: <?php echo $isTabActive ? '#0A58CA' : '#f8fafc'; ?>; color: <?php echo $isTabActive ? '#fff' : '#1e293b'; ?>; border-radius: 4px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; font-family: 'DM Sans', sans-serif; font-size: 14px;">
                    <?php echo htmlspecialchars($disciplineName); ?>
                </button>
                <?php
            endforeach;
            ?>
        </div>

        <div class="pr-discipline-content-wrapper">
            <?php
            foreach ($testsByDiscipline as $disciplineName => $disciplinetests):
                $tabId = strtolower(str_replace([' ', '&'], ['-', ''], $disciplineName));
                $isContentActive = ($disciplineName === $activeDisciplineName);
                ?>
                <div id="tab-<?php echo $tabId; ?>" class="pr-tab-content"
                    style="display: <?php echo $isContentActive ? 'block' : 'none'; ?>; animation: fadeEffect 0.5s;">
                    <?php if (!empty($disciplinetests)): ?>
                        <h3
                            style="margin-top: 10px; margin-bottom: 20px; color: #0A58CA; font-weight: 700; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; font-size: 1.5rem;">
                            <?php echo htmlspecialchars($disciplineName); ?> Tests
                        </h3>

                        <div class="pr-tests-grid" style="margin-bottom: 40px;">
                            <?php
                            $count = 1;
                            foreach ($disciplinetests as $test):
                                $numStr = str_pad($count, 2, '0', STR_PAD_LEFT);
                                ?>
                                <div class="pr-test-card">
                                    <div class="pr-test-header">
                                        <span class="pr-test-num">TEST <?php echo $numStr; ?></span>
                                    </div>
                                    <h3 class="pr-test-title"><?php echo htmlspecialchars($test['name']); ?></h3>
                                    <p class="pr-test-desc"><?php echo htmlspecialchars($test['description']); ?></p>
                                    <div class="pr-test-footer">
                                        <span
                                            class="pr-test-standards"><?php echo htmlspecialchars(isset($test['standards']) && !empty($test['standards']) ? $test['standards'] : 'ISO / ASTM'); ?></span>
                                        <a href="javascript:void(0);"
                                            onclick="openBookTestPopup('<?php echo htmlspecialchars(addslashes($disciplineName)); ?>', '<?php echo htmlspecialchars(addslashes($prodName)); ?>', '<?php echo htmlspecialchars(addslashes($test['name'])); ?>', '<?php echo htmlspecialchars(addslashes(isset($test['standards']) && !empty($test['standards']) ? $test['standards'] : 'ISO / ASTM')); ?>')"
                                            class="pr-btn pr-btn-primary pr-btn-sm">Request Test</a>
                                    </div>
                                </div>
                                <?php
                                $count++;
                            endforeach;
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="custom-test-cta"
                        style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 25px; border-radius: 8px; text-align: center; margin-bottom: 40px;">
                        <h4 style="color: #081D33; margin-bottom: 10px; font-weight: 700;">Need a Custom Test?</h4>
                        <p style="color: #64748B; margin-bottom: 15px;">Our scientists can design bespoke
                            <?php echo strtolower($disciplineName); ?> test programmes for specialised material compositions
                            and regulatory requirements.
                        </p>
                        <a href="mailto:info@akc-ptarc.com"
                            style="color: #0A58CA; font-weight: 600; text-decoration: none;">Discuss Requirements &nbsp;<i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <?php
                $firstContent = false;
            endforeach;
            ?>
        </div>

        <?php if (empty($testsByDiscipline)): ?>
            <p>No tests mapped to this product yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ==============================
     PRODUCTS FOR TESTING SECTION
============================== -->
<section class="pr-products-section">
    <div class="container">
        <div class="pr-section-label"><span class="pr-label-line"></span> - Related Categories</div>
        <h2 class="pr-section-title">Products for <span class="pr-blue-italic">Testing</span></h2>
        <p>Explore related chemical testing categories available at AKC:</p>

        <div class="pr-products-grid">
            <div class="pr-product-card">
                <div class="pr-product-img-wrap">
                    <span class="pr-product-badge">
                        <?php
                        if ($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing')
                            echo 'Leather & Leatherite Products';
                        elseif ($slug === 'polymer-polymeric-products' || $slug === 'polymer-polymeric-products-testing' || $slug === 'leather-and-leather-products' || $slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing')
                            echo 'Textile & Textile Products';
                        elseif ($slug === 'rubber-and-rubber-products' || $slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing')
                            echo 'Polymer & Polymeric Products';
                        elseif ($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food')
                            echo 'Food & Agricultural Products';
                        elseif ($slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing')
                            echo 'Residue & Contaminants in Food';
                        else
                            echo 'Toys & Similar Products';
                        ?>
                    </span>
                    <img src="assets/images/plastic_resin/PET Bottles.webp" alt="Product Image">
                </div>
                <div class="pr-product-label">
                    <span>Explore Category </span>

                </div>
                <p class="explore-text">Chemical safety testing for
                    <?php echo ($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing') ? 'leather and related products' : (($slug === 'polymer-polymeric-products' || $slug === 'polymer-polymeric-products-testing' || $slug === 'leather-and-leather-products' || $slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing' || $slug === 'rubber-and-rubber-products' || $slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing') ? 'polymers, textiles and related materials' : (($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food' || $slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing') ? 'environmental and food samples' : 'toys and children\'s products')); ?>,
                    ensuring compliance with international directives.
                </p>

            </div>
            <div class="pr-product-card">
                <div class="pr-product-img-wrap">
                    <span class="pr-product-badge">
                        <?php
                        if ($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing')
                            echo 'Polymer & Polymeric Products';
                        elseif ($slug === 'rubber-and-rubber-products' || $slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing')
                            echo 'Leather & Leatherite Products';
                        elseif ($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food')
                            echo 'Animal Food & Feed';
                        elseif ($slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing')
                            echo 'Food & Agricultural Products';
                        else
                            echo 'Rubber & Rubber Products';
                        ?>
                    </span>
                    <img src="assets/images/plastic_resin/Packaging Film.webp" alt="Product Image">
                </div>
                <div class="pr-product-label">
                    <span>Explore Category </span>

                </div>
                <p class="explore-text">Determine formulation compliance and safety of
                    <?php echo strtolower($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing' || $slug === 'rubber-and-rubber-products' || $slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing' ? 'polymer & leatherite products' : (($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food' || $slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing') ? 'food and agricultural feed' : 'rubber & rubber products')); ?>
                    ensuring performance in demanding environments.
                </p>

            </div>
            <div class="pr-product-card">
                <div class="pr-product-img-wrap">
                    <span class="pr-product-badge">
                        <?php
                        if ($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing' || $slug === 'leather-and-leather-products' || $slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing')
                            echo 'Toys & Similar Products';
                        elseif ($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food')
                            echo 'Atmospheric Pollution';
                        elseif ($slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing')
                            echo 'Animal Food & Feed';
                        else
                            echo 'Packaging Specific Products';
                        ?>
                    </span>
                    <img src="assets/images/plastic_resin/Plumbing Equipment.webp" alt="Product Image">
                </div>
                <div class="pr-product-label">
                    <span>Explore Category </span>

                </div>
                <p class="explore-text">Comprehensive testing protocols evaluating safety and properties of
                    <?php echo strtolower($slug === 'textiles-and-textile-products' || $slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing' || $slug === 'leather-and-leather-products' || $slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing' ? 'toys and similar products' : (($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food' || $slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing') ? 'environmental monitoring samples' : 'food and industrial packaging')); ?>.
                </p>

            </div>

        </div>
    </div>
</section>

<!-- ==============================
     FAQ + EXPERT GUIDANCE SECTION
============================== -->
<section class="pr-faq-section">
    <div class="container pr-faq-inner">

        <!-- Left: Expert Guidance -->
        <div class="pr-faq-left">
            <div class="pr-section-label">- Support</div>
            <h2 class="pr-faq-title"><em>Frequently Asked<br>Questions</em></h2>
            <p class="pr-faq-sub">Find answers to the most common questions about our chemical testing and certification
                services.</p>

            <div class="pr-expert-card">
                <div class="pr-expert-chat-icon"><i class="bi bi-chat-dots"></i></div>
                <h3 class="pr-expert-title">Need Expert <span class="pr-blue">Guidance?</span></h3>
                <p class="pr-expert-desc">Our qualified team of polymer scientists and compliance specialists are
                    available to discuss your testing requirements and recommend the right programme.</p>
                <p class="pr-expert-contact-label">Direct contact:</p>
                <p class="pr-expert-email"><i class="bi bi-lock"></i>info@akc-ptarc.com</p>
                <a href="contact-us.php" class="pr-btn pr-btn-outline-white pr-btn-full">Contact Our Team</a>
            </div>
        </div>

        <!-- Right: FAQ Accordion -->
        <div class="pr-faq-right">
            <?php if ($slug === 'polymer-polymeric-products' || $slug === 'polymer-polymeric-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for polymer products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for polymer materials and polymeric products across all eight disciplines -
                            Chemical, Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and
                            Electrical & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'textile-textile-products' || $slug === 'textile-textile-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for textile products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for textile fibres, yarns, and fabrics across all eight disciplines -
                            Chemical, Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and
                            Electrical & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'leather-leatherite-products' || $slug === 'leather-leatherite-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for leather products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for leather and leatherite (synthetic leather) materials across all eight
                            disciplines - Chemical, Mechanical, Thermal, Rheological, Optical, Material Characterisation,
                            Biological, and Electrical & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'rubber-rubberic-products' || $slug === 'rubber-rubberic-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for rubber products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for rubber compounds and rubber products across all eight disciplines -
                            Chemical, Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and
                            Electrical & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'toys-similar-products' || $slug === 'toys-similar-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for toys and similar products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for toys and similar products across all eight disciplines - Chemical,
                            Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and Electrical
                            & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'packaging-specific-products' || $slug === 'packaging-specific-products-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for packaging specific products?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for packaging specific products across all eight disciplines - Chemical,
                            Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and Electrical
                            & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'residue-contaminants-in-food' || $slug === 'residue-contaminants-in-food-testing' || $slug === 'residue-contaminants-food'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for residues and contaminants in food?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for residues and contaminants in food across all eight disciplines - Chemical,
                            Mechanical, Thermal, Rheological, Optical, Material Characterisation, Biological, and Electrical
                            & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php elseif ($slug === 'atmospheric-pollution' || $slug === 'atmospheric-pollution-testing'): ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What disciplines of testing are available for atmospheric pollution and environmental monitoring?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC offers testing for atmospheric pollution samples and environmental matrices across all eight
                            disciplines - Chemical, Mechanical, Thermal, Rheological, Optical, Material Characterisation,
                            Biological, and Electrical & Electronics - through a single accredited laboratory platform.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time depends on the specific tests requested; standard test programmes are typically
                            completed within 5-10 working days, with expedited options available on request.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Are the test reports accepted internationally?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All reports are issued under ISO/IEC 17025 (NABL) accreditation and are recognised for
                            regulatory submissions in India and internationally, subject to the specific standard applied.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="pr-faq-item pr-faq-open">
                    <button class="pr-faq-q">
                        What types of chemical testing services does AKC provide?
                        <span class="pr-faq-icon active"><i class="bi bi-chevron-up"></i></span>
                    </button>
                    <div class="pr-faq-a">
                        <p>AKC provides a full spectrum of chemical testing services including chemical composition
                            analysis, restricted substance testing (REACH, RoHS, Prop 65), polymer and material testing,
                            contaminant screening, heavy metal analysis, formulation testing, and regulatory compliance
                            certification. Our accredited laboratories are equipped to handle a wide range of sample types
                            across multiple industry sectors.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        How long does chemical testing take?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Turnaround time varies by test type and sample complexity. Standard tests are typically completed
                            within 5-10 working days. Expedited processing is available on request - contact our lab team to
                            discuss your timeline.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Do AKC laboratories follow international standards?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. All testing at AKC is performed in accordance with ISO, ASTM, REACH, EN 71, FDA, and
                            NABL-accredited methodologies, ensuring results are accepted by regulators, certification
                            bodies, and international buyers.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        Can AKC provide certification reports?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Yes. AKC issues NABL-formatted test and certification reports suitable for product certification
                            submissions, regulatory filings, customer quality audits, and import-export compliance.</p>
                    </div>
                </div>
                <div class="pr-faq-item">
                    <button class="pr-faq-q">
                        What sample submission requirements apply?
                        <span class="pr-faq-icon"><i class="bi bi-chevron-down"></i></span>
                    </button>
                    <div class="pr-faq-a" style="display:none;">
                        <p>Sample requirements vary by test. Generally, we require labelled samples in appropriate
                            quantities with a completed test request form. Our team will advise you on specific submission
                            requirements based on your material and test protocol.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ==============================
     CUSTOM CTA SECTION
============================== -->
<section class="pr-cta">
    <div class="container pr-cta-inner">
        <div class="pr-cta-content">
            <div class="pr-section-label">- Get Started</div>
            <h2 class="pr-cta-title">Need Plastic & Polymer <br><span class="pr-blue">Testing Services?</span></h2>
            <p class="pr-cta-desc">Our accredited laboratory team is ready to discuss your testing needs, process
                globally recognised test results for your plastic and resin-based products.</p>
        </div>
        <div class="pr-cta-buttons">

            <a href="#available-tests"
                onclick="event.preventDefault(); const target = document.getElementById('available-tests'); const offset = 80; const bodyRect = document.body.getBoundingClientRect().top; const elementRect = target.getBoundingClientRect().top; const elementPosition = elementRect - bodyRect; const offsetPosition = elementPosition - offset; window.scrollTo({ top: offsetPosition, behavior: 'smooth' });"
                class="pr-btn pr-btn-outline-white"><i class="bi bi-flask"></i> Book a test</a>
        </div>
    </div>
</section>


<!-- Book Test Modal -->
<div id="bookTestModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div
        style="background:#fff; width:95%; max-width:1380px; height:94vh; max-height:920px; border-radius:14px; position:relative; overflow:hidden; box-shadow:0 15px 40px rgba(0,0,0,0.4);">
        <button onclick="closeBookTestPopup()"
            style="position:absolute; top:20px; right:20px; background:#fff; border:1px solid #e2e8f0; width:40px; height:40px; border-radius:50%; font-size:1.2rem; cursor:pointer; color:#0A58CA; z-index:10; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(0,0,0,0.1); transition:all 0.3s;"><i
                class="bi bi-x-lg"></i></button>
        <iframe id="bookTestIframe" src="" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script>
    function openBookTestPopup(disciplineName = '', productName = '', testName = '', standards = '') {
        var modal = document.getElementById('bookTestModal');
        var iframe = document.getElementById('bookTestIframe');

        if (!disciplineName) {
            var activeTab = document.querySelector('.pr-tab-btn.active') || document.querySelector('.pr-tab.active');
            if (activeTab) {
                disciplineName = activeTab.innerText.trim();
            }
        }

        var url = 'book-test.php?popup=1';
        if (disciplineName) url += '&discipline=' + encodeURIComponent(disciplineName);
        if (productName) url += '&product=' + encodeURIComponent(productName);
        if (testName) url += '&test=' + encodeURIComponent(testName);
        if (standards) url += '&standards=' + encodeURIComponent(standards);

        iframe.src = url;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeBookTestPopup() {
        var modal = document.getElementById('bookTestModal');
        var iframe = document.getElementById('bookTestIframe');
        iframe.src = '';
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>

<!-- Sidebar Accordion JS -->
<script>
    function toggleSidebarList(element) {
        var list = element.nextElementSibling;
        var icon = element.querySelector('span i');
        if (list && list.classList.contains('pr-sidebar-list')) {
            var isCurrentlyOpen = (list.style.display === 'block');

            // Close all other dropdown sections so only one remains open at a time
            document.querySelectorAll('.pr-sidebar .pr-sidebar-category').forEach(function (cat) {
                cat.classList.remove('active');
                var catIcon = cat.querySelector('span i');
                if (catIcon) {
                    catIcon.classList.remove('bi-chevron-down');
                    catIcon.classList.add('bi-chevron-right');
                }
            });
            document.querySelectorAll('.pr-sidebar .pr-sidebar-list').forEach(function (l) {
                l.style.display = 'none';
            });

            // If it was not open, open it
            if (!isCurrentlyOpen) {
                list.style.display = 'block';
                element.classList.add('active');
                if (icon) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                }
            }
        }
    }
</script>

<!-- FAQ Accordion JS -->
<script>
    document.querySelectorAll('.pr-faq-q').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.pr-faq-item');
            var answer = item.querySelector('.pr-faq-a');
            var icon = item.querySelector('.pr-faq-icon i');
            var isOpen = item.classList.contains('pr-faq-open');

            // Close all
            document.querySelectorAll('.pr-faq-item').forEach(function (i) {
                i.classList.remove('pr-faq-open');
                i.querySelector('.pr-faq-a').style.display = 'none';
                var ic = i.querySelector('.pr-faq-icon i');
                ic.className = 'bi bi-chevron-down';
                i.querySelector('.pr-faq-icon').classList.remove('active');
            });

            // Open clicked if it was closed
            if (!isOpen) {
                item.classList.add('pr-faq-open');
                answer.style.display = 'block';
                icon.className = 'bi bi-chevron-up';
                item.querySelector('.pr-faq-icon').classList.add('active');
            }
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const facilityTabs = document.querySelectorAll('.facility-tabs .tab-btn');
        const facilityImages = document.querySelectorAll('.facility-images img');

        // Image mapping for different categories using available lab images
        const imageSets = {
            'Chemical': [
                'assets/images/facility_lab_work.png',
                'assets/images/facility_lab_work.png',
                'assets/images/facility_lab_work.png'
            ],
            'Mechanical': [
                'assets/images/laboratory_facility_1.png',
                'assets/images/laboratory_facility_2.png',
                'assets/images/laboratory_facility_3.png'
            ],
            'Biological': [
                'assets/images/testing-qc.jpg',
                'assets/images/laboratory_facility_1.png',
                'assets/images/facility_lab_work.png'
            ],
            'Electrical': [
                'assets/images/laboratory_facility_3.png',
                'assets/images/testing-qc.jpg',
                'assets/images/laboratory_facility_2.png'
            ],
            'Material Characterization': [
                'assets/images/laboratory_facility_2.png',
                'assets/images/facility_lab_work.png',
                'assets/images/laboratory_facility_1.png'
            ],
            'Thermal': [
                'assets/images/facility_lab_work.png',
                'assets/images/laboratory_facility_3.png',
                'assets/images/testing-qc.jpg'
            ],
            'Rheological': [
                'assets/images/laboratory_facility_1.png',
                'assets/images/testing-qc.jpg',
                'assets/images/laboratory_facility_2.png'
            ],
            'Optical': [
                'assets/images/testing-qc.jpg',
                'assets/images/facility_lab_work.png',
                'assets/images/laboratory_facility_3.png'
            ]
        };

        facilityTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active class from all tabs
                facilityTabs.forEach(t => t.classList.remove('active'));
                // Add active class to the clicked tab
                this.classList.add('active');

                // Get category name from tab text
                const category = this.textContent.trim();

                // Get mapped images, fallback to Chemical if not found
                const newImages = imageSets[category] || imageSets['Chemical'];

                // Update images with a smooth fade effect
                facilityImages.forEach((img, index) => {
                    img.style.opacity = '0';
                    setTimeout(() => {
                        if (newImages[index]) {
                            img.src = newImages[index];
                        }
                        img.style.transition = 'opacity 0.4s ease';
                        img.style.opacity = '1';
                    }, 300);
                });
            });
        });
    });

    function openDisciplineTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("pr-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("pr-tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
            tablinks[i].style.background = "#f8fafc";
            tablinks[i].style.color = "#1e293b";
            tablinks[i].style.borderColor = "#cbd5e1";
        }
        var targetTab = document.getElementById(tabName);
        if (targetTab) {
            targetTab.style.display = "block";
        }
        evt.currentTarget.className += " active";
        evt.currentTarget.style.background = "#0A58CA";
        evt.currentTarget.style.color = "#fff";
        evt.currentTarget.style.borderColor = "#0A58CA";
    }

    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($catSlug || isset($_GET['category'])): ?>
            var targetSection = document.getElementById('available-tests');
            if (targetSection) {
                setTimeout(function () {
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 180);
            }
        <?php endif; ?>
    });
</script>

<style>
    @keyframes fadeEffect {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>



<?php include 'includes/footer.php'; ?>