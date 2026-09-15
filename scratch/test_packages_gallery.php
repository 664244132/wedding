<?php
/**
 * scratch/test_packages_gallery.php - Verification Suite for packages.php and gallery.php
 */

echo "====================================================\n";
echo "   PACKAGES & GALLERY INTEGRATION TEST SUITE       \n";
echo "====================================================\n\n";

require_once(__DIR__ . '/../config.php');

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

// 1. PHP Syntax Lints
$lintPkg = shell_exec("C:\\xampp\\php\\php.exe -l packages.php");
assertTrue(strpos($lintPkg, "No syntax errors detected") !== false, "Lint: packages.php");

$lintGal = shell_exec("C:\\xampp\\php\\php.exe -l gallery.php");
assertTrue(strpos($lintGal, "No syntax errors detected") !== false, "Lint: gallery.php");

// 2. Test Package Image Assets on Disk
$pkg_images = [
    'img/packages/pkg_1774564008_5.webp',
    'img/packages/pkg_1774564080_9.jpg',
    'img/packages/pkg_1774564119_0.jpg'
];

foreach ($pkg_images as $img) {
    $full = __DIR__ . '/../' . $img;
    assertTrue(file_exists($full) && filesize($full) > 1000, "Package Image Exists & Valid: $img (" . (file_exists($full) ? filesize($full) : 0) . " bytes)");
}

// 3. Test Gallery Image Assets on Disk
$res_gal = mysqli_query($conn, "SELECT id, image_path, caption FROM gallery ORDER BY id DESC");
$gal_count = 0;

while ($r = mysqli_fetch_assoc($res_gal)) {
    $gal_count++;
    $file_path = __DIR__ . '/../uploads/gallery/' . $r['image_path'];
    assertTrue(file_exists($file_path) && filesize($file_path) > 1000, "Gallery Image Exists & Valid [ID {$r['id']}]: {$r['image_path']}");
    assertTrue(!empty($r['caption']), "Gallery Caption Not Empty [ID {$r['id']}]: '{$r['caption']}'");
}

assertTrue($gal_count >= 12, "Total gallery items in database is 12 (Actual: $gal_count)");

// 4. Render packages.php
ob_start();
include(__DIR__ . '/../packages.php');
$pkg_html = ob_get_clean();

assertTrue(!empty($pkg_html), "packages.php rendered HTML successfully");
assertTrue(strpos($pkg_html, '1. แพ็คเกจราคาประหยัด') !== false, "packages.php contains Economy Package");
assertTrue(strpos($pkg_html, '2. แพ็คเกจมาตรฐาน') !== false, "packages.php contains Standard Package");
assertTrue(strpos($pkg_html, '3. แพ็คเกจพรีเมียม') !== false, "packages.php contains Premium Package");
assertTrue(strpos($pkg_html, '11,000') !== false, "packages.php displays ฿11,000");
assertTrue(strpos($pkg_html, '20,000') !== false, "packages.php displays ฿20,000");
assertTrue(strpos($pkg_html, '30,000') !== false, "packages.php displays ฿30,000");
assertTrue(strpos($pkg_html, 'pkg_1774564008_5.webp') !== false, "packages.php references Economy Image");
assertTrue(strpos($pkg_html, 'pkg_1774564080_9.jpg') !== false, "packages.php references Standard Image");
assertTrue(strpos($pkg_html, 'pkg_1774564119_0.jpg') !== false, "packages.php references Premium Image");

// 5. Render gallery.php
ob_start();
include(__DIR__ . '/../gallery.php');
$gal_html = ob_get_clean();

assertTrue(!empty($gal_html), "gallery.php rendered HTML successfully");
assertTrue(strpos($gal_html, 'uploads/gallery/1774565658_69c5b91a2c398.jpg') !== false, "gallery.php includes image 1774565658");
assertTrue(strpos($gal_html, 'uploads/gallery/1774565628_69c5b8fc92dfd.jpg') !== false, "gallery.php includes image 1774565628");
assertTrue(strpos($gal_html, 'uploads/gallery/1774565613_69c5b8eda7a82.jpg') !== false, "gallery.php includes image 1774565613");
assertTrue(strpos($gal_html, 'พรีเวดดิ้งเอาท์ดอร์ริมหาดทรายยามพระอาทิตย์ตก') !== false, "gallery.php renders Sunset Beach caption");
assertTrue(strpos($gal_html, 'พรีเวดดิ้งสไตล์เกาหลีมินิมอล') !== false, "gallery.php renders Korean Minimalist caption");
assertTrue(strpos($gal_html, 'ชุดไทยบรมพิมานและชุดไทยสากลคู่บ่าวสาว') !== false, "gallery.php renders Royal Thai caption");
assertTrue(strpos($gal_html, 'loading="lazy"') !== false, "gallery.php uses lazy loading");

echo "\n----------------------------------------------------\n";
echo " Total Tests: " . ($successCount + count($errors)) . "\n";
echo " Passed: $successCount\n";
echo " Failed: " . count($errors) . "\n";
echo "----------------------------------------------------\n";

if (empty($errors)) {
    echo "\n>>> ALL PACKAGES & GALLERY TESTS PASSED! <<<\n";
    exit(0);
} else {
    echo "\n>>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
