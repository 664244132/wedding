<?php
/**
 * scratch/test_services.php - Integration Verification Test Suite
 */

echo "====================================================\n";
echo "   ETERNAL LOVE STUDIO - VERIFICATION TEST SUITE\n";
echo "====================================================\n\n";

$errors = [];
$successCount = 0;

function assertTrue($condition, $testName) {
    global $errors, $successCount;
    if ($condition) {
        echo " [PASS] $testName\n";
        $successCount++;
    } else {
        echo " [FAIL] $testName\n";
        $errors[] = $testName;
    }
}

// 1. Test PHP syntax
$lintOutput = shell_exec("C:\\xampp\\php\\php.exe -l services.php");
assertTrue(strpos($lintOutput, "No syntax errors detected") !== false, "PHP Syntax Lint on services.php");

// 2. Render services.php buffer
ob_start();
include(__DIR__ . '/../services.php');
$html = ob_get_clean();

assertTrue(!empty($html), "services.php successfully rendered HTML output");

// 3. Test Modals presence
assertTrue(strpos($html, 'id="modal24"') !== false, "Modal for 'ชุดไทย' (#modal24) exists in DOM");
assertTrue(strpos($html, 'id="modal25"') !== false, "Modal for 'ชุดไทยสากล' (#modal25) exists in DOM");

// 4. Test Thai Dresses (5 items)
$thai_dresses = [
    'ชุดไทยศิวาลัย - Siwalai Royal Gold',
    'ชุดไทยบรมพิมาน - Classic Ivory Boromphiman',
    'ชุดไทยจักรพรรดิ - Imperial Masterpiece',
    'ชุดไทยจักรี - Golden Blossom Chakkri',
    'ชุดไทยดุสิต - Celestial Pearl Dusit'
];

foreach ($thai_dresses as $td) {
    assertTrue(strpos($html, $td) !== false, "Traditional outfit rendered: '$td'");
}

// 5. Test Modern Thai Dresses (5 items)
$modern_dresses = [
    'ชุดไทยประยุกต์ศิวาลัย - Modern Couture Siwalai',
    'ชุดไทยเมอร์เมดร่วมสมัย - Mermaid Contemporary Thai',
    'ชุดไทยประยุกต์มินิมอล - Minimalist Pure Ivory',
    'ชุดไทยโมเดิร์นล้านนา - Modern Lanna Royal Fusion',
    'ชุดไทยร่วมสมัยโรสโกลด์ - Rose Gold Contemporary Thai'
];

foreach ($modern_dresses as $md) {
    assertTrue(strpos($html, $md) !== false, "Modern Thai outfit rendered: '$md'");
}

// 6. Test Image assets physical existence
$image_files = [
    'img/icons/icon_thai.jpg',
    'img/icons/icon_modern_thai.jpg',
    'img/gallery/gal_thai_siwalai.jpg',
    'img/gallery/gal_thai_boromphiman.jpg',
    'img/gallery/gal_thai_chakkraphat.jpg',
    'img/gallery/gal_thai_chakkri.jpg',
    'img/gallery/gal_thai_dusit.jpg',
    'img/gallery/gal_modern_siwalai.jpg',
    'img/gallery/gal_modern_mermaid.jpg',
    'img/gallery/gal_modern_minimal.jpg',
    'img/gallery/gal_modern_lanna.jpg',
    'img/gallery/gal_modern_rosegold.jpg'
];

foreach ($image_files as $img) {
    $fullPath = __DIR__ . '/../' . $img;
    assertTrue(file_exists($fullPath) && filesize($fullPath) > 0, "Image asset exists & not empty: '$img'");
}

// 7. Test booking.php AJAX API integration
require_once(__DIR__ . '/../config.php');

$res24 = mysqli_query($conn, "SELECT count(*) as c FROM service_images WHERE service_id = 24");
$row24 = mysqli_fetch_assoc($res24);
assertTrue((int)$row24['c'] === 5, "Database service_images has 5 rows for service_id = 24 (ชุดไทย)");

$res25 = mysqli_query($conn, "SELECT count(*) as c FROM service_images WHERE service_id = 25");
$row25 = mysqli_fetch_assoc($res25);
assertTrue((int)$row25['c'] === 5, "Database service_images has 5 rows for service_id = 25 (ชุดไทยสากล)");

// 8. Test Accessibility and Security standards
assertTrue(strpos($html, 'aria-labelledby="modal24Label"') !== false, "Accessibility: modal24 has aria-labelledby");
assertTrue(strpos($html, 'aria-labelledby="modal25Label"') !== false, "Accessibility: modal25 has aria-labelledby");
assertTrue(strpos($html, 'loading="lazy"') !== false, "Performance: images have loading='lazy'");
assertTrue(strpos($html, 'redirectForm') !== false, "Booking workflow: redirectForm exists");

echo "\n----------------------------------------------------\n";
echo " Total Tests: " . ($successCount + count($errors)) . "\n";
echo " Passed: $successCount\n";
echo " Failed: " . count($errors) . "\n";
echo "----------------------------------------------------\n";

if (empty($errors)) {
    echo "\n>>> ALL VERIFICATION TESTS PASSED SUCCESSFULLY! <<<\n";
    exit(0);
} else {
    echo "\n>>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
