<?php
/**
 * scratch/seed_packages_gallery.php
 * Provision high-quality pre-wedding images for packages and gallery
 */

require_once(__DIR__ . '/../config.php');

$pkg_dir = __DIR__ . '/../img/packages';
$gal_dir = __DIR__ . '/../uploads/gallery';

if (!file_exists($pkg_dir)) mkdir($pkg_dir, 0777, true);
if (!file_exists($gal_dir)) mkdir($gal_dir, 0777, true);

/**
 * Helper to download an image or generate an elegant GD fallback
 */
function saveWeddingImage($targetPath, $downloadUrl, $fallbackTitle, $fallbackSubtitle, $width = 600, $height = 800) {
    echo "Processing $targetPath... ";
    
    // Attempt download with timeout
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 8,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);

    $data = @file_get_contents($downloadUrl, false, $ctx);

    if ($data !== false && strlen($data) > 2000) {
        // If webp is requested, check if we can save or convert
        file_put_contents($targetPath, $data);
        echo "Downloaded OK (" . strlen($data) . " bytes)\n";
        return;
    }

    // Fallback: Generate elegant luxury wedding graphic with GD
    echo "Generating GD Luxury Artwork... ";
    $img = imagecreatetruecolor($width, $height);
    
    // Luxury dark warm brown gradient
    $r1 = 58; $g1 = 46; $b1 = 36;
    $r2 = 25; $g2 = 20; $b2 = 15;

    for ($y = 0; $y < $height; $y++) {
        $ratio = $y / $height;
        $r = (int)($r1 + ($r2 - $r1) * $ratio);
        $g = (int)($g1 + ($g2 - $g1) * $ratio);
        $b = (int)($b1 + ($b2 - $b1) * $ratio);
        $col = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $width, $y, $col);
    }

    $gold = imagecolorallocate($img, 212, 175, 55);
    $lightGold = imagecolorallocate($img, 235, 215, 155);
    $white = imagecolorallocate($img, 255, 255, 255);
    
    // Elegant border
    imagerectangle($img, 20, 20, $width - 21, $height - 21, $gold);
    imagerectangle($img, 24, 24, $width - 25, $height - 25, $gold);

    // Title & Subtitle
    $font = 5;
    $tWidth = imagefontwidth($font) * strlen($fallbackTitle);
    imagestring($img, $font, max(30, (int)(($width - $tWidth) / 2)), (int)($height / 2 - 25), $fallbackTitle, $gold);

    $sWidth = imagefontwidth(3) * strlen($fallbackSubtitle);
    imagestring($img, 3, max(30, (int)(($width - $sWidth) / 2)), (int)($height / 2 + 10), $fallbackSubtitle, $lightGold);

    $brand = "ETERNAL LOVE PRE-WEDDING";
    $bWidth = imagefontwidth(2) * strlen($brand);
    imagestring($img, 2, (int)(($width - $bWidth) / 2), $height - 45, $brand, $white);

    // Save depending on extension
    $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    if ($ext === 'webp' && function_exists('imagewebp')) {
        imagewebp($img, $targetPath, 85);
    } else {
        imagejpeg($img, $targetPath, 88);
    }
    imagedestroy($img);
    echo "Generated OK\n";
}

// ----------------------------------------------------
// 1. Process 3 Package Images
// ----------------------------------------------------
echo "\n--- SEEDING PACKAGE IMAGES ---\n";
$packages_to_seed = [
    [
        'file' => $pkg_dir . '/pkg_1774564008_5.webp',
        'url'  => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=800',
        'title' => 'ECONOMY PACKAGE',
        'subtitle' => 'Romantic Natural Garden Pre-Wedding'
    ],
    [
        'file' => $pkg_dir . '/pkg_1774564080_9.jpg',
        'url'  => 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800',
        'title' => 'STANDARD PACKAGE',
        'subtitle' => 'Classic White Gown & Tuxedo Studio'
    ],
    [
        'file' => $pkg_dir . '/pkg_1774564119_0.jpg',
        'url'  => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=800',
        'title' => 'PREMIUM PACKAGE',
        'subtitle' => 'Grand Luxury 5-Star Suite & Ballroom'
    ]
];

foreach ($packages_to_seed as $pkg) {
    saveWeddingImage($pkg['file'], $pkg['url'], $pkg['title'], $pkg['subtitle'], 700, 500);
}

// ----------------------------------------------------
// 2. Process 11 Missing Gallery Images & Captions
// ----------------------------------------------------
echo "\n--- SEEDING GALLERY IMAGES & CAPTIONS ---\n";
$gallery_to_seed = [
    [
        'id'   => 16,
        'file' => '1774565658_69c5b91a2c398.jpg',
        'url'  => 'https://images.unsplash.com/photo-1519225438550-3a9483329176?q=80&w=800',
        'caption' => 'พรีเวดดิ้งชุดวิวาห์สากลคลาสสิก ดั่งเทพนิยาย',
        'title' => 'FAIRYTALE ROMANCE',
        'sub'   => 'Classic Royal Wedding Gown'
    ],
    [
        'id'   => 15,
        'file' => '1774565628_69c5b8fc92dfd.jpg',
        'url'  => 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?q=80&w=800',
        'caption' => 'พรีเวดดิ้งเอาท์ดอร์ริมหาดทรายยามพระอาทิตย์ตก',
        'title' => 'SUNSET BEACH ROMANCE',
        'sub'   => 'Outdoor Coastal Pre-Wedding'
    ],
    [
        'id'   => 14,
        'file' => '1774565613_69c5b8eda7a82.jpg',
        'url'  => 'https://images.unsplash.com/photo-1606800052052-a08af7148866?q=80&w=800',
        'caption' => 'พรีเวดดิ้งสไตล์เกาหลีมินิมอล อบอุ่นละมุนใจ',
        'title' => 'KOREAN MINIMALIST',
        'sub'   => 'Warm & Romantic Studio'
    ],
    [
        'id'   => 13,
        'file' => '1774565607_69c5b8e725caf.jpg',
        'url'  => 'https://images.unsplash.com/photo-1544078751-58fee2d8a03b?q=80&w=800',
        'caption' => 'ชุดไทยบรมพิมานและชุดไทยสากลคู่บ่าวสาว',
        'title' => 'ROYAL THAI ELEGANCE',
        'sub'   => 'Brocade Silk & Golden Drape'
    ],
    [
        'id'   => 12,
        'file' => '1774565598_69c5b8de9531b.jpg',
        'url'  => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=800',
        'caption' => 'พรีเวดดิ้งชุดไทยประยุกต์ร่วมสมัย หรูหราสง่างาม',
        'title' => 'CONTEMPORARY THAI',
        'sub'   => 'Modern Couture Thai Fusion'
    ],
    [
        'id'   => 11,
        'file' => '1774565591_69c5b8d748abd.jpg',
        'url'  => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=800',
        'caption' => 'มนต์สะกดแห่งรักในสวนสไตล์ยุโรป',
        'title' => 'ENCHANTED GARDEN',
        'sub'   => 'European Botanical Romance'
    ],
    [
        'id'   => 10,
        'file' => '1774565581_69c5b8cde8016.jpg',
        'url'  => 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=800',
        'caption' => 'ชุดวิวาห์สไตล์วินเทจคลาสสิก บนบันไดวนสุดหรู',
        'title' => 'VINTAGE ELEGANCE',
        'sub'   => 'Grand Spiral Staircase'
    ],
    [
        'id'   => 9,
        'file' => '1774565572_69c5b8c482692.jpeg',
        'url'  => 'https://images.unsplash.com/photo-1529636798458-92182e662485?q=80&w=800',
        'caption' => 'พรีเวดดิ้งราตรีใต้แสงดาวและโบเก้ระยิบระยับ',
        'title' => 'MIDNIGHT STARLIGHT',
        'sub'   => 'Fairy Tale Bokeh Romance'
    ],
    [
        'id'   => 8,
        'file' => '1774565565_69c5b8bd59d2c.jpg',
        'url'  => 'https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=800',
        'caption' => 'ธีมแชมเปญกลามัวร์ ชุดราตรีกลิตเตอร์วิบวับ',
        'title' => 'CHAMPAGNE GLAMOUR',
        'sub'   => 'Haute Couture Evening Gown'
    ],
    [
        'id'   => 6,
        'file' => '1774565545_69c5b8a977492.webp',
        'url'  => 'https://images.unsplash.com/photo-1594552072238-b8a33785b261?q=80&w=800',
        'caption' => 'พรีเวดดิ้งชุดเมอร์เมดเน้นสรีระคู่สูททักซิโด้โมเดิร์น',
        'title' => 'MERMAID SILHOUETTE',
        'sub'   => 'Modern Black-Tie Tuxedo'
    ],
    [
        'id'   => 5,
        'file' => '1774565538_69c5b8a20514e.webp',
        'url'  => 'https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=800',
        'caption' => 'ชุดไทยจักรีสีชมพูกลีบบัวคู่ชุดไทยชายประยุกต์',
        'title' => 'LOTUS BLOSSOM CHAKKRI',
        'sub'   => 'Royal Thai Pink Blossom'
    ]
];

$stmt_up_gal = mysqli_prepare($conn, "UPDATE gallery SET caption = ? WHERE id = ?");

foreach ($gallery_to_seed as $item) {
    saveWeddingImage($gal_dir . '/' . $item['file'], $item['url'], $item['title'], $item['sub'], 600, 800);
    
    // Update caption in gallery table
    mysqli_stmt_bind_param($stmt_up_gal, "si", $item['caption'], $item['id']);
    mysqli_stmt_execute($stmt_up_gal);
}
mysqli_stmt_close($stmt_up_gal);

echo "\nALL PACKAGES & GALLERY IMAGES SEEDED SUCCESSFULLY!\n";
