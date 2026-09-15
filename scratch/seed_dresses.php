<?php
// scratch/seed_dresses.php
require_once __DIR__ . '/../config.php';

// 1. Ensure directories exist
$icons_dir = __DIR__ . '/../img/icons';
$gallery_dir = __DIR__ . '/../img/gallery';

if (!file_exists($icons_dir)) {
    mkdir($icons_dir, 0777, true);
}
if (!file_exists($gallery_dir)) {
    mkdir($gallery_dir, 0777, true);
}

// 2. Helper to generate elegant wedding placeholder images using GD
function createWeddingImage($filePath, $width, $height, $title, $subtitle, $bgColorHex = '3a2e24', $accentColorHex = 'c5a059') {
    $img = imagecreatetruecolor($width, $height);
    
    // Parse hex colors
    $r = hexdec(substr($bgColorHex, 0, 2));
    $g = hexdec(substr($bgColorHex, 2, 2));
    $b = hexdec(substr($bgColorHex, 4, 2));
    
    $ar = hexdec(substr($accentColorHex, 0, 2));
    $ag = hexdec(substr($accentColorHex, 2, 2));
    $ab = hexdec(substr($accentColorHex, 4, 2));
    
    // Create gradient background
    for ($y = 0; $y < $height; $y++) {
        $factor = $y / $height;
        $currR = (int)($r * (1 - $factor * 0.4));
        $currG = (int)($g * (1 - $factor * 0.4));
        $currB = (int)($b * (1 - $factor * 0.4));
        $lineColor = imagecolorallocate($img, $currR, $currG, $currB);
        imageline($img, 0, $y, $width, $y, $lineColor);
    }
    
    $gold = imagecolorallocate($img, $ar, $ag, $ab);
    $white = imagecolorallocate($img, 255, 255, 255);
    $cream = imagecolorallocate($img, 245, 240, 230);
    $border = imagecolorallocate($img, $ar, $ag, $ab);
    
    // Decorative border
    imagerectangle($img, 15, 15, $width - 16, $height - 16, $border);
    imagerectangle($img, 18, 18, $width - 19, $height - 19, $border);
    
    // Center title and subtitle
    $font = 5; // Built-in font
    $titleWidth = imagefontwidth($font) * strlen($title);
    $titleX = max(25, (int)(($width - $titleWidth) / 2));
    $titleY = (int)($height / 2 - 20);
    imagestring($img, $font, $titleX, $titleY, $title, $gold);
    
    $subFont = 3;
    $subWidth = imagefontwidth($subFont) * strlen($subtitle);
    $subX = max(25, (int)(($width - $subWidth) / 2));
    $subY = (int)($height / 2 + 10);
    imagestring($img, $subFont, $subX, $subY, $subtitle, $cream);
    
    // Studio watermark
    $brand = "ETERNAL LOVE LUXURY";
    $bWidth = imagefontwidth(2) * strlen($brand);
    imagestring($img, 2, (int)(($width - $bWidth) / 2), $height - 35, $brand, $gold);
    
    imagejpeg($img, $filePath, 90);
    imagedestroy($img);
}

// 3. Generate icon images
createWeddingImage($icons_dir . '/icon_thai.jpg', 200, 200, 'THAI TRADITIONAL', 'Royal Collection', '2d2218', 'd4af37');
createWeddingImage($icons_dir . '/icon_modern_thai.jpg', 200, 200, 'MODERN THAI', 'Contemporary Fusion', '26201a', 'c5a059');

// 4. Update services table
$update_services = [
    [
        'id' => 24,
        'title' => 'ชุดไทย',
        'icon' => 'icon_thai.jpg',
        'desc' => 'สัมผัสความวิจิตรบรรจงแห่งชุดไทยพระราชนิยม ทรงคุณค่า สง่างามเหนือกาลเวลา'
    ],
    [
        'id' => 25,
        'title' => 'ชุดไทยสากล',
        'icon' => 'icon_modern_thai.jpg',
        'desc' => 'ผสมผสานอัตลักษณ์ไทยเข้ากับความหรูหราสากล ดีไซน์ร่วมสมัยโดดเด่นไม่ซ้ำใคร'
    ]
];

foreach ($update_services as $srv) {
    $stmt = mysqli_prepare($conn, "UPDATE services SET icon = ?, description = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $srv['icon'], $srv['desc'], $srv['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// 5. Clear previous images and insert 10 dresses
$stmt_del = mysqli_prepare($conn, "DELETE FROM service_images WHERE service_id IN (24, 25)");
if ($stmt_del) {
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
}

$dresses = [
    // ชุดไทย (service_id = 24)
    [24, 'gal_thai_siwalai.jpg', 'ชุดไทยศิวาลัย - Siwalai Royal Gold', 15900, 'Siwalai Royal Gold', 'Traditional Thai Haute Couture'],
    [24, 'gal_thai_boromphiman.jpg', 'ชุดไทยบรมพิมาน - Classic Ivory Boromphiman', 13900, 'Boromphiman Classic', 'Royal Lamphun Brocade'],
    [24, 'gal_thai_chakkraphat.jpg', 'ชุดไทยจักรพรรดิ - Imperial Masterpiece', 18900, 'Chakkraphat Masterpiece', 'Imperial Crystal Embroidery'],
    [24, 'gal_thai_chakkri.jpg', 'ชุดไทยจักรี - Golden Blossom Chakkri', 12900, 'Chakkri Golden Blossom', 'Golden Chiffon Drape'],
    [24, 'gal_thai_dusit.jpg', 'ชุดไทยดุสิต - Celestial Pearl Dusit', 14500, 'Dusit Celestial Pearl', 'Handcrafted Pearl Beading'],
    
    // ชุดไทยสากล (service_id = 25)
    [25, 'gal_modern_siwalai.jpg', 'ชุดไทยประยุกต์ศิวาลัย - Modern Couture Siwalai', 16900, 'Modern Couture Siwalai', 'French Lace & Thai Silk'],
    [25, 'gal_modern_mermaid.jpg', 'ชุดไทยเมอร์เมดร่วมสมัย - Mermaid Contemporary Thai', 15500, 'Contemporary Mermaid Thai', 'Silk Organza Silhouette'],
    [25, 'gal_modern_minimal.jpg', 'ชุดไทยประยุกต์มินิมอล - Minimalist Pure Ivory', 12900, 'Minimalist Pure Ivory', 'Timeless Architectural Thai Gown'],
    [25, 'gal_modern_lanna.jpg', 'ชุดไทยโมเดิร์นล้านนา - Modern Lanna Royal Fusion', 14900, 'Modern Lanna Fusion', 'Northern Thai Silk Ballgown'],
    [25, 'gal_modern_rosegold.jpg', 'ชุดไทยร่วมสมัยโรสโกลด์ - Rose Gold Contemporary Thai', 17500, 'Rose Gold Contemporary', 'Champagne Rose Gold Drape']
];

$stmt_ins = mysqli_prepare($conn, "INSERT INTO service_images (service_id, image_path, caption, img_price) VALUES (?, ?, ?, ?)");
if ($stmt_ins) {
    foreach ($dresses as $d) {
        // Create dress image in img/gallery
        createWeddingImage($gallery_dir . '/' . $d[1], 400, 520, $d[4], $d[5], '33261c', 'c5a059');
        
        mysqli_stmt_bind_param($stmt_ins, "issi", $d[0], $d[1], $d[2], $d[3]);
        mysqli_stmt_execute($stmt_ins);
    }
    mysqli_stmt_close($stmt_ins);
}

echo "Database and Media Assets seeded successfully!\n";
